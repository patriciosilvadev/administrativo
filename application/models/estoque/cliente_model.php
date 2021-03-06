<?php

class cliente_model extends Model {

    var $_estoque_cliente_id = null;
    var $_nome = null;
    var $_cnpj = null;
    var $_cep = null;
    var $_logradouro = null;
    var $_numero = null;
    var $_complemento = null;
    var $_bairro = null;
    var $_municipio_id = null;
    var $_municipio_nome = null;
    var $_inscricao_estadual = null;
    var $_celular = null;
    var $_telefone = null;
    var $_email = null;
    var $_razao_social = null;
    var $_tipo_logradouro_id = null;
    var $_menu_id = null;
    var $_sala_id = null;
    var $_saida = null;
    var $_credor_devedor_id = null;

    function Cliente_model($estoque_cliente_id = null) {
        parent::Model();
        if (isset($estoque_cliente_id)) {
            $this->instanciar($estoque_cliente_id);
        }
    }

    function listar($args = array()) {
        $this->db->select('estoque_cliente_id,
                            nome, telefone');
        $this->db->from('tb_estoque_cliente');
        $this->db->where('ativo', 'true');
        if (isset($args['nome']) && strlen($args['nome']) > 0) {
            $this->db->where('nome ilike', "%" . $args['nome'] . "%");
        }
        return $this->db;
    }

    function listarmenu() {
        $this->db->select('estoque_menu_id,
                            descricao');
        $this->db->from('tb_estoque_menu');
        $this->db->where('ativo', 'true');
        $return = $this->db->get();
        return $return->result();
    }

    function listarsalamenu() {
        $empresa_id = $this->session->userdata('empresa_id');
        $this->db->select('exame_sala_id,
                            nome');
        $this->db->from('tb_exame_sala');
        $this->db->where('ativo', 'true');
        $this->db->where('empresa_id', $empresa_id);
        $return = $this->db->get();
        return $return->result();
    }

    function listarclientes() {
        $this->db->select('estoque_cliente_id,
                            nome');
        $this->db->from('tb_estoque_cliente');
        $this->db->where('ativo', 'true');
        $this->db->orderby('nome');
        $return = $this->db->get();
        return $return->result();
    }

    function contador($operador_id) {
        $this->db->select();
        $this->db->from('tb_estoque_operador_cliente');
        $this->db->where('operador_id', $operador_id);
        $return = $this->db->count_all_results();
        return $return;
    }
    
    
    function descricaodepagamento() {
        $this->db->select('fp.descricao_forma_pagamento_id,
                            fp.boleto,
                            fp.nome as nome');
        $this->db->from('tb_descricao_forma_pagamento fp');
        $this->db->where('ativo', 't');
        $this->db->orderby('fp.nome');
        $return = $this->db->get();
        $retorno = $return->result();

        if (empty($retorno)) {
            $this->db->select('fp.descricao_forma_pagamento_id,
                                fp.boleto,
                                fp.nome as nome');
            $this->db->from('tb_descricao_forma_pagamento fp');
            $this->db->orderby('fp.nome');
            $return = $this->db->get();
            return $return->result();
        } else {
            return $retorno;
        }
    }

    function listarcliente($operador_id) {
        $this->db->select('ec.nome, oc.estoque_operador_cliente_id');
        $this->db->from('tb_estoque_operador_cliente oc');
        $this->db->join('tb_estoque_cliente ec', 'ec.estoque_cliente_id = oc.cliente_id');
        $this->db->where('oc.operador_id', $operador_id);
        $this->db->where('oc.ativo', 't');
        $return = $this->db->get();
        return $return->result();
    }

    function testaclienterepetidos($cliente_id) {
        $operador_id = $this->session->userdata('operador_id');
        $this->db->select('estoque_operador_cliente_id');
        $this->db->from('tb_estoque_operador_cliente oc');
        $this->db->join('tb_operador o', 'o.operador_id = oc.operador_id');
        $this->db->where('oc.operador_id', $operador_id);
        $this->db->where('oc.cliente_id', $cliente_id);
        $this->db->where('oc.ativo', 't');
        $this->db->where('o.ativo', 't');
        $return = $this->db->get();
        return $return->result();
    }

    function listardadoscliente($cliente_id) {
        $this->db->select('vendedor_id,
                           descricaopagamento');
        $this->db->from('tb_estoque_cliente ec');
        $this->db->where('ativo', 'true');
        $this->db->where('estoque_cliente_id', $cliente_id);
        $return = $this->db->get();
        return $return->result();
    }

    function listaroperadores($operador_id) {
        $this->db->select('operador_id,
                            usuario');
        $this->db->from('tb_operador');
        $this->db->where('ativo', 'true');
        $this->db->where('operador_id', $operador_id);
        $return = $this->db->get();
        return $return->result();
    }

    function gravarclientes() {
        try {
            /* inicia o mapeamento no banco */
            $this->db->set('operador_id', $_POST['txtoperador_id']);
            $this->db->set('cliente_id', $_POST['clientes_id']);
            $horario = date("Y-m-d H:i:s");
            $operador_id = $this->session->userdata('operador_id');
            $this->db->set('data_cadastro', $horario);
            $this->db->set('operador_cadastro', $operador_id);
            $this->db->insert('tb_estoque_operador_cliente');
            $erro = $this->db->_error_message();
            if (trim($erro) != "") // erro de banco
                return -1;
            else
                $estoque_menu_produtos_id = $this->db->insert_id();

            return $estoque_menu_produtos_id;
        } catch (Exception $exc) {
            return -1;
        }
    }

    function excluirclientes($operado_cliente) {

        $horario = date("Y-m-d H:i:s");
        $operador_id = $this->session->userdata('operador_id');
        $this->db->set('ativo', 'f');
        $this->db->set('data_atualizacao', $horario);
        $this->db->set('operador_atualizacao', $operador_id);
        $this->db->where('estoque_operador_cliente_id', $operado_cliente);
        $this->db->update('tb_estoque_operador_cliente');
        $erro = $this->db->_error_message();
        if (trim($erro) != "") // erro de banco
            return -1;
        else
            return 0;
    }

    function excluir($estoque_cliente_id) {

        $horario = date("Y-m-d H:i:s");
        $operador_id = $this->session->userdata('operador_id');
        $this->db->set('ativo', 'f');
        $this->db->set('data_atualizacao', $horario);
        $this->db->set('operador_atualizacao', $operador_id);
        $this->db->where('estoque_cliente_id', $estoque_cliente_id);
        $this->db->update('tb_estoque_cliente');
        $erro = $this->db->_error_message();
        if (trim($erro) != "") // erro de banco
            return -1;
        else
            return 0;
    }

    function gravar() {
        try {
            if ($_POST['criarcredor'] == "on") {
                $this->db->set('razao_social', $_POST['txtrazaosocial']);
                if ($_POST['txtCNPJ'] != '') {
                    $this->db->set('cnpj', str_replace("/", "", str_replace("-", "", str_replace(".", "", $_POST['txtCNPJ']))));
                } else {
                    $this->db->set('cnpj', null);
                }
                $this->db->set('telefone', str_replace("(", "", str_replace(")", "", str_replace("-", "", $_POST['telefone']))));
                $this->db->set('celular', str_replace("(", "", str_replace(")", "", str_replace("-", "", $_POST['celular']))));
                if ($_POST['txttipo_id'] != '') {
                    $this->db->set('tipo_logradouro_id', $_POST['txttipo_id']);
                }
                if ($_POST['municipio_id'] != '') {
                    $this->db->set('municipio_id', $_POST['municipio_id']);
                }
                $this->db->set('logradouro', $_POST['endereco']);
                $this->db->set('numero', $_POST['numero']);
                $this->db->set('bairro', $_POST['bairro']);
                $this->db->set('complemento', $_POST['complemento']);
                $horario = date("Y-m-d H:i:s");
                $operador_id = $this->session->userdata('operador_id');
                $this->db->set('data_cadastro', $horario);
                $this->db->set('operador_cadastro', $operador_id);
                $this->db->insert('tb_financeiro_credor_devedor');
                $financeiro_credor_devedor_id = $this->db->insert_id();
            }


            if ($_POST['criarcredor'] == "on") {
                $this->db->set('credor_devedor_id', $financeiro_credor_devedor_id);
            } elseif ($_POST['credor_devedor'] != "") {
                $this->db->set('credor_devedor_id', $_POST['credor_devedor']);
            }

            /* inicia o mapeamento no banco */
            $estoque_cliente_id = $_POST['txtestoqueclienteid'];
            $this->db->set('indicadorie', $_POST['indIEdest']);
            $this->db->set('nome', $_POST['txtfantasia']);
            $this->db->set('menu_id', $_POST['menu']);
            $this->db->set('telefone', $_POST['telefone']);
            $this->db->set('celular', str_replace("(", "", str_replace(")", "", str_replace("-", "", $_POST['celular']))));
            $this->db->set('razao_social', $_POST['txtrazaosocial']);
            $this->db->set('cep', $_POST['txtCep']);
            $this->db->set('logradouro', $_POST['endereco']);
            $this->db->set('numero', $_POST['numero']);
            $this->db->set('bairro', $_POST['bairro']);
            $this->db->set('complemento', $_POST['complemento']);
            $this->db->set('email', $_POST['email']);
            if ($_POST['txtCNPJ'] != '') {
                $this->db->set('cnpj', str_replace("/", "", str_replace(".", "", $_POST['txtCNPJ'])));
            }
            if ($_POST['sala'] != '') {
                $this->db->set('sala_id', $_POST['sala']);
            }

            if ($_POST['saida'] != '') {
                $this->db->set('saida', 'true');
            } else {
                $this->db->set('saida', 'false');
            }

            if ($_POST['descricaopagamento'] != '') {
                $this->db->set('descricaopagamento', $_POST['descricaopagamento']);
            }
            if ($_POST['vendedor_id'] != '') {
                $this->db->set('vendedor_id', $_POST['vendedor_id']);
            }
            if ($_POST['municipio_id'] != '') {
                $this->db->set('municipio_id', $_POST['municipio_id']);
            }
            if ($_POST['txttipo_id'] != '') {
                $this->db->set('tipo_logradouro_id', $_POST['txttipo_id']);
            }
            $this->db->set('inscricao_estadual', $_POST['inscricaoestadual']);

            $horario = date("Y-m-d H:i:s");
            $operador_id = $this->session->userdata('operador_id');

            if ($_POST['txtestoqueclienteid'] == "") {// insert
                $this->db->set('data_cadastro', $horario);
                $this->db->set('operador_cadastro', $operador_id);
                $this->db->insert('tb_estoque_cliente');
                $erro = $this->db->_error_message();
                if (trim($erro) != "") // erro de banco
                    return -1;
                else
                    $estoque_cliente_id = $this->db->insert_id();
            }
            else { // update
                $this->db->set('data_atualizacao', $horario);
                $this->db->set('operador_atualizacao', $operador_id);
                $exame_cliente_id = $_POST['txtestoqueclienteid'];
                $this->db->where('estoque_cliente_id', $estoque_cliente_id);
                $this->db->update('tb_estoque_cliente');
            }

            return $estoque_cliente_id;
        } catch (Exception $exc) {
            return -1;
        }
    }

    private function instanciar($estoque_cliente_id) {

        if ($estoque_cliente_id != 0) {
            $this->db->select('ec.*, m.nome as municipio_nome, tl.descricao');
            $this->db->from('tb_estoque_cliente ec');
            $this->db->join('tb_municipio m', 'm.municipio_id = ec.municipio_id', 'left');
            $this->db->join('tb_tipo_logradouro tl', 'tl.tipo_logradouro_id = ec.tipo_logradouro_id', 'left');
            $this->db->where("estoque_cliente_id", $estoque_cliente_id);
            $query = $this->db->get();
            $return = $query->result();
            $this->_estoque_cliente_id = $estoque_cliente_id;
            $this->_nome = $return[0]->nome;
            $this->_telefone = $return[0]->telefone;
            $this->_cnpj = $return[0]->cnpj;
            $this->_cep = $return[0]->cep;
            $this->_bairro = $return[0]->bairro;
            $this->_logradouro = $return[0]->logradouro;
            $this->_inscricao_estadual = $return[0]->inscricao_estadual;
            $this->_numero = $return[0]->numero;
            $this->_complemento = $return[0]->complemento;
            $this->_municipio_id = $return[0]->municipio_id;
            $this->_municipio_nome = $return[0]->municipio_nome;
            $this->_tipo_logradouro_id = $return[0]->tipo_logradouro_id;
            $this->_celular = $return[0]->celular;
            $this->_email = $return[0]->email;
            $this->_razao_social = $return[0]->razao_social;
            $this->_menu_id = $return[0]->menu_id;
            $this->_sala_id = $return[0]->sala_id;
            $this->_saida = $return[0]->saida;
            $this->_credor_devedor_id = $return[0]->credor_devedor_id;
            $this->_descricaopagamento = $return[0]->descricaopagamento;
            $this->_vendedor_id= $return[0]->vendedor_id;
        } else {
            $this->_estoque_cliente_id = null;
        }
    }

}

?>
