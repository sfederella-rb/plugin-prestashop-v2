<?php
include_once dirname(__FILE__)."/FlatDb.php";
include_once dirname(__FILE__)."/../../vendor/autoload.php";

$operationid = $_GET['ord'];

$db = new FlatDb();
$db->openTable('ordenes');

$orden = $db->getRecords(array("id","merchantId","security","status","data","mediodepago","sar","form","gaa","requestkey","answerkey"),array("id" => $operationid));

$data = json_decode($orden[0]['data'],true);

$merchantId = $orden[0]['merchantId'];
$security = $orden[0]['security'];
$authorize = "PRISMA ".$security;

//común a todas los métodos
$http_header = array('Authorization'=>$authorize,
 'user_agent' => 'PHPSoapClient');

//datos constantes
define('CURRENCYCODE', "032");
define('MERCHANT', $merchantId);
define('ENCODINGMETHOD', 'XML');
define('SECURITY', $security);

$medio = $data['mediopago']['tipo'];
$data['mediopago']['medio_pago'] = $medio;
unset($data['mediopago']['tipo']);

if($medio == 26)  {
	$medio_pago = new Decidir\Data\Mediopago\Rapipago($data['mediopago']);
} else if($medio == 25) {
	$medio_pago = new Decidir\Data\Mediopago\PagoFacil($data['mediopago']);
} else if($medio == 41) {
	$medio_pago = new Decidir\Data\Mediopago\PagoMisCuentas($data['mediopago']);
} else {
	$medio_pago = new Decidir\Data\Mediopago\TarjetaCredito($data['mediopago']);
}

$cybersource = new Decidir\Data\Cybersource\Retail(
	$data['cs_data'],
	$data['cs_product']
);

if(array_key_exists("split", $data)) {
    $tipo = $data["split"]["tipo"];
    unset($data["split"]["tipo"]);
    if($tipo == "MontoFijo") {
        $split = new Decidir\Data\SplitTransacciones\MontoFijo($data["split"]);
    }
    else {
        $split = new Decidir\Data\SplitTransacciones\Porcentaje($data["split"]);
    }
}

$sar_data = new Decidir\Authorize\SendAuthorizeRequest\Data(array("security" => SECURITY, "encoding_method" => ENCODINGMETHOD, "merchant" => MERCHANT, "nro_operacion" => $operationid, "monto" => $data['monto'], "email_cliente" => $data["email_cliente"]));
$sar_data->setMedioPago($medio_pago);
//$sar_data->setCybersourceData($cybersource);
if(isset($split)) {
    $sar_data->setSplitData($split);
}

//creo instancia de la SDK
$connector = new Decidir\Connector($http_header, Decidir\Connector::DECIDIR_ENDPOINT_TEST);

try {
	$rta = $connector->Authorize()->sendAuthorizeRequest($sar_data);
} catch(Exception $e) {
	var_dump($e);die();
	$db->updateRecords(array("status" => "ERROR SAR"),array("id" => $operationid));
	header("Location: index.php");
}

$db->updateRecords(array("sar" => 1, "status" => "AUTORIZACION ENVIADA", "requestkey" => $rta->getRequestKey(), "publicrequestkey" => $rta->getPublicRequestKey()),array("id" => $operationid));
header("Location: index.php");

?>
