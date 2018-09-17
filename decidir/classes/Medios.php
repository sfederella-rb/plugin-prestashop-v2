<?php
  
class MediosCore extends ObjectModel
{
     /** @var string Name */
    public $name;
  
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'medios',
        'primary' => 'id_medios',
        'fields' => array(
            'name' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => true,
                'size' => 64
            )
        ),
    );

    public function getById($idPaymenMethod){
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'medios WHERE id_medio='.$idPaymenMethod;
        $result = Db::getInstance()->ExecuteS($sql);
        
        return $result;     
    }

    public function getAllPMethods(){
        $sql = 'SELECT id_medio AS id, name FROM ' . _DB_PREFIX_ . 'medios WHERE type="Tarjeta" AND active = 1 ORDER BY name DESC';
        $result = Db::getInstance()->ExecuteS($sql);
        
        return $result;     
    }

    public function getTokensUserList($userid, $pMethod){
        $element = array();
        $tokenInfo = array();
        $tokenList = array();

        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'decidir_tokens WHERE user_id="'.$userid.'" AND payment_method_id="'.$pMethod.'"';
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

    public function getTokenByUserId($userid, $bin, $pMethodIds){
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'decidir_tokens WHERE user_id="'.$userid.'" AND bin="'.$bin.'" AND payment_method_id="'.$pMethodIds .'"';


        $result = Db::getInstance()->ExecuteS($sql);



        return $result;
    }
}

