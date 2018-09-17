<?php
require_once(dirname(__FILE__).'../../../../../config/config.inc.php');

class AdminMediosController extends AdminController
{   
    public $modulesParams = "&configure=decidir&tab_module=payments_gateways&module_name=decidir";

    public $urlAddBank = "";

    public $name = "";
    
    public $sectionTitle = "MEDIOS DE PAGO";

    public function __construct()
    {
        //parent::__construct();
         if($_GET['controller'] == 'AdminModules' && $_GET['configure'] == 'decidir'){
            parent::__construct();
        }
    }    

    public function renderListMedios(){
        $list = $this->getAllMedios();

        $this->fields_list = array(
            'id_medio' => array(
                'title' => $this->l('Id'),
                'width' => 140,
                'type' => 'text',
                'align' => 'center',
            ),
            'name' => array(
                'title' => $this->l('Nombre'),
                'width' => 140,
                'type' => 'text',
            ),
            'type' => array(
                'title' => $this->l('Tipo'),
                'width' => 140,
                'type' => 'text',
            ),
            'id_decidir' => array(
                'title' => $this->l('ID Medio de pago Decidir'),
                'width' => 140,
                'type' => 'text',
                'align' => 'center',63
            ),    
            'active' => array(
                'title' => $this->l('Activado'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'class' => 'fixed-width-sm'
            )
        );
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = true;
        // Actions to be displayed in the "Actions" column
        $helper->actions = array('edit', 'delete');         
        $helper->identifier = 'id_medio';

        //arreglar esto!!!!!!
        $urlAddBank = AdminController::$currentIndex.'&configure=&section=4&add_mediopago&token='.Tools::getAdminTokenLite('AdminModules').$this->modulesParams; 

        $helper->title = 'Medios de pago <span style="float:right;" class="panel-heading-action">'
                                    .'<a style="decoration:none;" id="desc-zone-new" class="list-toolbar-btn" href="'.$urlAddBank.'">'
                                        .'<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="A&ntilde;adir nuevo" data-html="true" data-placement="top">'
                                            .'<i class="process-icon-new"></i>'
                                        .'</span>'
                                    .'</a>'
                                    .'<a class="list-toolbar-btn" href="javascript:location.reload();">'
                                        .'<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Refrescar lista" data-html="true" data-placement="top">'
                                            .'<i class="process-icon-refresh"></i>'
                                        .'</span>'
                                    .'</a>'
                                .'</span>';

        $helper->table = ((isset($this->name))? $this->name : "").'_mediopago';
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->token = Tools::getAdminTokenLite('AdminModules').$this->modulesParams;
        $helper->currentIndex = AdminController::$currentIndex.'&configure=&section=4';

        return $helper->generateList($list, $this->fields_list);
    }

    public function getAllMedios(){

        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'medios';
        $result = Db::getInstance()->ExecuteS($sql);
        
        return $result;     
    }

    public function updateMediosPago($ArrayMedioPagofields){

        ($ArrayMedioPagofields['id_tipo'] == 1)? $tipoName="Tarjeta": $tipoName = "Cupon";

        $query = 'UPDATE `'._DB_PREFIX_.'medios` SET name="'.$ArrayMedioPagofields['name'].'", type="'.$tipoName.'", id_decidir='.$ArrayMedioPagofields['id_decidir'].', active='.$ArrayMedioPagofields['active'].' WHERE id_medio = '.$ArrayMedioPagofields['id_medio'];

        if(!Db::getInstance()->execute($query)){
            die('Error de actualizacion.');        
        }
    }

    public function updateMedioPagoVisible($idMedioPago){

        $query = 'UPDATE '._DB_PREFIX_.'medios SET active = IF (active, 0, 1) WHERE id_medio='.$idMedioPago;
        if(!Db::getInstance()->execute($query)){
            die('Error de actualizacion.');        
        }
    }

    public function insertMediosPago($ArrayMedioPagofields){

        ($ArrayMedioPagofields['id_tipo'] == 1)? $tipoName="Tarjeta": $tipoName = "Cupon";

        $query = 'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("'.$ArrayMedioPagofields['name'].'","'.$tipoName.'",'.$ArrayMedioPagofields['id_decidir'].','.$ArrayMedioPagofields['active'].')';

        if(!Db::getInstance()->execute($query)){
            die('Error al insertar medio de pago.');        
        }
    }

    public function deleteMedioPago($idMedioPago){  
        Db::getInstance()->delete('medios', 'id_medio='.$idMedioPago);
        Db::getInstance()->delete('promociones', 'payment_method='.$idMedioPago);
    }
}