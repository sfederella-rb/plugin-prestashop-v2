<?php
  
class EntidadesCore extends ObjectModel
{
     /** @var string Name */
    public $name;
  
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'entidades',
        'primary' => 'id_entidad',
        'fields' => array(
            'name' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => true,
                'size' => 64
            )
        ),
    );

    public function getAllEntityName(){
        $sql = 'SELECT id_entidad AS id, name FROM ' . _DB_PREFIX_ . 'entidades WHERE active=1';
        $result = Db::getInstance()->ExecuteS($sql);

        return $result;     
    }

    public function getById($idEntity){
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'entidades WHERE id_entidad='.$idEntity;
        $result = Db::getInstance()->ExecuteS($sql);
        
        return $result;     
    }
}

