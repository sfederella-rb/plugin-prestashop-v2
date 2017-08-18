<?php

use DecidirTransaccion as Transaccion;
use DecidirProductoControlFraude as ProductoControlFraude;

require_once (dirname(__FILE__) . '../../../classes/Transaccion.php');
require_once (dirname(__FILE__) . '../../../classes/Productos.php');
require_once dirname(__FILE__) . '/../../../../config/config.inc.php';
require_once (dirname(__FILE__) . '/../../vendor/vendor/autoload.php');
require_once (dirname(__FILE__) . '/../../controllers/back/AdminMediosController.php');
require_once (dirname(__FILE__) . '/../../controllers/back/AdminInteresController.php');
require_once (dirname(__FILE__) . '/../../controllers/back/AdminPromocionesController.php');
require_once (dirname(__FILE__) . '../../../classes/Refunds.php');

class DecidirPaymentModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $display_column_left = false;
    private $codigoAprobacion = "approved";

    public function initContent()
    {
        $this->display_column_left = false;//para que no se muestre la columna de la izquierda
        $this->db = Db::getInstance();
        parent::initContent();//llama al init() de FrontController, que es la clase padre

        //variables a usar
        $cart = $this->context->cart;

        
        if($cart->id == null && Tools::getValue('order') != null) {
            $order = new Order((int)Tools::getValue('order'));
            $cart = new Cart((int)$order->id_cart);
        }

        $total = $cart->getOrderTotal(true, Cart::BOTH);
        $cliente = new Customer($cart->id_customer);//recupera al objeto cliente
        $paso = (int) Tools::getValue('paso');

        if($cart != null){
            $this->tranEstado = $this->_tranEstado($cart->id);
        }
    
        try 
        {
            if (!$this->module->checkCurrency($cart))
                Tools::redirect('index.php?controller=order');
            
            //si el carrito esta vacio
            if ($cart == NULL ||  $cart->getProducts() == NULL || $cart->getOrderTotal(true, Cart::BOTH) == 0)
                throw new Exception('Carrito vacio');

			//Prefijo que se usa para la peticion al servicio
			$prefijo = $this->module->getPrefijoModo();	

			$connector = $this->prepare_connector($prefijo);

            //servicio helthcheck
            $healthResponse = $this->healthCheckService($connector);

            switch ($paso)
            {
                case 1:
                    //seleccion de tarjetas, banco, promociones
                    $this->module->log->info('Carga formulario de pago');

                    $params['paso'] = 1;
                    $params['order'] = (int)$cart->id;
                    $params['total'] = $total;

                    Tools::redirect($this->context->link->getModuleLink('decidir', 'paymentform', $params, true));

                    break;
                case 2:
                    //formulario de pago, ejecucion de pago
                    $this->module->log->info('confirmacion de pago');

                    $data = array();
                    $data['pmethod'] = Tools::getValue('pmethod');
                    $data['entity'] = Tools::getValue('entity');
                    $data['installment'] = Tools::getValue('installment');
                    $data['intallmenttype'] = Tools::getValue('intallmenttype');
                    $data['token'] = Tools::getValue('token');

                    $this->paymentStep($cart, $prefijo, $cliente, $connector, $data);

                    $template='paymenterror';

                    break;
                case 3:
                    //devolucion-anulacion
                    $data['order'] = Tools::getValue('order');
                    $data['orderOperation'] = Tools::getValue('orderOperation');
                    $data['amount'] = Tools::getValue('amount');
                    $data['type'] = Tools::getValue('type');

                    $this->_refundExecute($data, $prefijo, $connector);
                    die;

                    break;      
                default:
                    //redirect to step 1
                    Tools::redirect($this->context->link->getModuleLink('decidir', 'payment', array ('paso' => '1'), true));
                    break;
            }

        }catch (Exception $e){

            $this->module->log->error('EXCEPCION',$e);
            $template='paymenterror';
        }

        //asigno las variables que se van a a ver en la template de payment (payment.tpl)
        $this->context->smarty->assign(array(
            'cart_id' => $cart->id,
            'nbProducts' => $cart->nbProducts(),//productos
            'url_base' => _PS_BASE_URL_.__PS_BASE_URI__
        ));

        //----------------------
        // 
        //  aca va codigo par conpatibilidad 1.7
        //
        //----------------------
        
        $this->setTemplate($template.'.tpl');//plantilla que se va a usar.
    }
    
    protected function ExecutePayment($optionsData, $req_params_data, $connector, $cliente, $cart)
    {   
        $this->module->log->info('servicio activo');

        try {
            $response = $connector->payment()->ExecutePayment($optionsData);
            $this->module->log->info('response de confirmacion de pago - '.json_encode($response));

            $this->_saveToken($connector, $response, $cliente, $req_params_data);

        }catch(\Exception $e) {
            $this->module->log->info('Error en el pago - '.json_encode($e->getData()));

            Tools::redirect($this->context->link->getModuleLink('decidir','errorpage',array(),true)); 
        }

        $now = new DateTime();
        $now->format('Y-m-d H:i:s');

        $responsetoJson = array();
        $responsetoJson['id'] = $response->getId();
        $responsetoJson['site_transaction_id'] = $response->getSiteTransactionId();
        $responsetoJson['token'] = $response->getToken();
        $responsetoJson['user_id'] =  $response->getUserId();
        $responsetoJson['amount'] = $response->getAmount();
        $responsetoJson['payment_type'] = $response->getPaymentType();
        $responsetoJson['status'] = $response->getStatus();
        $responsetoJson['bin'] = $response->getBin();

        $this->_tranUpdate($cart->id, array("user_id" => $cliente->id, "decidir_order_id" => $response->getSiteTransactionId(), "payment_response" => json_encode($responsetoJson), "marca" => $optionsData['payment_method_id'], "banco" => $req_params_data['entity'], "cuotas" => $optionsData['installments'], "date" => $now->format('Y-m-d H:i:s')));

        return $response; 
    }

    public function validatePayment($response){
        $this->module->log->info('Redireccionando al controller de validacion del pago');

        if($response->getStatus() == $this->codigoAprobacion){
            $param = array();
            $param['amount'] = $response->getAmount();
            $param['status'] = $response->getStatus();

            Tools::redirect($this->context->link->getModuleLink(strtolower($this->module->name), 'validation', $param, false));//redirijo al 

        }else{
            throw new Exception($e->getData()); 
            $this->module->log->info('Error en el pago:'.json_encode($e->getData()));

            Tools::redirect($this->context->link->getModuleLink('decidir','errorpage',array(),true));
        }
    }

	protected function prepare_connector($prefijo)
	{	
        $keys_data = array('public_key' => Configuration::get($prefijo.'_ID_KEY_PUBLIC'), 
                           'private_key' => Configuration::get($prefijo.'_ID_KEY_PRIVATE'));

        $connector = new \Decidir\Connector($keys_data, $this->module->getEnvironment());

		return $connector;
	}
	
	protected function prepare_order($cart, $client)
	{  
        $data = array("user_id" => $client->id, "order_id" => $cart->id);

		if($this->tranEstado == 0){ 
			$this->_tranCrear($cart->id, $data);
		}

        /*if($this->_tranEstado($cart->id) == 2){
            throw new Exception("pago ya realizado");
        }*/    
	}
	
	public function get_paydata($cart, $prefijo, $cliente, $params_data)
	{   
        
        $payment = new AdminMediosController();
        $pMethod = $payment->getById($params_data['pmethod']);

        $currency = $this->module->getCurrency((int)$cart->id_currency);

        $idInstallment = explode("_",$params_data['installment']);

        $params = array( "site_transaction_id" => "dec_".time().$cart->id.rand(1,900),
                          "token" => $params_data['token'],
                          "user_id" => $this->context->customer->email,
                          "payment_method_id" => intval($pMethod[0]['id_decidir']),
                          "amount" => (float)$cart->getOrderTotal(true),
                          "bin" => "450799",
                          "currency" => $currency[0]['iso_code'],
                          "installments" => intval($idInstallment[1]),
                          "description" => (string)$cart->id,
                          "payment_type" => "single",
                          "sub_payments" => array(),
                          "fraud_detection" => array()
                        );

        return $params;
	}
    
    public function paymentStep($cart, $prefijo, $cliente, $connector, $req_params_data)
    {   
        $this->module->log->info('realiza el pago');
        $this->prepare_order($cart, $cliente);

        //aca adentro hago el calculo de interes
        $optionsData = $this->get_paydata($cart, $prefijo, $cliente, $req_params_data);
        $this->module->log->info('payment params - '.json_encode($optionsData));

        //calcula costo financiero
        $optionsData = $this->_calcFinancialCost($optionsData, $cart, $req_params_data);

        //Cybersource
        if(Configuration::get('DECIDIR_CONTROLFRAUDE_ENABLE_CS')){
            $segmento = $this->module->getSegmentoTienda(true);
            $customer = '';

            $dataCSVertical = DecControlFraudeFactory::get_controlfraude_extractor($segmento, $customer, $cart)->getCSVertical();
            $dataProducst = DecControlFraudeFactory::get_controlfraude_extractor($segmento, $customer, $cart)->getProducts();

            $cs = new \Decidir\Cybersource\Retail($dataCSVertical, $dataProducst);
            
            $this->module->log->info('params Cybersource - '.json_encode($cs->getData()));

            $connector->payment()->setCybersource($cs->getData());
        }

        $rta = $this->ExecutePayment($optionsData, $req_params_data, $connector, $cliente, $cart);

        $this->validatePayment($rta);
    }
    
    private function _tranEstado($cartId)
    {   
        $res = $this->db->executeS("SELECT * FROM "._DB_PREFIX_."decidir_transacciones WHERE order_id=".$cartId);

        if(!$res) {
            return 0;
        } else {

            if($res[0]['payment_response'] == "") {
                return 1;
            }else{
                return 2; 
            } 
        }
    }
    

    private function _tranCrear($cartId, $data)
    {   
        $this->db->insert("decidir_transacciones", $data);
        $this->tranEstado = $this->_tranEstado($cartId);
    }
    
    private function _tranUpdate($cartId, $data)
    {   
        $this->db->update("decidir_transacciones", $data, "order_id = ".$cartId, 0, true);
        $this->tranEstado = $this->_tranEstado($cartId);
    }

    public function healthCheckService($connector){
        $response = $connector->healthcheck()->getStatus();

        if(empty($response->getName())){
            return false;
        }

        return true;
    }

    public function getTokenList($connector, $user_id){
        try {
            $response = $connector->cardToken()->tokensList(array(), $user_id);

        }catch(\Exception $e) {
            $this->module->log->info('Error al obtener el listado de tarjetas tokenizadas - '.json_encode($e->getData()));

            Tools::redirect($this->context->link->getModuleLink('decidir','errorpage',array(),true)); 
        }

        return $response->getTokens();
    }

    private function _tokensUpdate($data)
    {   
        $sql = 'UPDATE '._DB_PREFIX_.'decidir_tokens SET token="'.$data['token'].'", bin='.$data['bin'].', last_four_digits='.$data['last_four_digits'].', expiration_month='.$data['expiration_month'].', expiration_year='.$data['expiration_year'].' WHERE id='.$data['id'];

        if(!Db::getInstance()->execute($sql)){
            die('Error al actualizar el token de tarjeta.');        
        }
    }

    private function _tokensCreate($data)
    {   
        $this->db->insert("decidir_tokens", $data);
    }

    private function _saveToken($connector, $response, $cliente, $req_params_data){
        $tokens = array();
        $instPMethod = new AdminMediosController();
        $tokensListById = $instPMethod->getTokenByUserId($cliente->email, $response->getBin(), $response->getPaymentMethodId());
        $tokenList = $this->getTokenList($connector, $cliente->email);

        //search or insert new token
        foreach($tokenList as $index => $value){

            $instPMethod = new AdminMediosController();
            $tokensListById = $instPMethod->getTokenByUserId($cliente->email, $value['bin'], $value['payment_method_id']);
            $data = array();

            if(empty($tokensListById)){

                $data['user_id'] = $cliente->email;
                $data['name'] = $value['card_holder']['name'];
                $data['banco_id'] = $req_params_data['pmethod'];
                $data['marca_id'] =  $req_params_data['entity'];
                $data['token'] = $value['token'];
                $data['payment_method_id'] = $value['payment_method_id'];
                $data['bin'] = $value['bin'];
                $data['last_four_digits'] = $value['last_four_digits'];
                $data['expiration_month'] = $value['expiration_month'];
                $data['expiration_year'] = $value['expiration_year'];

                $this->_tokensCreate($data);
            }else{

                $data['id'] = $tokensListById[0]['id'];
                $data['token'] = $value['token'];
                $data['bin'] = $value['bin'];
                $data['last_four_digits'] = $value['last_four_digits'];
                $data['expiration_month'] = $value['expiration_month'];
                $data['expiration_year'] = $value['expiration_year'];

                $this->_tokensUpdate($data);
            }

        }    
    }

    private function _calcFinancialCost($optionsData, $cart, $req_params_data)
    {
        $installmentArray = explode("_", $req_params_data['installment']);

        $data = array();
        $data['id_interes'] = $installmentArray[0];
        $data['installment'] = $installmentArray[1];
        $data['payment_method'] = $optionsData['payment_method_id'];
        $data['active'] = 1;

        if($req_params_data['intallmenttype']){
            $instancePromos = new AdminPromocionesController();
            $res = $instancePromos->getById($installmentArray[0]);
            $data['coeficient'] = $res[0]['coeficient'];

        }else{
            $instanceInteres = new AdminInteresController();
            $res = $instanceInteres->getById($installmentArray[0]);
            $data['coeficient'] = $res[0]['coeficient'];
        }

        //recalcula el costo financiero y lo devuelve
        $calcRta = $this->module->calcFinancialCost($data,$optionsData['amount']);
        $optionsData['amount'] = floatval(str_replace(',', '.', str_replace('.', '', $calcRta['totalCost'])));

        return $optionsData;
    }
    
    private function _refundExecute($data, $prefijo, $connector)
    {
        $sql = 'SELECT payment_response FROM '._DB_PREFIX_.'decidir_transacciones WHERE decidir_order_id="'.$data['orderOperation'].'"';
        $res = $this->db->executeS($sql);

        $instanceRefund = new Refunds();
        $orderResponse = json_decode($res[0]['payment_response'], TRUE);

        if($data['type'] == 1)//type 1 = total refund
        {   
            $response = $instanceRefund->totalRefund($orderResponse['id'], $orderResponse['amount'], $data, $connector);
        }else{
            $response = $instanceRefund->partialRefund($orderResponse['id'], $data, $connector);
        }
        
        echo $response;  
    } 
    
}
