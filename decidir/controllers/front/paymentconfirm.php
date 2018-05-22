<?php

require_once(dirname(__FILE__). '/../../../../config/config.inc.php');

Class DecidirPaymentConfirmModuleFrontController extends ModuleFrontController
{
	public function init()
	{
	    $this->page_name = 'Confirmacion de Pago'; // page_name and body id
	    $this->display_column_left = false;
		$this->display_column_right = false;
	    parent::init();
	}

	public function initContent()
	{
        global $smarty;
        parent::initContent();

        if (version_compare(_PS_VERSION_, '1.7.0.0') >= 0 ) {
            $this->setTemplate('module:decidir/views/templates/front/confirmblock17.tpl');
        } else {
            $this->setTemplate('confirmblock16.tpl');
        }

	    //obtengo el order id
	    $sql = 'SELECT id_order FROM '._DB_PREFIX_.'orders WHERE id_cart ='.Tools::getValue('order');
		$orderID = Db::getInstance()->ExecuteS($sql);

	    $order = new Order((int)$orderID[0]['id_order']);

	    $state = $order->getCurrentState();

		$estadoDenegada = Module::getInstanceByName('decidir')->getOrderStatesModulo('DENEGADA');

		//corregir esto de abajo
	    if($state != $estadoDenegada){

			$smarty->assign(
						array(
							'order_ref' => $order->reference,
							'url_orderdetails' => _PS_BASE_URL_.__PS_BASE_URI__.'?controller=order-detail&id_order='.$orderID[0]['id_order'],
							'status' => 'ok',
							'status_desc' => $state
						)
					);

		}else{

			$smarty->assign(
						array(
							'status' => 'faile',
							'status_desc' => $state
						)
					);
		}

	}
}
