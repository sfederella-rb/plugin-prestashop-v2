<?php

if (!defined('_PS_VERSION_'))
	exit;

require_once (dirname(__FILE__) . '../../../config/config.inc.php');
require_once (dirname(__FILE__) . '/classes/Transaccion.php');
require_once (dirname(__FILE__) . '/classes/Productos.php');
require_once (dirname(__FILE__) . '/classes/AdminFieldForm.php');
require_once (dirname(__FILE__) . '/lib/ControlFraude/DecControlFraudeFactory.php');
require_once (dirname(__FILE__) . '/lib/Logger/logger.php');
require_once (dirname(__FILE__) . '/controllers/back/AdminEntityController.php');
require_once (dirname(__FILE__) . '/classes/Entidades.php');
require_once (dirname(__FILE__) . '/controllers/back/AdminMediosController.php');
require_once (dirname(__FILE__) . '/classes/Medios.php');
require_once (dirname(__FILE__) . '/controllers/back/AdminPromocionesController.php');
require_once (dirname(__FILE__) . '/classes/Promociones.php');
require_once (dirname(__FILE__) . '/controllers/back/AdminInteresController.php');
require_once (dirname(__FILE__) . '/classes/Interes.php');
require_once (dirname(__FILE__) . '/controllers/front/paymentselect.php');

class Decidir extends PaymentModule
{
    //protected $config_form = false;
    const DECIDIR_ENDPOINT_TEST = "test";
    const DECIDIR_ENDPOINT_PROD = "prod";

	/** segmento de la tienda */
	private $segmento = array(
			                array('key' => 'retail', 'name' => 'Retail'),
                            array('key' => 'ticketing', 'name' => 'Ticketing'),
                            array('key' => 'digitalgoods', 'name' => 'Digital Goods'),
                            array('key' => 'service', 'name' => 'Service'),
                            array('key' => 'travel', 'name' => 'Travel')
				        );

	/** canal de ingreso del pedido */
	private $canal = array(
		                array('key' => '1', 'name' => 'Web'),
		                array('key' => '2', 'name' => 'Mobile'),
		                array('key' => '3', 'name' => 'Telefono')
			        );

	/** tipo de envio. Usado en el sistema de prevencion del fraude (ticketing) */
	protected $envio = array ('Pickup', 'Email', 'Smartphone', 'Other');

	/** tipo de delivery. Usado en el sistema de prevencion del fraude (digital goods) */
	protected  $delivery = array('WEB Session', 'Email', 'SmartPhone');

	protected $product_code = array('default', 'adult_content', 'coupon', 'electronic_good', 'electronic_software', 'gift_certificate', 'handling_only', 'service', 'shipping_and_handling', 'shipping_only', 'subscription');
	//estados default que se agregan durante la instalacion
	
	protected $default_states = array('proceso'=>3,'aprobada'=>2,'denegada'=>6,'pendiente'=>1);

	public $log;

	public function __construct()//constructor
	{	
		//module info
		$this->name = 'decidir';
		$this->tab = 'payments_gateways';
		$this->version = '1.0.6';
		$this->author = 'Prisma';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array('min' => '1.6.0', 'max' => _PS_VERSION_); 
		$this->bootstrap = true;
		parent::__construct();
		$this->displayName = $this->l('Decidir');
		$this->description = $this->l('Decidir, medios de pago on-line.');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
		//instance logger
		$this->log = $this->configureLog();
	}
	
	/**
	 * Don't forget to create update methods if needed:
	 * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
	 */
	public function install()
	{//instalacion del modulo
		if (Module::isInstalled('decidir'))
		{
		  Module::disableByName($this->name);//note during testing if this is not done, your module will show as installed in modules
		  die(Tools::displayError('Primero debe desinstalar la version anterior del modulo.'));
		}
		
		$this->createConfigVariables();
				
		include(dirname(__FILE__).'/sql/install.php');

		return parent::install() && $this->registerHook('displayPayment') && 
							$this->registerHook('displayBackOfficeHeader') && 
							$this->registerHook('displayPaymentReturnPage') && 
							$this->unregisterHook('displayAdminProductsExtra') &&
							$this->registerHook('displayShoppingCart') && 
							$this->registerHook('displayHeader') &&
							$this->registerHook('actionOrderSlipAdd') && 
							$this->registerHook('displayAdminOrder') &&
                            $this->registerHook('paymentOptions');//prestashop 1.7
	}

	public function uninstall()
	{//desinstalacion
		$this->deleteConfigVariables();
		return parent::uninstall();
	}
	
	public function configureLog() {
		$cart = $this->context->cart;
		$endpoint = ($this->getModo())?"DECIDIR_ENDPOINT_PROD":"DECIDIR_ENDPOINT_TEST";
		$logger = new DecidirPagoLogger();
		$logger->setPhpVersion(phpversion());
		$logger->setCommerceVersion(_PS_VERSION_);
		$logger->setPluginVersion($this->version);
		$payment = false;
		
		if($cart != null){
			if($cart->id != null){
				$payment = true;		
			} 
		}

		if($payment) {
			$logger->setEndPoint($endpoint);
			$logger->setCustomer($cart->id_customer);
			$logger->setOrder($cart->id);
		}

		$logger->setLevels("debug","fatal");
		$logger->setFile(dirname(__FILE__)."/decidir.log");
		
		return $logger->getLogger($payment);
	}
	
	public function getPrefijo($nombre)
	{
		$prefijo = 'DECIDIR';
		$variables = parse_ini_file('config.ini');
		
		if ( strcasecmp($nombre, 'PREFIJO_CONFIG') == 0)
			return $prefijo;
		
		foreach($variables as $key => $value){
			if ( strcasecmp($key, $nombre) == 0 )
				return $prefijo.'_'.$value;
		}
		return '';
	}
		
	/**
	 * Crea las variables de configuracion, asi se encuentran todas juntas en la base de datos
	 */
	public function createConfigVariables()
	{
		$prefijo = 'DECIDIR';
		$variables = parse_ini_file('config.ini');

		
		foreach ( Decidir\AdminFieldForm::getFormInputsNames( Decidir\AdminFieldForm::getConfigFormInputs() ) as $nombre)
		{
			Configuration::updateValue($prefijo.'_'.strtoupper($nombre),"");
		}
		foreach ( Decidir\AdminFieldForm::getFormInputsNames( Decidir\AdminFieldForm::getAmbienteFormInputs('test') ) as $nombre)
		{
			Configuration::updateValue($prefijo.'_'.$variables['CONFIG_TEST'].'_'.strtoupper( $nombre ),"");
		}
		foreach ( Decidir\AdminFieldForm::getFormInputsNames( Decidir\AdminFieldForm::getAmbienteFormInputs('produccion') ) as $nombre)
		{
			Configuration::updateValue($prefijo.'_'.$variables['CONFIG_PRODUCCION'].'_'.strtoupper( $nombre ),"");
		}
		foreach ( Decidir\AdminFieldForm::getFormInputsNames( Decidir\AdminFieldForm::getEstadosFormInputs() ) as $nombre)
		{	
			Configuration::updateValue($prefijo.'_'.$variables['CONFIG_ESTADOS'].'_'.strtoupper( $nombre ), $this->default_states[$nombre]);
		}
		foreach ( Decidir\AdminFieldForm::getFormInputsNames( Decidir\AdminFieldForm::getCybersourceFields() ) as $nombre)
		{
			Configuration::updateValue($prefijo.'_'.$variables['CONTROL_FRAUDE'].'_'.strtoupper( $nombre ),"");
		}
		foreach ( Decidir\AdminFieldForm::getFormInputsNames( Decidir\AdminFieldForm::getRapipagoFormInputs() ) as $nombre)
		{
			Configuration::updateValue($prefijo.'_'.$variables['CONFIG_RAPIPAGO'].'_'.strtoupper( $nombre ),"");
		}
		foreach ( Decidir\AdminFieldForm::getFormInputsNames( Decidir\AdminFieldForm::getPagofacilFormInputs() ) as $nombre)
		{
			Configuration::updateValue($prefijo.'_'.$variables['CONFIG_PAGOFACIL'].'_'.strtoupper( $nombre ),"");
		}
		foreach ( Decidir\AdminFieldForm::getFormInputsNames( Decidir\AdminFieldForm::getPagomiscuentasFormInputs() ) as $nombre)
		{
			Configuration::updateValue($prefijo.'_'.$variables['CONFIG_PAGOMISCUENTAS'].'_'.strtoupper( $nombre ),"");
		}
	}

	/**
	 * Carga el formulario de configuration del modulo.
	 */
	public function getContent()
	{
		$this->_postProcess();

		if(isset($_GET['section'])){
			$index_section = Tools::getValue('section');
		}else{
			$index_section = 1;
		}  

		$this->context->smarty->assign(array(
			'module_dir' 	 	  => $this->_path,
			'version'    	 	  => $this->version,
			'url_base'			  => _PS_BASE_URL_.__PS_BASE_URI__,
			'section_adminpage'	  => $index_section, 
			'config_general' 	  => $this->renderConfigForms(),
			'config_cybersource'  => $this->renderCyberSourcePage(),
			'cms_mediospago'      => $this->renderCMSMediosPagos(),
			'cms_entidades'       => $this->renderCMSEntidades(),
			'cms_planespago'      => $this->renderCMSPlanes(),
			'cms_interes'	      => $this->renderCMSInteres(),
			'cms_selectInteresList' => $this->renderCMSSelectInteres()
		));
		$output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');//recupero el template de configuracion
		
		return $output;
	}

	public function renderCyberSourcePage()
	{	
		return $this->renderForm('config_cs');
	}
	
	/*
	public function renderMediosPago()
	{

		return $this->renderForm('config_rapipago')
				.$this->renderForm('config_pagofacil')
				.$this->renderForm('config_pagomiscuentas');
	}*/

	/**
	 * @return el html de todos los formularios
	 */
	public function renderConfigForms()
	{	

		return $this->renderForm('config')
				.$this->renderForm('test')
				.$this->renderForm('produccion')
				.$this->renderForm('estado');
	}
	
	/**
	 * @return el html seccion entidades
	 */
	public function renderCMSMediosPagos()
	{	
		$InstanceMedios = new AdminMediosController();

		if(isset($_GET['update_mediopago']) || isset($_GET['add_mediopago'])){
			return $this->renderForm('config_mediospago');

		}else{
			return $InstanceMedios->renderListMedios();
		}
	}  

	/**
	 * @return el html seccion Mediosp
	 */
	public function renderCMSEntidades()
	{	
		$InstanceBank = new AdminEntityController();

		if(isset($_GET['update_entidad']) || isset($_GET['add_entidad'])){		
			return $this->renderForm('config_entidades');
		}else{
			return $InstanceBank->renderListEntities();
		}
	}

	/**
	 * @return el html seccion Mediosp
	 */
	public function renderCMSPlanes()
	{	
		$list = new AdminPromocionesController();

		if(isset($_GET['update_promocion']) || isset($_GET['add_promocion'])){
			return $this->renderForm('config_promociones');
		}else{
			return $list->renderListPromociones();
		}
	}

	/**
	 * @return el html seccion Mediosp
	 */
	public function renderCMSInteres()
	{	
		$list = new AdminInteresController();

		if(isset($_GET['update_interes']) || isset($_GET['add_interes'])){
			
			return $this->renderForm('config_interes');
		}else{
			$listsInteresPaymentMethod = '';
			$elements = $list->getAllPMethods();

			foreach($elements as $index => $value){
				$listsInteresPaymentMethod .= '<div class="list_interes" id="box_'.$elements[$index]['id'].'">'.$list->renderListInteres($elements[$index]['id_decidir']).'</div>';

			}
			return $listsInteresPaymentMethod;
		}
	}

	public function renderCMSSelectInteres()
	{	
		$list = new AdminInteresController();
		return $list->renderSelect();
	}

	/**
	 * Crea las opciones para un select
	 * @param array $opciones
	 */
	public function getOptions($opciones)
	{
		$rta = array();
		
		foreach ($opciones as $item)
		{
			$rta[] = array(
				'id_option' => $item,
				'name' => $item	
			);
		}
		
		return $rta;
	}
	
	/**
	 * 	Genera el  formulario que corresponda segun la tabla ingresada
	 * @param string $tabla nombre de la tabla
	 * @param array $fields_value 
	 */
	public function renderForm($tabla)
	{
		$form_fields;
		$fields_value = array();

		switch ($tabla)
		{
			case 'config':
				$form_fields = Decidir\AdminFieldForm::getFormFields('general ', Decidir\AdminFieldForm::getConfigFormInputs($this->getOptions($this->segmento), $this->getOptions($this->canal)));
				$prefijo = $this->getPrefijo('PREFIJO_CONFIG');
				break;

			case 'test':

				$form_fields = Decidir\AdminFieldForm::getFormFields('ambiente '.$tabla, Decidir\AdminFieldForm::getAmbienteFormInputs($tabla));
				$prefijo = $this->getPrefijo('CONFIG_TEST');
				break;
			
			case 'produccion':
				$form_fields = Decidir\AdminFieldForm::getFormFields('ambiente '.$tabla, Decidir\AdminFieldForm::getAmbienteFormInputs($tabla));
				$prefijo = $this->getPrefijo('CONFIG_PRODUCCION');
				break;
			
			case 'estado':
				$form_fields = Decidir\AdminFieldForm::getFormFields('estados del pedido', Decidir\AdminFieldForm::getEstadosFormInputs($this->getOrderStateOptions()));
				$prefijo = $this->getPrefijo('CONFIG_ESTADOS');
				break;			

			case 'config_cs':
				$form_fields = Decidir\AdminFieldForm::getFormFields('cybersource', Decidir\AdminFieldForm::getCybersourceFields($this->segmento, $tabla));
				$prefijo = $this->getPrefijo('CONTROL_FRAUDE');
				break;	
				/*
			case 'config_rapipago':
				$form_fields = Decidir\AdminFieldForm::getFormFields('rapipago', Decidir\AdminFieldForm::getRapipagoFormInputs($tabla));
				$prefijo = $this->getPrefijo('CONFIG_RAPIPAGO');
				break;	

			case 'config_pagofacil':
				$form_fields = Decidir\AdminFieldForm::getFormFields('pago fácil', Decidir\AdminFieldForm::getPagofacilFormInputs($tabla));
				$prefijo = $this->getPrefijo('CONFIG_PAGOFACIL');
				break;	

			case 'config_pagomiscuentas':
				$form_fields = Decidir\AdminFieldForm::getFormFields('pago mis cuentas', Decidir\AdminFieldForm::getPagomiscuentasFormInputs($tabla));
				$prefijo = $this->getPrefijo('CONFIG_PAGOMISCUENTAS');
				break;
			*/	
			case 'config_entidades':
				//CMS entidad
				(Tools::getValue('id_entidad') != NULL)? $title="Actualizar Entidad": $title="Cargar Entidad";

				$form_fields = Decidir\AdminFieldForm::getFormFields($title, Decidir\AdminFieldForm::getConfigEntidades(Tools::getValue('id_entidad')));	
				break;

			case 'config_mediospago':
				//CMS medio de pago
				(Tools::getValue('id_medio') != NULL)? $title="Actualizar Medio de Pago": $title="Cargar Medio de Pago";

				$listPaymentMethodData = array();	
				$form_fields = Decidir\AdminFieldForm::getFormFields($title, Decidir\AdminFieldForm::getConfigMediosPago(Tools::getValue('id_medio')), $listPaymentMethodData);

				break;	

			case 'config_promociones':
				//CMS promociones
				(Tools::getValue('id_promocion') != NULL)? $title="Actualizar Promoción": $title="Cargar Promoción";

				$mediosInstance = new AdminMediosController();
				$entityInstance = new AdminEntityController();

				$form_fields = Decidir\AdminFieldForm::getFormFields($title, Decidir\AdminFieldForm::getConfigPromocion(Tools::getValue('id_promocion'), $mediosInstance->getAllPMethods(), $entityInstance->getAllEntityName()));

				break;

			case 'config_interes':
				//CMS interes
				(Tools::getValue('id_interes') != NULL)? $title="Actualizar Interes": $title="Cargar Interes";
				
				$listInteresData = array();	
				if(Tools::getValue('id_interes') != ''){
					$interestInstance = new AdminInteresController();
					$listInteresData = $interestInstance->getById(Tools::getValue('id_interes'));
				}

				$mediosInstance = new AdminMediosController();
				$form_fields = Decidir\AdminFieldForm::getFormFields($title, Decidir\AdminFieldForm::getConfigInteres(Tools::getValue('id_interes'), $mediosInstance->getAllPMethods(), $listInteresData));

				break;
		}

		if (isset($prefijo)){
			$fields_value = Decidir\AdminFieldForm::getConfigs($prefijo, Decidir\AdminFieldForm::getFormInputsNames($form_fields['form']['input']));	

		}else{

			if(Tools::getValue('id_entidad') != ''){
				//$entitydInstance = new AdminEntityController();
				//$currentData = $entitydInstance->getById(Tools::getValue('id_entidad'));

				$currentData = AdminEntityController::getById(Tools::getValue('id_entidad'));				

				$fields_value = Decidir\AdminFieldForm::getDataCMSEntities(Tools::getValue('id_entidad'), Decidir\AdminFieldForm::getFormInputsNames($form_fields['form']['input']), $currentData);

			}elseif(Tools::getValue('id_medio') != ''){
				//$PaymentMethodInstance = new AdminMediosController();
				//$currentData = $PaymentMethodInstance->getById(Tools::getValue('id_medio'));

				$currentData = AdminMediosController::getById(Tools::getValue('id_medio'));

				$fields_value = Decidir\AdminFieldForm::getDataPaymentMethod(Tools::getValue('id_medio'), Decidir\AdminFieldForm::getFormInputsNames($form_fields['form']['input']), $currentData);

			}elseif(Tools::getValue('id_promocion') != null){

				$currentData = AdminPromocionesController::getById(Tools::getValue('id_promocion'));

				$fields_value = Decidir\AdminFieldForm::getDataPromo(Tools::getValue('id_promocion'), Decidir\AdminFieldForm::getFormInputsNames($form_fields['form']['input']), $currentData);

			}elseif(Tools::getValue('id_interes') != null){

				//$interesInstance = new AdminInteresController();
				//$currentData = $interesInstance->getById(Tools::getValue('id_interes'));

				$currentData = AdminInteresController::getById(Tools::getValue('id_interes'));

				$fields_value = Decidir\AdminFieldForm::getDataPromo(Tools::getValue('id_interes'), Decidir\AdminFieldForm::getFormInputsNames($form_fields['form']['input']), $currentData);

				$fields_value = array();
			}
		}
		$fields_value = $this->getAuthorizationKeyFromJSON($fields_value);

		return $this->getHelperForm($tabla,$fields_value)->generateForm(array($form_fields));
	}
	
	/**
	 * Genera un formulario
	 * @param String $tabla nombre de la tabla que se usa para generar el formulario
	 */
	public function getHelperForm($tabla, $fields_value=NULL)
	{
		$helper = new HelperForm();

		$helper->show_toolbar = false;//no mostrar el toolbar
		$helper->table = $this->table;
		$helper->module = $this;
		$helper->default_form_language = $this->context->language->id;//el idioma por defecto es el que esta configurado en prestashop
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
		
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'btnSubmit'.ucfirst($tabla);//nombre del boton de submit. Util al momento de procesar el formulario

		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
		.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');

		$helper->tpl_vars = array(
				'fields_value' => $fields_value,
				'languages' => $this->context->controller->getLanguages(),
				'id_language' => $this->context->language->id
		);

		return $helper;
	}
	
	/**
	 * recupero y guardo los valores ingresados en el formulario
	 */
	protected function _postProcess()
	{
		if (Tools::isSubmit('btnSubmitConfig'))
		{
			Decidir\AdminFieldForm::postProcessFormularioConfigs($this->getPrefijo('PREFIJO_CONFIG'), Decidir\AdminFieldForm::getFormInputsNames( Decidir\AdminFieldForm::getConfigFormInputs() ) );
		}
		elseif (Tools::isSubmit('btnSubmitTest'))
		{
			Decidir\AdminFieldForm::postProcessFormularioConfigs($this->getPrefijo('CONFIG_TEST'), Decidir\AdminFieldForm::getFormInputsNames( Decidir\AdminFieldForm::getAmbienteFormInputs('test') ) );
		}
		elseif (Tools::isSubmit('btnSubmitProduccion'))
		{
			Decidir\AdminFieldForm::postProcessFormularioConfigs($this->getPrefijo('CONFIG_PRODUCCION'), Decidir\AdminFieldForm::getFormInputsNames( Decidir\AdminFieldForm::getAmbienteFormInputs('produccion') ) );
		}
		elseif (Tools::isSubmit('btnSubmitEstado'))
		{
			Decidir\AdminFieldForm::postProcessFormularioConfigs($this->getPrefijo('CONFIG_ESTADOS'), Decidir\AdminFieldForm::getFormInputsNames( Decidir\AdminFieldForm::getEstadosFormInputs() ) );
		}
		elseif (Tools::isSubmit('btnSubmitConfig_cs'))
		{
			Decidir\AdminFieldForm::postProcessFormularioConfigs($this->getPrefijo('CONTROL_FRAUDE'), Decidir\AdminFieldForm::getFormInputsNames( Decidir\AdminFieldForm::getCybersourceFields() ) );
		}/*
		elseif (Tools::isSubmit('btnSubmitConfig_rapipago'))
		{
			Decidir\AdminFieldForm::postProcessFormularioConfigs($this->getPrefijo('CONFIG_RAPIPAGO'), Decidir\AdminFieldForm::getFormInputsNames( Decidir\AdminFieldForm::getRapipagoFormInputs() ) );
		}
		elseif (Tools::isSubmit('btnSubmitConfig_pagofacil'))
		{
			Decidir\AdminFieldForm::postProcessFormularioConfigs($this->getPrefijo('CONFIG_PAGOFACIL'), Decidir\AdminFieldForm::getFormInputsNames( Decidir\AdminFieldForm::getPagofacilFormInputs() ) );
		}
		elseif (Tools::isSubmit('btnSubmitConfig_pagomiscuentas'))
		{
			Decidir\AdminFieldForm::postProcessFormularioConfigs($this->getPrefijo('CONFIG_PAGOMISCUENTAS'), Decidir\AdminFieldForm::getFormInputsNames( Decidir\AdminFieldForm::getPagomiscuentasFormInputs() ) );
		}*/
		elseif (Tools::isSubmit('btnSubmitConfig_entidades'))
		{//Formulario CMS de entidades
			$fields = array();
			
			$inputName = Decidir\AdminFieldForm::getFormInputsNames( Decidir\AdminFieldForm::getConfigEntidades(NULL) );	

			foreach ($inputName as $nombre){	
				$fields[$nombre] = \Tools::getValue($nombre);
			}

			$entityInstance = new AdminEntityController();

			if(isset($fields['id_entity']) && $fields['id_entity'] == ''){
				$entityInstance->insertEntity($fields);
			}else if(isset($fields['id_entity']) && $fields['id_entity'] != ''){	
				$entityInstance->updateEntity($fields);
			}

		}
		elseif(Tools::isSubmit('btnSubmitConfig_mediospago'))
		{	
			//cms medios de pago
			$fields = array();

			$inputName = Decidir\AdminFieldForm::getFormInputsNames( Decidir\AdminFieldForm::getConfigMediosPago(NULL) );

			foreach ($inputName as $nombre){	
				$fields[$nombre] = \Tools::getValue($nombre);
			}

			$mediosInstance = new AdminMediosController();

			if(isset($fields['id_medio']) && $fields['id_medio'] == ''){
				$mediosInstance->insertMediosPago($fields);
			}else if(isset($fields['id_medio']) && $fields['id_medio'] != ''){	
				$mediosInstance->updateMediosPago($fields);
			}
		}
		elseif(Tools::isSubmit('btnSubmitConfig_promociones'))
		{	
			//cms medios de pago
			$fields = array();

			$inputName = Decidir\AdminFieldForm::getFormInputsNames( Decidir\AdminFieldForm::getConfigPromocion());

			foreach ($inputName as $nombre){	
				$fields[$nombre] = \Tools::getValue($nombre);
			}

			$promoIntance = new AdminPromocionesController();

			if(isset($fields['id_promocion']) && $fields['id_promocion'] == ''){
				$promoIntance->insertPromocion($fields);
			}else if(isset($fields['id_promocion']) && $fields['id_promocion'] != ''){	
			
				$promoIntance->updatePromocion($fields);
			}

		}elseif(Tools::isSubmit('btnSubmitConfig_interes')){

			$fields = array();

			$inputName = Decidir\AdminFieldForm::getFormInputsNames( Decidir\AdminFieldForm::getConfigInteres(NULL) );

			foreach ($inputName as $nombre){	
				$fields[$nombre] = \Tools::getValue($nombre);
			}
			$interesInstance = new AdminInteresController();

			if(isset($fields['id_interes']) && $fields['id_interes'] == ''){
				$interesInstance->insertInteres($fields);
			}else if(isset($fields['id_interes']) && $fields['id_interes'] != ''){	
				$interesInstance->updateInteres($fields);
			}
		}		

		//mejorar este codigo!!!!!!
		if(isset($_GET['delete_entidad']) && $_GET['delete_entidad'] == null){
			$entityInstance = new AdminEntityController();
			$entityInstance->deleteEntity($_GET['id_entidad']);
		}

		if(isset($_GET['status_entidad']) && $_GET['status_entidad'] == null){
			$entityInstance = new AdminEntityController();
			$entityInstance->updateEntityVisible($_GET['id_entidad']);
		}

		if(isset($_GET['delete_mediopago']) && $_GET['delete_mediopago'] == null){
			$MediosInstace = new AdminMediosController();
			$MediosInstace->deleteMedioPago($_GET['id_medio']);
		}

		if(isset($_GET['status_mediopago']) && $_GET['status_mediopago'] == null){
			$MediosInstace = new AdminMediosController();
			$MediosInstace->updateMedioPagoVisible($_GET['id_medio']);
		}

		if(isset($_GET['delete_promocion']) && $_GET['delete_promocion'] == null){
			$interesInstance = new AdminPromocionesController();
			$interesInstance->deletePromocion($_GET['id_promocion']);
		}

		if(isset($_GET['status_promocion']) && $_GET['status_promocion'] == null){
			$interesInstance = new AdminPromocionesController();
			$interesInstance->updatePromocionVisible($_GET['id_promocion']);
		}

		if(isset($_GET['delete_interes']) && $_GET['delete_interes'] == null){
			$interesInstance = new AdminInteresController();
			$interesInstance->deleteInteres($_GET['id_interes']);
		}

		if(isset($_GET['status_interes']) && $_GET['status_interes'] == null){
			$interesInstance = new AdminInteresController();
			$interesInstance->updateInteresVisible($_GET['id_interes']);
		}
	}
	
	/**
	 * Usada en payment.php
	 */
	public function checkCurrency($cart)
	{
		$currency_order = new Currency($cart->id_currency);
		$currencies_module = $this->getCurrency($cart->id_currency);

		if (is_array($currencies_module))
			foreach ($currencies_module as $currency_module)
				if ($currency_order->id == $currency_module['id_currency'])
					return true;
		return false;
	}
	
	/**
	 * Verifica si el modulo esta activo para el usuario final
	 */
	public function isActivo()
	{
		return (boolean)Configuration::get($this->getPrefijo('PREFIJO_CONFIG').'_STATUS');
	}
	
	/**
	 * Verifica si el modulo esta en produccion o en test
	 */
	public function getModo()
	{	
		return  (bool) Configuration::get($this->getPrefijo('PREFIJO_CONFIG').'_MODE');
	}
	
	public function getEnvironment()
	{	
		if($this->getModo()){
			return self::DECIDIR_ENDPOINT_PROD;
		}else{
			return self::DECIDIR_ENDPOINT_TEST;
		}
	}

	/**
	 * Verifica si ControlFraude está hablitado
	 */
	public function isControlFraudeActivo()
	{
		return (boolean)Configuration::get($this->getPrefijo('CONFIG_CONTROLFRAUDE').'_STATUS');
	}
	
	/**
	 * Devuelve el prefijo correspondiente al modo en el que se ejecuta el modulo
	 */
	public function getPrefijoModo()
	{	
		if ($this->getModo())
		{
			return $this->getPrefijo('CONFIG_PRODUCCION');
		}else{	
			//false = test
			return $this->getPrefijo('CONFIG_TEST');
		}
	}
	
	/**
	 * Obtiene el segmento de la tienda
	 */
	public function getSegmentoTienda($cs = false)
	{
		$prefijo= $this->getPrefijo('PREFIJO_CONFIG');
		$segmento = Configuration::get($prefijo.'_CONTROLFRAUDE_VERTICAL');

        if($cs) {
			switch ($segmento)
			{
                case 'retail':
                    return DecControlFraudeFactory::RETAIL;
                    break;
		case 'digitalgoods':
			return DecControlFraudeFactory::DIGITAL_GOODS;
		    break;
		case 'ticketing':
			return DecControlFraudeFactory::TICKETING;
		    break;
		case 'service':
			return DecControlFraudeFactory::SERVICE;
		    break;
                case 'travel':
                    return DecControlFraudeFactory::TRAVEL;
                    break;
				default:
					return DecControlFraudeFactory::RETAIL;
				break;
			}
		}
		return 	$segmento;
	}
	
	public function getOrderStatesModulo($nombre=NULL)
	{
		$prefijo = $this->getPrefijo('CONFIG_ESTADOS');
		$sql = 'SELECT value FROM '._DB_PREFIX_.Configuration::$definition['table'];
		
		if ($nombre!=NULL && isset($nombre))//si se busca un valor especifico
		{	
			return Db::getInstance()->getValue($sql.' WHERE name="'.$prefijo.'_'.$nombre.'"');
		}
	}
	
	public function getOrderStateOptions()
	{
		$list =  Db::getInstance()->executeS('SELECT os.id_order_state as id, name, logable as valid_order
			FROM `'._DB_PREFIX_.OrderState::$definition['table'].'` os
			LEFT JOIN `'._DB_PREFIX_.OrderState::$definition['table'].'_lang` osl 
				ON (os.'.OrderState::$definition['primary'].' = osl.'.OrderState::$definition['primary'].' AND osl.`id_lang` = '.(int)$this->context->language->id.')
			WHERE deleted = 0');
		$options = array();
	
		//ingreso la opcion por defecto
		$options[] = array(
					'id_option' => NULL,
					'name' => 'Ninguno',
					'valid_order' => '1'
			);
		
		//si la query devuelve un resultado
		if (count($list) !=0)
		{
			foreach ($list as $item)
			{
					$options[] = array(
							'id_option' => $item['id'],
							'name' => $item['name'],
						    'valid_order' => $item['valid_order']
					);
			}
		}

		return $options;
	}
	
	public function getAuthorizationKeyFromJSON($fields_val)
	{	
		foreach($fields_val as $index => $value)
		{	
			if($index == "security")
			{
				$fields_val[$index] = $value;
			}
		}

		return $fields_val;
	}

	/**
	 * Recupera el authorize.
	 * @param String $prefijo indica el ambiente en uso
	 * @return array resultado de decodear el authorization que está en formato json.
	 */
	public function getAuthorization()
	{
		$prefijo = $this->getPrefijoModo();
		$auth = json_decode(Configuration::get($prefijo.'_AUTHORIZATION'), TRUE);
		if(!empty($auth)) return $auth;

		$prefijo = $this->getPrefijo('PREFIJO_CONFIG');
		return json_decode(Configuration::get($prefijo.'_AUTHORIZATION'), TRUE);
	}
	
	/**
	 * Borra las variables de configuracion de la base de datos
	 */
	public function deleteConfigVariables()
	{
		Db::getInstance()->delete(Configuration::$definition['table'],'name LIKE \'%'.$this->getPrefijo('PREFIJO_CONFIG').'%\'');
	}

	public function getInstallmentsList($type, $pmethod, $entity, $totalAmount)
	{
		$installmentList = array();
		$instanceInsterest = new DecidirPaymentSelect();
		$cart = $this->context->cart;

		//$type true is promo, false is average insterest
		if($type){
			$interestResultList = $instanceInsterest->getInstallmentsPromoList($pmethod, $entity);
			$promo = new AdminPromocionesController();
			$allDaysArray = $promo->numberDayList();
			$currentDay = date('w');

			//filtro por dia
			foreach($interestResultList as $index => $installment){
				$days = $interestResultList[$index]['days'];
				$promoDaysResult = $promo->getNumberCodes($days);

				if(!in_array($allDaysArray[$currentDay], $promoDaysResult)){		
					unset($interestResultList[$index]);
				}
			}

			//filtro por cuotas
			$finalListInstallment = array();
			foreach($interestResultList as $index => $installment){
				$installment = $interestResultList[$index]['installment'];
				$promoInstResult = $promo->getNumberCodes($installment);	

				foreach($promoInstResult as $install => $code){
					$element = array();
					if(in_array($code, $promo->numberInstallmentList())){
						$indexArray = array_search($code, $promo->numberInstallmentList());

						$element['id_interes'] = $interestResultList[$index]['id_promocion'];
						$element['name'] = $interestResultList[$index]['name'];
						$element['installment'] = $indexArray;
						$element['payment_method'] = $interestResultList[$index]['payment_method'];
						$element['coeficient'] = $interestResultList[$index]['coeficient'];
						$element['discount'] = $interestResultList[$index]['discount'];
						$element['active'] = $interestResultList[$index]['active'];

						array_push($finalListInstallment, $element);
						unset($element);
					}
				}
			}	
			$installmentList = $this->renderInstallmentSelectOptions(true, $finalListInstallment, $totalAmount);
		}else{
			$interestResultList = $instanceInsterest->getInstallentByPaymentId($pmethod);
			$installmentList = $this->renderInstallmentSelectOptions(false, $interestResultList, $totalAmount);
		}

		return $installmentList;
	}

	public function calcFinancialCost($interestResultList, $totalAmount)
	{	
		$coeficient = 1;
		$percentDiscount = 0;

		$total= array();
		$total['installment'] = $interestResultList['installment'];
		
		if(isset($interestResultList['coeficient']) && $interestResultList['coeficient'] > 0){
			$coeficient = ($interestResultList['coeficient']);
		}

		if(isset($interestResultList['discount'])) {
			//porcentaje de descuento
			$percentDiscount = ($interestResultList['discount'] / 100);
		}
		
		$totalCostCF = ($totalAmount * $coeficient);

		//$cf = $totalCostCF - $totalAmount;	

		$discount = $totalCostCF * $percentDiscount;

		//aplico el descuento al total
		$totalCostCF = $totalCostCF - $discount;

		$installmenCost = round(($totalCostCF / $total['installment']), 2, PHP_ROUND_HALF_UP);

		$total['installmenttotal'] = $installmenCost;
		$total['totalCost'] = number_format($totalCostCF, 2, ',', ' ');
			
		/*	
		if($discount > 0){
			$totalDiscount = (($installmenCost * $discount));
		}else{
			$totalDiscount = $discount;
		}
		*/

		$total['discount'] = $discount;

		return $total;
	}

	//esto es parte de los selects de tarjeta, entidad, planes del frontend
	public function renderInstallmentSelectOptions($type, $list, $total)
	{	
		$selecOption = array();
		$renderInfo = "";
		$element = array();
		$data = array();

		for($index = 0; $index < count($list); $index++){
			//id de promocion + cuota
			$element['id'] = $list[$index]['id_interes']."_".$list[$index]['installment'];
		
			$installmentCalcData = $this->calcFinancialCost($list[$index], $total);

			if(isset($list[$index]['name'])){
				$renderInfoName = $list[$index]['name']." - ";	
			}else{
				$renderInfoName = "";
			}

			//$element['name'] = "plan name - cuota 1 - valor: 100 - total: 100";
			$renderInfo ="<prev>";
			$renderInfo .= $renderInfoName;
			$renderInfo .= "Cuotas: ".$list[$index]['installment']." ";
			$renderInfo .= "- Valor: ".$installmentCalcData['installmenttotal']." ";
			$renderInfo .= "- Total: ".$installmentCalcData['totalCost']." ";
			$renderInfo .="</prev>";
			
			$element['name'] = $renderInfo;

			array_push($data, $element);
			unset($element);
		}

		$selecOption['data'] = $data;

		//type = true is promo type, false is common interest card
		if($type){
			$selecOption['type'] = 1;
		}else{
			$selecOption['type'] = 0;
		}

		return $selecOption;
	}
	
	public function getTokensUserList($userid){
		$element = array();
		$tokenInfo = array();
		$tokenList = array();

        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'decidir_tokens WHERE user_id="'.$userid.'"';
        $result = Db::getInstance()->ExecuteS($sql);

        if(!empty($result)){
        	foreach($result as $index => $data){
	        	$renderInfo ="<prev>";
				$renderInfo .= "xxxx xxxx xxxx ".$data['last_four_digits']." - ";
				$renderInfo .= $data['name']." ";
				$renderInfo .= "- Vto. ".$data['expiration_month']."/".$data['expiration_year'];
				$renderInfo .="</prev>";

				$element['id'] = $data['token'];
				$element['desc'] = $renderInfo;
				

				array_push($tokenInfo, $element);
				unset($element);
	        }

	        $tokenList['type'] = true;
        	$tokenList['data'] = $tokenInfo;

        }else{

        	$tokenList['type'] = false;
        	$tokenList['data'] = "";	
        }

        return $tokenList;     
    }

	/*
	public function hookDisplayBackOfficeHeader()
	{
		$this->context->controller->addCSS($this->local_path.'css/back.css', 'all');
		$this->context->controller->addJS($this->local_path.'js/back.js', 'all');
	}
	*/

    //prestashop 1.7, show payment method in options
	public function hookPaymentOptions($params)
	{
		if (!$this->active || !$this->isActivo()) {
	            return;
	        }

        if (!$this->checkCurrency($params['cart'])) {
            return;
        }

        $cart = $this->context->cart;

        $newOption = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();

        $ModuleName = Configuration::get($this->getPrefijo('PREFIJO_CONFIG').'_FRONTEND_NAME');//nombre que se muestra al momento de elegir los metodos de pago

        $urlForm = $this->context->link->getModuleLink('decidir', 'payment', array('paso' => '1'), true);

        $newOption->setCallToActionText($ModuleName)
                    ->setAction($urlForm)
                    ->setInputs([])
                    ->setAdditionalInformation($this->context->smarty->fetch('module:decidir/views/templates/hook/payment_option.tpl'));

        $payment_options = [
        	$newOption
        ];

        return $payment_options;
	}

	//prestashop 1.6, muestra el medio de pago en la lista
	public function hookDisplayPayment()
	{
		//si el modulo no esta activo
		if (!$this->active ||  !$this->isActivo())
			return;

		$this->smarty->assign(array(
			'nombre' => Configuration::get($this->getPrefijo('PREFIJO_CONFIG').'_FRONTEND_NAME'),//nombre que se muestra al momento de elegir los metodos de pago 
			'this_path' => $this->_path,
			'this_path_ejemplo' => $this->_path,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/',
			'module_path' => strtolower(__PS_BASE_URI__.'modules/'.$this->name.'/imagenes/logo-decidir.jpg'),

		));
		return $this->display(__FILE__, 'payment.tpl');//asigno el template que quiero usar
	}


	public function hookDisplayPaymentReturnPage($params)
	{
		//si el modulo no esta activo
		if (!$this->active || !$this->isActivo())
			return;

		return Tools::redirect('index.php?fc=module&module=decidir&controller=paymentconfirm&order='.$params['cart']->id);
	}
	
	/**
	 * Para crear una tab en la vista de cada producto  y mostrar contenido en ella
	 * @param array $params al parecer es null
	 */
	public function hookDisplayAdminProductsExtra($params) {
		$idProducto = Tools::getValue('id_product');//recupero el id del producto desde el backoffice
		
		$this->displayName = $this->l('Prevencion del Fraude');//cambio el nombre que aparece en la tab
				
		//obtengo los campos de los select del formulario
		$servicioOption = $this->getOptions($this->servicio);
		$deliveryOption = $this->getOptions($this->delivery);
		$envioOption = $this->getOptions($this->envio);
		$productOption = $this->getOptions($this->product_code);
		
		//recupero los campos del formulario
		$form_fields = Decidir\AdminFieldForm::getFormFields('Prevencion del fraude', Decidir\AdminFieldForm::getProductoFormInputs($this->getSegmentoTienda(true),$servicioOption, $deliveryOption, $envioOption, $productOption));
		
		//si no hay ningun input porque no hay datos para agregar para el segmento de la tienda
		if (count($form_fields['form']['input']) == 0)
		{
			$form_fields['form']['input'] = array(
				array(
						'label' => 'No se necesita agregar informaciÃ³n para este segmento.'
				)	
			);
		}
		//recupero el contenido del formulario, si existiera
		elseif (DecidirProductoControlFraude::existeRegistro($idProducto))
		{
			$campos = Decidir\AdminFieldForm::getFormInputsNames($form_fields['form']['input']);
			
			$fields_value = array();
		
			foreach ($campos as $nombre)
			{
				$fields_value[$nombre] = DecidirProductoControlFraude::getValorRegistro($idProducto, $nombre);
			}
		}
		
		//creo el helperForm y seteo el controlador, id de de producto y token necesarios para que el form apunte donde corresponde
		$helperForm = $this->getHelperForm('Controlfraude',$fields_value);
		$helperForm->currentIndex .= '&id_product='.$idProducto;
		
		//obtengo el html del formulario y lo agrego al smarty	
		$this->smarty->assign(array(
				'segmento' => $this->getSegmentoTienda(true),//para filtrar campos del formulario segun el segmento
				'tab' => $this->displayName,
				'nombreDiv' => strtolower($this->name).'-controlfraude',
				'form' => $helperForm->generateForm(array($form_fields)),
				'campos' => $campos
			)
		);
		
		return ;
	}
	
	/**
	 * Para recuperar lo que se ingreso en la tab
	 * @param array $params contiene el id del producto actualizado
	 */
	public function hookActionProductUpdate($params)
	{
		/**
		 * Params:
		 * id_product: id del producto. Viene tanto desde AdminProducts como desde el postProcess del modulo
		 * form: contiene lo escrito en los campos del formulario. No existe si el hook no se ejecuta desde el postProcess del modulo
		 */
		try
		{ 
			if (isset($params['form']) && count($params['form'])>0) //si el hook se ejecuto desde el _postProcess
			{		
				$idProducto = $params['id_product'];//recupero el id del producto desde el backoffice
				$segmento = $this->getSegmentoTienda(true);//recupero el segmento de la tienda
				$this->displayName = $this->l('Prevencion del fraude');//nombre que se muestra en la tab
					
				$this->log->info('ActionProductUpdate - Segmento '.$segmento.' - params: '.json_encode($params));
				
				$registro = $params['form'];//recupero desde los params
				
				if (isset($registro) && count($registro)>0)
				{
					//creo un nuevo registro o actualizo el existente
					if (!DecidirProductoControlFraude::existeRegistro($idProducto))
					{
						$registro['id_product'] = $idProducto;
						Db::getInstance()->insert(DecidirProductoControlFraude::$definition['table'],  $registro);
						$this->log->info('ActionProductUpdate - Segmento '.$segmento.' - insertado registro para producto id='.$idProducto.' : '.json_encode($registro));
					}
					else
					{
						Db::getInstance()->update(DecidirProductoControlFraude::$definition['table'],  $registro, DecidirProductoControlFraude::$definition['primary'].'='.$idProducto);
						$this->log->info('ActionProductUpdate - Segmento '.$segmento.' - actualizado registro para producto id='.$idProducto.' : '.json_encode($registro));
					}
				}
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminProducts').'&id_product='.$idProducto.'&updateproduct&token='.Tools::getAdminTokenLite('AdminProducts'));
			}
		}
		catch (Exception $e)
		{
			$this->log->error('EXCEPCION',$e);
		}
	}
	
	//este hook es para la seccion de devoluciones
	public function hookDisplayAdminOrder($params)
	{	
		//hook muestra seccion devolucion en la pagina de orden
		$id_order = Tools::getValue('id_order');

		$sql ='SELECT id_cart FROM ' . _DB_PREFIX_ . 'orders WHERE id_order ='.$id_order;
        $orderResult = Db::getInstance()->ExecuteS($sql);

        $sql ='SELECT decidir_order_id, order_id FROM ' . _DB_PREFIX_ . 'decidir_transacciones WHERE order_id ='.$orderResult[0]['id_cart'];
        $transResult = Db::getInstance()->ExecuteS($sql);

		$pmethod = '';
		$order = new Order((int)$id_order);

		$currency = new CurrencyCore($order->id_currency);
		$currency_code = $currency->iso_code;

		$finCost = 0;

		$this->smarty->assign(array(
						'order_id' => $id_order,
						'num_order_dec' => $transResult[0]['decidir_order_id'],
						'currency_code' => $currency_code,
						'total_pay' => number_format(($order->total_paid + $finCost), 2, ',', ' '),
						'url_base_ajax' => "//".Tools::getHttpHost(false).__PS_BASE_URI__,
						'url_refund' => $this->context->link->getModuleLink('decidir', 'payment', array ('paso' => '3', 'order' => $id_order), true) 
					)
				);

		return $this->display(__FILE__, 'views/templates/admin/order-page.tpl');

		return '';
	}

	public function validateOrderDecidir($id_cart, $id_order_state, $amount_paid, $payment_method = 'Unknown', $message = null, $extra_vars = array(), $currency_special = null, $dont_touch_amount = false, $secure_key = false, $totalFee){

		if (!isset($this->context)) {
			$this->context = Context::getContext();
		}
		$this->context->cart = new Cart((int)$id_cart);
		$this->context->customer = new Customer((int)$this->context->cart->id_customer);
		// The tax cart is loaded before the customer so re-cache the tax calculation method
		$this->context->cart->setTaxCalculationMethod();

		$this->context->language = new Language((int)$this->context->cart->id_lang);
		$this->context->shop = new Shop((int)$this->context->cart->id_shop);
		ShopUrl::resetMainDomainCache();
		$id_currency = $currency_special ? (int)$currency_special : (int)$this->context->cart->id_currency;
		$this->context->currency = new Currency((int)$id_currency, null, (int)$this->context->shop->id);
		if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_delivery') {
			$context_country = $this->context->country;
		}

		$order_status = new OrderState((int)$id_order_state, (int)$this->context->language->id);
		if (!Validate::isLoadedObject($order_status)) {
			PrestaShopLogger::addLog('PaymentModule::validateOrder - Order Status cannot be loaded', 3, null, 'Cart', (int)$id_cart, true);
			throw new PrestaShopException('Can\'t load Order status');
		}

		if (!$this->active) {
			PrestaShopLogger::addLog('PaymentModule::validateOrder - Module is not active', 3, null, 'Cart', (int)$id_cart, true);
			die(Tools::displayError());
		}

		// Does order already exists ?
		if (Validate::isLoadedObject($this->context->cart) && $this->context->cart->OrderExists() == false) {
			if ($secure_key !== false && $secure_key != $this->context->cart->secure_key) {
				PrestaShopLogger::addLog('PaymentModule::validateOrder - Secure key does not match', 3, null, 'Cart', (int)$id_cart, true);
				die(Tools::displayError());
			}

			// For each package, generate an order
			$delivery_option_list = $this->context->cart->getDeliveryOptionList();
			$package_list = $this->context->cart->getPackageList();
			$cart_delivery_option = $this->context->cart->getDeliveryOption();

			// If some delivery options are not defined, or not valid, use the first valid option
			foreach ($delivery_option_list as $id_address => $package) {
				if (!isset($cart_delivery_option[$id_address]) || !array_key_exists($cart_delivery_option[$id_address], $package)) {
					foreach ($package as $key => $val) {
						$cart_delivery_option[$id_address] = $key;
						break;
					}
				}
			}

			$order_list = array();
			$order_detail_list = array();

			do {
				$reference = Order::generateReference();
			} while (Order::getByReference($reference)->count());

			$this->currentOrderReference = $reference;

			$order_creation_failed = false;
			$cart_total_paid = (float)Tools::ps_round((float)$this->context->cart->getOrderTotal(true, Cart::BOTH), 2);

			foreach ($cart_delivery_option as $id_address => $key_carriers) {
				foreach ($delivery_option_list[$id_address][$key_carriers]['carrier_list'] as $id_carrier => $data) {
					foreach ($data['package_list'] as $id_package) {
						// Rewrite the id_warehouse
						$package_list[$id_address][$id_package]['id_warehouse'] = (int)$this->context->cart->getPackageIdWarehouse($package_list[$id_address][$id_package], (int)$id_carrier);
						$package_list[$id_address][$id_package]['id_carrier'] = $id_carrier;
					}
				}
			}
			// Make sure CartRule caches are empty
			CartRule::cleanCache();
			$cart_rules = $this->context->cart->getCartRules();
			foreach ($cart_rules as $cart_rule) {
				if (($rule = new CartRule((int)$cart_rule['obj']->id)) && Validate::isLoadedObject($rule)) {
					if ($error = $rule->checkValidity($this->context, true, true)) {
						$this->context->cart->removeCartRule((int)$rule->id);
						if (isset($this->context->cookie) && isset($this->context->cookie->id_customer) && $this->context->cookie->id_customer && !empty($rule->code)) {
							if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 1) {
								Tools::redirect('index.php?controller=order-opc&submitAddDiscount=1&discount_name='.urlencode($rule->code));
							}
							Tools::redirect('index.php?controller=order&submitAddDiscount=1&discount_name='.urlencode($rule->code));
						} else {
							$rule_name = isset($rule->name[(int)$this->context->cart->id_lang]) ? $rule->name[(int)$this->context->cart->id_lang] : $rule->code;
							$error = sprintf(Tools::displayError('CartRule ID %1s (%2s) used in this cart is not valid and has been withdrawn from cart'), (int)$rule->id, $rule_name);
							PrestaShopLogger::addLog($error, 3, '0000002', 'Cart', (int)$this->context->cart->id);
						}
					}
				}
			}

			foreach ($package_list as $id_address => $packageByAddress) {
				foreach ($packageByAddress as $id_package => $package) {
					/** @var Order $order */
					$order = new Order();
					$order->product_list = $package['product_list'];

					if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_delivery') {
						$address = new Address((int)$id_address);
						$this->context->country = new Country((int)$address->id_country, (int)$this->context->cart->id_lang);
						if (!$this->context->country->active) {
							throw new PrestaShopException('The delivery address country is not active.');
						}
					}

					$carrier = null;
					if (!$this->context->cart->isVirtualCart() && isset($package['id_carrier'])) {
						$carrier = new Carrier((int)$package['id_carrier'], (int)$this->context->cart->id_lang);
						$order->id_carrier = (int)$carrier->id;
						$id_carrier = (int)$carrier->id;
					} else {
						$order->id_carrier = 0;
						$id_carrier = 0;
					}

					$order->id_customer = (int)$this->context->cart->id_customer;
					$order->id_address_invoice = (int)$this->context->cart->id_address_invoice;
					$order->id_address_delivery = (int)$id_address;
					$order->id_currency = $this->context->currency->id;
					$order->id_lang = (int)$this->context->cart->id_lang;
					$order->id_cart = (int)$this->context->cart->id;
					$order->reference = $reference;
					$order->id_shop = (int)$this->context->shop->id;
					$order->id_shop_group = (int)$this->context->shop->id_shop_group;

					$order->secure_key = ($secure_key ? pSQL($secure_key) : pSQL($this->context->customer->secure_key));
					$order->payment = $payment_method;
					if (isset($this->name)) {
						$order->module = $this->name;
					}
					$order->recyclable = $this->context->cart->recyclable;
					$order->gift = (int)$this->context->cart->gift;
					$order->gift_message = $this->context->cart->gift_message;
					$order->mobile_theme = $this->context->cart->mobile_theme;
					$order->conversion_rate = $this->context->currency->conversion_rate;
					$amount_paid = !$dont_touch_amount ? Tools::ps_round((float)$amount_paid, 2) : $amount_paid;
					$order->total_paid_real = (float)Tools::ps_round((float)$this->context->cart->getOrderTotal(true, Cart::BOTH, $order->product_list, $id_carrier), _PS_PRICE_COMPUTE_PRECISION_) + $totalFee;

					$order->total_products = (float)$this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS, $order->product_list, $id_carrier);
					$order->total_products_wt = (float)$this->context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS, $order->product_list, $id_carrier);
					$order->total_discounts_tax_excl = (float)abs($this->context->cart->getOrderTotal(false, Cart::ONLY_DISCOUNTS, $order->product_list, $id_carrier));
					$order->total_discounts_tax_incl = (float)abs($this->context->cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS, $order->product_list, $id_carrier));
					$order->total_discounts = $order->total_discounts_tax_incl;

					$order->total_shipping_tax_excl = (float)$this->context->cart->getPackageShippingCost((int)$id_carrier, false, null, $order->product_list);
					$order->total_shipping_tax_incl = (float)$this->context->cart->getPackageShippingCost((int)$id_carrier, true, null, $order->product_list);
					$order->total_shipping = $order->total_shipping_tax_incl;

					if (!is_null($carrier) && Validate::isLoadedObject($carrier)) {
						$order->carrier_tax_rate = $carrier->getTaxesRate(new Address((int)$this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
					}

					$order->total_wrapping_tax_excl = (float)abs($this->context->cart->getOrderTotal(false, Cart::ONLY_WRAPPING, $order->product_list, $id_carrier));
					$order->total_wrapping_tax_incl = (float)abs($this->context->cart->getOrderTotal(true, Cart::ONLY_WRAPPING, $order->product_list, $id_carrier));
					$order->total_wrapping = $order->total_wrapping_tax_incl;

					$order->total_paid_tax_excl = (float)Tools::ps_round((float)$this->context->cart->getOrderTotal(false, Cart::BOTH, $order->product_list, $id_carrier), _PS_PRICE_COMPUTE_PRECISION_);
					$order->total_paid_tax_incl = (float)Tools::ps_round((float)$this->context->cart->getOrderTotal(true, Cart::BOTH, $order->product_list, $id_carrier), _PS_PRICE_COMPUTE_PRECISION_) + $totalFee;
					$order->total_paid = $order->total_paid_tax_incl;
					$order->round_mode = Configuration::get('PS_PRICE_ROUND_MODE');
					$order->round_type = Configuration::get('PS_ROUND_TYPE');

					$order->invoice_date = '0000-00-00 00:00:00';
					$order->delivery_date = '0000-00-00 00:00:00';

					if (self::DEBUG_MODE) {
						PrestaShopLogger::addLog('PaymentModule::validateOrder - Order is about to be added', 1, null, 'Cart', (int)$id_cart, true);
					}

					// Creating order
					$result = $order->add();

					if (!$result) {
						PrestaShopLogger::addLog('PaymentModule::validateOrder - Order cannot be created', 3, null, 'Cart', (int)$id_cart, true);
						throw new PrestaShopException('Can\'t save Order');
					}

					// Amount paid by customer is not the right one -> Status = payment error
					// We don't use the following condition to avoid the float precision issues : http://www.php.net/manual/en/language.types.float.php
					if ($order->total_paid != $order->total_paid_real){
					// We use number_format in order to compare two string
					//if ($order_status->logable && number_format($cart_total_paid, _PS_PRICE_COMPUTE_PRECISION_) != number_format($amount_paid, _PS_PRICE_COMPUTE_PRECISION_)) {
						$id_order_state = Configuration::get('PS_OS_ERROR');
					}

					$order_list[] = $order;

					if (self::DEBUG_MODE) {
						PrestaShopLogger::addLog('PaymentModule::validateOrder - OrderDetail is about to be added', 1, null, 'Cart', (int)$id_cart, true);
					}

					// Insert new Order detail list using cart for the current order
					$order_detail = new OrderDetail(null, null, $this->context);
					$order_detail->createList($order, $this->context->cart, $id_order_state, $order->product_list, 0, true, $package_list[$id_address][$id_package]['id_warehouse']);
					$order_detail_list[] = $order_detail;

					if (self::DEBUG_MODE) {
						PrestaShopLogger::addLog('PaymentModule::validateOrder - OrderCarrier is about to be added', 1, null, 'Cart', (int)$id_cart, true);
					}

					// Adding an entry in order_carrier table
					if (!is_null($carrier)) {
						$order_carrier = new OrderCarrier();
						$order_carrier->id_order = (int)$order->id;
						$order_carrier->id_carrier = (int)$id_carrier;
						$order_carrier->weight = (float)$order->getTotalWeight();
						$order_carrier->shipping_cost_tax_excl = (float)$order->total_shipping_tax_excl;
						$order_carrier->shipping_cost_tax_incl = (float)$order->total_shipping_tax_incl;
						$order_carrier->add();
					}
				}
			}

			// The country can only change if the address used for the calculation is the delivery address, and if multi-shipping is activated
			if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_delivery') {
				$this->context->country = $context_country;
			}

			if (!$this->context->country->active) {
				PrestaShopLogger::addLog('PaymentModule::validateOrder - Country is not active', 3, null, 'Cart', (int)$id_cart, true);
				throw new PrestaShopException('The order address country is not active.');
			}

			if (self::DEBUG_MODE) {
				PrestaShopLogger::addLog('PaymentModule::validateOrder - Payment is about to be added', 1, null, 'Cart', (int)$id_cart, true);
			}

			// Register Payment only if the order status validate the order
			if ($order_status->logable) {
				// $order is the last order loop in the foreach
				// The method addOrderPayment of the class Order make a create a paymentOrder
				// linked to the order reference and not to the order id
				if (isset($extra_vars['transaction_id'])) {
					$transaction_id = $extra_vars['transaction_id'];
				} else {
					$transaction_id = null;
				}

				if (!isset($order) || !Validate::isLoadedObject($order) || !$order->addOrderPayment($amount_paid, null, $transaction_id)) {
					PrestaShopLogger::addLog('PaymentModule::validateOrder - Cannot save Order Payment', 3, null, 'Cart', (int)$id_cart, true);
					throw new PrestaShopException('Can\'t save Order Payment');
				}
			}

			// Next !
			$only_one_gift = false;
			$cart_rule_used = array();
			$products = $this->context->cart->getProducts();

			// Make sure CartRule caches are empty
			CartRule::cleanCache();
			foreach ($order_detail_list as $key => $order_detail) {
				/** @var OrderDetail $order_detail */

				$order = $order_list[$key];
				if (!$order_creation_failed && isset($order->id)) {
					if (!$secure_key) {
						$message .= '<br />'.Tools::displayError('Warning: the secure key is empty, check your payment account before validation');
					}
					// Optional message to attach to this order
					if (isset($message) & !empty($message)) {
						$msg = new Message();
						$message = strip_tags($message, '<br>');
						if (Validate::isCleanHtml($message)) {
							if (self::DEBUG_MODE) {
								PrestaShopLogger::addLog('PaymentModule::validateOrder - Message is about to be added', 1, null, 'Cart', (int)$id_cart, true);
							}
							$msg->message = $message;
							$msg->id_cart = (int)$id_cart;
							$msg->id_customer = (int)($order->id_customer);
							$msg->id_order = (int)$order->id;
							$msg->private = 1;
							$msg->add();
						}
					}

					// Insert new Order detail list using cart for the current order
					//$orderDetail = new OrderDetail(null, null, $this->context);
					//$orderDetail->createList($order, $this->context->cart, $id_order_state);

					// Construct order detail table for the email
					$products_list = '';
					$virtual_product = true;

					$product_var_tpl_list = array();
					foreach ($order->product_list as $product) {
						$price = Product::getPriceStatic((int)$product['id_product'], false, ($product['id_product_attribute'] ? (int)$product['id_product_attribute'] : null), 6, null, false, true, $product['cart_quantity'], false, (int)$order->id_customer, (int)$order->id_cart, (int)$order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
						$price_wt = Product::getPriceStatic((int)$product['id_product'], true, ($product['id_product_attribute'] ? (int)$product['id_product_attribute'] : null), 2, null, false, true, $product['cart_quantity'], false, (int)$order->id_customer, (int)$order->id_cart, (int)$order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});

						$product_price = Product::getTaxCalculationMethod() == PS_TAX_EXC ? Tools::ps_round($price, 2) : $price_wt;

						$product_var_tpl = array(
							'reference' => $product['reference'],
							'name' => $product['name'].(isset($product['attributes']) ? ' - '.$product['attributes'] : ''),
							'unit_price' => Tools::displayPrice($product_price, $this->context->currency, false),
							'price' => Tools::displayPrice($product_price * $product['quantity'], $this->context->currency, false),
							'quantity' => $product['quantity'],
							'customization' => array()
						);

						$customized_datas = Product::getAllCustomizedDatas((int)$order->id_cart);
						if (isset($customized_datas[$product['id_product']][$product['id_product_attribute']])) {
							$product_var_tpl['customization'] = array();
							foreach ($customized_datas[$product['id_product']][$product['id_product_attribute']][$order->id_address_delivery] as $customization) {
								$customization_text = '';
								if (isset($customization['datas'][Product::CUSTOMIZE_TEXTFIELD])) {
									foreach ($customization['datas'][Product::CUSTOMIZE_TEXTFIELD] as $text) {
										$customization_text .= $text['name'].': '.$text['value'].'<br />';
									}
								}

								if (isset($customization['datas'][Product::CUSTOMIZE_FILE])) {
									$customization_text .= sprintf(Tools::displayError('%d image(s)'), count($customization['datas'][Product::CUSTOMIZE_FILE])).'<br />';
								}

								$customization_quantity = (int)$product['customization_quantity'];

								$product_var_tpl['customization'][] = array(
									'customization_text' => $customization_text,
									'customization_quantity' => $customization_quantity,
									'quantity' => Tools::displayPrice($customization_quantity * $product_price, $this->context->currency, false)
								);
							}
						}

						$product_var_tpl_list[] = $product_var_tpl;
						// Check if is not a virutal product for the displaying of shipping
						if (!$product['is_virtual']) {
							$virtual_product &= false;
						}
					} // end foreach ($products)

					$product_list_txt = '';
					$product_list_html = '';
					if (count($product_var_tpl_list) > 0) {
						$product_list_txt = $this->getEmailTemplateContent('order_conf_product_list.txt', Mail::TYPE_TEXT, $product_var_tpl_list);
						$product_list_html = $this->getEmailTemplateContent('order_conf_product_list.tpl', Mail::TYPE_HTML, $product_var_tpl_list);
					}

					$cart_rules_list = array();
					$total_reduction_value_ti = 0;
					$total_reduction_value_tex = 0;
					foreach ($cart_rules as $cart_rule) {
						$package = array('id_carrier' => $order->id_carrier, 'id_address' => $order->id_address_delivery, 'products' => $order->product_list);
						$values = array(
							'tax_incl' => $cart_rule['obj']->getContextualValue(true, $this->context, CartRule::FILTER_ACTION_ALL_NOCAP, $package),
							'tax_excl' => $cart_rule['obj']->getContextualValue(false, $this->context, CartRule::FILTER_ACTION_ALL_NOCAP, $package)
						);

						// If the reduction is not applicable to this order, then continue with the next one
						if (!$values['tax_excl']) {
							continue;
						}

						// IF
						//	This is not multi-shipping
						//	The value of the voucher is greater than the total of the order
						//	Partial use is allowed
						//	This is an "amount" reduction, not a reduction in % or a gift
						// THEN
						//	The voucher is cloned with a new value corresponding to the remainder
						if (count($order_list) == 1 && $values['tax_incl'] > ($order->total_products_wt - $total_reduction_value_ti) && $cart_rule['obj']->partial_use == 1 && $cart_rule['obj']->reduction_amount > 0) {
							// Create a new voucher from the original
							$voucher = new CartRule((int)$cart_rule['obj']->id); // We need to instantiate the CartRule without lang parameter to allow saving it
							unset($voucher->id);

							// Set a new voucher code
							$voucher->code = empty($voucher->code) ? substr(md5($order->id.'-'.$order->id_customer.'-'.$cart_rule['obj']->id), 0, 16) : $voucher->code.'-2';
							if (preg_match('/\-([0-9]{1,2})\-([0-9]{1,2})$/', $voucher->code, $matches) && $matches[1] == $matches[2]) {
								$voucher->code = preg_replace('/'.$matches[0].'$/', '-'.(intval($matches[1]) + 1), $voucher->code);
							}

							// Set the new voucher value
							if ($voucher->reduction_tax) {
								$voucher->reduction_amount = ($total_reduction_value_ti + $values['tax_incl']) - $order->total_products_wt;

								// Add total shipping amout only if reduction amount > total shipping
								if ($voucher->free_shipping == 1 && $voucher->reduction_amount >= $order->total_shipping_tax_incl) {
									$voucher->reduction_amount -= $order->total_shipping_tax_incl;
								}
							} else {
								$voucher->reduction_amount = ($total_reduction_value_tex + $values['tax_excl']) - $order->total_products;

								// Add total shipping amout only if reduction amount > total shipping
								if ($voucher->free_shipping == 1 && $voucher->reduction_amount >= $order->total_shipping_tax_excl) {
									$voucher->reduction_amount -= $order->total_shipping_tax_excl;
								}
							}
							if ($voucher->reduction_amount <= 0) {
								continue;
							}

							if ($this->context->customer->isGuest()) {
								$voucher->id_customer = 0;
							} else {
								$voucher->id_customer = $order->id_customer;
							}

							$voucher->quantity = 1;
							$voucher->reduction_currency = $order->id_currency;
							$voucher->quantity_per_user = 1;
							$voucher->free_shipping = 0;
							if ($voucher->add()) {
								// If the voucher has conditions, they are now copied to the new voucher
								CartRule::copyConditions($cart_rule['obj']->id, $voucher->id);

								$params = array(
									'{voucher_amount}' => Tools::displayPrice($voucher->reduction_amount, $this->context->currency, false),
									'{voucher_num}' => $voucher->code,
									'{firstname}' => $this->context->customer->firstname,
									'{lastname}' => $this->context->customer->lastname,
									'{id_order}' => $order->reference,
									'{order_name}' => $order->getUniqReference()
								);
								Mail::Send(
									(int)$order->id_lang,
									'voucher',
									sprintf(Mail::l('New voucher for your order %s', (int)$order->id_lang), $order->reference),
									$params,
									$this->context->customer->email,
									$this->context->customer->firstname.' '.$this->context->customer->lastname,
									null, null, null, null, _PS_MAIL_DIR_, false, (int)$order->id_shop
								);
							}

							$values['tax_incl'] = $order->total_products_wt - $total_reduction_value_ti;
							$values['tax_excl'] = $order->total_products - $total_reduction_value_tex;
						}
						$total_reduction_value_ti += $values['tax_incl'];
						$total_reduction_value_tex += $values['tax_excl'];

						$order->addCartRule($cart_rule['obj']->id, $cart_rule['obj']->name, $values, 0, $cart_rule['obj']->free_shipping);

						if ($id_order_state != Configuration::get('PS_OS_ERROR') && $id_order_state != Configuration::get('PS_OS_CANCELED') && !in_array($cart_rule['obj']->id, $cart_rule_used)) {
							$cart_rule_used[] = $cart_rule['obj']->id;

							// Create a new instance of Cart Rule without id_lang, in order to update its quantity
							$cart_rule_to_update = new CartRule((int)$cart_rule['obj']->id);
							$cart_rule_to_update->quantity = max(0, $cart_rule_to_update->quantity - 1);
							$cart_rule_to_update->update();
						}

						$cart_rules_list[] = array(
							'voucher_name' => $cart_rule['obj']->name,
							'voucher_reduction' => ($values['tax_incl'] != 0.00 ? '-' : '').Tools::displayPrice($values['tax_incl'], $this->context->currency, false)
						);
					}

					$cart_rules_list_txt = '';
					$cart_rules_list_html = '';
					if (count($cart_rules_list) > 0) {
						$cart_rules_list_txt = $this->getEmailTemplateContent('order_conf_cart_rules.txt', Mail::TYPE_TEXT, $cart_rules_list);
						$cart_rules_list_html = $this->getEmailTemplateContent('order_conf_cart_rules.tpl', Mail::TYPE_HTML, $cart_rules_list);
					}

					// Specify order id for message
					$old_message = Message::getMessageByCartId((int)$this->context->cart->id);
					if ($old_message && !$old_message['private']) {
						$update_message = new Message((int)$old_message['id_message']);
						$update_message->id_order = (int)$order->id;
						$update_message->update();

						// Add this message in the customer thread
						$customer_thread = new CustomerThread();
						$customer_thread->id_contact = 0;
						$customer_thread->id_customer = (int)$order->id_customer;
						$customer_thread->id_shop = (int)$this->context->shop->id;
						$customer_thread->id_order = (int)$order->id;
						$customer_thread->id_lang = (int)$this->context->language->id;
						$customer_thread->email = $this->context->customer->email;
						$customer_thread->status = 'open';
						$customer_thread->token = Tools::passwdGen(12);
						$customer_thread->add();

						$customer_message = new CustomerMessage();
						$customer_message->id_customer_thread = $customer_thread->id;
						$customer_message->id_employee = 0;
						$customer_message->message = $update_message->message;
						$customer_message->private = 0;

						if (!$customer_message->add()) {
							$this->errors[] = Tools::displayError('An error occurred while saving message');
						}
					}

					if (self::DEBUG_MODE) {
						PrestaShopLogger::addLog('PaymentModule::validateOrder - Hook validateOrder is about to be called', 1, null, 'Cart', (int)$id_cart, true);
					}

					// Hook validate order
					Hook::exec('actionValidateOrder', array(
						'cart' => $this->context->cart,
						'order' => $order,
						'customer' => $this->context->customer,
						'currency' => $this->context->currency,
						'orderStatus' => $order_status
					));

					foreach ($this->context->cart->getProducts() as $product) {
						if ($order_status->logable) {
							ProductSale::addProductSale((int)$product['id_product'], (int)$product['cart_quantity']);
						}
					}

					if (self::DEBUG_MODE) {
						PrestaShopLogger::addLog('PaymentModule::validateOrder - Order Status is about to be added', 1, null, 'Cart', (int)$id_cart, true);
					}

					// Set the order status
					$new_history = new OrderHistory();
					$new_history->id_order = (int)$order->id;
					$new_history->changeIdOrderState((int)$id_order_state, $order, true);
					$new_history->addWithemail(true, $extra_vars);

					// Switch to back order if needed
					if (Configuration::get('PS_STOCK_MANAGEMENT') && ($order_detail->getStockState() || $order_detail->product_quantity_in_stock <= 0)) {
						$history = new OrderHistory();
						$history->id_order = (int)$order->id;
						$history->changeIdOrderState(Configuration::get($order->valid ? 'PS_OS_OUTOFSTOCK_PAID' : 'PS_OS_OUTOFSTOCK_UNPAID'), $order, true);
						$history->addWithemail();
					}

					unset($order_detail);

					// Order is reloaded because the status just changed
					$order = new Order((int)$order->id);

					// Send an e-mail to customer (one order = one email)
					if ($id_order_state != Configuration::get('PS_OS_ERROR') && $id_order_state != Configuration::get('PS_OS_CANCELED') && $this->context->customer->id) {
						$invoice = new Address((int)$order->id_address_invoice);
						$delivery = new Address((int)$order->id_address_delivery);
						$delivery_state = $delivery->id_state ? new State((int)$delivery->id_state) : false;
						$invoice_state = $invoice->id_state ? new State((int)$invoice->id_state) : false;

						$data = array(
						'{firstname}' => $this->context->customer->firstname,
						'{lastname}' => $this->context->customer->lastname,
						'{email}' => $this->context->customer->email,
						'{delivery_block_txt}' => $this->_getFormatedAddress($delivery, "\n"),
						'{invoice_block_txt}' => $this->_getFormatedAddress($invoice, "\n"),
						'{delivery_block_html}' => $this->_getFormatedAddress($delivery, '<br />', array(
							'firstname'    => '<span style="font-weight:bold;">%s</span>',
							'lastname'    => '<span style="font-weight:bold;">%s</span>'
						)),
						'{invoice_block_html}' => $this->_getFormatedAddress($invoice, '<br />', array(
								'firstname'    => '<span style="font-weight:bold;">%s</span>',
								'lastname'    => '<span style="font-weight:bold;">%s</span>'
						)),
						'{delivery_company}' => $delivery->company,
						'{delivery_firstname}' => $delivery->firstname,
						'{delivery_lastname}' => $delivery->lastname,
						'{delivery_address1}' => $delivery->address1,
						'{delivery_address2}' => $delivery->address2,
						'{delivery_city}' => $delivery->city,
						'{delivery_postal_code}' => $delivery->postcode,
						'{delivery_country}' => $delivery->country,
						'{delivery_state}' => $delivery->id_state ? $delivery_state->name : '',
						'{delivery_phone}' => ($delivery->phone) ? $delivery->phone : $delivery->phone_mobile,
						'{delivery_other}' => $delivery->other,
						'{invoice_company}' => $invoice->company,
						'{invoice_vat_number}' => $invoice->vat_number,
						'{invoice_firstname}' => $invoice->firstname,
						'{invoice_lastname}' => $invoice->lastname,
						'{invoice_address2}' => $invoice->address2,
						'{invoice_address1}' => $invoice->address1,
						'{invoice_city}' => $invoice->city,
						'{invoice_postal_code}' => $invoice->postcode,
						'{invoice_country}' => $invoice->country,
						'{invoice_state}' => $invoice->id_state ? $invoice_state->name : '',
						'{invoice_phone}' => ($invoice->phone) ? $invoice->phone : $invoice->phone_mobile,
						'{invoice_other}' => $invoice->other,
						'{order_name}' => $order->getUniqReference(),
						'{date}' => Tools::displayDate(date('Y-m-d H:i:s'), null, 1),
						'{carrier}' => ($virtual_product || !isset($carrier->name)) ? Tools::displayError('No carrier') : $carrier->name,
						'{payment}' => Tools::substr($order->payment, 0, 32),
						'{products}' => $product_list_html,
						'{products_txt}' => $product_list_txt,
						'{discounts}' => $cart_rules_list_html,
						'{discounts_txt}' => $cart_rules_list_txt,
						'{total_paid}' => Tools::displayPrice($order->total_paid, $this->context->currency, false),
						'{total_products}' => Tools::displayPrice(Product::getTaxCalculationMethod() == PS_TAX_EXC ? $order->total_products : $order->total_products_wt, $this->context->currency, false),
						'{total_discounts}' => Tools::displayPrice($order->total_discounts, $this->context->currency, false),
						'{total_shipping}' => Tools::displayPrice($order->total_shipping, $this->context->currency, false),
						'{total_wrapping}' => Tools::displayPrice($order->total_wrapping, $this->context->currency, false),
						'{total_tax_paid}' => Tools::displayPrice(($order->total_products_wt - $order->total_products) + ($order->total_shipping_tax_incl - $order->total_shipping_tax_excl), $this->context->currency, false));

						if (is_array($extra_vars)) {
							$data = array_merge($data, $extra_vars);
						}

						// Join PDF invoice
						if ((int)Configuration::get('PS_INVOICE') && $order_status->invoice && $order->invoice_number) {
							$order_invoice_list = $order->getInvoicesCollection();
							Hook::exec('actionPDFInvoiceRender', array('order_invoice_list' => $order_invoice_list));
							$pdf = new PDF($order_invoice_list, PDF::TEMPLATE_INVOICE, $this->context->smarty);
							$file_attachement['content'] = $pdf->render(false);
							$file_attachement['name'] = Configuration::get('PS_INVOICE_PREFIX', (int)$order->id_lang, null, $order->id_shop).sprintf('%06d', $order->invoice_number).'.pdf';
							$file_attachement['mime'] = 'application/pdf';
						} else {
							$file_attachement = null;
						}

						if (self::DEBUG_MODE) {
							PrestaShopLogger::addLog('PaymentModule::validateOrder - Mail is about to be sent', 1, null, 'Cart', (int)$id_cart, true);
						}

						if (Validate::isEmail($this->context->customer->email)) {
							Mail::Send(
								(int)$order->id_lang,
								'order_conf',
								Mail::l('Order confirmation', (int)$order->id_lang),
								$data,
								$this->context->customer->email,
								$this->context->customer->firstname.' '.$this->context->customer->lastname,
								null,
								null,
								$file_attachement,
								null, _PS_MAIL_DIR_, false, (int)$order->id_shop
							);
						}
					}

					// updates stock in shops
					if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
						$product_list = $order->getProducts();
						foreach ($product_list as $product) {
							// if the available quantities depends on the physical stock
							if (StockAvailable::dependsOnStock($product['product_id'])) {
								// synchronizes
								StockAvailable::synchronize($product['product_id'], $order->id_shop);
							}
						}
					}

					$order->updateOrderDetailTax();
				} else {
					$error = Tools::displayError('Order creation failed');
					PrestaShopLogger::addLog($error, 4, '0000002', 'Cart', intval($order->id_cart));
					die($error);
				}
			} // End foreach $order_detail_list

			// Use the last order as currentOrder
			if (isset($order) && $order->id) {
				$this->currentOrder = (int)$order->id;
			}

			if (self::DEBUG_MODE) {
				PrestaShopLogger::addLog('PaymentModule::validateOrder - End of validateOrder', 1, null, 'Cart', (int)$id_cart, true);
			}

			return true;
		} else {
			$error = Tools::displayError('Cart cannot be loaded or an order has already been placed using this cart');
			PrestaShopLogger::addLog($error, 4, '0000001', 'Cart', intval($this->context->cart->id));
			die($error);
		}
	}
}
