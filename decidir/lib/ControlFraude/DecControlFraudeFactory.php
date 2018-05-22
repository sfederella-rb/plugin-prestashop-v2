<?php
require_once(dirname(__FILE__)."/DecControlFraudeRetail.php");
require_once(dirname(__FILE__)."/DecControlFraudeTicketing.php");
require_once(dirname(__FILE__)."/DecControlFraudeDigitalgoods.php");
require_once(dirname(__FILE__)."/DecControlFraudeService.php");
require_once(dirname(__FILE__)."/DecControlFraudeTravel.php");

class DecControlFraudeFactory {

	const DIGITAL_GOODS = "digitalgoods";
	const RETAIL = "retail";
    const TICKETING = "ticketing";
    const SERVICE = "service";
	const TRAVEL = "travel";

	public static function get_controlfraude_extractor($vertical, $customer, $cart, $amount){
		$instance = null;

        switch ($vertical) {
			case DecControlFraudeFactory::DIGITAL_GOODS:
				$instance = new DecControlFraudeDigitalgoods($customer, $cart, $amount);
			break;

			case DecControlFraudeFactory::RETAIL:
				$instance = new DecControlFraudeRetail($customer, $cart, $amount);
			break;
			
			case DecControlFraudeFactory::TICKETING:
				$instance = new DecControlFraudeTicketing($customer, $cart, $amount);
			break;
			
			case DecControlFraudeFactory::SERVICE:
				$instance = new DecControlFraudeService($customer, $cart, $amount);
			break;

            case DecControlFraudeFactory::TRAVEL:
                $instance = new DecControlFraudeTravel($customer, $cart, $amount);
                break;

			default:
				$instance = new DecControlFraudeRetail($customer, $cart);
			break;
		}

		return $instance;
	}
}