<?php
require_once(dirname(__FILE__)."/DecControlFraudeRetail.php");
require_once(dirname(__FILE__)."/DecControlFraudeService.php");
require_once(dirname(__FILE__)."/DecControlFraudeTicketing.php");
require_once(dirname(__FILE__)."/DecControlFraudeDigitalgoods.php");

class DecControlFraudeFactory {

	//const DIGITAL_GOODS = "digitalgoods";
	const RETAIL = "retail";
	//const TRAVEL = "travel";
	//const TICKETING = "ticketing";
	//const SERVICE = "service";
	
	public static function get_controlfraude_extractor($vertical, $customer, $cart){
		$instance;
		switch ($vertical) {
			/*case DecControlFraudeFactory::DIGITAL_GOODS:
				$instance = new DecControlFraudeDigitalgoods($customer, $cart, $devicefinger);
			break;*/

			case DecControlFraudeFactory::RETAIL:
				$instance = new DecControlFraudeRetail($customer, $cart);
			break;
			
			/*case DecControlFraudeFactory::TICKETING:
				$instance = new DecControlFraudeTicketing($customer, $cart, $devicefinger);
			break;*/

			/*case DecControlFraudeFactory::TRAVEL:
				$instance = new DecControlFraudeTicketing($customer, $cart, $devicefinger);
			break;*/
			
			/*case DecControlFraudeFactory::SERVICE:
				$instance = new DecControlFraudeService($customer, $cart, $devicefinger);
			break;

			default:
				$instance = new DecControlFraudeRetail($customer, $cart, $devicefinger);
			break;*/
		}
		return $instance;
	}
}