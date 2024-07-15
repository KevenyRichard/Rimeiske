<?php
session_start();
if($_SESSION['logado'] == false){
    header("Refresh: 0;url=Login.php");
    echo '<script type="text/javascript">alert("Porfavor, Faça Login!");</script>';
}
require_once "estruturas/conexao.php";
$pdo = conectar();
$_SESSION['menu'] = 0;
$_SESSION['showAlert'] = false;

// criando um select para pegar todos os serviços que tem na tabela.
$sqlserv = "SELECT * FROM chamado s JOIN cliente c ON s.fk_cliente = c.cod_cliente JOIN local l ON s.fk_local = l.cod_local JOIN setor a ON s.fk_setor = a.cod_setor WHERE s.fk_cliente = :c ORDER BY s.data_agendado";
    $stmtserv = $pdo->prepare($sqlserv);
    $stmtserv->bindValue(':c', $_SESSION['cod_cliente']);
    $stmtserv->execute();
    $servicos = $stmtserv->fetchAll();

//print_r($categorias);
$itens = $servicos;

if(isset($_POST['btn_cancelamento'])) {
    $id = isset($_POST['btn_cancelamento']) ? $_POST['btn_cancelamento'] : null;
    $_SESSION['alert'] = "Solicitação Cancelada Com Sucesso!";
    $_SESSION['alertTitle'] = "Cancelar Solicitação";
    $_SESSION['msgPositiva'] = "Entendido";
    $_SESSION['showAlert'] = true;
    
    $sqldado = "DELETE FROM agenda WHERE fk_chamado = :c;";
    $stmtdado = $pdo->prepare($sqldado);
    $stmtdado->bindParam(':c', $id);
    $stmtdado->execute();

    $sqldel = "DELETE FROM chamado WHERE cod_chamado = :g;";
    $stmtdel = $pdo->prepare($sqldel);
    $stmtdel->bindValue(':g', $id);
    if ($stmtdel->execute()) {
        $_SESSION['alert'] = "Sucesso ao cancelar o chamado!";
        $_SESSION['alertTitle'] = "Chamado Cancelado";
        $_SESSION['showAlert'] = true;
        $_SESSION['msgPositiva'] = "Entendido";
        header("Refresh: 3;url=VerChamados.php");
    }
    else {
        $_SESSION['alert'] = "Não Foi Possivel Cancelar o Chamado..";
        $_SESSION['alertTitle'] = "Erro ao cancelar o chamado";
        $_SESSION['showAlert'] = true;
        $_SESSION['msgPositiva'] = "Entendido";
        header("Refresh: 3;url=VerChamados.php");
    }
}

include "estruturas/indice.php";
?>
<!-- Estrutura Inicial -->
<?php include "estruturas/estrut.php";?>
<!-- MENU-->
<?php include "estruturas/menu.php";?>

        <div class="dados_gerenciamento">
            <table class="tabela_servicos">
                <thead>
                    <th colspan="9"><b>Suas Solicitações</b></th>
                    <tr class="chamados-info">
                        <th id="data_pedido">Data Pedido</th>
                        <th id="nome_setor">Setor Responsavel</th>
                        <th id="nome_func">Funcionario</th>
                        <th id="local_serv">Local Do Serviço</th>
                        <th id="desc_problema">Descrição Do Pedido</th>
                        <th id="agenda">Data Para Realização</th>
                        <th id="conclusao">Conclusão</th>
                        <th id="confirmacao">Cancelar Solicitação?</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($itens as $s){ ?>
                    <tr class="informacoes">
                        <?php $pedidoFormatada = $s['data_pedido'];
                        $pedidoFormatada = DateTime::createFromFormat("Y-m-d H:i:s", $pedidoFormatada);?>
                        <td><?php echo $pedidoFormatada->format('d-m-Y H:i:s');?></td>
                        <td><?php echo $s['nome_setor']?></td>
                        <td><?php echo $s['nome_func']?></td>
                        <td><?php echo $s['nome_local']?></td>
                        <td><?php echo $s['descricao']?></td>
                        <?php $agendaFormatada = $s['data_agendado'];
                        $agendaFormatada = DateTime::createFromFormat("Y-m-d H:i:s", $agendaFormatada);?>
                        <td><?php echo $agendaFormatada->format('d-m-Y H:i:s');?></td>
                        <td class="<?php if($s['data_finalizado'] == null){ echo 'bg-danger';} else{echo 'bg-success';}?>">
                        <?php if($s['data_finalizado'] !== null){
                                $finalFormatada = $s['data_finalizado'];
                                $finalFormatada = DateTime::createFromFormat("Y-m-d H:i:s", $finalFormatada);?>
                                <?php echo $finalFormatada->format('d-m-Y H:i:s');?></td><?php }
                            else{?></td><?php }?>
                        <td>
                            <a class="btn btnPequeno btn_verde" href="AltServico.php?id=<?php echo $s['cod_chamado']; ?>">Alterar</a>
                            <form method="POST">
                                <button type="submit" class="btn btnPequeno btn_vermelho" name="btn_cancelamento" value="<?php echo $s['cod_chamado'];?>">Cancelar</button>
                            </form>
                        </td>
                    </tr>
                    <?php }?>
                </tbody>
                <!-- ButtonsPages-->
                <?php include "estruturas/buttonsPages.php";?>
            </table>
        </div>
<?php    
// -- Footer - rodapé --
include "estruturas/rodape.php";
// -- Fim Footer - rodapé --
?>