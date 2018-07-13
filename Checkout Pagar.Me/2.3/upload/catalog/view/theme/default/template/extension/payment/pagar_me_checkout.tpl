<script src="https://assets.pagar.me/checkout/1.1.0/checkout.js"></script>
<style>
    .payment-information{text-align:center;font-size:18px;font-weight:800;letter-spacing:-0.55pt;margin-bottom:10px;}
</style>
<?php if ($text_information) { ?>
    <div class="payment-information"><?php echo $text_information; ?></div>
<?php } ?>

<div class="buttons">
    <div class="pull-right">
        <input type="button" value="<?php echo $texto_botao; ?>" id="button-confirm" class="<?php echo $button_css_class; ?>"
               data-loading-text="Aguarde..."/>
    </div>
</div>

<script>

    $('#button-confirm').on('click', function (e) {

        $.ajax({
            url: 'index.php?route=extension/payment/pagar_me_checkout/submit',
            dataType: 'json',
            success: function (response) {
                // INICIAR A INSTÂNCIA DO CHECKOUT
                // declarando um callback de sucesso
                var checkout = new PagarMeCheckout.Checkout({
                    'customer_data': false,
                    'encryption_key': '<?php echo $encryption_key; ?>', success: function (data) {
                        var input_form = $('<input>').attr({
                            type: 'hidden',
                            name: 'token',
                            value: data.token
                        });
                        var form = $('<form>').attr({
                            action: '<?php echo $url; ?>',
                            id: 'form-pagarme',
                            name: 'form-pagarme',
                            method: 'POST'
                        });
                        form.append(input_form);
                        $('body').append(form);
                        form.submit();
                    }
                });

                // DEFINIR AS OPÇÕES
                // e abrir o modal
                var params = {
                    'buttonText': response['button_text'],
                    'amount': response['amount'],
                    'buttonClass': response['button_class'],
                    'paymentMethods': response['payment_methods'],
                    'cardBrands': response['card_brands'],
                    'maxInstallments': response['max_installments'],
                    'freeInstallments': response['free_installments'],
                    'uiColor': response['ui_color'],
                    customer: response['customer'],
                    items : response['items'],
                    shipping: {
                        name: response['customer_name'],
                        fee: response['fee'],
                        address:{
                            country: response['customer_country'],
                            city: response['customer_address_city'],
                            neighborhood: response['customer_address_neighborhood'],
                            street: response['customer_address_street'],
                            street_number: response['customer_address_street_number'],
                            zipcode: response['customer_address_zipcode'],
                            state: response['customer_address_state']
                            
                        }
                    },
                    billing:{
                        name: response['customer_name'],
                        address:{
                            country: response['customer_country'],
                            city: response['customer_address_city'],
                            neighborhood: response['customer_address_neighborhood'], 
                            street: response['customer_address_street'],
                            street_number: response['customer_address_street_number'],
                            zipcode: response['customer_address_zipcode'],
                            state: response['customer_address_state']        
                        }

                    },
                    'interestRate': response['interest_rate'],
                    'boletoDiscountAmount': response['boleto_discount_amount']
                };
                console.log(params);
                checkout.open(params);
            },
            error: function (xhr, error) {
                console.debug(xhr);
                console.debug(error);
            }
        });
    });

</script>