{if $status == 'ok'}
	<h3>{l s='Gracias por su compra' mod='todopago'}</h3>
	<p>
		<br/> - {l s='Codigo de referencia: %s.' sprintf=$reference mod='todopago'}
		<br/> - {l s='Total:' mod='todopago'} <span id="amount" class="price">{displayPrice price=$total_to_pay}</span>
		<!-- <br/> - {l s='Mensaje: %s' sprintf=$mensaje mod='todopago'} -->
		<br/> - {l s='Mail del cliente: %s' sprintf=$customer mod='todopago'}
	</p>
{else}
	<h3>Ocurrio un error</h3>
	<p class="warning">
		{l s='Por favor intente nuevamente.' mod='todopago'}
		<br/>
		<!-- {l s='Status order: %s' sprintf=$status_desc mod='todopago'} --!> <!-- Muestra el status order de la compra --!>
	</p>
{/if}