<?php
require_once (dirname(__FILE__) . '../../../../../config/config.inc.php');

Class DecidirPaymentSelect
{
	public function init()
	{	

	}

	public function getInstallmentsPromoList($pMethod, $entity){
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'promociones WHERE payment_method = '.$pMethod.' AND entity = '.$entity.' AND active = 1 AND (init_date IS NULL OR NOW() >= init_date) AND (final_date IS NULL OR NOW() <= final_date)';

        $result = Db::getInstance()->ExecuteS($sql);

        return $result;     
    }

	public function getInstallentByPaymentId($pMethod){
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'interes WHERE payment_method IN (SELECT id_decidir FROM ' . _DB_PREFIX_ . 'medios WHERE id_medio ='.$pMethod.' AND active=1) AND active=1';

        $result = Db::getInstance()->ExecuteS($sql);
        return $result;     
    }
}
