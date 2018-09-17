<?php
  
class PromocionesCore extends ObjectModel
{
     /** @var string Name */
    public $name;
  
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'promociones',
        'primary' => 'id_promociones',
        'fields' => array(
            'name' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => true,
                'size' => 64
            )
        ),
    );

    public function numberDayList(){
        //return a array with days and bits 
        $days = array();
        $val = 1;
        $places = 1;

        for($index=0; $index<=6; $index++){
            $days[$index] = $val;                   
            $res = $val << $places;
            $val = $res;
        }

        return $days;
    }

    public function getNumberCodes($CodesValues){
        $result = 0;
        $suma = $CodesValues;        
        $array_codes = array();

        do{ 
            $result = (int)pow(2, (int)(log($suma)/log(2)));
            
            $suma = $suma - $result; 
            array_push($array_codes, $result);   
            
        }while($suma > 0); 

        $array_codes_reverse = array_reverse($array_codes);

        return $array_codes_reverse;
    }

    public function numberInstallmentList(){
        $installment = array();
        $val = 1;
        $places = 1;

        for($index=0; $index<=24; $index++){
            $installment[$index] = $val;                   
            $res = $val << $places;
            $val = $res;
        }

        return $installment;
    }

    public function getById($idPromocion){
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'promociones WHERE id_promocion='.$idPromocion;

        $result = Db::getInstance()->ExecuteS($sql);
        
        return $result;     
    }
}

