{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2014 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
*}

<!-- Add jQuery library -->
<script type="text/javascript">
	$(document).ready(function(){
		var id_section = {$section_adminpage};

		if(id_section == 4){
			$("#cms_medios_pago").addClass("active");
			$("#general").removeClass("active");
			$("#control_fraude").removeClass("active");
			$("#medios_pago").removeClass("active");
			$("#cms_entidades").removeClass("active");
			$("#cms_planes").removeClass("active");
			$("#cms_interes").removeClass("active");
			$("#tab5").addClass("active");
			$("#tab4").removeClass("active");
			$("#tab6").removeClass("active");
			$("#tab7").removeClass("active");
			$("#tab3").removeClass("active");
			$("#tab2").removeClass("active");
			$("#tab1").removeClass("active");

		}else if(id_section == 5){

			$("#cms_entidades").addClass("active");
			$("#medios_pago").removeClass("active");
			$("#general").removeClass("active");
			$("#control_fraude").removeClass("active");
			$("#cms_medios_pago").removeClass("active");
			$("#cms_planes").removeClass("active");
			$("#cms_interes").removeClass("active");
			$("#tab4").addClass("active");
			$("#tab5").removeClass("active");
			$("#tab6").removeClass("active");
			$("#tab7").removeClass("active");
			$("#tab3").removeClass("active");
			$("#tab2").removeClass("active");
			$("#tab1").removeClass("active");

		}else if(id_section == 6){
			$("#general").removeClass("active");
			$("#control_fraude").removeClass("active");
			$("#medios_pago").removeClass("active");
			$("#cms_planes").addClass("active");
			$("#cms_entidades").removeClass("active");
			$("#cms_medios_pago").removeClass("active");
			$("#cms_interes").removeClass("active");
			$("#tab6").addClass("active");
			$("#tab7").removeClass("active");
			$("#tab4").removeClass("active");
			$("#tab5").removeClass("active");
			$("#tab3").removeClass("active");
			$("#tab2").removeClass("active");
			$("#tab1").removeClass("active");

		}else if(id_section == 7){
			$("#general").removeClass("active");
			$("#control_fraude").removeClass("active");
			$("#medios_pago").removeClass("active");
			$("#cms_entidades").removeClass("active");
			$("#cms_medios_pago").removeClass("active");
			$("#cms_planes").removeClass("active");
			$("#cms_interes").addClass("active");
			$("#tab7").addClass("active");
			$("#tab6").removeClass("active");
			$("#tab4").removeClass("active");
			$("#tab5").removeClass("active");
			$("#tab3").removeClass("active");
			$("#tab2").removeClass("active");
			$("#tab1").removeClass("active");
		}

		//click tab event
		$("#general_tab").click(function(){
			$("#general").addClass("active");
			$("#control_fraude").removeClass("active");
			$("#medios_pago").removeClass("active");
			$("#cms_entidades").removeClass("active");
			$("#cms_medios_pago").removeClass("active");
			$("#cms_planes").removeClass("active");
			$("#cms_interes").removeClass("active");
			$("#tab1").addClass("active");
			$("#tab2").removeClass("active");
			$("#tab3").removeClass("active");
			$("#tab4").removeClass("active");
			$("#tab5").removeClass("active");
			$("#tab6").removeClass("active");
			$("#tab7").removeClass("active");

		});
		$("#control_fraude_tab").click(function(){
			$("#control_fraude").addClass("active");
			$("#general").removeClass("active");
			$("#medios_pago").removeClass("active");
			$("#cms_entidades").removeClass("active");
			$("#cms_medios_pago").removeClass("active");
			$("#cms_planes").removeClass("active");
			$("#cms_interes").removeClass("active");
			$("#tab2").addClass("active");
			$("#tab1").removeClass("active");
			$("#tab3").removeClass("active");
			$("#tab4").removeClass("active");
			$("#tab5").removeClass("active");
			$("#tab6").removeClass("active");
			$("#tab7").removeClass("active");

		});
		/*
		$("#medios_pago_tab").click(function(){
			$("#medios_pago").addClass("active");
			$("#general").removeClass("active");
			$("#control_fraude").removeClass("active");
			$("#cms_entidades").removeClass("active");
			$("#cms_medios_pago").removeClass("active");
			$("#cms_planes").removeClass("active");
			$("#cms_interes").removeClass("active");
			$("#tab3").addClass("active");
			$("#tab4").removeClass("active");
			$("#tab5").removeClass("active");
			$("#tab6").removeClass("active");
			$("#tab2").removeClass("active");
			$("#tab1").removeClass("active");
			$("#tab7").removeClass("active");

		});
		*/
		$("#cms_medios_pago_tab").click(function(){
			$("#cms_medios_pago").addClass("active");
			$("#general").removeClass("active");
			$("#control_fraude").removeClass("active");
			$("#medios_pago").removeClass("active");
			$("#cms_entidades").removeClass("active");
			$("#cms_planes").removeClass("active");
			$("#cms_interes").removeClass("active");
			$("#tab5").addClass("active");
			$("#tab4").removeClass("active");
			$("#tab6").removeClass("active");
			$("#tab3").removeClass("active");
			$("#tab2").removeClass("active");
			$("#tab1").removeClass("active");
			$("#tab7").removeClass("active");

		});
		$("#cms_entidades_tab").click(function(){
			$("#cms_entidades").addClass("active");
			$("#medios_pago").removeClass("active");
			$("#general").removeClass("active");
			$("#control_fraude").removeClass("active");
			$("#cms_medios_pago").removeClass("active");
			$("#cms_planes").removeClass("active");
			$("#cms_interes").removeClass("active");
			$("#tab4").addClass("active");
			$("#tab5").removeClass("active");
			$("#tab6").removeClass("active");
			$("#tab3").removeClass("active");
			$("#tab2").removeClass("active");
			$("#tab1").removeClass("active");
			$("#tab7").removeClass("active");

		});
		$("#cms_planes_tab").click(function(){
			$("#cms_planes").addClass("active");
			$("#general").removeClass("active");
			$("#control_fraude").removeClass("active");
			$("#medios_pago").removeClass("active");
			$("#cms_entidades").removeClass("active");
			$("#cms_medios_pago").removeClass("active");
			$("#cms_interes").removeClass("active");
			$("#tab6").addClass("active");
			$("#tab4").removeClass("active");
			$("#tab5").removeClass("active");
			$("#tab3").removeClass("active");
			$("#tab2").removeClass("active");
			$("#tab1").removeClass("active");
			$("#tab7").removeClass("active");
		});
		$("#cms_interes_tab").click(function(){
			$("#cms_planes").removeClass("active");
			$("#general").removeClass("active");
			$("#control_fraude").removeClass("active");
			$("#medios_pago").removeClass("active");
			$("#cms_entidades").removeClass("active");
			$("#cms_medios_pago").removeClass("active");
			$("#cms_interes").addClass("active");
			$("#tab7").addClass("active");
			$("#tab6").removeClass("active");
			$("#tab5").removeClass("active");
			$("#tab4").removeClass("active");
			$("#tab3").removeClass("active");
			$("#tab2").removeClass("active");
			$("#tab1").removeClass("active");
		});

		//mejorar este codigo
		$("#id_entity").hide();
		$("#id_medio").hide();
		$("#id_promocion").hide();
		$("#id_interes").hide();

		//script seccion add promociones
		var urlAjax = "{$url_base}modules/decidir/ajax/back/ajaxadminloadpromos.php";

		$.ajax({
			url: urlAjax,
			type: 'get',
			dataType: "json",
			data: {
				ajax_payment_method: 1
			},
			success: function(dataResponse){
				$("#promo_type").attr("selected","selected");
				$("#payment_method")[0].options.length = 0;
				$.each(dataResponse, function (key, data) {
				    $("#payment_method").append("<option value='"+data.id+"''>"+data.name+"</option>");
				})
			} 
		});

		$("#promo_type").change(function(){

			$('#payment_method')[0].options.length = 0;

			$.ajax({
				url: urlAjax,
				type: 'get',
				dataType: "json",
				data: {
					ajax_payment_method: $("#promo_type").val()
				},
				success: function(dataResponse){

					$.each(dataResponse, function (key, data) {
							$("#payment_method").append("<option value='"+data.id+"''>"+data.name+"</option>");
					})
				} 
			});

			if($("#promo_type").val() == 1){
				$("#entity").removeAttr("disabled");
				$("#quote").val("0");
				$("#quote").removeAttr("disabled");
				$("#porcentaje").val("0");
				$("#porcentaje").removeAttr("disabled");
			}else{
				$('#entity').val(0).change();
				$("#entity").attr("disabled","disabled");	
				$("#quote").val("0");
				$("#quote").attr("disabled","disabled");
				$("#porcentaje").val("0");
				$("#porcentaje").attr("disabled","disabled");
			}

		});

		$("#interes_visa").addClass("active");	
		//init show payment method
		$(".list_interes").hide();
		var select_value = $("#interest_pmethod_field").val();
		var id_first_element = "#box_"+select_value;
		$(id_first_element).show();

		$("#interest_pmethod_field").change(function(){
			$(".list_interes").each(function(index) {
				$(".list_interes").hide();
				var select_value = $("#interest_pmethod_field").val();
				var id_first_element = "#box_"+select_value;
				$(id_first_element).show();
			});
		});
		
		//visibility of select 
		/*if($("#module_form_8").is(':visible')){
			$("#pmethod_select_interes").hide();
		}else{
			$("#pmethod_select_interes").show();
		}*/

		//hide select
		if($("#fieldset_0_5_5").is(":visible")){
		  $("#pmethod_select_interes").hide();		
		}
	});	

	//promo datepickers
    $(function(){
        $("#TRdatepicker").datepicker({
						                dateFormat:"yyyy-mm-dd"
						              });
    });
</script>
<!-- Tab nav -->
<style>
	@media (max-width: 992px) {
			.table-responsive-row td:nth-of-type(1):before {
				content: "Id";
			}
					.table-responsive-row td:nth-of-type(2):before {
				content: "Cuotas";
			}
					.table-responsive-row td:nth-of-type(3):before {
				content: "Marca";
			}
					.table-responsive-row td:nth-of-type(4):before {
				content: "Tarjetas";
			}
					.table-responsive-row td:nth-of-type(5):before {
				content: "Coeficiente";
			}
					.table-responsive-row td:nth-of-type(6):before {
				content: "Activado";
			}
	}
	#pmethod_select_interes{
		margin:5px 0 15px 0;
	}
	#date_from.datepicker, #date_to.datepicker{
		width: 100% !important;			
	}
</style>

<ul class="nav nav-tabs" id="todopagoConfig">				
	<li id="tab1" class="active">
		<a href="#" id="general_tab">
			<i class="icon-cogs"></i>
			Configuraci&oacute;n General
		</a>
	</li>
	<li id="tab2">
		<a href="#" id="control_fraude_tab">
			<i class="icon-cogs"></i>
			Configuraci&oacute;n Cybersource
		</a>
	</li>
	<!--li id="tab3">
		<a href="#" id="medios_pago_tab">
			<i class="icon-cogs"></i>
			Configuraci&oacute;n Medios de Pago
		</a>
	</li-->
	<li id="tab5">
		<a href="#" id="cms_medios_pago_tab">
			<i class="icon-cogs"></i>
			ABM Medios de pago
		</a>
	</li>
	<li id="tab4">
		<a href="#" id="cms_entidades_tab">
			<i class="icon-cogs"></i>
			ABM Entidades
		</a>
	</li>
	<li id="tab6">
		<a href="#" id="cms_planes_tab">
			<i class="icon-cogs"></i>
			ABM Planes de pago
		</a>
	</li>
	<li id="tab7">
		<a href="#" id="cms_interes_tab">
			<i class="icon-cogs"></i>
			ABM Interes
		</a>
	</li>
</ul>
<div class="tab-content panel">	
	<!-- Tab Configuracion -->
	<div class="tab-pane active" id="general">
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-cogs"></i>Versi&oacute;n utilizada
			</div>
			Utilizando la versi&oacute;n: {$version}
		</div>	
		{$config_general}	
	</div>
	<!-- Tab Cybersource -->
	<div class="tab-pane" id="control_fraude">
		{$config_cybersource}
	</div>
	<!-- Tab Medios de pago -->
	<div class="tab-pane" id="medios_pago">
		{$config_mediospago}
	</div>
	<!-- Tab CMS entidad -->
	<div class="tab-pane" id="cms_entidades">
		{$cms_entidades}
	</div>
	<!-- Tab CMS Medios de pago -->
	<div class="tab-pane" id="cms_medios_pago">
		{$cms_mediospago}
	</div>
	<!-- Tab CMS planes de pago -->
	<div class="tab-pane" id="cms_planes">
		{$cms_planespago}
	</div>
	<!-- Tab CMS intereses -->
	<div class="tab-pane" id="cms_interes">
		<div>
			{$cms_selectInteresList}
		</div>
		<div class="tab-content panel">	
			<div class="tab-pane" id="interes_visa">
				{$cms_interes}
			</div>	
		</div>	
	</div>
</div>