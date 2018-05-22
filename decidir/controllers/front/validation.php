<?php
require_once dirname(__FILE__) . '/../../../../config/config.inc.php';

class DecidirValidationModuleFrontController extends ModuleFrontController
{
	//valida que todo este bien
	public function postProcess()
	{	
		$prefijo= $this->module->getPrefijo('CONFIG_ESTADOS');
		//$orderState = Configuration::get($prefijo.'_APROBADA');//Order State si la transaccion es aprobada
		$orderState = 2;
		$cart = $this->context->cart;//recupero el carrito

        //si no hay un cliente registrado, o una direccion de entrega, o direccion de contacto o el modulo no esta activo
		if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active || 
				 !$this->module->isActivo())
			Tools::redirect('index.php?controller=order&step=1');//redirecciona al primer paso

		// Verifica que la opcion de pago este disponible
		$authorized = false;
		foreach (Module::getPaymentModules() as $module)
		{
			if ($module['name'] == $this->module->name)
			{
				$authorized = true;
				break;
			}
		}

		if (!$authorized)//si no esta disponible la opcion de pago
			die($this->module->l('Este modo de pago no esta disponible.', 'validation'));//avisa

		$customer = new Customer($cart->id_customer);//recupera al objeto cliente

		if (!Validate::isLoadedObject($customer))//si no hay un cliente
			Tools::redirect('index.php?controller=order&step=1');//redirecciona al primer paso

		$currency = $this->context->currency;//recupero la moneda de la compra

		$originAmount = (float)$cart->getOrderTotal(true, Cart::BOTH);//recupero el total de la compra
		
		$totalAmount = ((Tools::getValue('amount'))/100);

		$finCost = round(($totalAmount-$originAmount),2);

		$this->module->validateOrderDecidir((int)$cart->id, $orderState, $originAmount, $this->module->displayName, NULL, NULL, (int)$currency->id, false, $customer->secure_key, $finCost);

		Hook::exec('displayPaymentReturnPage',array());
	}
	
	/**
	 * Agrego los detalles propios de la transaccion al registro OrderPayment correspondiente
	 * @param int $id_order id de la orden creada
	 * @param array $transaccion respuesta de la transaccion
	 */
	private function _addPaymentDetalle($id_order, $transaccion)
	{
		$orden = new Order($id_order);

		$detalles = array(
			'transaction_id' => $transaccion['OPERATIONID'],
			'card_number' => $transaccion['CARDNUMBERVISIBLE'],
			'card_brand' => $transaccion['PAYMENTMETHODNAME']
		);

		Db::getInstance()->update(OrderPayment::$definition['table'], $detalles, 'order_reference=\''.$orden->reference.'\'');
	}
}