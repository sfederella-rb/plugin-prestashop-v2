<?php

require_once(dirname(__FILE__)."/DecControlFraude.php");
//require_once(dirname(__FILE__)."/../../classes/Productos.php");

class DecControlFraudeTravel extends DecControlFraude {
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
        $datosCS["currency"] 				= $this->getField($this->datasources['extra'],"moneda");
        $datosCS["amount"]					= $this->totalAmount;//number_format($this->getField($this->datasources['extra'],"total"),2,".","");
        if($this->datasources['cart']->id_guest == 2){
            $datosCS["is_guest"] = true;
        }else{
            $datosCS["is_guest"] = false;
        }
        $datosCS["days_in_site"]			= 0;
        $datosCS["is_guest"]                = false;
        $datosCS["password"]				= $this->datasources['customer']->passwd;
        $datosCS["num_of_transactions"] 	= 1;
        $datosCS["cellphone_number"] 		= $this->_getPhone($this->datasources,false);
        $datosCS["date_of_birth"]			= $this->datasources['customer']->birthday;//buscar fecha de nacimiento
        $datosCS["street"] 					= $this->getField($this->datasources['address'],"address1");
        $datosCS["reservation_code"] 		= "";
        $datosCS["third_party_booking"] 	= false;
        $datosCS["departure_city"] 		    = "";
        $datosCS["final_destination_city"] 	= "";
        $datosCS["international_flight"] 	= false;
        $datosCS["frequent_flier_number"] 	= "";
        $datosCS["class_of_service"] 	    = "";
        $datosCS["day_of_week_of_flight"] 	= 2;
        $datosCS["airline_code"] 	        = "";
        $datosCS["code_share"] 	            = "";
        $datosCS["week_of_year_of_flight"] 	= 5;
        $datosCS["decision_manager_travel"]["complete_route"] 	= "";
        $datosCS["decision_manager_travel"]["journey_type"] 	= "";
        $datosCS["decision_manager_travel"]["departure_date"]["departure_time"]     = "";
        $datosCS["decision_manager_travel"]["departure_date"]["departure_zone"]     = "";
        $datosCS["airline_number_of_passengers"] 	= 1;

        return $datosCS;
    }

	protected function getCategoryArray($id_product){
		$controlFraude = new DecidirProductoControlFraude($id_product);
        return $controlFraude->codigo_producto;
	}
}
