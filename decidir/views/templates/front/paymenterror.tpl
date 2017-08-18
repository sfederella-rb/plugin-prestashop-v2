{capture name=path}
	<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" title="{l s='Go back to the Checkout' mod='decidir'}">{l s='Checkout' mod='decidir'}</a><span class="navigation-pipe">{$navigationPipe}</span>Todo Pago
{/capture}

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

	<h3 class="warning">Ocurrio un error</h3>	
{if isset($nbProducts) && $nbProducts <= 0}
	<p class="warning">{l s='Your shopping cart is empty.' mod='decidir'}</p>
	</br>
	</br>
	<div class="cart_navigation clearfix">
		<a href="{$link->getPageLink('index', true, NULL, "")|escape:'html'}" class="button-exclusive btn btn-default">
			<i class="icon-chevron-left"></i>
			{l s='Volver a la tienda' mod='decidir'}
		</a>
	</div>
{else}
	<p class="warning">Por favor intente nuevamente. </p>
	</br>
	</br>
	<div class="cart_navigation clearfix">
		<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html'}" class="button-exclusive btn btn-default">
			<i class="icon-chevron-left"></i>
			{l s='Volver a medios de pago' mod='decidir'}
		</a>
	</div>	
{/if}