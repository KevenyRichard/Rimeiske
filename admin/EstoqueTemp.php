<?php
session_start();
require_once "../estruturas/conexao.php";
$pdo = conectar();

$_SESSION['menu'] = 1;
$_SESSION['showAlert'] = false;

if($_SESSION['tipo'] == 0){
    header("Refresh: 0;url=../index.php");
    echo ("ERRO!");
}

$sqllocal = "SELECT * FROM produto p JOIN produto_local f ON f.fk_produto = p.cod_produto JOIN local l ON f.local_atual = l.cod_local WHERE categoria = 1;";
$stmtlocal = $pdo->prepare($sqllocal);
$stmtlocal->execute();
$produtos = $stmtlocal->fetchAll();
$itens = $produtos;
//INDICE DOS BOTÕES DE SELEÇÃO DE PAGINA
include "../estruturas/indice.php";

if (isset($_POST['btnExcluir'])) {
    $id = isset($_POST['btnExcluir']) ? $_POST['btnExcluir'] : null;
    $sql = "DELETE FROM produto_local WHERE fk_produto = :c;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":c", $id);
    $sqldel = "DELETE FROM produto WHERE cod_produto = :d;";
    $stmtdel = $pdo->prepare($sqldel);
    $stmtdel->bindParam(":d", $id);
    if ($stmt->execute()) {
        if ($stmtdel->execute()) {
            $_SESSION['alertTitle'] = "Item Excluido";
            $_SESSION['alert'] = "Item Excluido Do Estoque De Produtos!";
            $_SESSION['msgPositiva'] = "OK!";
            $_SESSION['showAlert'] = true;
            header("Refresh: 3;url=EstoqueTemp.php");
        }
    }
    else{
        $_SESSION['alertTitle'] = "Erro Ao Excluir Item";
        $_SESSION['alert'] = "Não Foi Possivel Excluir Este Item";
        $_SESSION['msgPositiva'] = "OK!";
        $_SESSION['showAlert'] = true;
        header("Refresh: 2;url=EstoqueTemp.php");
    }
}
?>
<!-- Estrutura Inicial -->
<?php include "../estruturas/estrut.php";?>
<!-- MENU-->
<?php include "../estruturas/menu.php";?>
        <div class="dados_servicos">
            <table class="tabela_servicos">
                <thead>
                    <th colspan="8">
                        <div class="Acoes_produtos">
                            <a class="btn btn_adicionarProduto btn_verde" href="AddProduto.php">Adicionar Produto</a>
                        </div>
                        <b class="title_estoque">Estoque de Produtos</b>
                        <div class="estoque_patrimonio">
                            <a class="btn btn_verde" href="estoque.php">Patrimonio Informatica</a>
                        </div>
                    </th>
                    <tr class="chamados-info">
                        <th id="cod_prod">ID</th>
                        <th id="nome_prod">Produto</th>
                        <th id="quant_estoq">Estoque</th>
                        <th id="local_atual">Localização Atual</th>
                        <th id="entrada">Data de Entrada</th>
                        <th id="status">Status</th>
                        <th id="garantia">Garantia</th>
                        <th id="opcoes">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($itensPagina as $produtos){?>
                        <tr class="informacoes">
                            <td><?php echo $produtos['cod_produto']?></td>
                            <td><?php echo $produtos['nome_produto']?></td>
                            <td><?php echo $produtos['quantidade']?></td>
                            <td><?php echo $produtos['nome_local'];?></td>
                            <?php $entradaFormatada = $produtos['data_entrada'];
                            $entradaFormatada = DateTime::createFromFormat("Y-m-d", $entradaFormatada);?>
                            <td><?php echo $entradaFormatada->format('d-m-Y');?></td>
                            <td><?php if($produtos['ativo'] == 1){ echo "Ativo";} else{ echo "Inativo";}?></td>
                            <td><?php echo $produtos['garantia']?></td>
                            <td class="buttons_estoque">
                                <a href="AltProduto.php?id=<?php echo $produtos['cod_produto']; ?>" class="btn btnPequeno btn_alterarProduto btn_verde">Alterar</a>
                                <a href="AddGarantia.php?id=<?php echo $produtos['cod_produto']; ?>"><button class="btn btnPequeno btn_garantia btn_<?php if($produtos['garantia'] == null){ echo 'verde';} else{ echo 'vermelho';}?>" <?php if($produtos['garantia'] == null){ echo 'enabled';} else{ echo 'disabled';}?>><?php if($produtos['garantia'] == null){ echo 'Adicionar Garantia';} else{ echo 'Indisponivel';}?></button></a>
                                <form method="POST"><button type="submit" class="btn btnPequeno btn_vermelho" name="btnExcluir" value="<?php echo $produtos['cod_produto'];?>">Excluir</button></form>
                            </td>
                        </tr>
                    <?php }?>
                        
                        <!-- ButtonsPages-->
                        <?php include "../estruturas/buttonsPages.php";?>
                </tbody>
                
            </table>
        </div>
<!-- Footer - rodapé -->
<?php include "../estruturas/rodape.php";?>
<!-- Fim Footer - rodapé -->