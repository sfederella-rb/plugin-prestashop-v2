{capture name=path}
  <span class="navigation-pipe">{$navigationPipe}</span>Confirmacion de Pago
{/capture}

<style>
	.button-small{
		padding:6px 6px 7px 7px !important;
	}
	p{
		font-size: 17px;	
		margin:0 0 16px 0;
	}
</style>

{if $status == 'ok'}

	<!-- Block decidir -->
	<div id="mymodule_block_home" class="block">
		<h2>{l s='Gracias por su compra.' mod='decidir'} </h2><br>
		<p>{$cs_message}</p>
		<p>El c&oacute;digo de referencia de la order es: <a href='{$url_orderdetails}'>{$order_ref}</a></p>
	</div>

	<ul class="footer_links clearfix">
		<li>
			<a href="{$link->getPageLink('', true, NULL, "")|escape:'html'}" class="btn btn-default button button-small">
				<i class="icon-chevron-left"></i>
				{l s='Continuar comprando' mod='decidir'}
			</a>
		</li>
		<li>
			<a href="{$link->getPageLink('my-account', true, NULL, "")|escape:'html'}" class="btn btn-default button button-small">
				<i class="icon-chevron-right"></i>
				{l s='Mi cuenta' mod='decidir'}
			</a>
		</li>
	</ul>
	<!-- /Block decidir -->
{else}
	
	<p class="warning">{l s='Tu carrito esta vacio.' mod='decidir'}</p>

{/if}