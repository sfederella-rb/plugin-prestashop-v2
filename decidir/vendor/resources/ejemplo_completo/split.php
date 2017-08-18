<?php
include_once dirname(__FILE__)."/FlatDb.php";

$operationid = $_GET['ord'];

if($_POST) {
	$db = new FlatDb();
	$db->openTable('ordenes');

	$orden = $db->getRecords(array("id","status","data","mediodepago", "split", "sar","form","gaa","requestkey","answerkey"),array("id" => $operationid));

	$data = json_decode($orden[0]['data'],true);

	$tipo = $_POST["tipo"];
	if($tipo == "MontoFijo") {
		$data = array_merge($data, array("split" => array(
			"tipo" => $tipo,
			"idmodalidad" => $_POST["idmodalidad"],
			"sitedist" => $_POST["sitedist"],
			"cuotasdist" => $_POST["cuotasdist"],
			"impdist" => $_POST["impdist"]
		)));
	}
	else {
		$data = array_merge($data, array("split" => array(
			"tipo" => $tipo,
			"idmodalidad" => $_POST["idmodalidad"]
		)));
	}

	$db->updateRecords(array("data" => json_encode($data),"split" => 1),array("id" => $operationid));

	header("Location: index.php");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<title>Administrador</title>
	<meta name="description" content="kleith web site" />
	<meta name="keywords" content="html, css, js, php" />
        <!-- Le styles -->
<link href="css/css.css" media="screen" rel="stylesheet" type="text/css">

<script type="text/javascript" src="js/js.js"></script>
<script type="text/javascript">
	function addIdsite() {
		var newRow = '<tr id="row-'+$("#new-idsite").val()+'" class="row-dist"><td id="idsite-'+$("#new-idsite").val()+'" class="idsite">'+$("#new-idsite").val()+'</td><td id="amount-'+$("#new-idsite").val()+'" class="amount">'+$("#new-amount").val()+'</td><td id="payments-'+$("#new-idsite").val()+'" class="payments">'+$("#new-payments").val()+'</td><td><a id="delete-'+$("#new-idsite").val()+'" class="btn error" onclick="removeIdsite(\''+$("#new-idsite").val()+'\')">Quitar comercio</a></td></tr>';
		$("#dist-body").append(newRow);
		$(".input-new").val('');
	}

	function removeIdsite(idsiteToRemove) {
		$("#new-idsite").val($("#idsite-"+idsiteToRemove).text());
		$("#new-amount").val($("#amount-"+idsiteToRemove).text());
		$("#new-payments").val($("#payments-"+idsiteToRemove).text());
		$("#row-"+idsiteToRemove).remove();
	}

	function submitData() {
		sitedist = "";
		cuotasdist = "";
		impdist = "";
		$(".row-dist").each(function(index, row){
			if (index) {
				sitedist += "#";
				cuotasdist += "#";
				impdist += "#";
			}
			sitedist += $(row).children(".idsite").text();
			cuotasdist += $(row).children(".payments").text();
			impdist += $(row).children(".amount").text();
		})
		$("#sitedist").val(sitedist);
		$("#cuotasdist").val(cuotasdist);
		$("#impdist").val(impdist);

		$('#activeform').submit();
		return  false;
	}
</script>

 <style>
.ui-tooltip {
padding: 10px 20px;
color: black;
background-color:white;
width: 200px;
text-align:center;
border-radius: 20px;
font: bold 14px "Helvetica Neue", Sans-Serif;
text-transform: uppercase;
box-shadow: 0 0 7px black;

}
</style>
</head>
<body>
<div id="container">
	<div id="content">
		<div class="w-content">
		  <div class="w-section"></div>
<div id="m-status" style="margin-bottom: 300px">

	<div class="block">
	<form id="activeform" method="POST" action="split.php?ord=<?php echo $operationid; ?>" enctype="multipart/form-data">
		<table id="tablelist" class="full tablesorter">
			<tbody>
			          <tr>
				  <td><b>Opera con Split (IDMODALIDAD)</b></td><td><select name="idmodalidad"><option value="S">S</option><option value="N" >N</option></select></td>
				  </tr>
				  <tr>
				  <td><b>Tipo de Split</b></td><td><select name="tipo"><option value="MontoFijo">Monto fijo</option><option value="Porcentaje" >Porcentual</option></select></td>
			          </tr>
				  <tr>
					<td colspan="2"><b>Distribución de comercios</b>
						<input type="hidden" id="sitedist" name="sitedist" /><input type="hidden" id="impdist" name="impdist" /><input type="hidden" id="cuotasdist" name="cuotasdist" />
						<table class="full tablesorter">
							<thead>
								<tr>
									<th>Comercio</th><th>Importe</th><th>Cuotas</th><th>Acci&oacute;n</th>
								</tr>
							</thead>
							<tbody id="dist-body"></body>
							<tfoot>
								<tr>
									<td><input type="textbox" maxlength="8" name="new-idsite" id="new-idsite"  class="input-new"/></td><td><input type="number" name="new-amount" id="new-amount" class="input-new" step=0.01 value="" /></td><td><input type="number" name="new-payments" id="new-payments" class="input-new" /></td><td><a onclick="addIdsite()" class="btn site">Agregar comercio</a></td>
								</tr>
							</tfoot>
						</table>
				</tbody>
			<tfoot>
			  <tr>
				<td colspan="2"><a href="index.php" class="btn error site">Cancelar</a>&nbsp;&nbsp;&nbsp;<a onclick="submitData()" class="btn site" id="send">Enviar</a></td>
			  </tr>
			</tfoot>
		</table>
	</form>
	</div>
</div>
		</div>
		<div class="clearfix"></div>
	</div>
	<div id="footer">
	</div>
</div>
</body>
</html>
