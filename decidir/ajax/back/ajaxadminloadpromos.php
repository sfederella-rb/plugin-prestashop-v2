<?php

    class Promos{
        public static function getMedios($ajax_payment_methods=""){
            if($ajax_payment_methods == 1){
			$type = 'Tarjeta';
    		}elseif($ajax_payment_methods == 2){
    			$type = 'Cupon';
    		}
	        $sql = 'SELECT id_medio AS id, name FROM ' . _DB_PREFIX_ . 'medios WHERE type = "'.$type.'" AND active = 1 ORDER BY name ASC';
            $result = Db::getInstance()->ExecuteS($sql);
            
            return $result;
        }
        
        public static function makeOptions($ajax_option=""){
            $arreglo=array();
            foreach(self::getMedios($ajax_option) as $medio){
                $sub_arreglo=array("id_option"=>$medio["id"],"name"=>$medio["name"]);
                array_push($arreglo,$sub_arreglo);
            }
            
            return $arreglo;
        }
        
    }
?>