<div class="content"> <!-- Inicio da DIV content -->
    <div id="accordion">
        <h3><a href="#">Manter Solicitacao de Internacao</a></h3>
        <div>
            <table>
                <thead>
                    <tr>
                        <th class="tabela_title" colspan="4">
                            Lista de Solicitacoes de Internacao
                <form method="get" action="<?php echo base_url() ?>internacao/internacao/pesquisarsolicitacaointernacao">
                    <input type="text" name="nome" value="<?php echo @$_GET['nome']; ?>" />
                    <button type="submit" name="enviar">Pesquisar</button>
                </form>
                </th>
                </tr>
                <tr>
                    <th class="tabela_header">Codigo</th>
                    <th class="tabela_header">Tempo</th>
                    <th class="tabela_header">Nome</th>
                    <th class="tabela_header">Estado</th>
                    <th class="tabela_header">Unidade</th>
                    <th class="tabela_header" width="30px;"><center></center></th>

                </tr>
                </thead>
                <?php
                $url = $this->utilitario->build_query_params(current_url(), $_GET);
                $consulta = $this->solicitacaointernacao_m->listasolicitacao($_GET);
                $total = $consulta->count_all_results();
                $limit = 10;
                isset($_GET['per_page']) ? $pagina = $_GET['per_page'] : $pagina = 0;

                if ($total > 0) {
                    ?>
                    <tbody>
                        <?php
                        $lista = $this->solicitacaointernacao_m->listasolicitacao($_GET)->orderby('p.nome')->limit($limit, $pagina)->get()->result();
                        $estilo_linha = "tabela_content01";
                        foreach ($lista as $item) {
                            ($estilo_linha == "tabela_content01") ? $estilo_linha = "tabela_content02" : $estilo_linha = "tabela_content01";
                            ?>
                            <tr>
                                <td class="<?php echo $estilo_linha; ?>"><?php echo $item->paciente_id; ?></td>
                                <td class="<?php echo $estilo_linha; ?>"><?php echo $item->data_cadastro; ?></td>
                                <td class="<?php echo $estilo_linha; ?>"><?php echo $item->paciente; ?></td>
                                <td class="<?php echo $estilo_linha; ?>"><?php echo $item->estado; ?></td>
                                <td class="<?php echo $estilo_linha; ?>"><?php echo $item->unidade; ?></td>
                                <td class="<?php echo $estilo_linha; ?>" width="30px;">
                                    <a href="<?= base_url() ?>internacao/internacao/novointernacao/<?= $item->paciente_id ?>"><center>
                                            <img border="0" title="Solicitar Internação" alt="Detalhes"
                                                 src="<?= base_url() ?>img/form/page_white_gear.png" />
                                        </center></a>
                                </td>
                            </tr>
                        </tbody>
                        <?php
                    }
                }
                ?>
                <tfoot>
                    <tr>
                        <th class="tabela_footer" colspan="7">
                            <?php $this->utilitario->paginacao($url, $total, $pagina, $limit); ?>
                            Total de registros: <?php echo $total; ?>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>


</div> <!-- Final da DIV content -->
<link rel="stylesheet" href="<?php base_url() ?>css/jquery-ui-1.8.5.custom.css">
<script type="text/javascript">
   
    $(function() {
        $( "#accordion" ).accordion();
    });

</script>