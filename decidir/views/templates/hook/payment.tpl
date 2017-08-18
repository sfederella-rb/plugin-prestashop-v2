<style>
	a.checker-decidir{
		background: url({$this_path}imagenes/decidir-creditcard.png) 15px 12px no-repeat #fbfbfb !important;
	}

	a.checker-decidir:after {
	    display: block;
	    content: "\f054";
	    position: absolute;
	    right: 15px;
	    margin-top: -11px;
	    top: 50%;
	    font-family: "FontAwesome";
	    font-size: 25px;
	    height: 22px;
	    width: 14px;
	    color: #777777;
	}

</style>

<p  class="payment_module">
	<a href="{$link->getModuleLink('decidir', 'payment', ['paso' => '1'], true)|escape:'html'}" title="{$nombre}" class="checker-decidir">
		{$nombre}
	</a>
</p> 

