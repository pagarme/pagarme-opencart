<link rel="stylesheet" href="<?php echo $stylesheet; ?>">
<?php if ($text_information) { ?>
    <div class="payment-information"><?php echo $text_information; ?></div>
<?php } ?>

<div class="dados_cartao">

    <form id="payment_form" method="POST">        

        <!-- Total do pedido -->
        <input type="hidden" name="totalValue" id="totalValue" value="<?php echo $total; ?>" >

        <div class="input-block-float">
            <label for="card_number">Número do cartão</label>
            <input type="text" id="card_number"/>
        </div>

        <div class="input-block-float">
            <!-- aqui colocar um tooltip com uma imagem mostrando onde fica o CVV de um cartão de crédito -->

            <label for="card_cvv" id="label-cvc">CVV <span id="tool-tip-cvc"><i class="fa fa-question-circle"></i> <span id="tool-tip-content"><img src="catalog/view/theme/default/image/bancos/cartao-cvc.png" alt=""></span></span></label>
            <input type="text" id="card_cvv" size="4" placeholder="CVV" maxlength="4" class="so_numeros" />

        </div>
        <div class="cf"></div>
        <div class="input-block">
            <label>Validade do cartão</label>
            <input type="text" id="card_expiration_month" size="2" maxlength="2" placeholder="MM" class="so_numeros"/>
            <input type="text" id="card_expiration_year" size="4" placeholder="AAAA" maxlength="4" class="so_numeros" />
        </div>

        <div class="input-block">
            <label for="card_holder_name">Nome impresso no cartão</label>
            <input type="text" id="card_holder_name" value="<?php echo $nome_cartao ?>" />
        </div>

        <div id="installmentsWrapper">            
            <div class="input-block">
                <label for="installmentQuantity">Parcelamento</label>
                <select name="installments" id="installments">
                    <?php foreach ($parcelas['installments'] as $parcela): ?>
                    <option value="<?php echo $parcela['installment'] ?>"><?php echo $parcela['installment'] ?>x de R$ <?php echo substr_replace((string)$parcela['installment_amount'], ',', -2, 0); ?></option>
                    <?php endforeach; ?>
                </select>                
            </div>
        </div>

    </form>
</div>

<div class="buttons">
    <div class="right"><a id="button-confirm" class="button disabled"><span><?php echo $button_confirm; ?></span></a><span id="aguardando">Aguarde...</span></div>
</div>
<style>
    #aguardando{
        display: none;
    }

    #installmentsWrapper{
        display: none;
    }
</style>

<?php if (!$this->config->get('dados_status')): ?>
    <script type="text/javascript" src="catalog/view/javascript/mask.js"></script>
<?php endif; ?>

<script type="text/javascript"><!--
    /* Máscaras dos inputs do cartão */
    $("#card_number").livequery(function () {
        $(this).mask("9999999999999999");
    });
    $("#card_cvv").livequery(function () {
        $(this).mask("999?9");
    });
    $("#card_expiration_month").livequery(function () {
        $(this).mask("99");
    });
    $("#card_expiration_year").livequery(function () {
        $(this).mask("9999");
    });

    $('#button-confirm').bind('click', function () {

        /*Função Pagar.me*/
        PagarMe.encryption_key = "<?php echo $this->config->get('pagar_me_cartao_criptografia'); ?>";

        var form = $("#payment_form");

        //form.submit(function (event) { // quando o form for enviado...
        // inicializa um objeto de cartão de crédito e completa
        // com os dados do form
        var creditCard = new PagarMe.creditCard();
        creditCard.cardHolderName = $("#payment_form #card_holder_name").val();
        creditCard.cardExpirationMonth = $("#payment_form #card_expiration_month").val();
        creditCard.cardExpirationYear = $("#payment_form #card_expiration_year").val();
        creditCard.cardNumber = $("#payment_form #card_number").val();
        creditCard.cardCVV = $("#payment_form #card_cvv").val();

        // pega os erros de validação nos campos do form
        var fieldErrors = creditCard.fieldErrors();

        //Verifica se há erros
        var hasErrors = false;
        for (var field in fieldErrors) {
            hasErrors = true;
            break;
        }

        if (hasErrors) {
            // realiza o tratamento de errors
            alert("Verifique se os dados informados estão corretos. Qualque rproblema entre em contato com a loja.");
            $('#button-confirm').show();
        } else {
            //console.log("oi")
            // se não há erros, gera o card_hash...
            creditCard.generateHash(function (cardHash) {
                // ...coloca-o no form...
                form.append($('<input type="hidden" id="card_hash" name="card_hash">').val(cardHash));
                // e envia o form
                $.ajax({
                    type: 'POST',
                    url: 'index.php?route=payment/pagar_me_cartao/payment',
                    dataType: 'json',
                    data: {amount: $("#totalValue").val(), card_hash: $("#card_hash").val(), installments: $("#installments").val()},
                    success: function (response) {
                        if (response['error']) {
                            alert('Ocorreu um erro inesperado. Por favor contate a loja.')
                        } else if (response['success']) {
                            // alert(response['success']);
                            //$.colorbox({href: response['success']});
                            //window.open(response['success']);
                            location = '<?php echo $url; ?>';
                        } else {
                            location = '<?php echo $url2; ?>';
                        }
                    }
                });
            });
        }
    });
//--></script>
</script>

