Decidir SDK PHP
===============

Modulo para conexión con gateway de pago DECIDIR2
  + [Introducción](#introduccion)
    + [Alcance](#alcance)
    + [Diagrama de secuencia](#diagrama-secuencia)			
  + [Instalación](#instalación)
    + [Versiones de PHP soportadas](#versiones)	
    + [Manual de Integración](#manualintegracion)
    + [Ambiente](#ambiente)
  + [Uso](#uso)
    + [Inicializar la clase correspondiente al conector](#initconector)
    + [Operatoria del Gateway](#operatoria)
      + [Health Check](#healthcheck)
      + [Ejecución del Pago](#payment)
      + [Listado de Pagos](#getallpayments)
      + [Información de un Pago](#getpaymentinfo)
      + [Anulación / Devolución Total de Pago](#refund)
      + [Anulación de Devolución Total](#deleterefund)
      + [Devolución Parcial de un Pago](#partialrefund)
      + [Anulación de Devolución Parcial](#deletepartialrefund)
    + [Tokenizacion de tarjetas de crédito](#tokenizaciontarjeta)
      + [Listado de tarjetas tokenizadas](#listadotarjetastokenizadas)	
      + [Ejecucion de pago tokenizado](#pagotokenizado)
      + [Eliminacion de tarjeta tokenizada](#eliminartarjetatokenizada)
    + [Integración con Cybersource](#cybersource)
      + [Retail](#retail)
      + [Ticketing](#ticketing)
      + [Digital Goods](#digital-goods)  	
  + [Tablas de referencia](#tablasreferencia)
    + [Códigos de Medios de Pago](#códigos-de-medios-de-pago)
	  + [Divisas Aceptadas](#divisasa)
    + [Provincias](#provincias)

## Introducción
El flujo de una transacción a través de las **sdks** consta de dos pasos, la **generaci&oacute;n de un token de pago** por parte del cliente y el **procesamiento de pago** por parte del comercio. Existen sdks espec&iacute;ficas para realizar estas funciones en distintos lenguajes que se detallan a continuaci&oacute;n:

+ **Generaci&oacute;n de un token de pago.**  Se utiliza alguna de las siguentes **sdks front-end** :
  + [sdk IOS](https://github.com/decidir/SDK-IOS.v2)
  + [sdk Android](https://github.com/decidir/SDK-Android.v2)
  + [sdk Javascript](https://github.com/decidir/sdk-javascript-v2)
+ **Procesamiento de pago.**  Se utiliza alguna de las siguentes **sdks back-end** :
  + [sdk Java](https://github.com/decidir/SDK-JAVA.v2)
  + [sdk PHP](https://github.com/decidir/SDK-PHP.v2)
  + [sdk .Net](https://github.com/decidir/SDK-.NET.v2)
  + [sdk Node](https://github.com/decidir/SDK-.NODE.v2)


## Alcance
La **sdk PHP** provee soporte para su **aplicaci&oacute;n back-end**, encargandose de la comunicaci&oacute;n del comercio con la **API Decidir** utilizando su **API Key privada**<sup>1</sup> y el **token de pago** generado por el cliente.

Para generar el token de pago, la aplicaci&oacute;n cliente realizar&aacute; con **Decidir** a trav&eacute;s de alguna de las siguentes **sdks front-end**:
+ [sdk IOS](https://github.com/decidir/SDK-IOS.v2)
+ [sdk Android](https://github.com/decidir/SDK-Android.v2)
+ [sdk Javascript](https://github.com/decidir/sdk-javascript-v2)

![imagen de sdks](./docs/img/DiagramaSDKs.png)</br>

[Volver al inicio](#alcance)

<a name="diagrama-secuencia"></a>
## Diagrama de secuencia

El flujo de una transacción a través de las sdks consta de dos pasos, a saber:

sdk front-end: Se realiza una solicitud de token de pago con la Llave de Acceso pública (public API Key), enviando los datos sensibles de la tarjeta (PAN, mes y año de expiración, código de seguridad, titular, y tipo y número de documento) y obteniéndose como resultado un token que permitirá realizar la transacción posterior.

sdk back-end: Se ejecuta el pago con la Llave de Acceso privada (private API Key), enviando el token generado en el Paso 1 más el identificador de la transacción a nivel comercio, el monto total, la moneda y la cantidad de cuotas.

A continuación, se presenta un diagrama con el Flujo de un Pago.

![imagen de configuracion](./docs/img/FlujoPago.png)</br>

[Volver al inicio](#diagramasecuencia)


## Instalación
El SDK se encuentra disponible para descargar desde [Github](https://github.com/decidir/sdk-php-v2) o desde composer con el siguiente comando:

```php
  
composer require decidir2/php-sdk 

```

Una vez instalo el SDK dentro del proyecto, es necesario tener descomentada la extension=php_curl.dll en el php.ini, ya que para la conexión al gateway se utiliza la clase curl del API de PHP.
<br />		

[Volver al inicio](#decidir-sdk-php)


## Versiones de PHP soportadas

La versión implementada de la SDK, está testeada para las versiones PHP desde 5.3.

[Volver al inicio](#versiones)


<a name="manualintegracion"></a>
## Manual de Integración

Se encuentra disponible la documentación **[Manual de Integración Decidir2](https://decidir.api-docs.io/1.0/guia-de-inicio/)** para su consulta online, en este se detalla el proceso de integración. En el mismo se explican los servicios y operaciones disponibles, con ejemplos de requerimientos y respuestas, aquí sólo se ejemplificará la forma de llamar a los distintos servicios utilizando la presente SDK.

<a name="ambiente"></a>
## Ambientes

El sdk PHP permite trabajar con los ambientes de Sandbox y Producción de Decidir. El ambiente se debe definir al instanciar el SDK.

```php
	
$ambient = "test";//valores posibles: "test" o "prod"
$connector = new \Decidir\Connector($keys_data, $ambient);		

```

[Volver al inicio](#ambiente)

<a name="uso"></a>
## Uso

<a name="initconector"></a>
### Inicializar la clase correspondiente al conector.

El SDK-PHP permite trabajar con los ambientes de desarrollo y de producción de Decidir.
El ambiente se debe instanciar como se indica a continuación.
Instanciación de la clase `Decidir\Connector`
La misma recibe como parámetros la public key o private key provisto por Decidir para el comercio y el ambiente en que se trabajara.
```php

$keys_data = array('public_key' => 'e9cdb99fff374b5f91da4480c8dca741',
           'private_key' => '92b71cf711ca41f78362a7134f87ff65');

$ambient = "test";//valores posibles: "test" o "prod"
$connector = new \Decidir\Connector($keys_data, $ambient);

```
*Nota:* La sdk incluye un ejemplo de prueba completo el cual se debe acceder desde el navegador, allí permitirá configurar las distintas opciones.

[<sub>Volver a inicio</sub>](#decidir-sdk-php)
<a name="operatoria"></a>

## Operatoria del Gateway
<a name="healthcheck"></a>
### Health Check
Este recurso permite conocer el estado actual de la API RESTful de DECIDIR.

```php
$connector = new \Decidir\Connector($keys_data, $ambient);
$response = $connector->healthcheck()->getStatus();
$response->getName();
$response->getVersion();
$response->getBuildTime();
```
[<sub>Volver a inicio</sub>](#decidir-sdk-php)


<a name="payment"></a>

### Ejecución del Pago
Una vez generado y almacenado el token de pago, se deberá ejecutar la solicitud de pago más el token previamente generado.
Además del token de pago y los parámetros propios de la transacción, el comercio deberá identificar la compra con el site_transaction_id.

*Aclaracion* : amount es un campo double el cual debería tener solo dos dígitos decimales.

#### Ejemplo
```php
$connector = new \Decidir\Connector($keys_data, $ambient);

$data = array(
      "site_transaction_id" => "12042017_20",
      "token" => "be211413-757b-487e-bb0c-283d21c0fb6f",
      "user_id" => "usuario",
      "payment_method_id" => 1,
      "bin" => "450799",
      "amount" => 5.00,
      "currency" => "ARS",
      "installments" => 1,
      "description" => "",
      "payment_type" => "single",
      "sub_payments" => array()
    );

try {
	$response = $connector->payment()->ExecutePayment($data);
	$response->getId();
	$response->getToken();
	$response->getUser_id();
	$response->getPayment_method_id();
	$response->getBin();
	$response->getAmount();
	$response->getCurrency();
	$response->getInstallments();
	$response->getPayment_type();
	$response->getDate_due();
	$response->getSub_payments();
	$response->getStatus();
	$response->getStatus_details();
	$response->getDate();
	$response->getEstablishment_name();
	$response->getFraud_detection();
	$response->getAggregate_data();
	$response->getSite_id();
} catch( \Exception $e ) {
	var_dump($e->getData());
}
```

[<sub>Volver a inicio</sub>](#decidir-sdk-php)


<a name="getallpayments"></a>

### Listado de Pagos

Mediante este recurso, se genera una solicitud de listado de pagos.
Este recurso admite la posibilidad de agregar los filtros adicionales:

- (opcional) offset: desplazamiento en los resultados devueltos. Valor por defecto = 0.
- (opcional) pageSize: cantidad máxima de resultados retornados. Valor por defecto = 50.
- (opcional) siteOperationId: ID único de la transacción a nivel comercio (equivalente al site_transaction_id).
- (opcional) merchantId: ID Site del comercio.

```php
$connector = new \Decidir\Connector($keys_data, $ambient);

$data = array("pageSize" => 5);
$response = $connector->payment()->PaymentList($data);
$response->getLimit();
$response->getOffset();
$response->getResults();
$response->getHas_more();
```

[<sub>Volver a inicio</sub>](#decidir-sdk-php)

<a name="getpaymentinfo"></a>

### Información de un Pago

Mediante este recurso, se genera una solicitud de información de un pago previamente realizado, pasando como parámetro el id del pago.

```php
$connector = new \Decidir\Connector($keys_data, $ambient);

$data = array();

$response = $connector->payment()->PaymentInfo($data, '574421');
$response->getId();
$response->getSiteTransaction_id();
$response->getToken();
$response->getUser_id();
$response->getPayment_method_id();
$response->getCard_brand();
$response->getBin();
$response->getAmount();
$response->getCurrency();
$response->getInstallments();
$response->getPayment_type();
$response->getSub_payments();
$response->getStatus();
$response->getStatus_details();
$response->getDate();
$response->getEstablishment_name();
$response->getFraud_detection();
$response->getAggregate_data();
$response->getSite_id();
```

[<sub>Volver a inicio</sub>](#decidir-sdk-php)

<a name="refund"></a>

### Anulación / Devolución Total de Pago

Mediante este recurso, se genera una solicitud de anulación / devolución total de un pago puntual, pasando como parámetro el id del pago.

```php

$data = array();
$response = $connector->payment()->Refund($data, '574671'); //574671 es el id de la operacion de compra
$response->getId();
$response->getAmount();
$response->getSub_payments();
$response->getStatus();

```

[<sub>Volver a inicio</sub>](#decidir-sdk-php)


<a name="deleterefund"></a>

### Anulación de Devolución Total

Mediante este recurso, se genera una solicitud de anulación de devolución total de un pago puntual, pasando como parámetro el id del pago y el id de la devolución.

```php

$data = array();
$response = $connector->payment()->deleteRefund($data, '574671', '164'); //574671 id de la operacion de compra, 164 id de la devolucion
$response->getResponse();
$response->getStatus();

```

[<sub>Volver a inicio</sub>](#decidir-sdk-php)

<a name="partialrefund"></a>

### Devolución Parcial de un Pago

Mediante este recurso, se genera una solicitud de devolución parcial de un pago puntual, pasando como parámetro el id del pago y el monto de la devolución.

```php

$data = array(
	"amount" => 1.00
	);
$response = $connector->payment()->partialRefund($data,'574673'); //574671 id de la operacion de compra
$response->getId();
$response->getAmount();
$response->getSub_payments();
$response->getStatus();


```

[<sub>Volver a inicio</sub>](#decidir-sdk-php)


<a name="deletepartialrefund"></a>

### Anulación de Devolución Parcial

Mediante este recurso, se genera una solicitud de anulación de devolución parcial de un pago puntual, pasando como parámetro el id del pago y el id de la devolución.

```php

$data = array();
$response = $connector->payment()->deleteRefund($data, '574671', '164'); //574671 id de la operacion de compra, 164 id de la devolucion parcial
$response->getResponse());
$response->getStatus());


```


<a name="Tokenizacion de tarjetas de crédito"></a>

## Tokenizacion de tarjetas de crédito

Esta funcionalidad permite que luego de realizar una compra con una tarjeta, se genere un token alfanumerico unico en el backend de Decidir, esto permite que a la hora de comprar nuevamente con esta tarjeta solo requerira el token de la tarjeta y el codigo de seguridad.
Como primer paso se debe realizar una un pago normal, el token generado estara en el campo "token" de la respuesta.

<a name="listadotarjetastokenizadas"></a>

### Listado de tarjetas tokenizadas

Este metodo permite conocer el listado de tarjetas tokenizadas que posee un usuario determinado. Esto requerira el nombre de usuario (user_id) al momento de llamar al metodo tokensList.

```php

$data = array();
$response = $connector->token()->tokensList($data, 'prueba'); //prueba, es el usuario dueño de la tarjeta de credito
var_dump($response);
var_dump($response->getTokens());


```

[<sub>Volver a inicio</sub>](#listadotarjetastokenizadas)


<a name="pagotokenizado"></a>

### Ejecucion de pago tokenizado

Una vez que se obtiene el token a partir de la tarjeta tokenizada, se deberá ejecutar la solicitud de pago. Además del token de pago y los parámetros propios de la transacción, el comercio deberá identificar la compra con el "site_transaction_id" y "user_id".

```php

$connector = new \Decidir\Connector($keys_data, $ambient);

$data = array(
      "site_transaction_id" => "12042017_20",
      "token" => "be211413-757b-487e-bb0c-283d21c0fb6f",
      "user_id" => "pepe",
      "payment_method_id" => 1,
      "bin" => "450799",
      "amount" => 10.00,
      "currency" => "ARS",
      "installments" => 1,
      "description" => "",
      "payment_type" => "single",
      "sub_payments" => array()
    );

$response = $connector->payment()->ExecutePayment($data);
$response->getId();
$response->getToken();
$response->getUser_id();
$response->getPayment_method_id();
$response->getBin();
$response->getAmount();
$response->getCurrency();
$response->getInstallments();
$response->getPayment_type();
$response->getDate_due();
$response->getSub_payments();
$response->getStatus();
$response->getStatus_details();
$response->getDate();
$response->getEstablishment_name();
$response->getFraud_detection();
$response->getAggregate_data();
$response->getSite_id();
```

[<sub>Volver a inicio</sub>](#pagotokenizado)


<a name="eliminartarjetatokenizada"></a>

### Eliminacion de tarjeta tokenizada

El servicio da la posibilidad de eliminar un token de tarjeta generada, esto se logra instanciando token y utilizando el metodo tokenDelete() y enviando la tarjeta tokenizada.

```php

$data = array();
$response = $connector->token()->tokenDelete($data, 'af49025a-f1b7-4363-a1cb-1ed38c3d4d75');

```

[<sub>Volver a inicio</sub>](#eliminartarjetatokenizada)


<a name="cybersource"></a>

### Integración con Cybersource

Para utilizar el Servicio de Control de Fraude Cybersource, en la ejecución del pago, deben enviarse datos adicionales sobre la operación de compra que se quiere realizar.
Se han definido cinco verticales de negocio que requieren parámetros específicos, así como también parámetros comunes a todas las verticales.

[Volver al inicio](#cybersource)

#### Retail

Los siguientes parámetros se deben enviar específicamente para la vertical Retail. Además se deben enviar datos específicos de cada producto involucrado en la transacción.


```php

  $cs_data = array(
        "send_to_cs" => true,
        "channel" => "Web",
        "bill_to" => array(
          "city" => "Buenos Aires",
          "country" => "AR",
          "customer_id" => "martinid",
          "email" => "accept@decidir.com.ar",
          "first_name" => "martin",
          "last_name" => "perez",
          "phone_number" => "1547766111",
          "postal_code" => "1768",
          "state" => "BA",
          "street1" => "GARCIA DEL RIO 3333",
          "street2" => "GARCIA DEL RIO 3333",
        ),
        "ship_to" => array(
          "city" => "Buenos Aires",
          "country" => "AR",
          "customer_id" => "martinid",
          "email" => "accept@decidir.com.ar",
          "first_name" => "martin",
          "last_name" => "perez",
          "phone_number" => "1547766111",
          "postal_code" => "1768",
          "state" => "BA",
          "street1" => "GARCIA DEL RIO 3333",
          "street2" => "GARCIA DEL RIO 3333",
        ),
        "currency" => "ARS",
        "amount" => 12.00,
        "days_in_site" => 243,
        "is_guest" => false,
        "password" => "password",
        "num_of_transactions" => 1,
        "cellphone_number" => "12121",
        "date_of_birth" => "129412",
        "street" => "RIO 4041",
        "days_to_delivery" => "55",
        "dispatch_method" => "storepickup",
        "tax_voucher_required" => true,
        "customer_loyality_number" => "123232",
        "coupon_code" => "cupon22",
        "csmdd17" => "17"
      );

  //Datos de productos, array con los diferentes productos involucrados.
  $cs_products = array(
        array(
          "csitproductcode" => "electronic_product", //Código de producto. MANDATORIO.
          "csitproductdescription" => "NOTEBOOK L845 SP4304LA DF TOSHIBA", //Descripción del producto. MANDATORIO.
          "csitproductname" => "NOTEBOOK L845 SP4304LA DF TOSHIBA",  //Nombre del producto. MANDATORIO.
          "csitproductsku" => "LEVJNSL36GN", //Código identificador del producto. MANDATORIO.
          "csittotalamount" => 6.00, //MANDATORIO
          "csitquantity" => 1,//Cantidad del producto. MANDATORIO.
          "csitunitprice" => 6.00 //Formato Idem CSITTOTALAMOUNT. MANDATORIO 
          ),
        array(
          "csitproductcode" => "default", //Código de producto. MANDATORIO.
          "csitproductdescription" => "PENDRIVE 2GB KINGSTON", //Descripción del producto. MANDATORIO.
          "csitproductname" => "PENDRIVE 2GB", //Nombre del producto. MANDATORIO.
          "csitproductsku" => "KSPDRV2g", //Código identificador del producto. MANDATORIO.
          "csittotalamount" => 6.00, //MANDATORIO
          "csitquantity" => 1, //Cantidad del producto. MANDATORIO.
          "csitunitprice" => 6.00 //Formato Idem CSITTOTALAMOUNT. MANDATORIO 
        )
      );   

      

```

Para incorporar estos datos en el requerimiento inicial, se debe instanciar un objeto de la clase Decidir\Data\Cybersource\Retail de la siguiente manera.

```php

$cybersource = new Decidir\Cybersource\Retail(
                    $datos_cs,  // Datos de la operación
                    $cs_productos, // Datos de los productos
  );

$connector->payment()->setCybersource($cybersource->getData());

$data = array(
      "site_transaction_id" => "12042017_20",
      "token" => "be211413-757b-487e-bb0c-283d21c0fb6f",
      "user_id" => "usuario",
      "payment_method_id" => 1,
      "bin" => "450799",
      "amount" => 12.00,
      "currency" => "ARS",
      "installments" => 1,
      "description" => "",
      "payment_type" => "single",
      "sub_payments" => array()
    );

    $response = $connector->payment()->ExecutePayment($data);

```

[Volver al inicio](#decidir-sdk-php)


#### Ticketing

Los siguientes parámetros se deben enviar específicamente para la vertical Ticketing. Además se deben enviar datos específicos de cada producto involucrado en la transacción.

```php
    
  $cs_data = array(
        "send_to_cs" => true,
        "channel" => "Web",
        "bill_to" => array(
          "city" => "Buenos Aires",
          "country" => "AR",
          "customer_id" => "martinid",
          "email" => "accept@decidir.com.ar",
          "first_name" => "martin",
          "last_name" => "perez",
          "phone_number" => "1547766111",
          "postal_code" => "1427",
          "state" => "BA",
          "street1" => "GARCIA DEL RIO 4000",
          "street2" => "GARCIA DEL RIO 4000",
        ),
        "ship_to" => array(
          "city" => "Buenos Aires",
          "country" => "AR",
          "customer_id" => "martinid",
          "email" => "accept@decidir.com.ar",
          "first_name" => "martin",
          "last_name" => "perez",
          "phone_number" => "1547766111",
          "postal_code" => "1427",
          "state" => "BA",
          "street1" => "GARCIA DEL RIO 4000",
          "street2" => "GARCIA DEL RIO 4000",
        ),
        "currency" => "ARS",
        "amount" => 12.00,
        "days_in_site" => 243,
        "is_guest" => false,
        "password" => "abracadabra",
        "num_of_transactions" => 1,
        "cellphone_number" => "12121",
        "date_of_birth" => "129412",
        "street" => "RIO 4041",
        "delivery_type"=> "Pick up",
        "days_to_event"=> 55,
        "csmdd17" => "17"
      );

  //Datos de productos, array con los diferentes productos involucrados.
  $cs_products = array(
        array(
          "csitproductcode" => "concierto2016",
                  "csitproductdescription" => "Popular Concierto 2016",
                  "csitproductname" => "concierto2016",
                  "csitproductsku" => "BS01",
                  "csittotalamount" => 6.00,
                  "csitquantity" => 1,
                  "csitunitprice" => 6.00
          ),
        array(
          "csitproductcode" => "concierto2017",
                  "csitproductdescription" => "Popular Concierto 2017",
                  "csitproductname" => "concierto2017",
                  "csitproductsku" => "BS01",
                  "csittotalamount" => 6.00,
                  "csitquantity" => 1,
                  "csitunitprice" => 6.00
        )
      );  
     

```

Para incorporar estos datos en el requerimiento inicial, se debe instanciar un objeto de la clase Decidir\Data\Cybersource\Ticketing de la siguiente manera.

```php
$cybersource = new Decidir\Cybersource\Ticketing(
                    $datos_cs,  // Datos de la operación
                    $cs_productos, // Datos de los productos
  );

$connector->payment()->setCybersource($cybersource->getData());

$data = array(
      "site_transaction_id" => "12042017_20",
      "token" => "be211413-757b-487e-bb0c-283d21c0fb6f",
      "user_id" => "usuario",
      "payment_method_id" => 1,
      "bin" => "450799",
      "amount" => 12.00,
      "currency" => "ARS",
      "installments" => 1,
      "description" => "",
      "payment_type" => "single",
      "sub_payments" => array()
    );

$response = $connector->payment()->ExecutePayment($data);

```

[Volver al inicio](#decidir-sdk-php)


#### Digital Goods

Los siguientes parámetros se deben enviar específicamente para la vertical Digital Goods. Además se deben enviar datos específicos de cada producto involucrado en la transacción.


```php

$cs_data = array(
      "send_to_cs" => true,
      "channel" => "Web",
      "bill_to" => array(
        "city" => "Buenos Aires",
        "country" => "AR",
        "customer_id" => "martinid",
        "email" => "accept@decidir.com.ar",
        "first_name" => "martin",
        "last_name" => "perez",
        "phone_number" => "1547766111",
        "postal_code" => "1427",
        "state" => "BA",
        "street1" => "GARCIA DEL RIO 4000",
        "street2" => "GARCIA DEL RIO 4000",
      ),
      "ship_to" => array(
        "city" => "Buenos Aires",
        "country" => "AR",
        "customer_id" => "martinid",
        "email" => "accept@decidir.com.ar",
        "first_name" => "martin",
        "last_name" => "perez",
        "phone_number" => "1547766111",
        "postal_code" => "1427",
        "state" => "BA",
        "street1" => "GARCIA DEL RIO 4000",
        "street2" => "GARCIA DEL RIO 4000",
      ),
      "currency" => "ARS",
      "amount" => 12.00,
      "days_in_site" => 243,
      "is_guest" => false,
      "password" => "abracadabra",
      "num_of_transactions" => 1,
      "cellphone_number" => "12121",
      "date_of_birth" => "129412",
      "street" => "RIO 4041",
      "delivery_type"=> "Pick up",
      "csmdd17" => "17"
    );

//lista de productos cybersource
$cs_products = array(
      array(
        "csitproductcode" => "software2016",
                "csitproductdescription" => "Software 2016",
                "csitproductname" => "soft2016",
                "csitproductsku" => "ST01",
                "csittotalamount" => 6.00,
                "csitquantity" => 1,
                "csitunitprice" => 6.00
      ),
      array(
        "csitproductcode" => "software2017",
                "csitproductdescription" => "Software 2017",
                "csitproductname" => "soft2017",
                "csitproductsku" => "ST01",
                "csittotalamount" => 6.00,
                "csitquantity" => 1,
                "csitunitprice" => 6.00
      )
    );
  
```


Para incorporar estos datos en el requerimiento inicial, se debe instanciar un objeto de la clase Decidir\Data\Cybersource\Ticketing de la siguiente manera.

```php

$cybersource = new Decidir\Cybersource\DigitalGoods(
                    $datos_cs,  // Datos de la operación
                    $cs_productos, // Datos de los productos
  );

$connector->payment()->setCybersource($cybersource->getData());

$data = array(
      "site_transaction_id" => "12042017_20",
      "token" => "be211413-757b-487e-bb0c-283d21c0fb6f",
      "user_id" => "usuario",
      "payment_method_id" => 1,
      "bin" => "450799",
      "amount" => 12.00,
      "currency" => "ARS",
      "installments" => 1,
      "description" => "",
      "payment_type" => "single",
      "sub_payments" => array()
    );

$response = $connector->payment()->ExecutePayment($data);

```

[Volver al inicio](#decidir-sdk-php)


<a name="tablasreferencia"></a>

## Tablas de Referencia


### Códigos de Medios de pago

| MEDIO DE PAGO | NOMBRE |
----------------|--------
| 1 | VISA |
| 8 | DINERS |
| 15 | MASTERCARD |
| 20 | MASTERCARD TEST |
| 23 | TARJETA SHOPPING |
| 24 | TARJETA NARANJA |
| 25 | PAGO FACIL |
| 26 | RAPIPAGO |
| 27 | CABAL |
| 29 | ITALCRED |
| 30 | ARGENCARD |
| 31 | VISA DEBITO<sup>1</sup> |
| 34 | COOPEPLUS |
| 36 | ARCASH |
| 37 | NEXO |
| 38 | CREDIMAS |
| 39 | TARJETA NEVADA |
| 41 | PAGOMISCUENTAS |
| 42 | NATIVA |
| 43 | TARJETA MAS |
| 44 | TARJETA CARREFOUR |
| 45 | TARJETA PYMENACION |
| 46 | PAYSAFECARD |
| 47 | MONEDERO ONLINE |
| 48 | CAJA DE PAGOS |
| 50 | BBPS |
| 51 | COBRO EXPRESS |
| 52 | QIDA |
| 53 | LAPOS WEB TRAVEL |
| 54 | GRUPAR |
| 55 | PATAGONIA 365 |
| 56 | TARJETA CLUD DIA |
| 59 | TARJETA TUYA |
| 60 | DISTRIBUTION |
| 61 | LA ANONIMA |
| 62 | CREDIGUIA |
| 63 | CABAL PRISMA |
| 64 | TARJETA SOL |
| 65 | AMEX MT |
| 66 | MC DEBIT |
| 67 | CABAL DEBITO (Cabal24) |
| 99 | MAESTRO |

1. Visa Debito no acepta devoluciones parciales en e-commerce.


[Volver al inicio](#decidir-sdk-php)



### Divisas Aceptadas

| Divisa | Descripción | Código API
---------|-------------|--------
| AR$ | Pesos Argentinos | ARS |
| U$S | Dólares Americanos | USD | 

**NOTA** Si bien la API RESTful de DECIDIR admite compras en Dólares Americanos, la legislación argentina sólo permite transacciones en Pesos Argentinos. Es por esto que DECIDIR recomienda que todas las transacciones se cursen en dicha moneda.

[Volver al inicio](#decidir-sdk-php)



### Provincias

| Provincia | Código |
|----------|-------------|
| CABA | C |
| Buenos Aires | B |
| Catamarca | K |
| Chaco | H |
| Chubut | U |
| Córdoba | X |
| Corrientes | W |
| Entre Ríos | R |
| Formosa | P |
| Jujuy | Y |
| La Pampa | L |
| La Rioja | F |
| Mendoza | M |
| Misiones | N |
| Neuquén | Q |
| Río Negro | R |
| Salta | A |
| San Juan | J |
| San Luis | D |
| Santa Cruz | Z |
| Santa Fe | S |
| Santiago del Estero | G |
| Tierra del Fuego | V |
| Tucumán | T | 	

[Volver al inicio](#decidir-sdk-php)
