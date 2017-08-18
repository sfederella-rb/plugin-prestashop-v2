

{block name="page_content"}
<link rel="stylesheet" type="text/css" href="{$content_dir}modules/todopago/css/form_todopago.css">
<link rel="stylesheet" type="text/css" href="{$content_dir}modules/decidir/css/select_paymentmethod.css">
<script language="javascript" src="{$jsLinkForm}"></script>

{capture name=path}
  <a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" title="{l s='Go back to the Checkout' mod='formulario todopago'}">{l s='Checkout' mod='formulario todopago'}</a><span class="navigation-pipe">{$navigationPipe}</span>Formulario de Pago
{/capture}

<!DOCTYPE HTML>
<html>
<head>
<script src="https://live.decidir.com/static/v1/decidir.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<meta charset="utf-8" />
</head>
<style>
  #loader{
    background-image: url({$url_base}modules/decidir/imagenes/loader.gif);
    width:30px;
    height:30px;
    border:1px solid 000;
    float:left;
    margin:2px 0 0 8px;
    display:none;
  }

  input{
    width:100%;
    display: block;
    height:40px;
  }

  select{
    display: block;
    height:40px;
    margin:0 5px 0 0;
  }

  button{
    margin:0 5px 0 5px !important;
  }

  label{
    margin:10px 0 0 0;
    display:block;
  }

  input[type="text"]{
    width:none;
  }

  #warning{
    display:none;
  }

  #formbox{
    margin:0 0 0px 27px;
  }

  #formbox form .row {
    margin-bottom: 10px;
  }

  #form_normal{
    width:46%;
  }

  #form_token{
    width:46%; 
  }

  .formbox form button[type="submit"] {
    border: 1px solid #54c7c3;
    padding: 6px;
    border-radius: 5px;
    background: #54c7c3;
    color: #fff;
    font-size: 16px;
    width: 10% !important;
    margin-left: 25%;
    cursor: pointer;
  }

  #CardSecurityCodeHelper {
    background-color: #f2f2f2;
    color: #5e656b;
    display: inline-block;
    border: 1px solid #c7cbce;
    border-left: 0px;
    height: 40px;
    padding: 5px 4px;
    border-radius: 0px 5px 5px 0px;
    margin:0 0px 0 0 !important;
  }

  #CardExpirationYear{
    margin:5px 0px 0 -12px;
  }

  #security_code{
    width: 5%;
  }

  #cvc{
    width: 16%; 
  }

  .left{
    float:left;
  }

  .clear{
    clear: both;
  }

  #plan_selection{
    width:40%;
  }

  #wrap_form{
    width:50%;
  }

  .divCardExpirationDate{
    display: inline
    border:1px solid #000;
    float:left;
  }

  .divCardExpirationDateField{
    display: inline
    border:1px solid #000;
    float:left;
  }

  .divCardExpirationDCVC{
    display: inline
    border:1px solid #000;
    float:left;
    width:16%;
  }

  #new_card, #tokens_cards{
    width: auto;
    /* display: block; */
    height: 40px;
  }  

  .label_chose_card{
    margin:14px 0 0 8px;
  }

  /*#cvcfield{
    width:29%;
  }*/

  /*
  @media (max-width: 1000px) {
    #plan_selection {
      float:none;
    }

    #wrap_form{
      float:none;
    }
  }

  @media (min-width: 1001px) {
    #plan_selection {
      float:left;
    }

    #wrap_form{
      float:left;
    }
  }
  */
  #confirm_button_token, #confirm_button_norm{
    padding: 0 10px 0 12px;
    width:75px !important;
  }

</style>
<script type="text/javascript">
  const publicApiKey = "{$publicKey}";
  const urlSandbox = "{$endpoint}";

  const decidirSandbox = new Decidir(urlSandbox);
  decidirSandbox.setPublishableKey(publicApiKey);
  decidirSandbox.setTimeout(0);//se configura sin timeout

  //funcion para manejar la respuesta
  function sdkResponseHandler(status, response) {
    if(status == '200' ||status == '201'){
        token=response.id;
        pmethod=$("select#payment_method_select option:checked").val();
        entity=$("select#entities_select option:checked").val();
        installment=$("select#installment_select option:checked").val();
        intallmenttype=$("#instype").val();

        window.location.href = "index.php?paso=2&token="+response.id+"&pmethod="+pmethod+"&entity="+entity+"&installment="+installment+"&intallmenttype="+intallmenttype+"&fc=module&module=decidir&controller=payment";
    
    }else {
      console.log('ERROR - STATUS: ' + status + ' - Respuesta: ' +response );
    }
  }
    
  //funcion de invocacion con sdk
  function sendFormFunc(idname) {
    //event.preventDefault();
    var form = document.querySelector('#'+idname);
    decidirSandbox.createToken(form, sdkResponseHandler);//formulario y callback

    return false;
  }

  $(document).ready(function(){
    $("#confirm_button_norm").click(function(){
      form_id = $(this).closest("form").attr('id');
      if(validate()){
        sendFormFunc(form_id);  
      }
    })

    $("#confirm_button_token").click(function(){
      form_id = $(this).closest("form").attr('id');
      if(validate()){
        sendFormFunc(form_id);  
      }
    })

    $('input[type=radio][name=new_card]').change(function() {
      $(this).prop("checked", false);
      $("#form_normal").show();
      $("#form_token").hide(); 
    });

    $('input[type=radio][name=tokens_cards]').change(function() {
      $(this).prop("checked", false);
      $("#form_normal").hide();
      $("#form_token").show(); 
    });

    //seleccion de planes
    var urlAjax = "{$url_base}modules/decidir/ajax/front/ajaxpaymentselection.php";
    var pmethodselected = null;
    var entityselected = null;

    init();
    //pmethod change
    $("#payment_method_select").change(function(){
      clearEntityList()
      clearInstallment()
      pmethodselected = $(this).val();
      response = ajaxService("entities_select", urlAjax, "entities", true, null, null);

      if(response){
        showEntity();
        $(".loader.lentities").hide();
      }
    }); 

    //entity change
    $("#entities_select").change(function(){
      clearInstallment()

      entityselected = $(this).val();

      response = ajaxService("installment_select", urlAjax, "installment", false, pmethodselected, entityselected);

      if(response){
        showInstallment();
        $(".loader.linstallment").hide();
      }
    });

    //installment change
    $("#installment_select").change(function(){
      if($(this).val()){
        $("#confirm_button_token").removeAttr("disabled");
      }
    });

    //-------------------------------------------------------------------

    function init(){
      ajaxService("payment_method_select", urlAjax, "pmethod", false, null, null);
      $(".loader.lpayment").hide();
      $("#confirm_button_token").attr("disabled", "disabled");
      $("#form_normal").hide();
      $("#form_token").hide(); 
    }

    function clearmethodList(){
      $("#payment_method_select").empty();
      $("#paymentm_selectbox").hide();
      $("#confirm_button_token").attr("disabled", "disabled");
    }

    function clearEntityList(){
      $("#entities_select").empty();
      $("#entitybox").hide();
      $("#confirm_button_token").attr("disabled", "disabled");
    }

    function clearInstallment(){
      $("#installment_select").empty();
      $("#confirm_button_token").attr("disabled", "disabled");
    }

    function showEntity(){
      $("#entitybox").show();
    }

    function showInstallment(){
      $("#installmentbox").show();
    } 

    //general ajax function
    function ajaxService(id, url_ajax, stype, others, pmethodselected, entityselected){
      loader_id = id.split("_");
      loaderid = ".loader.l"+loader_id;
      $(loaderid).show();

      $.ajax({
        url: url_ajax,
        type: 'post',
        dataType: "json",
        data:{
          selecttype: stype,
          method_selected: pmethodselected,
          entity_selected: entityselected,
          total: {$total}
        },
        success: function(dataResponse){

          $("#"+id)[0].options.length = 0;
          $("#"+id).append("<option value=''>---Elija una opci&oacute;n---</option>");

          $.each(dataResponse.data, function (key, data) {
              $("#"+id).append("<option value='"+data.id+"'>"+data.name+"</option>");
              interesttype = data.type;
          })

          if(others){
            $("#"+id).append("<option value='0'>Otros</option>");
          } 

          if(stype == "installment"){
            $("#instype").val(dataResponse.type);
          }

        },
        error: function (e, status){
          console.log('Error en servicio'+ status);
          console.log(e);
        } 
      });

      return true;
    }

    //---------------------------
    //seccion formulario gettoken
    $.ajax({
      url: "{$url_base}modules/decidir/ajax/front/ajaxpaymentselection.php",
      type: 'post',
      dataType: "json",
      data:{
        userid: "{$email}"
      },
      success: function(dataResponse){
        if(dataResponse.type){
          $("#form_token").show(); 

          console.log(dataResponse.data);

          $.each(dataResponse.data, function (key, data) {
              if(data != 0){
                $("#tokenselectlist").append("<option value='"+data.id+"'>"+data.desc+"</option>");
              }
          })
        }else{
          $("#form_normal").show();
        }
      },
      error: function (e, status){
        console.log('Error en servicio'+ status);
        console.log(e);
      } 
    });

    function validate(){
      pmethodvalue = $("#payment_method_select").val();
      entityvalue = $("#entities_select").val();
      installmentvalue = $("#installment_select").val();

      if(pmethodvalue != "" && entityvalue != "" && installmentvalue != ""){
        return true;

      }else{
        $("#warning").html("Debe seleccionar un plan");
        $("#warning").show();
        return false;
      }
    }
  });

</script>

<body>
  <div id="formbox">
    <div id="plan_selection">
      <div id="warning">123123</div> 
      <fieldset>
        <ul>
          <li>
            <label class="">Medio de Pago</label>
            <div id="select_one">
              <select name="paymethod" id="payment_method_select" class="selectpicker fixed-width-lg">
              </select>
            </div>
          </li>
          <li>
            <label class="">Entidad</label>
            <div>
              <select name="entities" id="entities_select" class="selectpicker fixed-width-lg">
                <option value=''>---Elija una opci&oacute;n---</option>
              </select>
            </div>
          </li>
          <li>
            <label class="">Cuotas</label>
            <div>
              <select name="installment" id="installment_select" class="selectpicker fixed-width-lg">
                <option value=''>---Elija una opci&oacute;n---</option>
              </select>
            </div>
            <input type="hidden" id="instype" name="installment_type" value="0"/>
          </li>
        </ul>
      </fieldset>    
    </div>
    <div id="wrap_form">
      <form action="#" id="form_normal">
        <fieldset>
          <ul>
            <li>
              <label for="card_number">Numero de tarjeta</label>
              <input type="text" data-decidir="card_number" placeholder="" value=""/>
            </li>
            <li>
              <div>
                <div class="divCardExpirationDate">
                  
                  <div class="divCardExpirationDateField">
                    <label for="card_expiration_month" style="width:5%;">Vencimiento</label>
                    <select id="CardExpirationMonth" data-decidir="card_expiration_month">
                      <option>MM</option><option value="01">01</option><option value="02">02</option><option value="03">03</option><option value="04">04</option><option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option>
                    </select>
                  </div>
                  <div class="divCardExpirationDateField" style="margin-top:22px;">
                    <select id="CardExpirationYear" data-decidir="card_expiration_year">
                      <option>AAAA</option><option value="17">2017</option><option value="18">2018</option><option value="19">2019</option><option value="20">2020</option><option value="21">2021</option><option value="22">2022</option><option value="23">2023</option><option value="24">2024</option><option value="25">2025</option><option value="26">2026</option><option value="27">2027</option>
                    </select>
                  </div>
                  <div class="divCardExpirationDCVC" style="margin-left:10px;">
                    <label for="security_code">CVC</label>
                    <input type="text"  data-decidir="security_code" placeholder="" id="cvcfield" value="" />
                  </div>
                  
              </div> 
              <div class="clear"></div>
            </li>
            <li>
              <label for="card_holder_name">Nombre del titular</label>
              <input type="text" data-decidir="card_holder_name" placeholder="TITULAR" value=""/>
            </li>
            <li>
              <label for="card_holder_doc_type">Tipo de documento</label>
              <select data-decidir="card_holder_doc_type">
                <option value="dni">DNI</option>
              </select>
            </li>
            <li>
              <label for="card_holder_doc_type">Numero de documento</label>
              <input type="text"data-decidir="card_holder_doc_number" placeholder="" value=""/>
            </li>
            <li>
              <input type="radio" name="tokens_cards" id="tokens_cards" class="left">
              <label for="security_code" class="label_chose_card left">Tarjetas Tokenizadas</label>  
              <div class="clear"></div>
            </li>
          </ul>
          <input type="button" class="button btn btn-default button-medium" id="confirm_button_norm" value="Pagar">
        </fieldset>
      </form>
      <form action="#" id="form_token" onsubmit="sendFormFunc()">
        <fieldset>
          <ul>
            <li>
              <label for="token">Tarjeta tokenizada</label>
              <select data-decidir="token" id="tokenselectlist">
              </select>
            </li>
            <li>
              <label for="security_code">Codigo de seguridad</label>
              <input type="text"  data-decidir="security_code" id="cvc" placeholder="" value="" />
            </li>
            <li>
              <input type="radio" name="new_card" id="new_card" class="left">
              <label for="security_code" class="label_chose_card left">Nueva Tarjeta</label>  
              <div class="clear"></div>
            </li>
          </ul>
          <input type="button" class="button btn btn-default button-medium" id="confirm_button_token" value="Pagar">
        </fieldset>
      </form>
    </div>
  </div>
</body>
</html>

{/block}