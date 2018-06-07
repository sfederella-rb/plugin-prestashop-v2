<script type="text/javascript" src="/prestashop_1.7.1.2/js/jquery/jquery-1.11.0.min.js"></script>
<!-- Add jQuery library -->
<script type="text/javascript">
	$(document).ready(function(){
		var id_section = {$section_adminpage};
		//$("#discount").attr("disabled","disabled");
		$("#discount").removeAttr("disabled","disabled");

		var section = ["general", "control_fraude", "medios_pago", "cms_entidades","cms_medios_pago", "cms_planes", "cms_interes"];
		var tabs = ["tab1", "tab2", "tab3", "tab4", "tab5", "tab6", "tab7"];

		switch(id_section) {
		    case 4:
		        sectindex = "cms_medios_pago";
		        tabindex = "tab5";
		        break;
		    case 5:
		    	sectindex = "cms_entidades";
		        tabindex = "tab4";
		        break;
		    case 1:
		    	sectindex = "general";
		        tabindex = "tab1";
		        break;
		    default:
		    	sectindex = "general";
		        tabindex = "tab1";
		        break;
		}

		loop_section(sectindex, tabindex);

		//click tab event
		$("#general_tab").click(function(){
			loop_section("general", "tab1");
		});

		$("#control_fraude_tab").click(function(){
			loop_section("control_fraude", "tab2");
		});

		$("#cms_medios_pago_tab").click(function(){
			loop_section("cms_medios_pago", "tab5");
		});

		$("#cms_entidades_tab").click(function(){
			loop_section("cms_entidades", "tab4");
		});

		$("#cms_planes_tab").click(function(){
			loop_section("cms_planes", "tab6");
		});
		$("#cms_interes_tab").click(function(){
			loop_section("cms_interes", "tab7");
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


		function loop_section(contentindex, tab){

			//index of section
			var index;
			for (index = 0; index < section.length; ++index) {
			    console.log(section[index]+"=="+contentindex);

			    if(section[index] == contentindex){
			    	$("#"+contentindex).addClass("active");
			    }else{
			    	$("#"+section[index]).removeClass("active");
			    }
			}

			var indextab;
			for (indextab = 0; indextab < tabs.length; ++indextab) {
			    console.log(tabs[indextab]+"=="+tab);

			    if(tabs[indextab] == tab){
			    	console.log("#"+tab);

			    	$("#"+tab).addClass("active");
			    }else{
			    	$("#"+tabs[indextab]).removeClass("active");
			    }
			}
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