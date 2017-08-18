<?php
  
class InteresCore extends ObjectModel
{
     /** @var string Name */
    public $name;
  
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'interes',
        'primary' => 'id_interes',
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

