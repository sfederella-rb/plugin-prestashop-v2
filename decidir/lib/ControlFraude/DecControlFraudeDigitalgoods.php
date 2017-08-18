<?php

require_once(dirname(__FILE__)."/DecControlFraude.php");
require_once(dirname(__FILE__)."/../../classes/Productos.php");

class DecControlFraudeDigitalgoods extends DecControlFraude {

	protected function completeCSVertical(){
		$productos = $this->datasources["cart"]->getProducts();
		$controlFraude = new DecidirProductoControlFraude($productos[0]['id_product']);
		
		$datosCS["csmdd31"] = $controlFraude->tipo_delivery;
		return array_merge($this->getMultipleProductsInfo(), $datosCS);
	}

	protected function getCategoryArray($id_product){
		$controlFraude = new DecidirProductoControlFraude($id_product);
        return $controlFraude->codigo_producto;
	}
}
