<?php
// Heading
$_['heading_title']       			= 'Checkout Pagar.me - Desenvolvido por <a target="_blank" href="http://www.santive.com">Santive Tecnologia</a>';

// Text
$_['text_payment']        			= 'Pagamento';
$_['text_success']        			= 'Módulo Checkout Pagar.me atualizado com sucesso!';
$_['text_pagar_me_checkout'] 				= '<a onclick="window.open(\'http://www.pagar.me/\');"><img src="view/image/payment/pagarme.png" alt="Pagar.me" title="Pagar.me" style="border: 1px solid #EEEEEE;" /></a>';


// Entry
$_['entry_nome'] = 'Texto a ser exibido na loja:<br /><span class="help">Ex: Checkout Pagar.Me</span>';
$_['entry_customer_data'] = 'Solicitar dados do cliente:<br /><span class="help">Solicita, ou não, os dados do cliente antes pagar o pedido. Caso "Não", os dados utilizados serão os do cadastro do cliente.</span>';
$_['entry_text_information'] = 'Texto informativo exibido na confirmação do pedido:';
$_['entry_criptografia']         				= 'Chave de criptografia:<br /><span class="help">Encontrada nos dados da sua conta no Dashboard do Pagar.me</span>';
$_['entry_api']         				= 'Chave de API:<br /><span class="help">Encontrada nos dados da sua conta no Dashboard do Pagar.me</span>';
$_['entry_texto_botao']         				= 'Texto para o botão de pagamento:<br /><span class="help">Ex: Pagar agora ou Finalizar Compra';
$_['entry_payment_methods'] = 'Métodos de pagamento aceitos:';
$_['entry_card_brands'] = 'Bandeiras de cartão aceitas:';
$_['entry_max_installments'] = 'Número máximo de parcelas:';
$_['entry_free_installments'] = 'Número de parcelas sem juros:';
$_['entry_max_installment_value'] = 'Valor mínimo por parcela:';
$_['entry_insterest_rate'] = 'Taxa de juros a ser cobrada na transação (%):<br /><span class="help">Ex: 1.99 (utilizar somente número com "." como separador)</span>';
$_['entry_boleto_discount_percentage'] = 'Desconto para boleto (%):<br /><span class="help">Ex: 10 (utilizar somente número)</span>';
$_['entry_ui_color'] = 'Cor primária do checkout:';
$_['entry_button_css_class'] = 'Classe css a ser aplicada no botão:';

$_['entry_order_waiting_payment'] 	= 'Status Aguardando Pagamento:<br /><span class="help">a loja aguarda o pagamento do boleto.</span>';
$_['entry_order_paid'] 	= 'Status Pago:<br /><span class="help">status para pedido pago (serve para boleto ou cartão).</span>';


$_['entry_geo_zone']      			= 'Região geográfica:';
$_['entry_status']        			= 'Situação:';
$_['entry_sort_order']    			= 'Ordenação:';


// Error
$_['error_permission']    		= 'Atenção: Você não possui permissão para modificar o Checkout Pagar.me!';
$_['error_criptografia']         		= 'Digite a chave de criptografia';
$_['error_dias_vencimento']         		= 'Digite o número de dias de vencimento';
$_['error_api']         		= 'Digite a chave de API';
$_['error_nome']         		= 'Digite um texto para exibição na loja';
$_['error_payment_methods']         		= 'Ao menos um método de pagamento deve ser selecionado';
$_['error_card_brands']         		= 'Ao menos uma bandeira deve ser selecionada (mesmo que você não vá utililizar o meio de pagamento cartão de crédito)';
?>