<?php

Class DecidirErrorpageModuleFrontController extends ModuleFrontController
{	
	private $errorCode;
	private $paymentMethod;
	private $message;


	public function init()
	{
	    $this->page_name = 'Payment'; // page_name and body id
	    $this->display_column_left = false;
		$this->display_column_right = false;
	    parent::init();
	    $this->setTemplate('errorpage.tpl');
	}

	/*
	public function initContent()
	{	
		global $smarty;

	    parent::initContent();
	    $this->setTemplate('errorpage.tpl');

		$smarty->assign(array(
			'errorCode' => $this->Code(),
			'errorMessage' => $this->Message()
		));
	}
	
	public function Code(){

		$id_error = Tools::getValue('id_error');

		return $id_error; 
	}

	public function Message(){

		$instErroData = new DecidirErrorData($this->paymentMethod, $this->errorCode);
        $message = $instErroData->errorPage();

		return $message;
	}
	*/
}
