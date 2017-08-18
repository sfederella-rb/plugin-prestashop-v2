<?php
require_once dirname(__FILE__) . '/../../../../config/config.inc.php';

abstract class DecControlFraude {
  
	protected $datasources = array();
	
	public function __construct($customer = array(), $cart = array()){
		$customer= new Customer($cart->id_customer);

		$this->datasources = array("cart" => $cart, "customer" => $customer);

 		$address = $this->datasources['cart']->id_address_delivery;	
        $address = new Address($address);
		$country = new Country($address->id_country);

        $validOrders = Db::getInstance()->getValue('SELECT COUNT(`'.Order::$definition['primary'].'`) FROM '._DB_PREFIX_.Order::$definition['table'].' WHERE id_customer = '.$this->datasources['customer']->id.' AND valid = 1');
       	$extra = array("total" => $this->datasources['cart']->getOrderTotal(true, Cart::BOTH), "validOrders" => $validOrders, "ip" => Tools::getRemoteAddr(), "moneda" => "ARS");
		$this->datasources['address'] = $address;
		$this->datasources['country'] = $country;
		$this->datasources['extra'] = $extra;
	}
	
	/*	
	public function getDataCS(){
		$datosCS = $this->completeCS();

		$datos_cs = $datosCS;

		return $datos_cs;
	}
	*/
	
	public function getCSVertical(){
		
		$products_css = $this->completeCSVertical();

		return $products_css; 
	}	

	public function getProducts(){
		
		$products_css = $this->getMultipleProductsInfo();

		return $products_css; 
	}	

	protected abstract function completeCSVertical();

	protected abstract function getCategoryArray($productId);
	
	protected function getMultipleProductsInfo(){
		$productos = $this->datasources["cart"]->getProducts();
		$productsData = array();

        foreach ($productos as $key => $item) {
            $singleProdData = array(
	            'csitproductcode'=> $this->getCategoryArray($item['id_product']), 
                'csitproductdescription'=> substr(str_replace("#","",strip_tags($item['description_short'])),0,100), //Descripción del producto. MANDATORIO.
                'csitproductname'=> substr($item['name'],0,50), //Nombre del producto. MANDATORIO.
                'csitproductsku'=> substr((empty($item['reference'])? $item['id_product']: $item['reference']),0,250), //Código identificador del producto. MANDATORIO.
                'csittotalamount'=> number_format($item['total_wt'],2,".",""), 
                'csitquantity'=> intval($item['cart_quantity']),
                'csitunitprice'=> number_format($item['price_wt'],2,".","") //Formato Idem CSITTOTALAMOUNT. MANDATORIO
	        );

            $productsData[] = $singleProdData;
        }

		return $productsData;
	}
	
	protected function _getPhone($datasources, $mobile = false){
		if($mobile) {
			$data = $this->getField($datasources['address'],"phone_mobile");
			if (empty($data)) {
					return $this->_phoneSanitize($this->getField($datasources['address'],"phone"));
			}
			return $this->_phoneSanitize($this->getField($datasources['address'],"phone_mobile"));
		}
		$data = $this->getField($datasources['address'],"phone");
		if(empty($data)){
			return $this->_phoneSanitize($this->getField($datasources['address'],"phone_mobile"));
		}
		return $this->_phoneSanitize($this->getField($datasources['address'],"phone"));
	}
	
	protected function getField($datasource, $key){
		$return = "";
		try{
			if(is_array($datasource))
				$return = $datasource[$key];
			elseif(property_exists($datasource,$key))
				$return = $datasource->$key;
			else
				throw new Exception("No encontrado");
		}catch(Exception $e){
			$this->log("a ocurrido un error en el campo ". $key. " se toma el valor por defecto");
		}
		return $return;
	}

	protected function log($mensaje)
	{
		$nombre = 'CSlog';
		
		$archivo = fopen(dirname(__FILE__).'/../'.$nombre.'.txt', 'a+');
		fwrite($archivo, date('Y/m/d - H:i:s').' - '.$mensaje . PHP_EOL);
		fclose($archivo);
	}

	protected function getProdDescription($idProduct){
		global $cookie;

		$sql = 'SELECT description FROM '._DB_PREFIX_.'product_lang as pl WHERE pl.id_product = '.$idProduct .' AND pl.id_lang = '.$cookie->id_lang;

		$dataProduct = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);

		return $dataProduct[0]['description']; 
	}
	protected function _phoneSanitize($number){
		$number = str_replace(array(" ","(",")","-","+"),"",$number);
		
		if(substr($number,0,2)=="54") return $number;
		
		if(substr($number,0,2)=="15"){
			$number = substr($number,2,strlen($number));
		}
		if(strlen($number)==8) return "5411".$number;
		
		if(substr($number,0,1)=="0") return "54".substr($number,1,strlen($number));
		return "54".$number;
	}

    protected function _getStateIso($id)
    {
        $state = new State($id);
        return $state->iso_code;
    }
	
    protected function _getDateTimeDiff($fecha)
    {
        return date_diff(new DateTime($fecha), new DateTime())->format('%a');
    }
}
