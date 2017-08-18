<?php
	include_once('../../../../config/config.inc.php');

	if($_GET['ajax_payment_method'] && $_GET['ajax_payment_method'] != '')
	{	
		$type = "";

		if($_GET['ajax_payment_method'] == 1){
			$type = 'Tarjeta';
		}elseif($_GET['ajax_payment_method'] == 2){
			$type = 'Cupon';
		}

		$sql = 'SELECT id_medio AS id, name FROM ' . _DB_PREFIX_ . 'medios WHERE type = "'.$type.'" AND active = 1 ORDER BY name ASC';

    	$result = Db::getInstance()->ExecuteS($sql);


		echo json_encode($result);
	}

?>