<?php
require_once(dirname(__FILE__)."/DecControlFraude.php");
require_once(dirname(__FILE__)."/../../classes/Productos.php");
require_once dirname(__FILE__) . '/../../../../config/config.inc.php';

class DecControlFraudeRetail extends DecControlFraude {
    public $totalAmount = 0;

	public function __construct($customer = array(), $cart = array(), $amount = NULL){
		parent::__construct($customer, $cart);
		$this->datasources["carrier"] = new Carrier($this->datasources["cart"]->id_carrier);
		$this->totalAmount = $amount;
	}

	protected function completeCSVertical() {
		$datosCS["send_to_cs"] 				= true;
		$datosCS["channel"] 				= "Web";
		$datosCS["bill_to"]["city"] 		= substr($this->getField($this->datasources['address'],"city"),0,250);
		$datosCS["bill_to"]["country"]		= $this->getField($this->datasources['country'],"iso_code");
		$datosCS["bill_to"]["customer_id"]	= (string)($this->datasources['customer']->id);
		$datosCS["bill_to"]["email"]		= $this->getField($this->datasources['customer'],"email");
		$datosCS["bill_to"]["first_name"]	= $this->getField($this->datasources['customer'],"firstname");
		$datosCS["bill_to"]["last_name"]	= $this->getField($this->datasources['customer'],"lastname"); 
		$datosCS["bill_to"]["phone_number"] = $this->_getPhone($this->datasources,false);
		$datosCS["bill_to"]["postal_code"]  = $this->getField($this->datasources['address'],"postcode");
		$datosCS["bill_to"]["state"]		= $this->_getStateIso($this->getField($this->datasources['address'],"id_state"));
		$datosCS["bill_to"]["street1"]		= $this->getField($this->datasources['address'],"address1");
		$datosCS["bill_to"]["street2"]		= "";
		$datosCS["ship_to"]["city"] 		= substr($this->getField($this->datasources['address'],"city"),0,250);
		$datosCS["ship_to"]["country"]		= $this->getField($this->datasources['country'],"iso_code");
		$datosCS["ship_to"]["customer_id"]	= (string)($this->datasources['customer']->id);
		$datosCS["ship_to"]["email"]		= $this->getField($this->datasources['customer'],"email");
		$datosCS["ship_to"]["first_name"]	= $this->getField($this->datasources['customer'],"firstname");
		$datosCS["ship_to"]["last_name"]	= $this->getField($this->datasources['customer'],"lastname"); 
		$datosCS["ship_to"]["phone_number"] = $this->_getPhone($this->datasources,false);
		$datosCS["ship_to"]["postal_code"]  = $this->getField($this->datasources['address'],"postcode");
		$datosCS["ship_to"]["state"]		= $this->_getStateIso($this->getField($this->datasources['address'],"id_state"));
		$datosCS["ship_to"]["street1"]		= $this->getField($this->datasources['address'],"address1");
		$datosCS["ship_to"]["street2"]		= "";
		$datosCS["currency"] 				= $this->getField($this->datasources['extra'],"moneda");
		$datosCS["amount"]					= $this->totalAmount;//number_format($this->getField($this->datasources['extra'],"total"),2,".","");
		if($this->datasources['cart']->id_guest == 2){
            $datosCS["is_guest"] = true;
        }else{
            $datosCS["is_guest"] = false;
        }
		$datosCS["days_in_site"]			= 0;
		$datosCS["password"]				= $this->datasources['customer']->passwd;
		$datosCS["num_of_transactions"] 	= 1;
		$datosCS["cellphone_number"] 		= $this->_getPhone($this->datasources,false);
		$datosCS["date_of_birth"]			= $this->datasources['customer']->birthday;//buscar fecha de nacimiento
		$datosCS["street"] 					= $this->getField($this->datasources['address'],"address1");
		$datosCS["days_to_delivery"]		= "0";
		$datosCS["dispatch_method"]			= "";
		$datosCS["tax_voucher_required"]	= false;
		$datosCS["customer_loyality_number"] = ""; 
		$datosCS["coupon_code"]				= "";

		return $datosCS;
	}

	protected function getCategoryArray($id_product){
		$controlFraude = new Product($id_product);
		$categories = $controlFraude->getDefaultCategory();
		
		$category_id = $categories[0];
		$category = new Category($category_id);
		
        $name = $category->getName();
		if(empty($name)) return "default";

		return $name;		
	}
}
