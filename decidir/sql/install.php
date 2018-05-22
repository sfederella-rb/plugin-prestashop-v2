<?php

	$delete_tables = array(
						'DROP TABLE IF EXISTS `'._DB_PREFIX_.'promociones`',
						'DROP TABLE IF EXISTS `'._DB_PREFIX_.'entidades`',
						'DROP TABLE IF EXISTS `'._DB_PREFIX_.'medios`',
						'DROP TABLE IF EXISTS `'._DB_PREFIX_.'interes`'
					);

	$create_tables = array('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'decidir_transacciones'.'`(
									`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
									`user_id` INT(11) UNSIGNED NOT NULL,
									`order_id` VARCHAR(30) NULL DEFAULT NULL,
									`decidir_order_id` VARCHAR(30) NULL DEFAULT NULL,
									`payment_response` TEXT NULL DEFAULT NULL,
									`marca` INT(10) UNSIGNED NOT NULL,
									`banco` INT(10) UNSIGNED NOT NULL,
									`cuotas` INT(3) UNSIGNED NOT NULL,
									`date` DATE NOT NULL,
									PRIMARY KEY (`id`)
							)',
							'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'decidir_tokens'.'`(
									`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
									`user_id` VARCHAR(255) NULL DEFAULT NULL,
									`name` VARCHAR(255) NULL DEFAULT NULL,
									`marca_id` INT(11) UNSIGNED NOT NULL,
									`banco_id` INT(11) UNSIGNED NOT NULL,
									`token` VARCHAR(255) NULL DEFAULT NULL,
									`payment_method_id` INT(11) UNSIGNED NULL DEFAULT NULL,
									`bin` VARCHAR(10) NULL DEFAULT NULL,
									`last_four_digits` VARCHAR(4) NULL DEFAULT NULL,
									`expiration_month` VARCHAR(2) NULL DEFAULT NULL,
									`expiration_year` VARCHAR(2) NULL DEFAULT NULL,
									`expired` VARCHAR(5) NULL DEFAULT NULL,
									`token_response` VARCHAR(255) NULL DEFAULT NULL,
									PRIMARY KEY (`id`)
							)',
							'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'medios'.'`(
									`id_medio` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
									`name` VARCHAR(50),
									`type` VARCHAR(50) NOT NULL,
									`id_decidir` INT(10) UNSIGNED NOT NULL,
									`active` INT(1) UNSIGNED NOT NULL,
									PRIMARY KEY (`id_medio`)
							)',
							'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'entidades'.'`(
									`id_entidad` INT UNSIGNED NOT NULL AUTO_INCREMENT,
									`name` VARCHAR(50),
									`active` INT(1) UNSIGNED NOT NULL,
									PRIMARY KEY (`id_entidad`),
									UNIQUE `name` (`name`)
							)',
							'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'promociones'.'`(
									`id_promocion` INT UNSIGNED NOT NULL AUTO_INCREMENT,
									`name` TEXT,
									`payment_method` INT(11) NOT NULL,
									`entity` INT(11) NOT NULL,
									`send_installment` INT(11),
									`days` INT(11) NOT NULL,
									`init_date` DATE NOT NULL,
									`final_date` DATE NOT NULL,
									`installment` INT(11) NOT NULL,
									`coeficient` DECIMAL(5,4) NOT NULL,
									`discount` INT(11) NOT NULL,
									`reimbursement` INT(11) NOT NULL,
									`active` INT(1) UNSIGNED NOT NULL,
									PRIMARY KEY (`id_promocion`)
							)',
							'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'interes'.'`(
									`id_interes` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
									`installment` INT(11) NULL,
									`payment_method` INT(11) NOT NULL,
									`coeficient` DECIMAL (5,4),
									`active` TINYINT(1) DEFAULT 0,
									PRIMARY KEY (`id_interes`)
							);'
					);

	//Data insert for CMS entidades, entidades, promociones
	$insert_data = array('INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(2,"1",1.0589,0)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(3,"1",1.0799,1)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(4,"1",1.1012,0)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(5,"1",1.1228,0)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(6,"1",1.1447,1)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(7,"1",1.1774,0)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(8,"1",1.2013,0)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(9,"1",1.2255,0)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(10,"1",1.2500,0)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(11,"1",1.2748,0)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(12,"1",1.2999,1)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(2,"15",1.0540,0)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(3,"15",1.0733,1)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(4,"15",1.0928,0)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(5,"15",1.1126,0)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(6,"15",1.1325,1)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(7,"15",1.1633,0)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(8,"15",1.1852,1)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(9,"15",1.2073,0)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(10,"15",1.2297,1)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(11,"15",1.2524,0)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(12,"15",1.2753,1)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(2,"6",1.0555,1)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(3,"6",1.0753,1)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(4,"6",1.0953,0)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(5,"6",1.1156,0)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(6,"6",1.1362,1)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(7,"6",1.1750,0)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(8,"6",1.1985,1)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(9,"6",1.2224,1)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(10,"6",1.2465,1)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(11,"6",1.2710,1)',
						'INSERT INTO `'._DB_PREFIX_.'interes` (installment, payment_method, coeficient, active) VALUES(12,"6",1.2957,1)',
						'INSERT INTO `'._DB_PREFIX_.'promociones` (name, payment_method, entity, send_installment, days, init_date, final_date, installment, coeficient, discount, reimbursement, active) VALUES ("promo Visa 1", 1, 2, "", 30, "2016-11-01", "2018-12-31", 72, 1, 10, 15, 1)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("Visa","Tarjeta",1,1)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("MasterCard","Tarjeta",15,1)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("American Express","Tarjeta",65,1)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("Tarjeta Shopping","Tarjeta",23,1)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("Tarjeta Naranja","Tarjeta",24,1)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("Cabal","Tarjeta",27,0)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("Italcred","Tarjeta",29,0)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("ArgenCard","Tarjeta",30,0)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("Visa Débito","Tarjeta",31,0)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("CoopePlus","Tarjeta",34,0)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("Nexo","Tarjeta",37,0)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("'.utf8_encode("Credim\E1s").'","Tarjeta",38,0)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("Tarjeta Nevada","Tarjeta",39,1)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("Nativa","Tarjeta",42,0)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("'.utf8_encode("Tarjeta Cencosud").'","Tarjeta",43,0)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("Tarjeta Carrefour / Cetelem","Tarjeta",44,0)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("Tarjeta PymeNacion","Tarjeta",45,0)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("BBPS","Tarjeta",50,0)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("Qida","Tarjeta",52,0)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("Grupar","Tarjeta",54,0)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("Patagonia 365","Tarjeta",55,0)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("'.utf8_encode("Tarjeta Club Dia").'","Tarjeta",56,1)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("Tuya","Tarjeta",59,0)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("Distribution","Tarjeta",60,0)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("'.utf8_encode("Tarjeta La An\F3nima").'","Tarjeta",61,1)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("CrediGuia","Tarjeta",62,0)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("Tarjeta SOL","Tarjeta",64,0)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("MasterCard Debit","Tarjeta",66,0)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("Cabal Débito","Tarjeta",67,0)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("Diners Club","Tarjeta",8,0)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("Maestro","Tarjeta",99,0)',
						'INSERT INTO `'._DB_PREFIX_.'medios` (name, type, id_decidir, active) VALUES("Favacard","Tarjeta",103,0)',
						'INSERT INTO `'._DB_PREFIX_.'entidades` (name, active) VALUES("BANCO DE GALICIA Y BUENOS AIRES S.A.",1)',
						'INSERT INTO `'._DB_PREFIX_.'entidades` (name, active) VALUES("'.utf8_encode("BANCO DE LA NACI\D3N ARGENTINA").'",1)',
						'INSERT INTO `'._DB_PREFIX_.'entidades` (name, active) VALUES("BANCO DE LA PROVINCIA DE BUENOS AIRES",1)',
						'INSERT INTO `'._DB_PREFIX_.'entidades` (name, active) VALUES("STANDARD BANK ARGENTINA S.A. - ICBC",1)',
						'INSERT INTO `'._DB_PREFIX_.'entidades` (name, active) VALUES("CITIBANK N.A.",1)',
						'INSERT INTO `'._DB_PREFIX_.'entidades` (name, active) VALUES("'.utf8_encode("BBVA BANCO FRANC?S S.A.").'",1)',
						'INSERT INTO `'._DB_PREFIX_.'entidades` (name, active) VALUES("BANCO SANTANDER RIO S.A.",1)',
						'INSERT INTO `'._DB_PREFIX_.'entidades` (name, active) VALUES("HSBC BANK ARGENTINA S.A.",1)',
						'INSERT INTO `'._DB_PREFIX_.'entidades` (name, active) VALUES("BANCO CIUDAD",1)',
						'INSERT INTO `'._DB_PREFIX_.'entidades` (name, active) VALUES("BANCO SUPERVIELLE S.A.",1)',
						'INSERT INTO `'._DB_PREFIX_.'entidades` (name, active) VALUES("BANCO MACRO S.A.",1)',
						'INSERT INTO `'._DB_PREFIX_.'entidades` (name, active) VALUES("BANCO PATAGONIA S.A.",1)',
						'INSERT INTO `'._DB_PREFIX_.'entidades` (name, active) VALUES("BANCO HIPOTECARIO S.A.",1)',
						'INSERT INTO `'._DB_PREFIX_.'entidades` (name, active) VALUES("BANCO COMAFI S.A.",1)',
						'INSERT INTO `'._DB_PREFIX_.'entidades` (name, active) VALUES("BANCO CREDICOOP",1)',
						'INSERT INTO `'._DB_PREFIX_.'entidades` (name, active) VALUES("BANCO INDUSTRIAL",1)'
					);
	
	//delete tables
	foreach ($delete_tables as $query)
		if (Db::getInstance()->execute($query) == false)
			return false;

	//create tables	
	foreach ($create_tables as $query)
		if (Db::getInstance()->execute($query) == false)
			return false;
		
		
	//insert data	
	foreach ($insert_data as $queryInsert)
		if (Db::getInstance()->execute($queryInsert) == false)
			return false;	

	/*$query = 'SELECT codigo_producto FROM `'._DB_PREFIX_.'decidir_productos`';

	if (Db::getInstance()->execute($query) == false)
	{
		$sqlalter = 'ALTER TABLE `'._DB_PREFIX_.'decidir_productos` ADD `codigo_producto` VARCHAR(50) NULL DEFAULT NULL';
		Db::getInstance()->execute($sqlalter);
	}*/

?>
