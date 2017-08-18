<?php
  
class PromocionesCoreCore extends ObjectModel
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
}

