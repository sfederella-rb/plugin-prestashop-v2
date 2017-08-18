<?php
include_once dirname(__FILE__)."/../../vendor/autoload.php";

$http_header = array('Authorization'=>'PRISMA DU2VD1SD7WBH91BZLXBGR9BY',
 'user_agent' => 'PHPSoapClient');

//datos constantes
define('CURRENCYCODE', "032");
define('MERCHANT', "00040716");
define('ENCODINGMETHOD', 'XML');
define('SECURITY', 'DU2VD1SD7WBH91BZLXBGR9BY');

//creo instancia de la SDK
$connector = new Decidir\Connector($http_header, Decidir\Connector::DECIDIR_ENDPOINT_TEST);


//SendAuthorizeRequest
$medio_pago = new Decidir\Data\Mediopago\TarjetaCredito(array(
                                                          "medio_pago" => 1,
                                                          "cuotas" => 6
                                                        ));
// $split = new Decidir\Data\SplitTransacciones\MontoFijo( array(
//                                                        'impdist'=>'40.00#10.00',//Importe de cada una de las substransacciones. Los importes deben postearse separados por "#".
//                                                        'sitedist'=>'00040716#00050716',//Número de comercio de cada uno de los subcomercios asociados al comercio padre
//                                                        'cuotasdist'=>'05#01',//cantidad de cuotas para cada subcomercio. Decimal de 2 dígitos.
//                                                        'idmodalidad'=>'S',// indica si la transacción es distribuida. (S= transacción distribuida; N y null = no distribida)
//                                                    ));

$sar_data = new Decidir\Authorize\SendAuthorizeRequest\Data(array(
	"security" => SECURITY,
	"encoding_method" => ENCODINGMETHOD,
	"merchant" => MERCHANT,
  // "nro_comercio" => "00050716",
	"nro_operacion" => 'php-pc-0706-6',
	"monto" => 50.00,
	"email_cliente" => "ejemplo@misitio.com"
));
$sar_data->setMedioPago($medio_pago);
// $sar_data->setSplitData($split);

var_dump($sar_data->getData());

$rta = $connector->Authorize()->sendAuthorizeRequest($sar_data);

echo "<h3>Respuesta SendAuthorizeRequest</h3>";
var_dump($rta);
die;

//GetAuthorizeAnswer
$gaa_data = new \Decidir\Authorize\GetAuthorizeAnswer\Data(array(
	"security" => SECURITY,
	"merchant" => MERCHANT,
	"requestKey" => '9eda0564-12c7-4019-5b49-410b0337eae6',
	"answerKey" => '42c9cf67-f24f-b8d7-7d7c-1984a22ba1c4'
));

$rta = $connector->Authorize()->getAuthorizeAnswer($gaa_data);

echo "<h3>Respuesta GetAuthorizeAnswer</h3>";
echo "<pre>";
print_r($rta);
echo "</pre>";

//GetOperationById
$gobi_data = new \Decidir\Operation\GetByOperationId\Data(array("idsite" => MERCHANT, "idtransactionsit" => 123456));

$rta = $connector->Operation()->getByOperationId($gobi_data);

echo "<h3>Respuesta GetOperationById</h3>";
var_dump($rta);

//Execute - Anulacion
$anul = new \Decidir\Authorize\Execute\Anulacion(array("security" => SECURITY, "merchant" => MERCHANT, "nro_operacion" => 123456));

$rta = $connector->Authorize()->execute($anul);

echo "<h3>Respuesta Execute - Anulacion</h3>";
var_dump($rta);

//Execute - Devolucion Totoal
$devol = new \Decidir\Authorize\Execute\Devolucion\Total(array("security" => SECURITY, "merchant" => MERCHANT, "nro_operacion" => 123456));

$rta = $connector->Authorize()->execute($devol);

echo "<h3>Respuesta Execute - Devolucion Total</h3>";
var_dump($rta);

//Execute - Devolucion Parcial
$devol = new \Decidir\Authorize\Execute\Devolucion\Parcial(array("security" => SECURITY, "merchant" => MERCHANT, "nro_operacion" => 123456, "monto" => 10.00));

$rta = $connector->Authorize()->execute($devol);

echo "<h3>Respuesta Execute - Devolucion Parcial</h3>";
var_dump($rta);
