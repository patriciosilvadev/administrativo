<?php

require_once APPPATH . 'controllers/base/BaseController.php';

/**
 * Esta classe é o controler de Servidor. Responsável por chamar as funções e views, efetuando as chamadas de models
 * @author Equipe de desenvolvimento APH
 * @version 1.0
 * @copyright Prefeitura de Fortaleza
 * @access public
 * @package Model
 * @subpackage GIAH
 */
class Notafiscal extends BaseController {

    function Notafiscal() {
        parent::Controller();
        $this->load->model('estoque/notafiscal_model', 'notafiscal');
        $this->load->model('cadastro/convenio_model', 'convenio');
        $this->load->model('estoque/fornecedor_model', 'fornecedor');
        $this->load->library('mensagem');
        $this->load->library('utilitario');
        $this->load->library('pagination');
        $this->load->library('validation');
    }

    function index() {
        $this->pesquisar();
    }

    function pesquisar($args = array()) {

        $this->loadView('estoque/notafiscal-lista', $args);
    }

    function carregarnotafiscalopcoes($solicitacao_cliente_id, $notafiscal_id = null) {
        $data['solicitacao_cliente_id'] = $solicitacao_cliente_id;
        $data['notafiscal_id'] = $notafiscal_id;
        $data['destinatario'] = $this->notafiscal->listaclientenotafiscal($solicitacao_cliente_id);
        $data['produtos'] = $this->notafiscal->listarresumosolicitacao($solicitacao_cliente_id);
        $data['notafiscal'] = $this->notafiscal->instanciarnotafiscal($notafiscal_id);
//        echo "<pre>";
//        var_dump($data['produtos']);die;
        $this->loadView('estoque/notafiscal-ficha', $data);
    }

    function impostosaida($solicitacao_itens_id) {
        $data['solicitacao_itens_id'] = $solicitacao_itens_id;

        $data['cst_icms'] = $this->notafiscal->listarcsticms();
//        var_dump($data['cst_icms']);die;
        $data['cst_ipi'] = $this->notafiscal->listarcstipi();
        $data['cst_pis_cofins'] = $this->notafiscal->listarcstpiscofins();

        $data['produto'] = $this->notafiscal->listardadositem($solicitacao_itens_id);
        $this->load->view('estoque/impostosaida-form', $data);
    }

    function gravarimpostosaida() {
        $this->notafiscal->gravarimpostosaida();

        echo "<script type='text/javascript'> 
                    window.close();
                </script>";
    }

    function informacoesnotafiscal($solicitacao_cliente_id, $notafiscal_id = null) {
        $data['solicitacao_cliente_id'] = $solicitacao_cliente_id;
        $data['notafiscal_id'] = $notafiscal_id;
        $this->loadView('estoque/notafiscal-form', $data);
    }

    function gravarnotafiscaleletronica() {
//        die;
        $solicitacao_id = $_POST['estoque_cliente_id'];
        $resultado = $this->notafiscal->listanotasolicitacao($solicitacao_id);
//        var_dump($resultado);die;
        if (count($resultado) == 0) {
            $notafiscal_id = $this->notafiscal->gravarnotafiscaleletronica();
        } else {
            $notafiscal_id = $resultado[0]->notafiscal_id;
        }
        redirect(base_url() . "estoque/notafiscal/gerarnotafiscal/$solicitacao_id/$notafiscal_id");
    }

    function geraconfignfephp($solicitacao_id) {
        $data['empresa'] = $this->notafiscal->empresa();
        $json = array(
            'atualizacao' => date('Y-m-d H:i:s'),
            'tpAmb' => (int) $data["empresa"][0]->ambiente_producao,
            'pathXmlUrlFileNFe' => 'nfe_ws3_mod55.xml',
            'pathXmlUrlFileCTe' => 'cte_ws2.xml',
            'pathXmlUrlFileMDFe' => 'mdf2_ws1.xml',
            'pathXmlUrlFileCLe' => '',
            'pathXmlUrlFileNFSe' => '',
            'pathNFeFiles' => './upload/nfe/' . $solicitacao_id . '/',
            'pathCTeFiles' => '',
            'pathMDFeFiles' => '',
            'pathCLeFiles' => '',
            'pathNFSeFiles' => '',
            'pathCertsFiles' => './upload/certificado/' . $data["empresa"][0]->empresa_id . '/',
            'siteUrl' => base_url() . '/ambulatorio/empresa',
            'schemesNFe' => 'PL_008i2',
            'schemesCTe' => 'PL_CTe_200',
            'schemesMDFe' => 'PL_MDFe_100',
            'schemesCLe' => '',
            'schemesNFSe' => '',
            'razaosocial' => $data["empresa"][0]->razao_social,
            'nomefantasia' => $data["empresa"][0]->empresa,
            'siglaUF' => $this->utilitario->codigo_uf($data["empresa"][0]->codigo_ibge, "sigla"),
            'cnpj' => $this->utilitario->remover_caracter($data["empresa"][0]->cnpj),
            'ie' => $this->utilitario->remover_caracter($data["empresa"][0]->inscricao_estadual),
            'im' => $this->utilitario->remover_caracter($data["empresa"][0]->inscricao_municipal),
            'iest' => $this->utilitario->remover_caracter($data["empresa"][0]->inscricao_estadual_st),
            'cnae' => $this->utilitario->remover_caracter($data["empresa"][0]->cnae),
            'regime' => $this->utilitario->remover_caracter($data["empresa"][0]->cod_regime_tributario),
            'tokenIBPT' => '',
            'tokenNFCe' => '',
            'tokenNFCeId' => '',
            'certPfxName' => $data["empresa"][0]->certificado_nome,
            'certPassword' => $data["empresa"][0]->certificado_senha,
            'certPhrase' => '',
            'aDocFormat' => array(
                'format' => 'L',
                'paper' => 'A4',
                'southpaw' => '1',
                'pathLogoFile' => './img/logo peq.jpg',
                'pathLogoNFe' => './img/logo peq.jpg',
                'pathLogoNFCe' => './img/logo peq.jpg',
                'logoPosition' => 'L',
                'font' => 'Times',
                'printer' => ''
            ),
            'aMailConf' => array(
                'mailAuth' => '1',
                'mailFrom' => false,
                'mailSmtp' => '',
                'mailUser' => '',
                'mailPass' => '',
                'mailProtocol' => '',
                'mailPort' => '',
                'mailFromMail' => false,
                'mailFromName' => '',
                'mailReplayToMail' => false,
                'mailReplayToName' => '',
                'mailImapHost' => null,
                'mailImapPort' => null,
                'mailImapSecurity' => null,
                'mailImapNocerts' => null,
                'mailImapBox' => null
            ),
            'aProxyConf' => array(
                'proxyIp' => '',
                'proxyPort' => '',
                'proxyUser' => '',
                'proxyPass' => ''
            )
        );

//        $str = json_encode($json);
//        var_dump("<pre>", $str, "<hr>");
//        $conv = json_decode($str, true);
//        var_dump($conv);die;
        return json_encode($json);
    }

    function gerarnotafiscal($solicitacao_cliente_id, $notafiscal_id) {

        $notafiscal = $this->notafiscal->instanciarnotafiscal($notafiscal_id);

        if ($notafiscal[0]->enviada == 't' && $notafiscal[0]->cancelada == 'f') {
            $mensagem = 'Nota Fiscal ja foi Enviada para SEFAZ.';
            $this->session->set_flashdata('message', $mensagem);
            header("Location: " . base_url() . "estoque/notafiscal/carregarnotafiscalopcoes/{$solicitacao_cliente_id}/{$notafiscal_id}");
            exit;
        }

        if ($notafiscal[0]->cancelada == 't') {
            if (!is_dir("./upload/nfe/CANCELADAS/")) {
                mkdir("./upload/nfe/CANCELADAS/");
            }
            
            if ( rename("./upload/nfe/{$solicitacao_cliente_id}/", "./upload/nfe/CANCELADAS/") ){ // movendo arquivo
                
            }
//            system("mv /home/sisprod/projetos/administrativo/upload/nfe/{$solicitacao_cliente_id}/ /home/sisprod/projetos/administrativo/upload/nfe/CANCELADAS/");
        }

        $data['empresa'] = $this->notafiscal->empresa();

        $tipoAmbiente = (int) $data["empresa"][0]->ambiente_producao; //1=Produção; 2=Homologação

        $data['destinatario'] = $this->notafiscal->listaclientenotafiscal($solicitacao_cliente_id);
        $data['produtos'] = $this->notafiscal->listarsolicitacaosnota($solicitacao_cliente_id);

        if ($this->utilitario->codigo_uf($data['empresa'][0]->codigo_ibge) == $this->utilitario->codigo_uf($data['destinatario'][0]->codigo_ibge)) {
            $data['identificaDestOp'] = '1';
        } else {
            $data['identificaDestOp'] = '2';
        }
        
        //Essa variavel de configuração sera usada para carregar as configurações iniciais da NFe
        $config = $this->geraconfignfephp($solicitacao_cliente_id);
        
//        var_dump($data['empresa'][0]->cep, $this->utilitario->remover_caracter($data['empresa'][0]->cep)); die;
        $dadosNFe = array(
            /* Dados da NFe - infNFe */
            'verProc' => '1.0', //Versao do SISTEMA ADMINISTRATIVO
            'cUF' => $this->utilitario->codigo_uf($data['empresa'][0]->codigo_ibge, 'codigo'), //codigo do estado (IBGE)
            'identificaDestOp' => $data['identificaDestOp'], // Olhar se as UF's da empresa e do destinatario sao as mesmas.
            'cNF' => $this->utilitario->tamanho_string($solicitacao_cliente_id, 8, 'numero'),
            'cMunFG' => $data['empresa'][0]->codigo_ibge, //codigo do municipio da empresa (IBGE)
            'naturezaOpe' => $notafiscal[0]->natureza_operacao,
            'modeloNota' => $notafiscal[0]->modelo_nf,
            'numSerie' => $notafiscal[0]->notafiscal_id, //deve ser incrementado a cada nova nota_fiscal gerada
            'numNF' => $notafiscal[0]->notafiscal_id,
            'tipoNF' => $notafiscal[0]->tipo_nf, // 0 = entrada | 1 = saida
            'indPres' => $notafiscal[0]->indicador_presenca, // Tipo de Compra. (1=Presencial, 2=Nao Presencial e etc...)
            'finalidadeNFe' => $notafiscal[0]->finalidade_nf,
            'indicadorPagamento' => '0', //0=Pagamento à vista; 1=Pagamento a prazo; 2=Outros

            /* ENDEREÇO DO EMITENTE */
            "logradouro" => $data['empresa'][0]->logradouro,
            "numero" => $data['empresa'][0]->numero,
            "complemento" => $data['empresa'][0]->complemento,
            "bairro" => $data['empresa'][0]->bairro,
            "codMunicipio" => $data['empresa'][0]->codigo_ibge,
            "nomMunicipio" => $data['empresa'][0]->municipio,
            "UF" => $this->utilitario->codigo_uf($data['empresa'][0]->codigo_ibge, 'sigla'),
            "cep" => $this->utilitario->remover_caracter($data['empresa'][0]->cep),
            "fone" => $this->utilitario->remover_caracter(str_replace(' ', '', $data['empresa'][0]->telefone)),
            
            /* DADOS DO DESTINATARIO */
            "destCNPJ" => $this->utilitario->remover_caracter($data['destinatario'][0]->cnpj),
            "destCPF" => '',
            "destNOME" => $data['destinatario'][0]->nome,
            "destIND_IE" => $data['destinatario'][0]->indicadorie, //criar um novo campo na tb_estoque_cliente (VERIFICAR SE O CLIENTE É ISENTO DE IE)
            "destIE" => $this->utilitario->remover_caracter($data['destinatario'][0]->inscricao_estadual),
            "destIM" => '', //criar um novo campo na tb_estoque_cliente
            "destEMAIL" => $data['destinatario'][0]->email,
            "destLOG" => $data['destinatario'][0]->logradouro,
            "destNUM" => $data['destinatario'][0]->numero,
            "destCOMP" => $data['destinatario'][0]->complemento,
            "destBAIRRO" => $data['destinatario'][0]->bairro,
            "destCOD_MUN" => $data['destinatario'][0]->codigo_ibge,
            "destMUN" => $data['destinatario'][0]->municipio,
            "destUF" => $this->utilitario->codigo_uf($data['destinatario'][0]->codigo_ibge, 'sigla'),
            "destCEP" => $this->utilitario->remover_caracter(str_replace(' ', '', $data['destinatario'][0]->cep)),
            "destFONE" => $this->utilitario->remover_caracter(str_replace(' ', '', $data['destinatario'][0]->telefone))
        );

        $totalBC = 0;
        $totalICMS = 0;
        $totalIPI = 0;
        $totalPIS = 0;
        $totalCOFINS = 0;
        $totalProduto = 0;
        $totalImposto = 0;
        /* DADOS DOS PRODUTOS */
        for ($i = 0, $n = 1; $i < count($data['produtos']); $i++, $n++) {

            $valBC = number_format(((int) $data['produtos'][$i]->quantidade * (float) $data['produtos'][$i]->valor_venda), 2, '.', '');
            $valTotICMS = number_format($valBC * ( ((float) $data['produtos'][$i]->icms) / 100 ), 2, '.', '');
            $valTotIPI = number_format($valBC * ( ((float) $data['produtos'][$i]->ipi) / 100 ), 2, '.', '');
            $valTotPIS = number_format($valBC * ( ((float) $data['produtos'][$i]->pis) / 100 ), 2, '.', '');
            $valTotCOFINS = number_format($valBC * ( ((float) $data['produtos'][$i]->cofins) / 100 ), 2, '.', '');
            $totImpostoItem = number_format(($valTotICMS + $valTotIPI + $valTotPIS + $valTotCOFINS), 2, '.', '');
            $totProdItem = number_format(( (int) $data['produtos'][$i]->quantidade * (float) $data['produtos'][$i]->valor_venda), 2, '.', '');

            /* CALCULANDO VALORES TOTAIS DA NOTA */
            $totalBC += $totalBC;
            $totalICMS += $valTotICMS;
            $totalIPI += $valTotIPI;
            $totalPIS += $valTotPIS;
            $totalCOFINS += $valTotCOFINS;
            $totalProduto += $totProdItem;
            $totalImposto += $totImpostoItem;

            $valTotImpostos = $valTotICMS + $valTotIPI + $valTotPIS;

            $dadosProdutos[$i] = array(
                /* Dados Basicos */
                "numItem" => $n,
                "codigoProduto" => $data['produtos'][$i]->codigo, // Codigo de controle no sistema do cliente
                "cEAN" => '', //FALTA ESSE
                "nomeProd" => $data['produtos'][$i]->descricao,
                "ncm" => $data['produtos'][$i]->ncm,
                "ex_tipi" => '',
                "cfop" => $data['produtos'][$i]->codigo_cfop,
                "unCompra" => $data['produtos'][$i]->unidade,
                "qtdeCompra" => $data['produtos'][$i]->quantidade,
                "valUniComp" => $data['produtos'][$i]->valor_venda,
                "valProduto" => number_format((int) $data['produtos'][$i]->quantidade * (float) $data['produtos'][$i]->valor_venda, 2, '.', ''),
                "cEAN_Trib" => '',
                "uniTrib" => $data['produtos'][$i]->unidade,
                "qtdeTrib" => $data['produtos'][$i]->quantidade,
                "valUniTrib" => number_format((float) $data['produtos'][$i]->valor_venda, 2, '.', ''),
                "valorFrete" => '', //deixar em branco
                "valorSeguro" => '', //deixar em branco
                "valorDesconto" => '', //deixar em branco   
                "valorOutros" => '', //deixar em branco   
                "indTot" => 1, // | 1 = somar ao valor total da NFe | 0 = nao somar ao vlr tot da nota |
                "numPedido" => $solicitacao_cliente_id,
                "itemPedido" => $n,
                "prodCEST" => $data['produtos'][$i]->cest,
                /* DESCRIÇÃO DO PRODUTO(informações adicionais. Ex: Validade, Lote e etc) */
                "prodDescricao" => 'Lote: ' . $data['produtos'][$i]->lote . ' - ' . date('d/m/Y', strtotime($data['produtos'][$i]->validade)),
                /* CALCULO DE IMPOSTOS 
                  +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
                 * EXISTEM VARIAS MODALIDADES PARA O CALCULO DO ICMS, E CADA UMA DELAS 
                 * DIFERE EM ALGUNS DETALHES. 
                  EXEMPLOS:
                  + icms 00,
                  + icms 10,
                  + icms 20,
                  + icms 30,
                  + icms 41 entre outros.
                 * CASO NECESSITE MUDAR O TIPO DE ICMS É BOM QUE OLHE NO ARQUIVO 'geraXml.php'
                 * PARA ENTENDER QUAIS AS TAGS NECESSARIAS QUE DEVEM SER ACRESCENTADAS 
                 * ENTRE OUTRAS INFORMAÇÕES          
                  +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
                //ICMS
                "orig_ICMS" => substr($data['produtos'][$i]->cst_icms, 0, 1),
                "cst_ICMS" => substr($data['produtos'][$i]->cst_icms, 1),
                //CASO SEJA 40, 41 OU 50, SERÃO NECESSARIAS APENAS AS INFORMAÇÕES ACIMAS
                "modBC_ICMS" => '3',
                "valorBC_ICMS" => number_format((int) $data['produtos'][$i]->quantidade * (float) $data['produtos'][$i]->valor_venda, 2, '.', ''),
                "percICMS_ICMS" => $data['produtos'][$i]->icms, //tag pICMS
                "valorICMS_ICMS" => $valTotICMS,
                //IPI
                "codEnq_IPI" => '999',
                "cst_IPI" => $data['produtos'][$i]->cst_ipi, //CRIAR UM CST EXCLUSIVO PARA O IPI
                "valorBC_IPI" => number_format((int) $data['produtos'][$i]->quantidade * (float) $data['produtos'][$i]->valor_venda, 2, '.', ''),
                "percIPI_IPI" => $data['produtos'][$i]->ipi,
                "valorIPI_IPI" => $valTotIPI,
                //PIS
                "cst_PIS" => $data['produtos'][$i]->cst_pis,
                "valorBC_PIS" => number_format((int) $data['produtos'][$i]->quantidade * (float) $data['produtos'][$i]->valor_venda, 2, '.', ''),
                "percPIS_PIS" => $data['produtos'][$i]->pis,
                "valorPIS_PIS" => $valTotPIS,
                "qBCProd" => '',
                "vAliqProd" => '',
                //COFINS
                "cst_COFINS" => $data['produtos'][$i]->cst_cofins,
                "valorBC_COFINS" => number_format((int) $data['produtos'][$i]->quantidade * (float) $data['produtos'][$i]->valor_venda, 2, '.', ''),
                "percPIS_COFINS" => $data['produtos'][$i]->cofins,
                "valorCOFINS_COFINS" => $valTotCOFINS,
                /* VALOR TOTAL DE IMPOSTO */
                "valTotImposto" => $totImpostoItem
            );
        }

        //VALORES TOTAIS DA NOTA
        $totalNota = array(
            "totalBC" => number_format($totalBC, 2, '.', ''),
            "totalICMS" => number_format($totalICMS, 2, '.', ''),
            "totalIPI" => number_format($totalIPI, 2, '.', ''),
            "totalPIS" => number_format($totalPIS, 2, '.', ''),
            "totalCOFINS" => number_format($totalCOFINS, 2, '.', ''),
            "totalProduto" => number_format($totalProduto, 2, '.', ''),
            "totalImposto" => number_format($totalImposto, 2, '.', '')
        );

        /*
         * POR ALGUM MOTIVO, O PHP NÃO PERMITE O USO DO COMANDO 'USE'
         * DENTRO DE UMA FUNÇÃO/METODO, POR ISSO, TODO O XML DA NFe SERA 
         * GERADO DENTRO DO ARQUIVO 'geraXml.php'
         */
        
        
        require_once ('./application/libraries/nfephp/vendor/nfephp-org/nfephp/bootstrap.php');

        // GERA O XML PRINCIPAL
        require_once ('./application/libraries/nfephp/arquivosNfe/geraXml.php');

        // GERA AS TAGS DA ASSINATURA DIGITAL
        require_once ('./application/libraries/nfephp/arquivosNfe/assinaNFe.php');

        // VALIDA O XML POR MEIO DE UM SCHEMA XSD
        require_once ('./application/libraries/nfephp/arquivosNfe/validaXml.php');
        if ($validacaoXSD) {
//            echo $mensagem;die;
            $this->session->set_flashdata('message', $mensagem); // Variavel criada dentro de validaXml.php
            redirect(base_url() . "estoque/notafiscal/carregarnotafiscalopcoes/{$solicitacao_cliente_id}/{$notafiscal_id}");
        }
//        die;
        //Salvando o XML em um arquivo
        $caminho = "./upload/nfe";
        chmod($caminho, 0777);

        if (!is_dir("{$caminho}/{$solicitacao_cliente_id}")) {
            mkdir("{$caminho}/{$solicitacao_cliente_id}");
        }
        if (!is_dir("{$caminho}/{$solicitacao_cliente_id}/validada")) {
            mkdir("{$caminho}/{$solicitacao_cliente_id}/validada");
        }
        chmod($caminho, 0777);
        $filename = "{$caminho}/{$solicitacao_cliente_id}/validada/{$chave}-nfe.xml"; // Ambiente Linux
        $arq = fopen($filename, 'w+');
        fwrite($arq, $xml);
        fclose($arq);
        chmod($filename, 0777);

//        die('morreu');
        $this->notafiscal->gravarxmlvalidado($chave, $notafiscal_id, $tipoAmbiente, $xml);

        redirect(base_url() . "estoque/notafiscal/enviaNfe/$solicitacao_cliente_id/$notafiscal_id");
    }

    function enviaNfe($solicitacao_cliente_id, $notafiscal_id) {
        $notafiscal = $this->notafiscal->instanciarnotafiscal($notafiscal_id);
        $config = $this->geraconfignfephp($solicitacao_cliente_id);

        $xml = ($notafiscal[0]->xml == '') ? 'SEM XML' : $notafiscal[0]->xml; //1=Produção; 2=Homologação
        if ($xml == 'SEM XML') {
            $data['mensagem'] = 'Não foi possivel encontrar o XML dessa Nota Fiscal. Por favor, gere novamente.';
            $this->session->set_flashdata('message', $data['mensagem']);
            header("Location: " . base_url() . "estoque/notafiscal/carregarnotafiscalopcoes/{$solicitacao_cliente_id}/{$notafiscal_id}");
            exit;
        }

        $indSinc = '0'; //0=asíncrono, 1=síncrono
        $tipoAmbiente = ($notafiscal[0]->tipo_ambiente == '') ? '2' : $notafiscal[0]->tipo_ambiente; //1=Produção; 2=Homologação
        $chave = $notafiscal[0]->chave_nfe;
        $caminho = "./upload/nfe";

        if ($notafiscal[0]->enviada == 'f' OR ( $notafiscal[0]->enviada == 't' && $notafiscal[0]->cancelada == 't')) {
            require_once ('./application/libraries/nfephp/arquivosNfe/enviaNFe.php');
            $data = date("Ym");
//            die('morreu');

            /* Consultando a situacao do Recibo no sistema da SEFAZ  e adcionando Tag de Protocolo */
            require_once ('./application/libraries/nfephp/arquivosNfe/consultaRecibo.php');
//            die(',prr');

            $this->notafiscal->gravardataenvio($notafiscal_id);
        } else {
            $data = date("Ym", strtotime($notafiscal[0]->data_envio));
            require_once ('./application/libraries/nfephp/arquivosNfe/consultaRecibo.php');
        }

        $filename = "{$caminho}/{$solicitacao_cliente_id}/validada/{$chave}-protNFe.xml"; // Ambiente Linux
        $arq = fopen($filename, 'w+');
        fwrite($arq, $xmlFinalizado);
        fclose($arq);
        chmod($filename, 0777);


//        $data['empresa'] = $this->notafiscal->empresa();
//        $this->notafiscal->gravarxmlfinalizado($notafiscal_id, $xmlFinalizado, $numeroRecibo, $numeroProtocolo);
//
// 

        $zip = new ZipArchive;
        $zip->open("{$caminho}/{$solicitacao_cliente_id}/validada/xml.zip", ZipArchive::CREATE);
        $zip->addFile("$filename", "{$chave}-xml.xml");
        $zip->close();

        header("Location: " . base_url() . "estoque/notafiscal/carregarnotafiscalopcoes/{$solicitacao_cliente_id}/{$notafiscal_id}");
        exit;
    }

    function enviaremail() {

        $notafiscal_id = $_POST['notafiscal_id'];
        $solicitacao_cliente_id = $_POST['solicitacao_id'];

        $notafiscal = $this->notafiscal->instanciarnotafiscal($_POST['notafiscal_id']);
        $this->load->library('My_phpmailer');
        $mail = new PHPMailer(true);

        $mail->setLanguage('br');                             // Habilita as saídas de erro em Português
        $mail->CharSet = 'UTF-8';                             // Habilita o envio do email como 'UTF-8'
        $mail->SMTPDebug = 3;                               // Habilita a saída do tipo "verbose"
        $mail->isSMTP();                                      // Configura o disparo como SMTP
        $mail->Host = 'smtp.gmail.com';                       // Especifica o enderço do servidor SMTP da Locaweb
        $mail->SMTPAuth = true;                               // Habilita a autenticação SMTP
        $mail->Username = $_POST['email'];                    // Usuário do SMTP
        $mail->Password = $_POST['senha'];                   // Senha do SMTP
        $mail->SMTPSecure = 'ssl';                            // Habilita criptografia TLS | 'ssl' também é possível
        $mail->Port = 465;                                    // Porta TCP para a conexão
        $mail->From = $_POST['email'];             // Endereço previamente verificado no painel do SMTP
        $mail->FromName = $_POST['email'];                        // Nome no remetente
        $mail->addAddress($_POST['remetente']);                            // Acrescente um destinatário
        $mail->isHTML(true);                                  // Configura o formato do email como HTML
        $mail->Subject = 'NFe ' . $notafiscal[0]->chave_nfe;
        $mail->Body = $_POST['texto'];

        if (isset($_POST['danfe'])) {
            $mail->AddAttachment("./upload/nfe/$solicitacao_cliente_id/validada/" . $notafiscal[0]->chave_nfe . '-danfe.pdf', $notafiscal[0]->chave_nfe . '-danfe.pdf');
        }
        if (isset($_POST['xml'])) {
            $mail->AddAttachment("./upload/nfe/$solicitacao_cliente_id/validada/" . $notafiscal[0]->chave_nfe . '-protNFe.xml', $notafiscal[0]->chave_nfe . '-protNFe.xml');
        }

        if (!$mail->Send()) {
            $mensagem = "Erro: " . $mail->ErrorInfo;
        } else {
            $mensagem = "Email enviado com sucesso!";
        }


        $this->session->set_flashdata('message', $mensagem);
        redirect(base_url() . "estoque/notafiscal/carregarnotafiscalopcoes/$solicitacao_cliente_id/$notafiscal_id");
    }

    function enviarporemail($solicitacao_cliente_id, $notafiscal_id) {
        $data['solicitacao_cliente_id'] = $solicitacao_cliente_id;
        $data['notafiscal_id'] = $notafiscal_id;
        $data['empresa'] = $this->notafiscal->empresa();
        $this->loadView('estoque/enviaremail-form', $data);
    }

    function carregarcancelarnotafiscal($solicitacao_cliente_id, $notafiscal_id = '') {
        if ($notafiscal_id == '') {
            $mensagem = 'A Nota Fiscal Eletronica ainda não foi gerada.';
            $this->session->set_flashdata('message', $mensagem);
            redirect(base_url() . "estoque/notafiscal/carregarnotafiscalopcoes/$solicitacao_cliente_id/$notafiscal_id");
        }
        $data['solicitacao_cliente_id'] = $solicitacao_cliente_id;
        $data['notafiscal_id'] = $notafiscal_id;
        $this->loadView('estoque/cancelarnotafiscal', $data);
    }

    function cancelarnotafiscal($solicitacao_cliente_id, $notafiscal_id = '') {
        if ($notafiscal_id == '') {
            $mensagem = 'A Nota Fiscal Eletronica ainda não foi gerada.';
            $this->session->set_flashdata('message', $mensagem);
            redirect(base_url() . "estoque/notafiscal/carregarnotafiscalopcoes/$solicitacao_cliente_id/$notafiscal_id");
        } else {
            $this->notafiscal->gravarmotivocancelamento($notafiscal_id);

            $config = $this->geraconfignfephp($solicitacao_cliente_id);

            $notafiscal = $this->notafiscal->instanciarnotafiscal($notafiscal_id);

            if ($notafiscal[0]->enviada == 't') {
                $chave = $notafiscal[0]->chave_nfe;
                $numProtocolo = $notafiscal[0]->numero_protocolo;
                $modelo = $notafiscal[0]->modelo_nf;
                $tpAmbiente = $notafiscal[0]->tipo_ambiente;
                $motivo = $_POST['txtmotivo'];

                require_once ('./application/libraries/nfephp/vendor/nfephp-org/nfephp/bootstrap.php');
                require_once ('./application/libraries/nfephp/arquivosNfe/cancelaNfe.php');
                $data['mensagem'] = 'Cancelamento Efetuado com sucesso.';
            }
            else{
                $data['mensagem'] = 'Operação cancelada. Nota Fiscal ainda não foi enviada para a SEFAZ.';
            }
            $this->session->set_flashdata('message', $data['mensagem']);
            redirect(base_url() . "estoque/notafiscal/carregarnotafiscalopcoes/$solicitacao_cliente_id/$notafiscal_id");
        }
    }

    function impressaodanfe($solicitacao_cliente_id, $notafiscal_id = '') {
        if ($notafiscal_id == '') {
            $mensagem = 'A Nota Fiscal Eletronica ainda não foi gerada.';
            $this->session->set_flashdata('message', $mensagem);
            redirect(base_url() . "estoque/notafiscal/carregarnotafiscalopcoes/$solicitacao_cliente_id/$notafiscal_id");
        } else {
            $config = $this->geraconfignfephp($solicitacao_cliente_id);

            $notafiscal = $this->notafiscal->instanciarnotafiscal($notafiscal_id);
            $chave = $notafiscal[0]->chave_nfe;
            $data = date("Ym", strtotime($notafiscal[0]->data_envio));
            $caminho = "./upload/nfe";

            if ($chave == '') {
                $mensagem = 'Não foi possivel encontrar a Chave da Nota fiscal, por favor gere novamente.';
                $this->session->set_flashdata('message', $mensagem);
                redirect(base_url() . "estoque/notafiscal/carregarnotafiscalopcoes/$solicitacao_cliente_id/$notafiscal_id");
            }


            require_once ('./application/libraries/nfephp/vendor/nfephp-org/nfephp/bootstrap.php');
            require_once ('./application/libraries/nfephp/arquivosNfe/geraDanfe.php');
        }
    }

    function downloaddanfe($solicitacao_cliente_id, $notafiscal_id = '') {
        if ($notafiscal_id == '') {
            echo "<script>window.close();</script>";
        } else {
            $config = $this->geraconfignfephp($solicitacao_cliente_id);

            $notafiscal = $this->notafiscal->instanciarnotafiscal($notafiscal_id);
            $chave = $notafiscal[0]->chave_nfe;
            $data = date("Ym", strtotime($notafiscal[0]->data_envio));
            $caminho = "./upload/nfe";
            if ($chave == '') {
                echo "<script>window.close();</script>";
            }
            $download = true;

            require_once ('./application/libraries/nfephp/vendor/nfephp-org/nfephp/bootstrap.php');
            require_once ('./application/libraries/nfephp/arquivosNfe/geraDanfe.php');
        }
    }

    function teste($solicitacao_cliente_id, $notafiscal_id) {
        $chave = '23170308852545000138550560000000561000001090';
        $config = $this->geraconfignfephp($solicitacao_cliente_id);
        $tipoAmbiente = 2; //1=Produção; 2=Homologação
        require_once ('./application/libraries/nfephp/arquivosNfe/consultaRecibo.php');
    }

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
