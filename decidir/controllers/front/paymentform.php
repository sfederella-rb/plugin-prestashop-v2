<?php
require_once (dirname(__FILE__) . '../../../../../config/config.inc.php');

Class DecidirPaymentformModuleFrontController extends ModuleFrontController
{

	public function init()
	{
	    $this->page_name = 'Payment'; // page_name and body id
	    $this->display_column_left = false;
		$this->display_column_right = false;
	    parent::init();
	}

	public function initContent()
	{	
		global $smarty;
		
	    parent::initContent();

		$smarty->assign(array(
			'jsLinkForm' => $this->getJsForm(),
			'endpoint' => $this->getEndpoint(),
			'publicKey' => $this->getPublicKey(),
			'email' => $this->getMail(),
			'name' => $this->getCompleteName(),
			'orderId' => "",
			'total' => $this->getTotal(),
			'currency' => $this->getCurrency(),
			'pmethod' => $this->getPaymentMethod(),
			'csDescription' => 'decidir_agregador',
			//'orderIdDec' => $this->getOrderIdDec(),
			'url_base' => _PS_BASE_URL_.__PS_BASE_URI__
		));
		
		//if(Tools::getValue('order') == null){
		if(false){
	    	$this->setTemplate('paymenterror.tpl');
	    }else{
	    	$this->setTemplate('paymentform.tpl');	
	    }
	}
	
	public function getOrgId()
	{
		$orgId = Configuration::get('DECIDIR_PRODUCCION_ORGID');
		//verifica si es ambiente de test o produccion
        if(!$this->module->getModo()){
            $orgId = Configuration::get('DECIDIR_TEST_ORGID');    
        }

        return $orgId;
	}
	/*
	public function getOrderIdDec()
	{
		$id_orden = Tools::getValue('order');
		$orderIdDec = ""; 

		$sql = 'SELECT id_orden_decidir FROM '._DB_PREFIX_.'decidir_transaccion WHERE id_orden = '.$id_orden;

		$dataTransacciontions = Db::getInstance()->ExecuteS($sql);

		if (!$dataTransacciontions){
			return null;
		}else{
			foreach($dataTransacciontions as $result){
				$orderIdDec = $result['id_orden_decidir'];
			}
		}

		return $orderIdDec; 
	}*/

	public function getPublicKey()
	{	
		$prefijo = $this->module->getPrefijoModo();
		return (string) Configuration::get($prefijo.'_ID_KEY_PUBLIC');
	}
	
	public function getTotal(){
		$cart = $this->context->cart;
		$total = $cart->getOrderTotal(true, Cart::BOTH);

		return $total; 
	}

	public function getMail()
	{
		return $this->context->customer->email;
	}

	public function getCompleteName()
	{
		$completeName = $this->context->customer->firstname." ";
		$completeName .= $this->context->customer->lastname;

		return $completeName;
	}

	public function getPaymentMethod()
	{	
		$paymethod = Tools::getValue('method');
			
		return $paymethod;

	}

	public function getCurrency()
	{
		global $cookie;
		
		$currency = new CurrencyCore($cookie->id_currency);
		$currency_code = $currency->iso_code;

		return $currency_code;
	}

	public function getJsForm()
	{	
		return "https://live.decidir.com/static/v1/decidir.js";
	}

	public function getEndpoint()
	{	
		$endpoint = "https://developers.decidir.com/api/v1";		

		if($this->module->getModo()){
			$endpoint = "https://live.decidir.com/api/v1";
		}

		return (string)$endpoint;
	}
}
