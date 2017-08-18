<?php

namespace Decidir\Exception;

class RequiredValueException extends \Decidir\Exception\DecidirException {
	
	public function __construct($field) {
		$message = "Campo: " . $field . " es requerido.";
		$code = 99977;
		parent::__construct($message, $code);
	}
}