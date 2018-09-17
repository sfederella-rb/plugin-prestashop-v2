<?php
require_once(dirname(__FILE__).'../../../../../config/config.inc.php');

class AdminEntityController extends AdminController
{	
    public $modulesParams = "&configure=decidir&tab_module=payments_gateways&module_name=decidir";

    public $urlAddEntity = "";

    public $name = "";
    
    public $sectionTitle = "ENTIDAD";

    public function __construct()
    {
        //parent::__construct();
         if($_GET['controller'] == 'AdminModules' && $_GET['configure'] == 'decidir'){
            parent::__construct();
        }
    }    

    public function renderListEntities(){
        $list = $this->getAllEntities();

        $this->fields_list = array(
            'id_entidad' => array(
                'title' => $this->l('Id'),
                'width' => 140,
                'type' => 'text',
            ),
            'name' => array(
                'title' => $this->l('Nombre Entidad'),
                'width' => 140,
                'type' => 'text',
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
         
        //Actions to be displayed in the "Actions" column
        $helper->actions = array('edit', 'delete');
         
        $helper->identifier = 'id_entidad';

        //arreglar esto!!!!!!
        $urlAddEntity = AdminController::$currentIndex.'&configure=&section=5&add_entidad&token='.Tools::getAdminTokenLite('AdminModules').$this->modulesParams; 
        
        $helper->title = 'Entidades Financieras <span style="float:right;" class="panel-heading-action">'
                            .'<a style="decoration:none;" id="desc-zone-new" class="list-toolbar-btn" href="'.$urlAddEntity.'">'
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

        $helper->table = ((isset($this->name))? $this->name : "").'_entidad';
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->token = Tools::getAdminTokenLite('AdminModules').$this->modulesParams;
        $helper->currentIndex = AdminController::$currentIndex.'&configure=&section=5';

        return $helper->generateList($list, $this->fields_list);
    }

    public function getAllEntities(){
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'entidades';
        $result = Db::getInstance()->ExecuteS($sql);
        
        return $result;     
    }

    public function updateEntity($ArrayEntityfields){
        $query = 'UPDATE '._DB_PREFIX_.'entidades SET name="'.$ArrayEntityfields['name'].'", active='.$ArrayEntityfields['active'].' WHERE id_entidad='.$ArrayEntityfields['id_entity'];

        if(!Db::getInstance()->execute($query)){
            die('Error de actualizacion.');        
        }
    }

    public function updateEntityVisible($idEntity){

        $query = 'UPDATE '._DB_PREFIX_.'entidades SET active = IF (active, 0, 1) WHERE id_entidad='.$idEntity;
        if(!Db::getInstance()->execute($query)){
            die('Error de actualizacion.');        
        }
    }

    public function insertEntity($ArrayEntityfields){
        $query = 'INSERT INTO `'._DB_PREFIX_.'entidades` (name, active) VALUES("'.$ArrayEntityfields['name'].'",'.$ArrayEntityfields['active'].')';

        if(!Db::getInstance()->execute($query)){
            die('Error al insertar entidad.');        
        }
    }

    public function deleteEntity($idEntity){
        Db::getInstance()->delete('entidades', 'id_entidad='.$idEntity);
        Db::getInstance()->delete('promociones', 'entity='.$idEntity);
    }
}