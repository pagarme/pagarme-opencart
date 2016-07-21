<?php
require_once(DIR_SYSTEM . '/library/cielo.php');
class ControllerPaymentCielo extends Controller {
    private $error;
    protected $_valida_cartao = array(
        'visa' => array(
            'regexp' => '/^4[0-9]{15,18}$/',
            'requiresCVV2' => true
        ),
        'amex' => array(
            'regexp' => '/^(34|37)[0-9]{13}$/',
            'requiresCVV2' => true
        ),
        'mastercard' => array(
            'regexp' => '/^5[1-5]{1}[0-9]{14}$/',
            'requiresCVV2' => true
        ),
        'diners' => array(
            'regexp' => '/^(30|36|38|55)[0-9]{12}([0-9]{2})?$/',
            'requiresCVV2' => false
        ),
        'discover' => array(
            'regexp' => '/^6011[0-9]{12}$/',
            'requiresCVV2' => true
        ),
        'elo' => array(
            'regexp' => '/^6[0-9]{15,18}$/',
            'requiresCVV2' => true
        ),
    );
    private function isOk($status) {
        return !empty($status) && !in_array((int)$status, array(
            \Tritoq\Payment\Cielo\Transacao::STATUS_ERRO,
            \Tritoq\Payment\Cielo\Transacao::STATUS_EM_AUTENTICACAO,
            \Tritoq\Payment\Cielo\Transacao::STATUS_EM_CANCELAMENTO,
            \Tritoq\Payment\Cielo\Transacao::STATUS_ANDAMENTO,
            \Tritoq\Payment\Cielo\Transacao::STATUS_CRIADA,
            \Tritoq\Payment\Cielo\Transacao::STATUS_NAO_AUTENTICADA,
            \Tritoq\Payment\Cielo\Transacao::STATUS_NAO_AUTORIZADA,
        ));
    }
    private function juroComposto($capital, $tempo, $juros, $tipo = 0) {
        $juros = !empty($juros) ? preg_replace('/[^0-9\s]+/', '.', $juros) : 0;
        settype($juros, 'float');
        $m = $capital * pow((1 + ($juros / 100)), $tempo);
        if ($tipo == 0) {
            return $m;
        } else {
            return ($m / $tempo);
        }
    }
    public function index() {
        $this->language->load('payment/cielo');
        $data['text_barra'] = $this->language->get('text_barra');
        $data['text_teste'] = $this->language->get('text_teste');
        $data['text_pagamento'] = $this->language->get('text_pagamento');
        $data['text_info'] = $this->language->get('text_info');
        $data['text_loading'] = $this->language->get('text_loading');
        $data['teste'] = $this->config->get('cielo_teste');
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['action'] = $this->url->link('payment/cielo/processar', '', 'SSL');
        $order_info  = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $valor_total = number_format((float)$order_info['total'],2);
        $valor_total = str_replace(",","",$valor_total);
        $data['total'] = $this->currency->format((float)$valor_total);
        $data['cartoes'] = array();
        if ($this->config->get('cielo_cartao_visa') == 1) {
            $data['cartoes']['visa']['nome'] = 'Visa';
            $data['cartoes']['visa']['imagem'] = 'image/cielo/visa.jpg';
            $data['cartoes']['visa']['parcelas'] = $this->config->get('cielo_parcela_maximo');
        }
        if ($this->config->get('cielo_cartao_mastercard') == 1) {
            $data['cartoes']['mastercard']['nome'] = 'Mastercard';
            $data['cartoes']['mastercard']['imagem'] = 'image/cielo/mastercard.jpg';
            $data['cartoes']['mastercard']['parcelas'] = $this->config->get('cielo_parcela_maximo');
        }
        if ($this->config->get('cielo_cartao_diners') == 1) {
            $data['cartoes']['diners']['nome'] = 'Diners';
            $data['cartoes']['diners']['imagem'] = 'image/cielo/diners.jpg';
            $data['cartoes']['diners']['parcelas'] = $this->config->get('cielo_parcela_maximo');
        }
        if ($this->config->get('cielo_cartao_discover') == 1) {
            $data['cartoes']['discover']['nome'] = 'Discover';
            $data['cartoes']['discover']['imagem'] = 'image/cielo/discover.jpg';
            $data['cartoes']['discover']['parcelas'] = $this->config->get('cielo_parcela_maximo');
        }
        if ($this->config->get('cielo_cartao_elo') == 1) {
            $data['cartoes']['elo']['nome'] = 'Elo';
            $data['cartoes']['elo']['imagem'] = 'image/cielo/elo.jpg';
            $data['cartoes']['elo']['parcelas'] = $this->config->get('cielo_parcela_maximo');
        }
        if ($this->config->get('cielo_cartao_amex') == 1) {
            $data['cartoes']['amex']['nome'] = 'Amex';
            $data['cartoes']['amex']['imagem'] = 'image/cielo/amex.jpg';
            $data['cartoes']['amex']['parcelas'] = $this->config->get('cielo_parcela_maximo');
        }
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/cielo.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/payment/cielo.tpl', $data);
        } else {
            return $this->load->view('default/template/payment/cielo.tpl', $data);
        }
    }
    protected function validar() {
        $this->request->post['creditcard_ccno'] = preg_replace('/\s*/', '', $this->request->post['creditcard_ccno']);
        if (!isset($this->request->post['formaPagamento'])) {
            $this->error['formaPagamento'] = $this->language->get('error_pagamento');
        }
        if (!isset($this->request->post['creditcard_cctype']) || utf8_strlen(trim($this->request->post['creditcard_cctype'])) < 1) {
            $this->error['creditcard_cctype'] = $this->language->get('error_bandeira');
        } else {
            if (!isset($this->request->post['creditcard_ccno']) || trim($this->request->post['creditcard_ccno']) < 16) {
                $this->error['creditcard_ccno'] = $this->language->get('error_numero');
            } else if(!isset($this->_valida_cartao[$this->request->post['creditcard_cctype']]) || !preg_match($this->_valida_cartao[$this->request->post['creditcard_cctype']]['regexp'], $this->request->post['creditcard_ccno'])) {
                $this->error['creditcard_ccno'] = $this->language->get('error_cartao');
            }
            if($this->_valida_cartao[$this->request->post['creditcard_cctype']]['requiresCVV2']) {
                if (!isset($this->request->post['creditcard_cccvd']) || trim($this->request->post['creditcard_cccvd']) < 3) {
                    $this->error['creditcard_cccvd'] = $this->language->get('error_cod_seg');
                }
            }
        }
        if (!isset($this->request->post['creditcard_name']) || utf8_strlen(trim($this->request->post['creditcard_name'])) < 1) {
            $this->error['creditcard_name'] = $this->language->get('error_nome');
        }
        if (!isset($this->request->post['validade']) || trim($this->request->post['validade']) < 6) {
            $this->error['creditcard_ccexpy'] = $this->language->get('error_validade');
        }
        return !$this->error;
    }
    public function processar() {
        $this->language->load('payment/cielo');
        $this->load->model('checkout/order');
        $this->load->model('payment/cielo');
        $json = array();
        if($this->validar()) {
            try {
                $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
                $valor_total = $order_info['total'];
                $loja = new \Tritoq\Payment\Cielo\Loja();
                $loja
                    ->setNomeLoja(substr($order_info['store_name'], 0, 13))
                    ->setAmbiente(\Tritoq\Payment\Cielo\Loja::AMBIENTE_PRODUCAO)
                    ->setUrlRetorno($this->url->link('payment/cielo/callback'))
                    ->setChave($this->config->get('cielo_chave'))
                    ->setNumeroLoja($this->config->get('cielo_afiliacao'))
                    ->setSslCertificado(DIR_SYSTEM . 'library/Tritoq/Payment/Cielo/ssl/ecommerce.cielo.com.br.crt');
                if ($this->config->get('cielo_teste') == '1') {
                    $loja->setAmbiente(\Tritoq\Payment\Cielo\Loja::AMBIENTE_TESTE)
                        ->setChave(\Tritoq\Payment\Cielo\Loja::LOJA_CHAVE_AMBIENTE_TESTE)
                        ->setNumeroLoja(\Tritoq\Payment\Cielo\Loja::LOJA_NUMERO_AMBIENTE_TESTE);
                }
                $cartao = new \Tritoq\Payment\Cielo\Cartao();
                $cartao
                    ->setNumero($this->request->post['creditcard_ccno'])
                    ->setCodigoSegurancaCartao($this->request->post['creditcard_cccvd'])
                    ->setBandeira($this->request->post['creditcard_cctype'])
                    ->setNomePortador($this->request->post['creditcard_name'])
                    ->setValidade($this->request->post['validade']);
                $transacao = new \Tritoq\Payment\Cielo\Transacao();
                $transacao
                    ->setAutorizar($this->config->get('cielo_autorizacao'))
                    ->setCapturar(\Tritoq\Payment\Cielo\Transacao::CAPTURA_SIM)
                    ->setParcelas(1)
                    ->setProduto(\Tritoq\Payment\Cielo\Transacao::PRODUTO_CREDITO_AVISTA);
                if ( ! $this->config->get('cielo_captura')) {
                    $transacao->setCapturar(\Tritoq\Payment\Cielo\Transacao::CAPTURA_NAO);
                }
                if ($this->request->post["formaPagamento"] == 'A') {
                    $transacao->setProduto(\Tritoq\Payment\Cielo\Transacao::PRODUTO_DEBITO);
                    $transacao->setAutorizar(\Tritoq\Payment\Cielo\Transacao::AUTORIZAR_SOMENTE_AUTENTICADA);
                } else {
                    if ($this->request->post["formaPagamento"] != '1') {
                        $transacao->setProduto($this->config->get('cielo_parcelamento'));
                        $transacao->setParcelas($this->request->post["formaPagamento"]);
                        if ($this->config->get('cielo_parcelamento') == \Tritoq\Payment\Cielo\Transacao::PRODUTO_PARCELADO_LOJA) {
                            $valor_total = $this->juroComposto($valor_total, $this->request->post["formaPagamento"],
                                $this->config->get('cielo_parcela_juros'));
                        }
                    }
                }
                if ($this->config->get('cielo_teste') == '1') {
                    $transacao->setAutorizar(\Tritoq\Payment\Cielo\Transacao::AUTORIZAR_SEM_AUTENTICACAO);
                }
                $pedido = new \Tritoq\Payment\Cielo\Pedido();
                $pedido
                    ->setDataHora(new \DateTime())
                    ->setDescricao('Compra na loja ' . $order_info['store_name'])
                    ->setIdioma(\Tritoq\Payment\Cielo\Pedido::IDIOMA_PORTUGUES)
                    ->setNumero($this->session->data['order_id'])
                    ->setValor(preg_replace('/[^0-9]/', '', number_format($valor_total, 2)));
                if ($this->config->get('cielo_teste') == '1') {
                    $pedido->setValor(preg_replace('/[^0-9]/', '', ceil($valor_total) . '00'));
                }
                $this->load->model('account/customer');
                $this->load->model('account/address');
                $customer_info = $this->model_account_customer->getCustomer($order_info['customer_id']);
                $portador = new \Tritoq\Payment\Cielo\Portador();
                $portador
                    ->setBairro($order_info['payment_address_2'])
                    ->setCep(preg_replace('/[^0-9]/', '', $order_info['payment_postcode']))
                    ->setEndereco($order_info['payment_address_1']);
                if ($this->config->get('cielo_analise_risco') == '1' && !empty($customer_info['address_id'])) {
                    $customer_address_info = $this->model_account_address->getAddress($customer_info['address_id']);
                    $country_code = $this->model_payment_cielo->getCountryCodeById($order_info['payment_country_id']);
                    $zone_code = $this->model_payment_cielo->getZoneCodeById($order_info['payment_zone_id']);
                    $pedidoAnalise = new \Tritoq\Payment\Cielo\AnaliseRisco\PedidoAnaliseRisco();
                    $pedidoAnalise
                        ->setEstado($zone_code)
                        ->setCep(preg_replace('/[^0-9]/', '', $order_info['payment_postcode']))
                        ->setCidade($order_info['payment_city'])
                        ->setEndereco($order_info['payment_address_1'])
                        ->setId($this->session->data['order_id'])
                        ->setPais($country_code)
                        ->setPrecoTotal($valor_total);
                    $country_code = $this->model_payment_cielo->getCountryCodeById($customer_address_info['country_id']);
                    $zone_code = $this->model_payment_cielo->getZoneCodeById($customer_address_info['zone_id']);
                    $cliente = new \Tritoq\Payment\Cielo\AnaliseRisco\ClienteAnaliseRisco();
                    $cliente->senha = '';
                    $cliente->nome = $customer_info['firstname'];
                    $cliente->sobrenome = $customer_info['lastname'];
                    $cliente->endereco = $customer_address_info['address_1'];
                    $cliente->complemento = '';
                    $cliente->cep = preg_replace('/[^0-9]/', '', $customer_address_info['postcode']);
                    $cliente->documento = '';
                    $cliente->email = $customer_info['email'];
                    $cliente->estado = $zone_code;
                    $cliente->cidade = $customer_address_info['city'];
                    $cliente->id = $order_info['customer_id'];
                    $cliente->ip = $order_info['forwarded_ip'];
                    $cliente->pais = $country_code;
                    $cliente->telefone = preg_replace('/[^0-9]/', '', $customer_info['telephone']);
                    /*
                    *
                    * Usando a Análise de Risco
                    *
                    */
                    // Para qualquer ação será revista com ação manual posterior, caso seja de baixo risco, a transação será capturada automaticamente
                    $analise = new \Tritoq\Payment\Cielo\AnaliseRisco();
                    $analise
                        ->setCliente($cliente)
                        ->setPedido($pedidoAnalise)
                        ->setAfsServiceRun(true)
                        ->setAltoRisco(\Tritoq\Payment\Cielo\AnaliseRisco::ACAO_MANUAL_POSTERIOR)
                        ->setMedioRisco(\Tritoq\Payment\Cielo\AnaliseRisco::ACAO_MANUAL_POSTERIOR)
                        ->setBaixoRisco(\Tritoq\Payment\Cielo\AnaliseRisco::ACAO_CAPTURAR)
                        ->setErroDados(\Tritoq\Payment\Cielo\AnaliseRisco::ACAO_MANUAL_POSTERIOR)
                        ->setErroIndisponibilidade(\Tritoq\Payment\Cielo\AnaliseRisco::ACAO_MANUAL_POSTERIOR)
                        ->setDeviceFingerPrintID(md5($this->config->get('config_name')));
                    $service = new \Tritoq\Payment\Cielo\CieloService(array(
                        'portador'  => $portador,
                        'loja'      => $loja,
                        'cartao'    => $cartao,
                        'transacao' => $transacao,
                        'pedido'    => $pedido,
                        'analise'   => $analise
                    ));
                    // Setando o tipo de versão de conexão SSL
                    $service->setSslVersion(4);
                    // Desabilitando a analise de risco
                    $service->setHabilitarAnaliseRisco(true);
                    $gerarToken = false;
                    $checkAvs = $this->config->get('cielo_avs') == '1';
                } else {
                    $service = new \Tritoq\Payment\Cielo\CieloService(array(
                        'portador'  => $portador,
                        'loja'      => $loja,
                        'cartao'    => $cartao,
                        'transacao' => $transacao,
                        'pedido'    => $pedido,
                    ));
                    // Setando o tipo de versão de conexão SSL
                    $service->setSslVersion(4);
                    // Desabilitando a analise de risco
                    $service->setHabilitarAnaliseRisco(false);
                    $gerarToken = false;
                    $checkAvs = $this->config->get('cielo_avs') == '1';
                }
                $service->doTransacao($gerarToken, $checkAvs);
                $urlAutenticacao = (string)$transacao->getUrlAutenticacao();
                if ((int)$transacao->getStatus() == \Tritoq\Payment\Cielo\Transacao::STATUS_AUTORIZADA
                    || (int)$transacao->getStatus() == \Tritoq\Payment\Cielo\Transacao::STATUS_CAPTURADA
                ) {
                    $finalizacao = 'Aprovado';
                    $comentario = "Situação: " . $finalizacao . "<br />";
                    $comentario .= " Pedido: " . (string)$pedido->getNumero() . "<br />";
                    $comentario .= " TID: " . (string)$transacao->getTid() . "<br />";
                    $comentario .= " Cartão: " . strtoupper((string)$cartao->getBandeira()) . "<br />";
                    $comentario .= " Parcelado em: " . (string)$transacao->getParcelas() . "x";
                    $this->model_checkout_order->addOrderHistory((string)$pedido->getNumero(),
                        $this->config->get('cielo_aprovado_id'), $comentario,
                        true);
                    $requisicoes
                        = $transacao->getRequisicoes(\Tritoq\Payment\Cielo\Transacao::REQUISICAO_TIPO_TRANSACAO);
                    $requisicao = end($requisicoes);
                    $xmlRetorno = $requisicao->getXmlRetorno();
                    $data = $this->model_payment_cielo->parseData($xmlRetorno);
                    $this->model_payment_cielo->addTransaction($data);
                    $json['redirect'] = $this->url->link('checkout/success');
                } else {
                    if ( ! empty($urlAutenticacao)) {
                        $requisicoes
                            = $transacao->getRequisicoes(\Tritoq\Payment\Cielo\Transacao::REQUISICAO_TIPO_TRANSACAO);
                        $requisicao = end($requisicoes);
                        $xmlRetorno = $requisicao->getXmlRetorno();
                        $data = $this->model_payment_cielo->parseData($xmlRetorno);
                        $this->model_payment_cielo->addTransaction($data);
                        $json['redirect'] = $urlAutenticacao;
                    } else {
                        $requisicoes
                            = $transacao->getRequisicoes(\Tritoq\Payment\Cielo\Transacao::REQUISICAO_TIPO_TRANSACAO);
                        $requisicao = end($requisicoes);
                        $errors = $requisicao->getErrors();
                        $xmlRetorno = $requisicao->getXmlRetorno();
                        $data = $this->model_payment_cielo->parseData($xmlRetorno);
                        $this->model_payment_cielo->addTransaction($data);
                        if(isset($xmlRetorno->autorizacao->mensagem) && (string)$xmlRetorno->autorizacao->mensagem != '') {
                            $this->error[] = (string)$xmlRetorno->autorizacao->mensagem;
                        }
                        if(isset($xmlRetorno->autenticacao->mensagem) && (string)$xmlRetorno->autenticacao->mensagem != '') {
                            //$this->error[] = (string)$xmlRetorno->autenticacao->mensagem;
                        }
                        if ( ! empty($errors)) {
                            $this->error = array_merge((array)$this->error, $errors);
                        }
                    }
                }
            } catch(\Exception $e) {
                $this->error = array_merge((array)$this->error, array($e->getMessage()));
            }
        }
        if(!empty($this->error)) {
            $json['error'] = $this->error;
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    public function parcelamento() {
        if (isset($this->request->get['bandeira'])) {
            $bandeira = $this->request->get['bandeira'];
        } else {
            $bandeira = null;
        }
        if (isset($this->request->get['parcelas'])) {
            $maximo_parcelas = $this->request->get['parcelas'];
        } else {
            $maximo_parcelas = 0;
        }
        $this->load->model('checkout/order');
        $order_info  = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $valor = str_replace(',','',number_format($order_info['total'],2));
        $parcelas_sem_juros = $this->config->get('cielo_parcela_semjuros');
        $juros = $this->config->get('cielo_parcela_juros');
        $parcela_minima = $this->config->get('cielo_parcela_minimo');
        $parcelamento = '';
        $info = '';
        if (!empty($bandeira)) {
            $parcelamento .= '<div class="form-group">
                            <label for="formaPagamento" class="col-sm-3 control-label">Valor</label>
                            <div class="col-sm-9">
                                <select name="formaPagamento" class="form-control">
                                    <option value="1">1x de '. number_format($valor, 2, ',', '.') .' sem juros</option>';
            for ($p = 2; $p <= $maximo_parcelas; $p++) {
                $valor_parcela = 0;
                if ($p <= $parcelas_sem_juros) {
                    $valor_parcela = $valor / $p;
                }
                if ($p > $parcelas_sem_juros) {
                    $valor_parcela = $this->juroComposto($valor, $p, $juros, 1);
                }
                if ($valor_parcela >= $parcela_minima) {
                    if ($p <= $parcelas_sem_juros) {
                        $parcelamento .= '<option value="'. $p .'"> '. $p .'x de '. number_format($valor_parcela, 2, ',', '.') .' sem juros</option>';
                    } else {
                        $parcelamento .= '<option value="' . $p . '"> ' . $p . 'x de ' . number_format($valor_parcela, 2,',','.') . ' com juros</option>';
                    }
                } else {
                    $info .= '<span class="help-inline fixed-help">Parcela mínima de '. number_format($parcela_minima, 2, ',', '.') .'</span>';
                    break;
                }
            }
            if ($parcelas_sem_juros < $maximo_parcelas) {
                $juros = !empty($juros) ? preg_replace('/[^0-9\s]+/', ',', $juros) : 0;
                $info .= '<span class="help-inline fixed-help">Juros de '. $juros .'% ao mês</span>';
            }
            if($bandeira == 'visa' || $bandeira == 'mastercard') {
                $parcelamento .= '<optgroup label="Débito"><option value="A">1x Débito à vista</option></optgroup>';
            }
            $parcelamento .= '</select>' . $info . '</div></div>';
        }
        $this->response->setOutput($parcelamento);
    }
    public function callback() {
        if(!isset($this->session->data['order_id'])) {
            return $this->response->redirect($this->url->link('common/home'));
        }
        if(method_exists($this->load, 'library')) {
            $this->load->library('cielo');
        }
        $this->language->load('payment/cielo');
        $this->load->model('checkout/order');
        $this->load->model('payment/cielo');
        $order_id = $this->session->data['order_id'];
        $transaction = $this->model_payment_cielo->getTransactionByOrderId($order_id);
        if(!empty($transaction['tid'])) {
            $loja = new \Tritoq\Payment\Cielo\Loja();
            $loja->setAmbiente(\Tritoq\Payment\Cielo\Loja::AMBIENTE_PRODUCAO)
                ->setUrlRetorno($this->url->link('payment/cielo/callback'))
                ->setChave($this->config->get('cielo_chave'))
                ->setNumeroLoja($this->config->get('cielo_afiliacao'))
                ->setSslCertificado(DIR_SYSTEM . 'library/Tritoq/Payment/Cielo/ssl/ecommerce.cielo.com.br.crt');
            if($this->config->get('cielo_teste') == '1') {
                $loja->setAmbiente(\Tritoq\Payment\Cielo\Loja::AMBIENTE_TESTE)
                    ->setChave(\Tritoq\Payment\Cielo\Loja::LOJA_CHAVE_AMBIENTE_TESTE)
                    ->setNumeroLoja(\Tritoq\Payment\Cielo\Loja::LOJA_NUMERO_AMBIENTE_TESTE);
            }
            $transacao = new \Tritoq\Payment\Cielo\Transacao();
            $transacao->setTid($transaction['tid']);
            $service = new \Tritoq\Payment\Cielo\CieloService(array(
                'loja' => $loja,
                'transacao' => $transacao,
            ));
            // Setando o tipo de versão de conexão SSL
            $service->setSslVersion(4);
            $service->doConsulta();
            if(!in_array((int)$transacao->getStatus(), array(\Tritoq\Payment\Cielo\Transacao::STATUS_ANDAMENTO, \Tritoq\Payment\Cielo\Transacao::STATUS_CAPTURADA, \Tritoq\Payment\Cielo\Transacao::STATUS_NAO_AUTORIZADA))
                && $this->config->get('cielo_autorizacao') != \Tritoq\Payment\Cielo\Transacao::AUTORIZAR_NAO_AUTORIZAR) {
                $service->doAutorizacao();
            }
            if((int)$transacao->getStatus() == \Tritoq\Payment\Cielo\Transacao::STATUS_AUTORIZADA) {
                $situacao = 'Autorizada';
                $comentario = "Situação: ". $situacao ."<br />";
                $comentario .= " Pedido: ". $order_id ."<br />";
                $comentario .= " TID: ". (string)$transacao->getTid() ."<br />";
                $comentario .= " Parcelado em: ". (string)$transacao->getParcelas() ."x";
                $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('cielo_aprovado_id'), $comentario, true);
            } else {
                $situacao = 'Não Autorizada';
                $comentario = "Situação: ". $situacao ."<br />";
                $comentario .= " Pedido: ". $order_id ."<br />";
                $comentario .= " TID: ". (string)$transacao->getTid() ."<br />";
                $comentario .= " Parcelado em: ". (string)$transacao->getParcelas() ."x";
                $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('cielo_nao_capturado_id'), $comentario, true);
            }
            if($this->config->get('cielo_captura') && (int)$transacao->getStatus() == \Tritoq\Payment\Cielo\Transacao::STATUS_AUTORIZADA) {
                $service->doCaptura();
            }
            if((int)$transacao->getStatus() == \Tritoq\Payment\Cielo\Transacao::STATUS_CAPTURADA) {
                $situacao = 'Capturada';
                $comentario = "Situação: ". $situacao ."<br />";
                $comentario .= " Pedido: ". $order_id ."<br />";
                $comentario .= " TID: ". (string)$transacao->getTid() ."<br />";
                $comentario .= " Parcelado em: ". (string)$transacao->getParcelas() ."x";
                $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('cielo_capturado_id'), $comentario, true);
            }
            $requisicoes = $transacao->getRequisicoes(\Tritoq\Payment\Cielo\Transacao::REQUISICAO_TIPO_TRANSACAO);
            if(empty($requisicoes)) {
                $requisicoes = $transacao->getRequisicoes(\Tritoq\Payment\Cielo\Transacao::REQUISICAO_TIPO_CAPTURA);
            }
            if(empty($requisicoes)) {
                $requisicoes = $transacao->getRequisicoes(\Tritoq\Payment\Cielo\Transacao::REQUISICAO_TIPO_AUTORIZACAO);
            }
            if(empty($requisicoes)) {
                $requisicoes = $transacao->getRequisicoes(\Tritoq\Payment\Cielo\Transacao::REQUISICAO_TIPO_CONSULTA);
            }
            $requisicao = end($requisicoes);
            if(is_array($requisicao)) {
                $requisicao = end($requisicao);
            }
            $xmlRetorno = $requisicao->getXmlRetorno();
            $data = $this->model_payment_cielo->parseData($xmlRetorno);
            $this->model_payment_cielo->addTransaction($data);
            if($this->isOk((int)$transacao->getStatus())) {
                return $this->response->redirect($this->url->link('checkout/success'));
            }
        }
        return $this->response->redirect($this->url->link('checkout/failure'));
    }
}