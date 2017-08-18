<?php
	include_once('../../../../config/config.inc.php');
	include_once('../../controllers/front/payment.php');
	include_once('../../decidir.php');
	include_once('../../controllers/back/AdminMediosController.php');
	include_once('../../controllers/back/AdminEntityController.php');

	if(isset($_POST['selecttype']) && $_POST['selecttype'] == "pmethod")
	{	
		$pMethodList = array();	
		$list = new AdminMediosController();
		$pMethodList['data'] = $list->getAllPMethods();
		echo(json_encode($pMethodList));
	}

	if(isset($_POST['selecttype']) && $_POST['selecttype'] == "entities")
	{	
		$entityList = array();
		$list = new AdminEntityController();
		$entityList['data'] = $list->getAllEntityName();
		echo json_encode($entityList);
	}

	if(isset($_POST['selecttype']) && $_POST['selecttype'] == "installment")
	{	
		$decidir = new Decidir();
		if($_POST['entity_selected'] == 0){
			$selectList = $decidir->getInstallmentsList(false, $_POST['method_selected'], "", $_POST['total']);
			echo(json_encode($selectList));
		}else{
			$selectPromoList = $decidir->getInstallmentsList(true, $_POST['method_selected'], $_POST['entity_selected'], $_POST['total']);
			echo(json_encode($selectPromoList));
		}
	}

	if(isset($_POST['userid']) && $_POST['userid'] != "")
	{	
		$instToken = new AdminMediosController();
		$tokenList = $instToken->getTokensUserList($_POST['userid']);
		echo json_encode($tokenList);
	}
