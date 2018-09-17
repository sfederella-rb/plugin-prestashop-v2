<?php
require_once(dirname(__FILE__).'../../../../../config/config.inc.php');
include_once(dirname(__FILE__) .'/../../classes/Promociones.php');

class AdminPromocionesController extends AdminController
{	
    public $modulesParams = "&configure=decidir&tab_module=payments_gateways&module_name=decidir";

    public $urlAddBank = "";

    public $name = "";

    public $sectionTitle = "PLANES DE PAGO";
    
    public function __construct()
    {
        //parent::__construct();
        if($_GET['controller'] == 'AdminModules' && $_GET['configure'] == 'decidir'){
            parent::__construct();
        }
    }   

    public function renderListPromociones(){
        $list = $this->getAllPromociones();

        $this->fields_list = array(
            'id_promocion' => array(
                'title' => $this->l('Id'),
                'width' => 50,
                'type' => 'text',
                'align' => 'center',
            ),
            'name' => array(
                'title' => $this->l('Nombre'),
                'width' => 140,
                'type' => 'text',
                'align' => 'center',
            ),
            'payment_method' => array(
                'title' => $this->l('Tarjeta'),
                'width' => 50,
                'type' => 'text',
                'align' => 'center',
            ),
            'entity' => array(
                'title' => $this->l('Entidad Financiera'),
                'width' => 60,
                'type' => 'text',
                'align' => 'center',
            ),
            'days' => array(
                'title' => $this->l('Días'),
                'width' => 80,
                'type' => 'text',
                'align' => 'center',
            ),
            'init_date' => array(
                'title' => $this->l('Fecha inicio'),
                'width' => 50,
                'type' => 'text',
                'align' => 'center',
            ),
            'final_date' => array(
                'title' => $this->l('Fecha final'),
                'width' => 50,
                'type' => 'text',
                'align' => 'center',
            ),
            'installment' => array(
                'title' => $this->l('Cuotas'),
                'width' => 70,
                'type' => 'text',
                'align' => 'center',
            ),
            'send_installment' => array(
                'title' => $this->l('Cuotas a enviar'),
                'width' => 50,
                'type' => 'text',
                'align' => 'center',
            ),
            'coeficient' => array(
                'title' => $this->l('Coeficiente'),
                'width' => 20,
                'type' => 'text',
                'align' => 'center',
            ),      
            'discount' => array(
                'title' => $this->l('Descuento (%)'),
                'width' => 15,
                'type' => 'text',
                'align' => 'center',
            ),  
            'reimbursement' => array(
                'title' => $this->l('Reintegro (%)'),
                'width' => 15,
                'type' => 'text',
                'align' => 'center',
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
         
        $helper->identifier = 'id_promocion';

        $urlAddBank = AdminController::$currentIndex.'&configure='.(isset($this->name)? $this->name: "").'&section=6&add_promocion&token='.Tools::getAdminTokenLite('AdminModules').$this->modulesParams; 
        
        $helper->title = 'Promociones <span style="float:right;" class="panel-heading-action">'
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

        $helper->table = (isset($this->name)? $this->name: "").'_promocion';
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->token = Tools::getAdminTokenLite('AdminModules').$this->modulesParams;
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name.'&section=6';

        return $helper->generateList($list, $this->fields_list);
    }

    public function getAllPromociones(){

        $sql = 'SELECT promos.id_promocion, promos.name, medios.name AS payment_method, entidades.name AS entity, promos.send_installment, promos.days, promos.init_date, promos.final_date, promos.installment, promos.coeficient, promos.discount, promos.reimbursement, promos.active 
                    FROM ' . _DB_PREFIX_ . 'promociones AS promos INNER JOIN ' . _DB_PREFIX_ . 'medios AS medios ON promos.payment_method = medios.id_medio INNER JOIN ' . _DB_PREFIX_ . 'entidades AS entidades ON promos.entity = entidades.id_entidad WHERE medios.active = 1 ORDER BY promos.id_promocion, promos.entity, promos.payment_method ASC';                       

        $result = Db::getInstance()->ExecuteS($sql);

        $result = $this->getDaysList($result);
        $result = $this->getInstallmentList($result);
        $result = $this->validateformat($result);

        return $result;
    }

    public function getById($idPromocion){
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'promociones WHERE id_promocion='.$idPromocion;
        $result = Db::getInstance()->ExecuteS($sql);
        
        return $result;     
    }

    public function updatePromocionVisible($idPromocion){

        $query = 'UPDATE '._DB_PREFIX_.'promociones SET active = IF (active, 0, 1) WHERE id_promocion='.$idPromocion;

        if(!Db::getInstance()->execute($query)){
            die('Error de actualizacion.');        
        }
    }

    public function updatePromocion($ArrayPromocionfields){

        if($ArrayPromocionfields['payment_method'] == '' || $ArrayPromocionfields['payment_method'] == null) 
                $ArrayPromocionfields['payment_method'] ='0';

        if($ArrayPromocionfields['entity'] == '' || $ArrayPromocionfields['entity'] == null)
                $ArrayPromocionfields['entity'] = '0';
         
        //if($ArrayPromocionfields['installment'] == '' || $ArrayPromocionfields['installment'] == '0')
        //        $ArrayPromocionfields['installment'] = '0';            
        
        if($ArrayPromocionfields['send_installment'] == '' || $ArrayPromocionfields['installment'] == '0')
                $ArrayPromocionfields['send_installment'] = '0';

        if($ArrayPromocionfields['discount'] == '' )
                $ArrayPromocionfields['discount'] = '0';

        if($ArrayPromocionfields['reinbursement'] == '' )
                $ArrayPromocionfields['reinbursement'] = '0';     

        $daysList = array_sum($ArrayPromocionfields['id_days']);
        $InstallmentList = array_sum($ArrayPromocionfields['id_installment']);

        $query = 'UPDATE `'._DB_PREFIX_.'promociones` SET name="'.$ArrayPromocionfields['plan_name'].'", payment_method='.$ArrayPromocionfields['payment_method'].', entity= '.$ArrayPromocionfields['entity'].', send_installment="'.$ArrayPromocionfields['send_installment'].'", days="'.$daysList.'", init_date="'.$ArrayPromocionfields['date_from'].'", final_date="'.$ArrayPromocionfields['date_to'].'", installment="'.$InstallmentList.'", coeficient='.$ArrayPromocionfields['coeficient'].', discount='.$ArrayPromocionfields['discount'].', reimbursement='.$ArrayPromocionfields['reinbursement'].' WHERE id_promocion = '.$ArrayPromocionfields['id_promocion'];         
        if(!Db::getInstance()->execute($query)){
            die('Error de actualizacion.');        
        }
    }

    public function insertPromocion($ArrayPromocionfields){
        $daysList = array_sum($ArrayPromocionfields['id_days']);

        $InstallmentList = array_sum($ArrayPromocionfields['id_installment']);

        if($ArrayPromocionfields['send_installment'] == '' )
                $ArrayPromocionfields['send_installment'] = 0;

        if($ArrayPromocionfields['discount'] == '' || $ArrayPromocionfields['discount'] == '0')
                $ArrayPromocionfields['discount'] = '0';

        if($ArrayPromocionfields['reinbursement'] == '' )
                $ArrayPromocionfields['reinbursement'] = '0';    

        $query = 'INSERT INTO `'._DB_PREFIX_.'promociones` (name, payment_method, entity, send_installment, days, init_date, final_date, installment, coeficient, discount, reimbursement, active) VALUES("'.$ArrayPromocionfields['plan_name'].'", '.$ArrayPromocionfields['payment_method'].', '.$ArrayPromocionfields['entity'].' , "'.$ArrayPromocionfields['send_installment'].'", "'.$daysList.'", "'.$ArrayPromocionfields['date_from'].'", "'.$ArrayPromocionfields['date_to'].'", "'.$InstallmentList.'", '.$ArrayPromocionfields['coeficient'].', '.$ArrayPromocionfields['discount'].', '.$ArrayPromocionfields['reinbursement'].', '.$ArrayPromocionfields['active'].')';                  

        if(!Db::getInstance()->execute($query)){
            die('Error al insertar promocion.');        
        }
    }

    public function deletePromocion($idPromocion){

        Db::getInstance()->delete('promociones', 'id_promocion='.$idPromocion);
    }

    public function getDaysList($result){

        $instanceEntity = new PromocionesCore();

        foreach($result as $indexPromo => $value){
            $daysCodes = $instanceEntity->getNumberCodes($result[$indexPromo]['days']);

            $daysNamesList = array();

            foreach($daysCodes as $val){

                switch ($val) {
                    case 1:
                        array_push($daysNamesList, "Dom");
                        break;
                    case 2:
                        array_push($daysNamesList, "Lun");
                        break;
                    case 4:
                        array_push($daysNamesList, "Mar");
                        break;
                    case 8:
                        array_push($daysNamesList, "Mie");
                        break;
                    case 16:
                        array_push($daysNamesList, "Jue");
                        break;  
                    case 32:
                        array_push($daysNamesList, "Vie");
                        break;
                    case 64:
                        array_push($daysNamesList, "Sab");
                        break;                 
                }
            }
            
            $daysJoin = implode(",",$daysNamesList);
            $result[$indexPromo]['days'] = $daysJoin;
        }
        return $result;
    }

    public function getInstallmentList($result){

        $instancePromo = new PromocionesCore();

        foreach($result as $indexPromo => $value){
            $installmentCodes = $instancePromo->getNumberCodes($result[$indexPromo]['installment']);
            $installmentNamesList = array();

            foreach($installmentCodes as $code){
                if(in_array($code, $installmentCodes)){
                    $indexArray = array_search($code, $instancePromo->numberInstallmentList());   
                    array_push($installmentNamesList, $indexArray);        
                }
            }

            $installmentNamesList = $installmentNamesList;
            
            $installmentJoin = implode(",",$installmentNamesList++);
            $result[$indexPromo]['installment'] = $installmentJoin;
        }

        return $result;
    }

    public function validateformat($result){

        foreach($result as $indexPromo => $value){

            if($result[$indexPromo]['init_date'] == 0){
                $result[$indexPromo]['init_date'] = "---";
            }

            if($result[$indexPromo]['final_date'] == 0){
                $result[$indexPromo]['final_date'] = "---";
            }   

            if($result[$indexPromo]['send_installment'] == 0 || $result[$indexPromo]['send_installment'] == ""){
                $result[$indexPromo]['send_installment'] = " ";
            }           
        }    

        return $result;
    }
}
