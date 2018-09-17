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
			'id_user' => $this->getUserId(),
			'name' => $this->getCompleteName(),
			'orderId' => "",
			'total' => $this->getTotal(),
			'currency' => $this->getCurrency(),
			'pmethod' => $this->getPaymentMethod(),
			'url_base' => _PS_BASE_URL_.__PS_BASE_URI__
		));

        if (version_compare(_PS_VERSION_, '1.7.0.0') >= 0 ) {
            $this->setTemplate('module:decidir/views/templates/front/formblock17.tpl');
        } else {
            $this->setTemplate('formblock16.tpl');
        }
    }

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

	public function getUserId(){
		return $this->context->customer->id;
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
		return "https://live.decidir.com/static/v2/decidir.js";
	}

	public function getEndpoint()
	{	
		$endpoint = "https://developers.decidir.com/api/v2";		

		if($this->module->getModo()){
			$endpoint = "https://live.decidir.com/api/v2";
		}

		return (string)$endpoint;
	}
}
