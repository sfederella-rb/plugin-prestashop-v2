{capture name=path}
    <a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" title="{l s='Go back to the Checkout' mod='formulario todopago'}">{l s='Checkout' mod='formulario todopago'}</a><span class="navigation-pipe"></span>Formulario de Pago
{/capture}

<!DOCTYPE HTML>
<html>
<head>
    <script src="https://live.decidir.com/static/v1/decidir.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <meta charset="utf-8" />
    <link rel="stylesheet" type="text/css" href="{$url_base}modules/decidir/css/form_decidir.css">
    <script language="javascript" src="{$jsLinkForm}"></script>
</head>
<style>
    
    .loader{
        background-image: url({$url_base}modules/decidir/imagenes/loader.gif);
        width:30px;
        height:30px;
        border:1px solid 000;
        float:left;
        margin:5px 0 0 8px;
        display:none;
    }

    h1{
        color: #5d6f78;
        font-size: 23px;
        margin: 10px 0 22px 0;
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
        text-align: left !important;
    }

    input[type="text"]{
        width:none;
    }

    #warning{
        display:none;
        background-color: #FFF3D7;
        color:#FCC94F;
        border:none;
        border-left:3px solid #FCC94F;
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
        margin:8px 0px 0 -12px;
    }

    #security_code{
        width: 5%;
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
        width:28%;
        margin:0 0 0 10px;
    }

    #new_card, #tokens_cards{
        width: auto;
        /* display: block; */
        height: 40px;
    }

    .label_chose_card{
        margin:14px 0 0 8px;
    }

    #cvcfield{
        width: 63% !important;
    }

    #cvc_help{
        width:17px;
        margin: 0 7px 0 0;
        padding:0 0px 0 0;
    }

    .left{ float:left; }
    .right{ float:right; }
    .clear{ clear:both; }

    .security{
        margin:0 0 0 0;
    }

    #cvc{
        width: 16%;
    }

    #confirm_button_token, #confirm_button_norm{
        padding: 0 10px 0 12px;
        width:75px !important;
        float:left;
    }

    .elemento {
        left: 506px;
        top: 251px;
        display: block;
    }

    #boxSecCodeHelpContainer {
        position: absolute;
        display: none;
        font-family: Roboto,sans-serif;
        border:1px solid #c2c2c2;
        /*left: 236px;
        top: 307px;*/
        width:296px !important;
        background-color: white;
    }

    .barcode{
        height: 28px;
        background-color: #5e656b;
        margin: 24px 0 0 0;
        border:1px solid #c2c2c2;
    }

    .cardcode{
        color:#8dd983;
        margin:10px 0 5px 5px;
    }

    .signature{
        margin:10px 0 5px 24px;
    }

    .explanation{
        margin: 0 0 10px 13px;
    }

    .paybox {
        font-family: Arial,sans-serif;
        font-size: 14px;
    }

</style>
<script type="text/javascript">
    const publicApiKey = "{$publicKey}";
    const urlSandbox = "{$endpoint}";

    const decidirSandbox = new Decidir(urlSandbox);
    decidirSandbox.setPublishableKey(publicApiKey);
    decidirSandbox.setTimeout(0);//se configura sin timeout

    //funcion para manejar la respuesta
    function sdkResponseHandler(status, response){
        if(status == '200' || status == '201'){
            pmethod=$("select#payment_method_select option:checked").val();
            entity=$("select#entities_select option:checked").val();
            installment=$("select#installment_select option:checked").val();
            intallmenttype=$("#instype").val();

            window.location.href = "index.php?paso=2&token="+response.id+"&pmethod="+pmethod+"&entity="+entity+"&installment="+installment+"&intallmenttype="+intallmenttype+"&bin="+response.bin+"&fc=module&module=decidir&controller=payment";

        }else{
            $.each(response.error, function (key, data) {
                $("#warning ul").append("<li>"+data.error.message+"</li>");
            });

            $("#warning").show(50);
            $(".loader").hide();
        }
    }

    //funcion de invocacion con sdk
    function sendFormFunc(idname) {
        //event.preventDefault();
        $("#warning").hide(50);
        $(".loader").show();

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

        $("#cvc_help").mouseover(function() {
            var position = $("#cvc_help").position();
            positionleft = position.left + $("#cvc_help").width() + 5;

            $("#boxSecCodeHelpContainer").css('top',position.top);
            $("#boxSecCodeHelpContainer").css('left',positionleft);
            $("#boxSecCodeHelpContainer").show();
        })
            .mouseout(function() {
                $("#boxSecCodeHelpContainer").hide();
            });

        $("#confirm_button_token").click(function(){
            form_id = $(this).closest("form").attr('id');
            if(validate()){
                $(".loader").show();
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
            clearEntityList();
            clearInstallment();
            hideForm();

            pmethodselected = $(this).val();

            response = ajaxService("entities_select", urlAjax, "entities", true, null, null);

            if(response){
                showEntity();
                showInstallment();
                $(".loader.lentities").hide();
            }
        });

        //entity change
        $("#entities_select").change(function(){
            clearInstallment();
            hideForm();
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
                hideForm();
                pmethod = $("#payment_method_select").val();
                getTokens(pmethod);
            }
        });

        //-------------------------------------------------------------------

        function init(){
            ajaxService("payment_method_select", urlAjax, "pmethod", false, null, null);
            $(".loader.lpayment").hide();
            $("#confirm_button_token").attr("disabled", "disabled");
            $("#form_token").hide();
            $("#form_normal").hide();

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
            $("#installment_select").html("").append("<option value=''>Seleccion&eacute; una opci&oacute;n </option>");
;
            $("#confirm_button_token").attr("disabled", "disabled");
        }

        function showEntity(){
            $("#entitybox").show();
        }

        function showInstallment(){
            $("#installmentbox").show();
        }

        function showForm(){
            $("#installmentbox").show();
            $("#form_token").show();
            $("#form_normal").show();
        }

        function hideForm(){
            $("#installmentbox").show();
            $("#form_token").hide();
            $("#form_normal").hide();
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
                    if(dataResponse.data.length > 0){
                        $("#"+id)[0].options.length = 0;
                        $("#"+id).append("<option value=''>Seleccion&eacute; una opci&oacute;n </option>");

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


                    }else{
                       $("#"+id).html("").append("<option value=''> Sin promociones</option>");
                    }
                }
            });

            return true;
        }

        //---------------------------
        //seccion formulario gettoken
        function getTokens(pmethod){
            $.ajax({
                url: "{$url_base}modules/decidir/ajax/front/ajaxpaymentselection.php",
                type: 'post',
                dataType: "json",
                data:{
                    userid: "{$id_user}",
                    pmethod: pmethod
                },
                success: function(dataResponse){
                    if(dataResponse.type){
                        $("#form_token").show();
                        $("#form_normal").hide();
                        $("#tokenselectlist").empty();

                        $.each(dataResponse.data, function (key, data) {
                            if(data != 0){
                                $("#tokenselectlist").append("<option value='"+data.id+"'>"+data.desc+"</option>");
                                $("#cvc").removeAttr("disabled");
                            }
                        });

                    }else{
                        $("#form_normal").show();
                        $("#form_token").hide();
                        $("#tokenselectlist").empty();

                        $("#tokenselectlist").append("<option value=''>No existen tarjetas Tokenizadas</option>");
                        $("#cvc").attr("disabled","disabled");
                    }
                },
                error: function (e, status){
                    console.log('Error en servicio'+ status);
                    console.log(e);
                }
            });

        }

        function validate(){
            pmethodvalue = $("#payment_method_select").val();
            entityvalue = $("#entities_select").val();
            installmentvalue = $("#installment_select").val();

            $('#warning ul li').remove();

            if(pmethodvalue != "" && entityvalue != "" && installmentvalue != ""){
                return true;
            }else{
                $("#warning ul").append("<li>Debe seleccionar un plan</li>").show();
                $("#warning").show(50);
                return false;
            }
        }
    });
</script>

<body>
<div id="formbox">
    <div id="plan_selection">
        <div id="warning" class="alert alert-warning">
            <ul>
                <li class="bullet">Campo CVC incorrecto.</li>
            </ul>
        </div>
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
                            <option value=''> Seleccion&eacute; una opci&oacute;n </option>
                        </select>
                    </div>
                </li>
                <li>
                    <label class="">Cuotas</label>
                    <div>
                        <select name="installment" id="installment_select" class="selectpicker fixed-width-lg">
                            <option value=''> Seleccion&eacute; una opci&oacute;n </option>
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
                        <input type="text" data-decidir="card_number" placeholder="•••• •••• •••• ••••" value=""/>
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
                                <div class="divCardExpirationDCVC">
                                    <label for="security_code">CVC</label>
                                    <input type="text"  data-decidir="security_code" placeholder="•••" class="left" id="cvcfield" value="" maxlength="4"/>
                                    <input type="button" placeholder="" class="right" id="cvc_help" value="?" />
                                </div>
                            </div>
                            <div class="clear"></div>
                    </li>
                    <li>
                        <label for="card_holder_name">Nombre del titular</label>
                        <input type="text" data-decidir="card_holder_name" placeholder="Ingrese su nombre" value=""/>
                    </li>
                    <li>
                        <label for="card_holder_doc_type">Tipo de documento</label>
                        <select data-decidir="card_holder_doc_type">
                            <option value="dni">DNI</option>
                        </select>
                    </li>
                    <li>
                        <label for="card_holder_doc_type">Numero de documento</label>
                        <input type="text" data-decidir="card_holder_doc_number" placeholder="Ingrese su DNI" value=""/>
                    </li>
                    <li>
                        <input type="radio" name="tokens_cards" id="tokens_cards" class="left">
                        <label for="security_code" class="label_chose_card left">Tarjetas Guardadas</label>
                        <div class="clear"></div>
                    </li>
                </ul>
                <input type="button" class="button btn btn-default button-medium" id="confirm_button_norm" value="Pagar">
                <div class="left loader"></div>
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
                        <input type="text"  data-decidir="security_code" class="left" id="cvc" placeholder="" value="" maxlength="4"/>
                        <input type="button" placeholder="" class="left" id="cvc_help" value="?" />
                    </li>
                    <li>
                        <input type="radio" name="new_card" id="new_card" class="clear left">
                        <label for="security_code" class="label_chose_card left">Nueva Tarjeta</label>
                        <div class="clear"></div>
                    </li>
                </ul>
                <input type="button" class="button btn btn-default button-medium" id="confirm_button_token" value="Pagar">
                <div class="loader"></div>
            </fieldset>
        </form>
    </div>
</div>
</body>
</html>
<div id="boxSecCodeHelpContainer">
    <div id="boxSecCodeHelp" style="display: block;">
        <div id="" class="barcode"></div>
        <div id="" class="signature left">Firma y digitos de la tarjeta #</div>
        <div id="" class="cardcode left">123</div>
        <div id="" class="clear explanation">En la mayoría de las tarjetas, los 3 dígitos del código de seguridad se encuentran detrás, a la derecha de la firma.</div>
    </div>
</div>
