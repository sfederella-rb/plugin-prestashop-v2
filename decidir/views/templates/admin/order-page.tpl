<script>
	$(document).ready(function() {
		var refundtype = false;

		//$("#loader").show();
		$('#total_void_check').change(function () {
		    if($(this).is(':checked')){
				console.log("active");
				$("#refundTotalCost").attr("disabled","disabled");
				$("#refundTotalCost").val($("#order_amount").val());
				refundtype = true;
			}else{
				console.log("no active");
				$("#refundTotalCost").removeAttr("disabled");
				$("#refundTotalCost").val(0);
				refundtype = false;
			}
		});

		$("#refund-button").click(function(){
			$('#message').html("");
			$("#loader").show();

			$.ajax({
				type: "POST",
				url: "{$url_refund}",
				accepts: "application/json",
				data: { 
			        'orderOperation': "{$num_order_dec}",
			        'orderecommerce': {$order_id},
			        'amount': $("#refundTotalCost").val(),
			        'refundtype': refundtype,
			    },
			 	success: function(data){
			 		//alert(data);
			 		$('#message_refund').text(data);
			 		$("#loader").hide();
			    },
			    error: function(data){
			    	
			    }
			});

		});
	});		
</script>
<style>
	.left-position{
		width: 40%;
		margin-top:10px;
	}
	#message_refund{
		color:red;
		font-size: 13px;
		margin:0px 0 5px 0;
	}
	#loader{
		background-image: url({$module_dir}imagenes/loader.gif);
		width:30px;
		height:30px;
		border:1px solid 000;
		float:left;
		margin:1px 0 0 4px;
		display:none;
	}
	.left{
		float:left;
	}
	.right{
		float:right;
	}
	.clear{
		clear:both;
	}

</style>
<div class="panel" id="refund-box ">
	<div class="panel-heading">
		<i class="icon-globe"></i>
		Decidir devoluciónes 
		<span class="badge" id="id_decidir">{$num_order_dec}</span>	(Id de operacion registrado en Decidir)
	</div>
	<div class="panel panel-total left-position">
		<div class="table-responsive">
			<table class="table">
						<tr>
							<td class="text-right">Productos+Envío</td>
							<td class="amount text-right nowrap">
								{$total_pay} {$currency_code}
							</td>
							<td class="partial_refund_fields current-edit" style="display:block;">
								<div class="input-group">
									<div class="input-group-addon">
										 {$currency_code}
									</div>
									<input type="text" name="partialRefundTotalCost" id="refundTotalCost" value="0">
								</div>
							</td>
							<input type="hidden" name="amount" id="order_amount" value="{$total_pay}">
						</tr>
						<tr id="total_order">
							<td class="text-right"><strong>Total</strong></td>
							<td class="amount text-right nowrap">
								<strong>{$total_pay} {$currency_code}</strong>
								<input type="hidden" id="total_payment_value" name="total_payment" value="{$total_pay}">
							</td>
							<td class="partial_refund_fields current-edit">
								<input type="checkbox" id="total_void_check" value="total_void">
								<span>Devoluci&oacute;n total</span>
							</td>
						</tr>
					</tbody>
			</table>
		</div>
	</div> 
	<div id="message_refund"></div>
	<a href="#dec-refund" id="refund-button" class="btn btn-default left">
		<i class="icon-exchange"></i>
		Devoluci&oacute;n
	</a>
	<div id="loader" class="right"></div>
	<div class="clear"></div>
	<!--a href="#dec-void" id="void-button" class="btn btn-default">
		<i class="icon-exchange"></i>
		Anulacion
	</a-->
</div>
