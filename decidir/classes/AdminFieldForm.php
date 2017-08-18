<?php
namespace Decidir;

require_once(dirname(__FILE__).'../../../../config/config.inc.php');

class AdminFieldForm {
	/**
	 * Genera los form fields necesarios para crear un formulario
	 */
	public static function getFormFields($titulo, $inputs)
	{	

		$elements = array(
					'form' => array(
							'legend' => array(
									'title' => $titulo,//titulo del form
									'icon' => 'icon-cogs',//icono
							),
							'input' =>$inputs,
							'submit' => array(
									'title' => 'Guardar',
									'class' => 'button'
							)
					)			
		);

		return $elements;
	}
	
	/**
	 * @return un array con los campos del formulario
	 */
	public static function getConfigFormInputs()
	{	


		return array(
				array(
						'type' => 'switch',
						'label' =>'Activar',
						'name' =>  'status',
						'desc' => 'Activa y desactiva el módulo de pago',
						'is_bool' => true,
						'values' => array(
								array(
										'id' => 'active_on',
										'value' => true,
										'label' =>'SI'
								),
								array(
										'id' => 'active_off',
										'value' => false,
										'label' =>'NO'
								)
						),
						'required' => false
				),
				array(
						'type' => 'text',
						'label' =>'Nombre a mostrar en front end',
						'name' =>  'frontend_name',
						'desc' => 'Nombre con el que aparecera el Medio de Pago',
						'required' => false
				),
				array(
						'type' => 'switch',
						'label' =>'Modo Producción',
						'name' => 'mode',
						'desc' => 'AMBIENTE PRODUCCION = SI, AMBIENTE TEST = NO',
						'is_bool' => true,
						'values' => array(
								array(
									'id' => 'active_on',
									'value' => true,
									'label' =>'Produccion'
								),
								array(
									'id' => 'active_off',
									'value' => false,
									'label' =>'Developers'
								)
						)
				)
		);
	}

	/**
	 * @return un array con los campos del formulario
	 */
	public static function getAmbienteFormInputs($tabla)
	{	

		return  array(
					array(
						'type' => 'text',
						'label' =>'API Key publica',
						'name' => 'id_key_public',
						'desc' => 'Llave de Acceso pública (public API Key)',
						'required' => false
					),
					array(
						'type' => 'text',
						'label' =>'API Key private',
						'name' => 'id_key_private',
						'desc' => 'La Llave de Acceso privada (private API Key)',
						'required' => false
					)
		);
	}
	
	/**
	 * @return un array con los campos del formulario
	 */
	public static function getCybersourceFields($cs_vertical_list = NULL, $canal= NULL)
	{	

		return  array(
					array(
	                    'type' => 'html',
	                    'name' => 'html_data',
	                    'html_content' => '<b>IMPORTANTE!!</b>: Si desea utilizar Cybersource tiene que estar contratado el servicio y debe seleccionar el vertical correcto, de otra forma no funcionar&aacute; en el Plugin.'
				    ),	
					array(
						'type' => 'switch',
						'label' =>'Activo',
						'name' =>  'enable_cs',
						'desc' => 'Envia datos adicionales para prevención de fraude',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => true,
								'label' =>'SI'
							),
							array(
								'id' => 'active_off',
								'value' => false,
								'label' =>'NO'
							)
						),
						'required' => true
					),
					array(
						'type' => 'select',
						'label' => 'Segmento del Comercio',
						'name' => 'vertical',
						'required' => true,
						'desc' => 'La elección del segmento determina los tipos de datos a enviar',
						'options' => array(
				            'query' => $cs_vertical_list,
				            'id' => 'key',
				            'name' => 'name'
				        )
					)/*,
					array(
						'type' => 'text',
						'label' =>'Cybersource Id Site',
						'name' => 'CS_idsite',
						'desc' => 'Merchant ID proporcionado por Decidir para CyberSource de produccion',
						'required' => false
					)*/
				);
	}


	/**
	 * @return un array con los campos del formulario
	 */
	public static function getRapipagoFormInputs()
	{	

		return  array(
					array(
	                    'type' => 'html',
	                    'name' => 'html_data',
	                    'html_content' => '<p>El servicio no se encuentra disponible.</p>'
				    ),
				    array(
				    	'type' => 'switch',
						'label' =>'Activo',
						'name' =>  'enable_cs',
						'desc' => '',
						'is_bool' => true,
						'values' => array(
								array(
										'id' => 'active_on',
										'value' => true,
										'label' =>'SI'
								),
								array(
										'id' => 'active_off',
										'value' => false,
										'label' =>'NO'
								)
						),
						'required' => true
					),
					array(
						'type' => 'text',
						'label' =>'Código de Cliente',
						'name' =>  'rp_cod_client',
						'desc' => 'C&oacute;digo de cliente provisto por Rapipago al momento de habilitar al comercio.',
						'required' => false
					),
					array(
						'type' => 'text',
						'label' =>'Días entre 1er y 2do vencimiento',
						'name' =>  'rp_ds_due_date',
						'desc' => 'Son los d&iacute;as que existe entre el 1er y 2do vencimiento de la factura ("00" si la factura no tiene segundo vencimiento)',
						'required' => false
					),
					array(
						'type' => 'text',
						'label' =>'Días vencimiento',
						'name' =>  'rp_daysto_due_date',
						'desc' => 'Son los d&iacute;as despues del 1er vencimiento y hasta que el cliente puede pagar su factura',
						'required' => false
					),
					array(
						'type' => 'text',
						'label' =>'Recargo',
						'name' =>  'rp_surcharge',
						'desc' => 'Recargo por vencimiento del plazo, expresado en valor porcentual. ejemplo: "12.3"',
						'required' => false
					),
					array(
						'type' => 'text',
						'label' =>'Vencimiento',
						'name' =>  'rp_due_date',
						'desc' => 'D&iacute;as para vencimiento para el pago del cup&oacute;n',
						'required' => false
					)						
		);
	}
	
	/**
	 * @return un array con los campos del formulario
	 */
	public static function getPagofacilFormInputs($tabla = NULL)
	{	

		return  array(
					array(
	                    'type' => 'html',
	                    'name' => 'html_data',
	                    'html_content' => '<p>El servicio no se encuentra disponible.</p>'
				    ),
				    array(
				    	'type' => 'switch',
						'label' =>'Activo',
						'name' =>  'enable_cs',
						'desc' => '',
						'is_bool' => true,
						'values' => array(
								array(
										'id' => 'active_on',
										'value' => true,
										'label' =>'SI'
								),
								array(
										'id' => 'active_off',
										'value' => false,
										'label' =>'NO'
								)
						),
						'required' => true
					),
					array(
						'type' => 'text',
						'label' =>'Código de Cliente',
						'name' =>  'pf_cod_client',
						'desc' => 'Código de cliente provisto por Rapipago al momento de habilitar al comercio.',
						'required' => false
					),
					array(
						'type' => 'text',
						'label' =>'Recargo',
						'name' =>  'pf_surcharge',
						'desc' => 'Recargo por vencimiento del plazo, expresado en valor porcentual. ejemplo: "12.3"',
						'required' => false
					),
					array(
						'type' => 'text',
						'label' =>'1er vencimiento',
						'name' =>  'pf_first_due_date',
						'desc' => 'Días transcurridos desde la compra hasta el 1er vencimiento',
						'required' => false
					),
					array(
						'type' => 'text',
						'label' =>'2er vencimiento',
						'name' =>  'pf_second_due_date',
						'desc' => 'Días transcurridos desde la compra hasta el 2do vencimiento',
						'required' => false
					)					
		);
	}

			/**
	 * @return un array con los campos del formulario
	 */
	public static function getPagomiscuentasFormInputs($tabla = NULL)
	{	

		return  array(
					array(
	                    'type' => 'html',
	                    'name' => 'html_data',
	                    'html_content' => '<p>El servicio no se encuentra disponible.</p>'
				    ),
				    array(
				    	'type' => 'switch',
						'label' =>'Activo',
						'name' =>  'enable_cs',
						'desc' => '',
						'is_bool' => true,
						'values' => array(
								array(
										'id' => 'active_on',
										'value' => true,
										'label' =>'SI'
								),
								array(
										'id' => 'active_off',
										'value' => false,
										'label' =>'NO'
								)
						),
						'required' => true
					),
					array(
						'type' => 'text',
						'label' =>'Días para vencimiento',
						'name' =>  'pmc_days_due_date',
						'desc' => 'Días para el vencimiento a partir de la compra (se le sumaran los minútos si son difententes de 0 o null)',
						'required' => false
					),
					array(
						'type' => 'text',
						'label' =>'Minutos para vencimiento',
						'name' =>  'pmc_min_due_date',
						'desc' => 'Minútos para el vencimiento a partir de la compra (se le sumaran los días si son difententes de 0 o null)',
						'required' => false
					)				
		);
	}

	/**
	 * @return un array con los campos del formulario
	 */
	public static function getEstadosFormInputs($estadosOption = NULL)
	{	
		if(is_array($estadosOption)){
			$approvalsStatus = array_filter($estadosOption, function ($item) {
				return $item['valid_order'] == 1;
			});
		}

		return array(
					array(
						'type' => 'select',
						'label' =>'En proceso',
						'name' =>  'proceso',
						'desc' => 'Para pagos con tarjeta de credito mientras se espera la respuesta del gateway.',
						'required' => false,
						'options' => array(
								'query' => $estadosOption,
								'id' => 'id_option',
								'name' => 'name'
						)
					),
					array(
						'type' => 'select',
						'label' =>'Aprobada',
						'name' =>  'aprobada',
						'desc' => 'Estado final de lo aprobado por el medio de pago',
						'required' => false,
						'options' => array(
								'query' => $approvalsStatus,
								'id' => 'id_option',
								'name' => 'name'
						)
					),
					array(
						'type' => 'select',
						'label' =>'Denegada',
						'name' =>  'denegada',
						'desc' => 'Cuando por cualquier motivo la transcaccion fue denegada.',
						'required' => false,
						'options' => array(
								'query' => $estadosOption,
								'id' => 'id_option',
								'name' => 'name'
						)
					),
					array(
						'type' => 'select',
						'label' =>'Cupon pendiente de pago',
						'name' =>  'pendiente',
						'required' => false,
						'options' => array(
								'query' => $estadosOption,
								'id' => 'id_option',
								'name' => 'name'
						)
					)
		);
	}
	
	public static function getProductoFormInputs($segmento, $servicioOption, $deliveryOption, $envioOption, $productOption)
	{
		if($segmento == 'retail')
		{
			return array(
					 	array(
							'type' => 'select',
							'label' =>'Código de producto',
							'name' =>  'codigo_producto',
							//'desc' => 'Utilizar esta opcion en el caso que el producto sea un servicio',
							'required' => false,
							'options' => array(
									'query' => $productOption,
									'id' => 'id_option',
									'name' => 'name'
							)
						)				
			);
		}
		elseif ($segmento == 'services')
		{
			return array(
				 array(
						'type' => 'select',
						'label' =>'Código de producto',
						'name' =>  'codigo_producto',
						//'desc' => 'Utilizar esta opcion en el caso que el producto sea un servicio',
						'required' => false,
						'options' => array(
								'query' => $productOption,
								'id' => 'id_option',
								'name' => 'name'
						)
					),			
				 array(
						'type' => 'select',
						'label' =>'Tipo de servicio',
						'name' =>  'tipo_servicio',
						//'desc' => 'Utilizar esta opcion en el caso que el producto sea un servicio',
						'required' => false,
						'options' => array(
								'query' => $servicioOption,
								'id' => 'id_option',
								'name' => 'name'
						)
					),
					array(
						'type' => 'text',
						'label' =>'Referencia de pago',
						'name' =>  'referencia_pago',
						//'desc' => 'Utilizar esta opcion en el caso que el producto sea un servicio',
						'required' => false
					)
			);
		}
		
		elseif ($segmento == 'digital goods')
		{
			return array(
				 array(
						'type' => 'select',
						'label' =>'Código de producto',
						'name' =>  'codigo_producto',
						//'desc' => 'Utilizar esta opcion en el caso que el producto sea un servicio',
						'required' => false,
						'options' => array(
								'query' => $productOption,
								'id' => 'id_option',
								'name' => 'name'
						)
					),			
				array(
						'type' => 'select',
						'label' =>'Tipo de delivery',
						'name' =>  'tipo_delivery',
						//'desc' => 'Utilizar esta opcion en el caso que el producto sea un bien digital',
						'required' => false,
						'options' => array(
								'query' => $deliveryOption,
								'id' => 'id_option',
								'name' => 'name'
						)
				)
			);
		}
		
		elseif ($segmento == 'ticketing')
		{
			return array(
				 array(
						'type' => 'select',
						'label' =>'Código de producto',
						'name' =>  'codigo_producto',
						//'desc' => 'Utilizar esta opcion en el caso que el producto sea un servicio',
						'required' => false,
						'options' => array(
								'query' => $productOption,
								'id' => 'id_option',
								'name' => 'name'
						)
					),			
				array(
						'type' => 'select',
						'label' =>'Tipo de envio',
						'name' =>  'tipo_envio',
						//'desc' => 'Utilizar esta opcion en el caso que el producto sea una entrada',
						'required' => false,
						'options' => array(
								'query' => $envioOption,
								'id' => 'id_option',
								'name' => 'name'
						)
				),
				array(
						'type' => 'date',
						'label' =>'Fecha del evento',
						'name' =>  'fecha_evento',
						//'desc' => 'Utilizar esta opcion en el caso que el producto sea una entrada',
						'required' => false
				)
			);
		}
	}
	
	/**
	 * Devuelve los nombres de los inputs que existen en el form
	 * @param array $inputs campos de un formulario
	 * @return un array con los nombres
	 */
	public static function getFormInputsNames($inputs)
	{
		$nombres=array();

		foreach ($inputs as $campo)
		{
			if (array_key_exists('name', $campo))
			{
				$nombres[] = $campo['name'];
			}
		}

		return $nombres;
	}
	
	/**
	 * Escribe en la base de datos los valores de tablas de configuraciones
	 * @param string $prefijo prefijo con el que se identifica al formulario en la tabla de configuraciones. Ejemplo: DECIDIR_TEST
	 * @param array $inputsName resultado de la funcion getFormInputsNames
	 */
	public static function postProcessFormularioConfigs($prefijo, $inputsName)
	{	
		foreach ($inputsName as $nombre)
		{	
			//mejorarlo este codigo
			if($nombre == "authorization"){

				$auth = \Tools::getValue($nombre);
				if(json_decode($auth) == NULL) {
					//armo json de autorization        
					$autorizationId = new \stdClass();
					$autorizationId->Authorization = $auth;
					$auth = json_encode($autorizationId);
				}

				$valueField = $auth;

			}else{
				$valueField = \Tools::getValue($nombre);
			}
			
			\Configuration::updateValue( $prefijo.'_'.strtoupper( $nombre ), $valueField);

		}
	}

	/**
	 * Escribe en la base de datos los valores de tablas de configuraciones
	 * @param string $prefijo prefijo con el que se identifica al formulario en la tabla de configuraciones. Ejemplo: DECIDIR_TEST
	 * @param array $inputsName resultado de la funcion getFormInputsNames
	 */
	public static function postProcessFormularioConfigsCMS($inputsName)
	{	
		foreach ($inputsName as $nombre)
		{	
			//mejorarlo este codigo
			if($nombre == "authorization"){

				$auth = \Tools::getValue($nombre);
				if(json_decode($auth) == NULL) {
					//armo json de autorization        
					$autorizationId = new \stdClass();
					$autorizationId->Authorization = $auth;
					$auth = json_encode($autorizationId);
				}

				$valueField = $auth;

			}else{
				$valueField = \Tools::getValue($nombre);
			}
			
			\Configuration::updateValue( $prefijo.'_'.strtoupper( $nombre ), $valueField);

		}
	}

	/**
	 * Trae de los valores de configuracion del modulo, listos para ser usados como fields_value en un form
	 * @param string $prefijo prefijo con el que se identifica al formulario en la tabla de configuraciones. Ejemplo: DECIDIR_TEST
	 * @param array $inputsName resultado de la funcion getFormInputsNames
	 */
	public static function getConfigs($prefijo, $inputsName)
	{
		$configs = array();

		foreach ($inputsName as $nombre)
		{
			$configs[$nombre] = \Configuration::get( $prefijo.'_'.strtoupper( $nombre ));
		}

		return $configs;
	}

	public function getDataCMSEntities($IdElement, $inputNames, $currentData){
		$values = array();

		if(!empty($IdElement)){

        	$values = array(
						'name' => $currentData[0]['name'], 
						'active' => $currentData[0]['active'], 
						'id_entity' => $IdElement
					);

        	return $values;
		}
		return $values; 
	}

	public function getDataPaymentMethod($IdElement, $inputNames, $currentData){
		$values = array();

		if(!empty($IdElement)){

        	$values = array(
						'name' => $currentData[0]['name'], 
						'id_tipo' => $currentData[0]['tipo'],
						'id_decidir' => $currentData[0]['id_decidir'],
						'id_medio' => $currentData[0]['id_medio'],
						'active' => $currentData[0]['active']
					);

        	return $values;
		}
		return $values; 
	}

	public function getDataPromo($IdElement, $inputNames, $currentData){
		$values = array();

		if(!empty($IdElement)){

        	$values = array(
						'plan_name' => $currentData[0]['name'], 
						'payment_method' => $currentData[0]['payment_method'],
						'entity' => $currentData[0]['entity'],
						'send_installment' => $currentData[0]['send_installment'],
						'id_days' => $currentData[0]['days'],
						'date_from' =>  $currentData[0]['init_date'],
						'date_to' => $currentData[0]['final_date'],
						'installment' => $currentData[0]['installment'],
						'coeficient' => $currentData[0]['coeficient'],
						'discount' => $currentData[0]['discount'],
						'reinbursement' => $currentData[0]['reimbursement'],
						'active' => $currentData[0]['id_promocion']
					);

        	return $values;
		}
		return $values; 
	}

	public function getDataInteres($IdElement, $inputNames, $currentData){
		$values = array();

		if(!empty($IdElement)){

        	$values = array(
						'id_installment' => $currentData[0]['installment'], 
						'id_tarjeta' => $currentData[0]['payment_method'],
						'coeficiente' => $currentData[0]['coeficient'],
						'active' => $currentData[0]['active']
					);

        	return $values;
		}
		return $values; 
	}


	public static function getConfigEntidades($idBank)
	{	

		$entity = array(
			array(
			'id' => $idBank,      
			'name' => $idBank  
			)
		);

		return  array(
					array(
						'type' => 'text',
						'label' =>'Nombre entidad Financiera',
						'name' =>  'name',
						'desc' => 'Ingresar Entidad',
						'required' => true,
						'value' => 'pepito'
					),
					array(
						'type' => 'switch',
						'label' => "Activar",
						'name' => 'active',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => "Si"
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => "No"
							)
						)
					),
					array(
						'type' => 'select',
						'name' =>  'id_entity',
						'required' => true,
						'options' => array(
							'query' => $entity,
							'id' => 'id',
							'name' => 'name'
						)	
					),
		);
	}

	public static function getConfigMediosPago($idMediosPago, $listPaymentMethodData = null)
	{		
		$pmethod = array(
			array(
			'id' => $idMediosPago,      
			'name' => $idMediosPago  
			)
		);

		$options = array(
		  array(
		    'id_tipo' => 1,      
		    'name' => 'Tarjeta'  
		  ),
		  array(
		    'id_tipo' => 2,
		    'name' => 'Cupon'
		  ),
		);

		return  array(
					array(
						'type' => 'text',
						'label' =>'Medio de Pago',
						'name' =>  'name',
						'desc' => 'Ingresar Medio de pago',
						'required' => false
					),
					array(
					  'type' => 'select',                             
					  'label' => 'Tipo de pago:',       
					  'name' => 'id_tipo',              
					  'options' => array(
					    'query' => $options,          
					    'id' => 'id_tipo',                   
					    'name' => 'name'           
					  )
					),
					array(
						'type' => 'text',
						'label' =>'ID Medio de pago Decidir',
						'name' =>  'id_decidir',
						'desc' => 'Ingresar Medio de pago de Decidir',
						'required' => true
					),
					array(
						'type' => 'select',
						'name' =>  'id_medio',
						'required' => true,
						'options' => array(
							'query' => $pmethod,
							'id' => 'id',
							'name' => 'name'
						)	
					),
					array(
						'type' => 'switch',
						'label' => "Activar",
						'name' => 'active',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => "Si"
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => "No"
							)
						)
					)
				);
	}

	public static function getConfigPromocion($idPromocion = NULL, $paymentMethodList = NULL, $entityList = NULL)
	{	
		$firstEntity = array('id_entity' => 0,'name' => 'Seleccionar');
		array_unshift($entityList, $firstEntity);

		$days = array(
				  array(
				    'id_day' => 1,      
				    'name' => 'Domingo'  
				  ),
				  array(
				    'id_day' => 2,
				    'name' => 'Lunes'
				  ),
				  array(
				    'id_day' => 4,
				    'name' => 'Martes'
				  ),
				  array(
				    'id_day' => 8,
				    'name' => 'Miercoles'
				  ),
				  array(
				    'id_day' => 16,
				    'name' => 'Jueves'
				  ),
				  array(
				    'id_day' => 32,
				    'name' => 'Viernes'
				  ),
				  array(
				    'id_day' => 64,
				    'name' => 'Sabado'
				  )
			);

		//list of installment fior input select
		$installment = array();
        $val = 1;
        $places = 1;

        for($index=0; $index<=24; $index++){
            $installmentElem[$index] = $val;                   

            $installmentElem = array(
							   "id"=>$val,	
							   "name"=>$index
							  );

            $res = $val << $places;
            $val = $res;

            array_push($installment, $installmentElem);
        }	

		return  array(
					array(
						'type' => 'text',
						'label' =>'Nombre',
						'name' =>  'plan_name',
						'desc' => '',
						'required' => false
					),
					array(
						'type' => 'select',                             
						'label' => 'Medio de pago:',       
						'name' => 'payment_method',
						'required' => true,              
						'options' => array(
							'query' => $paymentMethodList,          
							'id' => 'id_medio',                   
							'name' => 'name'           
						)
					),
					array(
						'type' => 'select',                             
						'label' => 'Entidad Financiera:',       
						'name' => 'entity',
						'required' => true,              
						'options' => array(
							'query' => $entityList,          
							'id' => 'id',                   
							'name' => 'name'           
						)
					),
					array(
						'type' => 'select',
						'label' =>'Días habilitados',
						'name' =>  'id_days',
						'multiple' => true ,
						'options' => array(
							'query' => $days,
							'id' => 'id_day',
							'name' => 'name'
						)	
					),
					array(
	                    'type' => 'date',
	                    'label' => 'Desde',
	                    'name' => 'date_from',
	                    'maxlength' => 10,
	                    'required' => false,
	                    'hint' => 'Formato: 2016-12-31.',
	                    'desc' => 'Fecha a partir de la cuál entrará en vigencia el plan'
	                ),
	                array(
	                    'type' => 'date',
	                    'label' => 'Hasta',
	                    'name' => 'date_to',
	                    'maxlength' => 10,
	                    'required' => false,
	                    'hint' => 'Formato: 2016-12-31.',
	                    'desc' => 'Fecha a partir de la cuál entrará en vigencia el plan'
	                ),
					array(
						'type' => 'select',
						'label' =>'Cuotas',
						'name' =>  'id_installment',
						'multiple' => true ,
						'options' => array(
							'query' => $installment,
							'id' => 'id',
							'name' => 'name'
						)	
					),
					array(
						'type' => 'text',
						'label' =>'Cuotas a enviar',
						'name' =>  'send_installment',
						'desc' => 'Para el caso de que el nro de cuotas a enviar sea contrario al real, en caso contrario dejar vacío',
						'required' => false
					),
					array(
						'type' => 'text',
						'label' =>'Tasa directa',
						'name' =>  'coeficient',
						'desc' => 'Valor informado por el medio de pago que debe aplicarse a cada plan de cuotas (ejemplo: "23.5")',
						'required' => true,
					),
					array(
						'type' => 'text',
						'label' =>'Descuento',
						'name' =>  'discount',
						'desc' => 'Porcentaje de descuento a aplicar en las compras con este plan (ejemplo: "10")',
						'required' => true
					),
					array(
						'type' => 'text',
						'label' =>'Reintegro',
						'name' =>  'reinbursement',
						'desc' => 'Porcentaje que se mostrará reintegrago en el resumen de la tarjeta, ejemplo: "10" (Sólo a modo informativo, no se incluirá en la facturación de Magento ni se enviará a Decidir)',
						'required' => true
					),
					array(
						'type' => 'switch',
						'label' => "Activar",
						'name' => 'active',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => "Si"
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => "No"
							)
						)
					),
					array(
						'type' => 'select',
						'name' =>  'id_promocion',
						'options' => array(
							'query' => $idPromocion,
							'id' => 'id',
							'name' => 'name'
						)	
					)
				);
	}

	public static function getConfigInteres($idInteres, $paymentMethod = null, $listInteresData = null)
	{	
		//fill installment list
		$installment = array();

		foreach (range(1, 24) as $value) {
			$installmentElem = array(
								   "id_installment"=>$value, 
								   "name"=>$value
								  );

			array_push($installment, $installmentElem);
		}

		$idInterests = array(
			  array(
			    'id_interes' => $idInteres,      
			    'name' => $idInteres  
			  )
		);

		if(empty($listInteresData)){
			$coeficient = '';
		}else{
			$coeficient = $listInteresData[0]['coeficient'];
		}
		
		return  array(
					array(
					  'type' => 'select',                             
					  'label' => 'Cuota:',       
					  'name' => 'id_installment',         
					  'options' => array(
					    'query' => $installment,          
					    'id' => 'id_installment',                   
					    'name' => 'name'           
					  )
					),
					array(
					  'type' => 'select',                             
					  'label' => 'Medio de Pago:',       
					  'name' => 'id_tarjeta',              
					  'options' => array(
					    'query' => $paymentMethod,          
					    'id' => 'id',                   
					    'name' => 'name'           
					  )
					),
					array(
	                    'type' => 'html',
	                    'label' => "Ingresar el coeficiente",
	                    'name' => 'coeficiente',
	                    'html_content' => '<input type="text" name="coeficiente" value="'.$coeficient.'">'
				    ),
					array(
						'type' => 'select',
						'name' =>  'id_interes',
						'required' => true,
						'options' => array(
							'query' => $idInterests,
							'id' => 'id_interes',
							'name' => 'name'
						)	
					),
					array(
						'type' => 'switch',
						'label' => "Activar",
						'name' => 'active',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => "Si"
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => "No"
							)
						)
					)
				);	
	}
}
