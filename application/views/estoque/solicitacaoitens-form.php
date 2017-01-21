<div class="content ficha_ceatox"> <!-- Inicio da DIV content -->
    <div class="clear"></div>
    <form name="form_solicitacaoitens" id="form_solicitacaoitens" action="<?= base_url() ?>estoque/solicitacao/gravaritens" method="post">
        <fieldset>
            <legend>Solicitacao produtos</legend>
            <div>
                <label>Nome</label>
                <input type="hidden" name="txtestoque_solicitacao_id" value="<?php echo $estoque_solicitacao_id; ?>"/>
                <input type="text" name="txtNome" class="texto10" value="<?php echo $nome[0]->nome; ?>" readonly />

            </div>
        </fieldset>
        <fieldset>
            <legend>Cadastro de Produtos</legend>
            <div>
                <label>Produtos</label>
                <select name="produto_id" id="produto_id" class="size4" required>
                    <option value=""  onclick="carregaValor('0.00')">SELECIONE</option>
                    <? foreach ($produto as $value) : ?>
                        <option value="<?= $value->estoque_produto_id; ?>"  onclick="carregaValor('<?= $value->valor_venda; ?>')">
                            <?php echo $value->descricao; ?></option>
                    <? endforeach; ?>
                </select>
            </div>
            <div>
                <label>Lote</label>
                <select name="lote" id="lote" class="size1" >
                    <option value="">Selecione</option>
                </select>
            </div>
            <div>
                <label>Quantidade</label>
                <input type="text" name="txtqtde" class="size1" alt="integer" required/>
            </div>
            <div>
                <label>Valor</label>
                <input type="text" name="valor" id="valor" alt="decimal" class="texto01" required readonly/>
            </div>
            <div>
                <label>&nbsp;</label>
                <button type="submit" name="btnEnviar">Adicionar</button>
            </div>
    </form>
</fieldset>

<fieldset>
    <?
    if ($contador > 0) {
        ?>
        <table id="table_agente_toxico" border="0">
            <thead>

                <tr>
                    <th class="tabela_header">Produto</th>
                    <th class="tabela_header">Qtde</th>
                    <th class="tabela_header">Valor (R$)</th>
                    <th class="tabela_header">&nbsp;</th>
                </tr>
            </thead>
            <?
            $valortotal = 0;
            $estilo_linha = "tabela_content01";
            ?>
            <tbody>
                <?
                foreach ($produtos as $item) {
                    ($estilo_linha == "tabela_content01") ? $estilo_linha = "tabela_content02" : $estilo_linha = "tabela_content01";
                    ?>
                    <tr>
                        <td class="<?php echo $estilo_linha; ?>"><?= $item->descricao; ?></td>
                        <td class="<?php echo $estilo_linha; ?>"><?= $item->quantidade; ?></td>
                        <td class="<?php echo $estilo_linha; ?>"><?
                            $v = (float) $item->valor_venda;
                            $a = (int) str_replace('.', '', $item->quantidade);
                            $preco = (float) $a * $v;
                            $valortotal += $preco;
                            echo "R$ <span id='valorunitario'>" . number_format($preco, 2, '.', ',') . '</span>';
                            ?></td>
                        <td class="<?php echo $estilo_linha; ?>" width="100px;">
                            <a href="<?= base_url() ?>estoque/solicitacao/excluirsolicitacao/<?= $item->estoque_solicitacao_itens_id; ?>/<?= $estoque_solicitacao_id; ?>" class="delete">
                            </a>

                        </td>
                    </tr>


                <? }
                ?>
                <tr id="tot">
                    <td class="<?php echo $estilo_linha; ?>">&nbsp;</td>
                    <td class="<?php echo $estilo_linha; ?>" id="textovalortotal"><span id="spantotal"> Total:</span> </td>
                    <td class="<?php echo $estilo_linha; ?>"><span id="spantotal">R$ <?= number_format($valortotal, 2, '.', ',') ?></span></td>
                    <td class="<?php echo $estilo_linha; ?>">&nbsp;</td>
                </tr>
            </tbody>    
            <?
        }
        ?>
        <tfoot>
            <tr>
                <th class="tabela_footer" colspan="4">
                </th>
            </tr>
        </tfoot>
    </table> 
    <br>

    <div class="bt_link">                                  
        <a onclick="javascript: window.open('<?= base_url() ?>estoque/solicitacao/gravartransportadora/<?= $estoque_solicitacao_id ?>', '_blank', 'toolbar=no,Location=no,menubar=no,scrollbars=yes,width=750,height=400');">Transportadora</a>
    </div>                                        
    <div class="bt_link">                                  
        <a  href="<?= base_url() ?>estoque/solicitacao/pesquisar" onclick="javascript: var a = confirm('Deseja realmente Liberar e Faturar a solicitacao?');
                if (a == true) {
                    window.open('<?= base_url() ?>estoque/solicitacao/liberarsolicitacaofaturar/<?= $estoque_solicitacao_id ?>', '_blank', 'toolbar=no,Location=no,menubar=no,scrollbars=yes,width=1000,height=750')};">Liberar/Faturar</a>
    </div>                                        
    <div class="bt_link">                                  
        <a onclick="javascript: return confirm('Deseja realmente Liberar a solicitacao?');" href="<?= base_url() ?>estoque/solicitacao/liberarsolicitacao/<?= $estoque_solicitacao_id ?>">Liberar</a>
    </div>
</fieldset>
</div> <!-- Final da DIV content -->

<style>
    #spantotal{

        color: black;
        font-weight: bolder;
        font-size: 18px;
    }
    #textovalortotal{
        text-align: right;
    }
    #tot td{
        background-color: #bdc3c7;
    }
</style>


<script type="text/javascript" src="<?= base_url() ?>js/jquery.validate.js"></script>
<script type="text/javascript">

            function carregaValor(valor) {
                $("#valor").val(valor);
            }

            $(function () {
                $('#produto_id').change(function () {
                    if ($(this).val()) {
                        $('.carregando').show();
                        $.getJSON('<?= base_url() ?>autocomplete/estoquepedidolote', {produto_id: $(this).val()}, function (j) {
                            options = '<option value=""></option>';
                            for (var c = 0; c < j.length; c++) {
                                var dia = j[c].validade.substring(8, 10);
                                var mes = j[c].validade.substring(5, 7);
                                var ano = j[c].validade.substring(0, 4);
                                
                                var data = dia +'/'+ mes +'/'+ ano;
                                
                                if ( j[c].lote == 'null' || j[c].lote == null ){
                                    j[c].lote = ' ';
                                }
                                
                                options += '<option value="' + j[c].estoque_entrada_id + '">LOTE: ' + j[c].lote + ' - ' + data + '</option>';
                            }
                            $('#lote').html(options).show();
                            $('.carregando').hide();
                        });
                    } else {
                        $('#lote').html('<option value="">Selecione</option>');
                    }
                });
            });


</script>