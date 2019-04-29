<a name="inicio"></a>
Prestashop - m&oacute;dulo Decidir (v1.0.x)
 
Plug-in para la integraci&oacute;n con pasarela de pago <strong>Decidir</strong>.
- [Consideraciones Generales](#consideracionesgenerales)
- [Instalaci&oacute;n](#instalacion)
- [Configuraci&oacute;n](#configuracion)
	- [Configuraci&oacute;n plug in](#confplugin)
	- [Nuevas columnas y atributos](#tca)
- [Prevencion de Fraude](#cybersource)
	- [Consideraciones generales](#cons_generales)
	- [Consideraciones para vertical retail](#cons_retail)
- [Caracter&iacute;sticas](#features)
	- [Formulario de pago ](#formulario)
	- [Devoluciones](#devoluciones)
- [Tablas de referencia](#tablas)
- [Versiones disponibles](#availableversions)

<a name="consideracionesgenerales"></a>
## Consideraciones Generales
El plugin de pagos de <strong>Decidir 2.0</strong>, provee a las tiendas Prestashop de un nuevo m&eacute;todo de pago, integrando la tienda al gateway de pago.
La versi&oacute;n de este plug in esta testeada en PHP 5.3 en adelante y Prestashop 1.6 en adelante.
Es sumamente importante que se compare con las necesidades de negocio para evaluar la utilización del mismo o recurrir a una integración vía SDK.

<a name="instalacion"></a>
## Instalaci&oacute;n
1. Descomprimir el archivo .zip.
2. Copiar carpeta la carpeta "Decidir" en la carpeta prestashop/modules.
3.	Ir a  "M&oacute;dulos" dentro del Area de Administraci&oacute;n.
4. En la lista de m&oacute;dulos, ir a la fila llamada "Decidir" y hacer click donde dice "Instalar". De aparecer un cartel de advertencia, elegir la opci&oacute;n "Seguir con la instalaci&oacute;n". Una vez instalado ir a la pagina de configuraci&oacute;n, que se puede acceder desde la lista de m&oacute;dulos.

**Para actualizar el plugin se debe desinstalar cualquier versi&oacute;n anterior antes de copiar el plugin a la carpeta modules de Prestashop. De lo contrario fallar&aacute; la instalacion de la nueva versi&oacute;n**

Observaci&oacute;n:
Descomentar: <em>extension=php_openssl.dll</em> y <em>extension=php_curl.dll</em> del php.ini.

<br />

[<sub>Volver a inicio</sub>](#inicio)

<a name="configuracion"></a>
## Configuraci&oacute;n

<a name="confplugin"></a>
Para llegar al menu de configuraci&oacute;n ir a <em>M&oacute;dulos</em> y en la lista buscar el &iacute;tem llamado <strong>Decidir</strong>.
El Plugin esta separado por secciones, Configuración Cybersource, ABM medios de pago, entidades y planes de pago.
<a name="confplanes"></a>

#### Configuración general

Esta sección permite configurar los parámetros de activación del plugin, el nombre del plugin a mostrar en el frontend en la sección de selección de medio de pago y el ambiente. Si está activado será producción en caso contrario el ambiente de prueba.
Además permite cargar las credenciales del comercio. Key publica y privada de ambos ambientes.
La subsección estados del pedido permiten agregar los estados que ira tomando la orden durante el proceso de compra. 
Durante la instalación se definen los estados por defecto, que pueden ser modificados por el administrador.

#### Configuración Cybersource

Esta sección permite activar Cybersource en Decidir y seleccionar el tipo de vertical (actualmente se encuentra disponible Retail, Ticketing y Digital Goods).

**NOTA**: Para poder habilitar Cybersource es necesario tener contratado el servicio y se debe seleccionar el vertical correcto de otra forma no funcionará en el Plugin. 

#### ABMs de configuración

Permite ingresar los Medios de pago, Entidades financieras, Promociones de pago y intereses de tarjetas de créditos habilitadas en el momento del pago.

##### ABM medios de pago
En el ABM de medio de pago se encuentran:
  - Medio de Pago: El nombre con el que se mostrará dicho medio de pago. (Obligatorio)
  - Tipo: Tarjeta u offline. Por el momento solo está disponible el primer tipo. (Obligatorio)
  - Id Decidir: El código del medio de pago en Decidir. (Ver Tabla en documentación del servicio) (Obligatorio)
  - Activar: Flag para activar/desactivar el medio de pago. (Obligatorio)

##### ABM de entidades financieras
En el ABM de entidades se encuentra:
  - Nombre Entidad: El nombre con el que se mostrará dicha entidad. (Obligatorio)
  - Activar: Flag para activar/desactivar la entidad (Obligatorio)	

##### ABM medios de promociones
En el ABM de promociones:
   - Nombre: Nombre del plan (Obligatorio)
   - Tarjeta: Tipo de medio de pago (Obligatorio)
   - Entidad Financiera: Entidad financiera de la promocion (Obligatorio)
   - ID Plan de la marca: (No obligatorio)
   - Cuotas a enviar: Cuota enviada en el caso que sea distinta a la real (No obligatorio)
   - Días: Días habilitados para la promoción. (Obligatorio)
   - Fecha inicio: Fecha de inicio de la promoción (No obligatorio)
   - Fecha final: Fecha fin de la promocion (No obligatorio)
   - Cuotas: Cuotas habilitadas para la promoción (Obligatorio)
   - Tasa Directa: Coeficiente de la promoción (Obligatorio)
   - Descuento: Porcentaje de descuento de la promoción (No obligatorio)
   - Reintegro: Porcentaje de reintegro de la promoción (No obligatorio)
   - Activado: Flag para activar/desactivar la promoción (Obligatorio)

##### ABM medios de interes
En el ABM de intereses:
  - Cuotas: Cuota a mostrar (Obligatorio)
  - Medio de Pago: Selección de medio de pago (Obligatorio)
  - Coeficiente: Coeficiente correspondiente a la cuota (Obligatorio)
  - activado: Flag para activar/desactivar al interés (Obligatorio)

<br/>

<a name="tca"></a>
#### Nuevas columnas y atributos
El plugin crear&aacute; nuevas tablas y registros en tablas existentes para lograr las nuevas funcionalidades y su persistencia dentro del framework. 

##### Tablas:
1. <i>decidir_transacciones</i>, Guarda registros  de las ordenes generadas en Decidir.
2. <i>decidir_tokens</i>, Guarda los tokens de tarjetas previamente utilizadas.
4. <i>medios</i>, Guarda los medios de pagos habilitados.
5. <i>bancos</i>, Guarda las entidades financieras.
6. <i>promociones</i>, Guarda las promociones de tarjetas.
7. <i>interes</i>, Guarda los intereses de las tarjeta de credito

##### Registros:
Las registros de configuraci&oacute;n se encuentran guardados en la tabla de <i>configuration</i> de Pretashop.
<br/>

[<sub>Volver a inicio</sub>](#inicio)

<a name="cybersource"></a>
## Prevenci&oacute;n de Fraude
- [Consideraciones Generales](#cons_generales)
- [Consideraciones para vertical RETAIL](#cons_retail)

<a name="cons_generales"></a>
#### Consideraciones Generales (para todas los verticales, por defecto RETAIL)
El plugin obtiene valores obligatorios para la compra con Cybersource. Para ello se utilizan las clases Customer, Address y State para recuperar los registros almacenados en la base de datos que corresponden al cliente que efectúa la compra y Cart para recuperar el carrito en el que se almacena los datos relativos a la compra en sí.

```php
   $cart = $this->context->cart;
   $customer = new Customer($cart->id_customer);
   $address = new Address($cart->id_address_invoice);
   $state = new State($address->id_state);

-- Ciudad de Facturación: $address->city;
-- País de facturación:  $address->country;
-- Identificador de Usuario: $customer->id;
-- Email del usuario al que se le emite la factura: $customer->email;
-- Nombre de usuario el que se le emite la factura: $customer->firstname;
-- Apellido del usuario al que se le emite la factura: $customer->lastname;
-- Teléfono del usuario al que se le emite la factura: $address->phone;
-- Provincia de la dirección de facturación: $state->iso_code;
-- Domicilio de facturación: $address->address1;
-- Moneda: $cart->id_currency;
-- Total:  $cart->getOrderTotal(true, Cart::BOTH);
-- IP de la pc del comprador: Tools::getRemoteAddr();
```
<br>

Tambi&eacute;n se utilizá la clase <em>Customer</em> para obtener el password del usuario (comprador) y la tabla <em>Orders</em>, donde se consultan las transacciones facturadas al comprador.
<a name="cons_retail"></a>

#### Consideraciones para vertical RETAIL
Las consideración para el caso de empresas del rubro <strong>RETAIL</strong> son similares a las <em>consideraciónes generales</em> con la diferencia de se utiliza el atributo id_address_delivery en lugar de id_address_invoice para recuperar el registro de la tabla address

```php
   $cart = $this->context->cart;
   $customer = new Customer($cart->id_customer);
   $address = new Address($cart->id_address_delivery);
   $state = new State($address->id_state);
   $carrier = new Carrier($cart->id_carrier);
   
-- Ciudad de env&iacute;o de la orden: $address->city;
-- País de env&iacute;o de la orden: $address->country;
-- Mail del destinatario: $customer->email;
-- Nombre del destinatario: $customer->firstname;
-- Apellido del destinatario: $customer->lastname;
-- N&uacute;mero de tel&eacute;fono del destinatario: $address->phone;
-- C&oacute;digo postal del domicio de env&iacute;o: $address->postcode;
-- Provincia de env&iacute;o: $state->iso_code;
-- Domicilio de env&iacute;o: $address->address1;
-- M&eacute;todo de despacho: $carrier->name;
-- Listado de los productos: $cart->getProducts();
```
<strong>Nota:</strong> la funcion $cart->getProducts() devuelve un array con el listado de los productos, que se usan para conseguir la informaci&oacute;n que se debe enviar mediante la funci&oacute;n <strong>_getProductsDetails()</strong>.

#### Muy Importante
<strong>Provincias:</strong> uno de los datos requeridos para prevenci&oacute;n com&uacute;n a todos los verticales  es el campo provincia/state tanto del comprador como del lugar de env&iacute;o, para tal fin el plug in utiliza el valor del campo id_state, que figura en el registro Address recuperado, para recuperar el objeto State correspondiente a ese id, y as&iacute; obtener el iso_code. El formato de estos datos deben ser tal cual la tabla de referencia (tabla provincias). En Prestashop el listado se encuentra en Localizaci&oacute;n -> Provincias.
<br/>
<strong>Celular:</strong> se utiliza el atributo phone_mobile del registro Address recuperado.

[<sub>Volver a inicio</sub>](#inicio)

<a name="features"></a>
## Caracter&iacute;sticas

<a name="formulario"></a>
#### Formulario de pago Decidir
El Plugin para realizar el pago posee un formulario integrado de pago. Este permite pagar de dos forma. Ingresando todos los datos de la tarjeta o ingresando solo el código de seguridad a partir de la segunda compra con la misma tarjeta.

[<sub>Volver a inicio</sub>](#inicio)

#### Devoluciónes - Anulacion de pago
Es posible realizar devoluciones o reembolsos mediante la página de resumen del Pedido "Pedidos->Pedido". Allí deberá hacer click en el botón "Devolución" ingresando el monto a devolver y sera procesada por Decidir.
Si la devolución es autorizada se genera una factura de devolución en la sección "Documentos".

Nota: En la pagina de resumen del Pedido mostrara el Id de operación Decidir de la transacción, cualquier consulta a soporte de Decidir sobre la operación requerirá este id.

[<sub>Volver a inicio</sub>](#inicio)

<a name="tablas"></a>
## Tablas de Referencia

###### [Provincias](#p)

<a name="p"></a>
<p>Provincias</p>
<table>
<tr><th>Provincia</th><th>Código</th></tr>
<tr><td>CABA</td><td>C</td></tr>
<tr><td>Buenos Aires</td><td>B</td></tr>
<tr><td>Catamarca</td><td>K</td></tr>
<tr><td>Chaco</td><td>H</td></tr>
<tr><td>Chubut</td><td>U</td></tr>
<tr><td>Córdoba</td><td>X</td></tr>
<tr><td>Corrientes</td><td>W</td></tr>
<tr><td>Entre Ríos</td><td>E</td></tr>
<tr><td>Formosa</td><td>P</td></tr>
<tr><td>Jujuy</td><td>Y</td></tr>
<tr><td>La Pampa</td><td>L</td></tr>
<tr><td>La Rioja</td><td>F</td></tr>
<tr><td>Mendoza</td><td>M</td></tr>
<tr><td>Misiones</td><td>N</td></tr>
<tr><td>Neuquén</td><td>Q</td></tr>
<tr><td>Río Negro</td><td>R</td></tr>
<tr><td>Salta</td><td>A</td></tr>
<tr><td>San Juan</td><td>J</td></tr>
<tr><td>San Luis</td><td>D</td></tr>
<tr><td>Santa Cruz</td><td>Z</td></tr>
<tr><td>Santa Fe</td><td>S</td></tr>
<tr><td>Santiago del Estero</td><td>G</td></tr>
<tr><td>Tierra del Fuego</td><td>V</td></tr>
<tr><td>Tucumán</td><td>T</td></tr>
</table>
[<sub>Volver a inicio</sub>](#inicio)

<a name="availableversions"></a>
## Versiones Disponibles
<table>
  <thead>
    <tr>
      <th>Version del Plugin</th>
      <th>Estado</th>
      <th>Versiones Compatibles</th>
    </tr>
  <thead>
  <tbody>
    <tr>
      <td><a href="">v1.0.x</a></td>
      <td>Stable (Current version)</td>
      <td>PrestaShop v1.6.x y 1.7.x<br />
      </td>
    </tr>
  </tbody>
</table>

[<sub>Volver a inicio</sub>](#inicio)
