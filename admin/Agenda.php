<?php
session_start();
require_once "../estruturas/conexao.php";
$pdo = conectar();

if($_SESSION['tipo'] == 0){
    header("Refresh: 0;url=../index.php");
    echo ("ERRO!");
}
$_SESSION['menu'] = 1;
$_SESSION['showAlert'] = false;

// criando um select para pegar todos os serviços que tem na tabela.
$sqlserv = "SELECT * FROM chamado s JOIN agenda a ON s.cod_chamado = a.fk_chamado JOIN cliente f ON f.cod_cliente = s.fk_cliente JOIN setor c ON c.cod_setor = s.fk_setor JOIN local l ON l.cod_local = s.fk_local WHERE a.fk_func = :c ORDER BY s.data_agendado";
$stmtserv = $pdo->prepare($sqlserv);
$stmtserv->bindParam(':c', $_SESSION['cod_cliente']);
$stmtserv->execute();
$agenda = $stmtserv->fetchAll();
$itens = $agenda;

if (isset($_POST['btnFimServico'])) {
    $id = isset($_POST['btnFimServico']) ? $_POST['btnFimServico'] : null;
    $sqlupd = "UPDATE chamado SET data_finalizado = CURRENT_TIMESTAMP WHERE cod_chamado = :g;";
    $stmtupd = $pdo->prepare($sqlupd);
    $stmtupd->bindParam(':g', $id);
    if ($stmtupd->execute()) {
        $sqlagen = "SELECT cod_agenda FROM agenda a JOIN chamado s ON s.cod_chamado = a.fk_chamado WHERE a.fk_chamado = :c;";
        $stmtagen = $pdo->prepare($sqlagen);
        $stmtagen->bindParam(':c', $id);
        $stmtagen->execute();
        $agen = $stmtagen->fetch();

        $sql = "SELECT ativo, cod_produto FROM produto p JOIN andamento a ON a.fk_cod_produto = p.cod_produto WHERE a.fk_agenda = :c";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":c", $agen['cod_agenda']);
        if ($stmt->execute()) {
            $status = $stmt->fetch();
            if($status['ativo'] == false){
                $sql = "UPDATE produto SET ativo = true WHERE cod_produto = :f";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':f', $status['cod_produto']);
                $stmt->execute();
                $_SESSION['alert'] = "Sucesso ao ativar o produto!";
                $_SESSION['alertTitle'] = "Produto Ativado";
                $_SESSION['msgPositiva'] = "Entendido";
                $_SESSION['showAlert'] = true;
            }
            else{
                $_SESSION['alert'] = "Não foi possivel ativar o produto do chamado!";
                $_SESSION['alertTitle'] = "Erro ao ativar Produto";
                $_SESSION['msgPositiva'] = "Entendido";
                $_SESSION['showAlert'] = true;
            }
        }
        $sql = "DELETE FROM andamento WHERE fk_agenda = :c";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":c", $agen['cod_agenda']);
        if ($stmt->execute()) {
            $sql = "DELETE FROM agenda WHERE cod_agenda = :c";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":c", $agen['cod_agenda']);
            if ($stmt->execute()) {
                $_SESSION['alert'] = "Serviço Finalizado Com Sucesso!";
                $_SESSION['alertTitle'] = "Serviço Finalizado";
                $_SESSION['msgPositiva'] = "Entendido";
                $_SESSION['showAlert'] = true;
                header("Refresh: 3;url=Agenda.php");
            }
        }
    }
    else {
        $_SESSION['alert'] = "Não foi possivel finalizar o chamado!";
        $_SESSION['alertTitle'] = "Erro ao tentar finalizar o chamado";
        $_SESSION['msgPositiva'] = "Entendido";
        $_SESSION['showAlert'] = true;
        header("Refresh: 3;url=Agenda.php");
    }
}
//INDICE DOS BOTÕES DE SELEÇÃO DE PAGINA
include "../estruturas/indice.php";
?>
<!-- Estrutura Inicial -->
<?php include "../estruturas/estrut.php";?>
<!-- MENU-->
<?php include "../estruturas/menu.php";?>

        <div class="dados_servicos">
            <table class="tabela_servicos">
                <thead>
                    <th colspan="9"><b>Essas são suas tarefas</b></th>
                    <tr class="chamados-info">
                        <th id="nome_cli">Nome Do Solicitante</th>
                        <th id="data_pedido">Data Pedido</th>
                        <th id="local_serv">Local Do Serviço</th>
                        <th id="desc_problema">Descrição Do Pedido</th>
                        <th id="agenda">Data Para Realização</th>
                        <th id="opcoes">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($agenda as $a){ ?>
                    <tr class="informacoes">
                        <td><?php echo $a['nome_cliente']?></td>
                        <?php $pedidoFormatada = $a['data_pedido'];
                        $pedidoFormatada = DateTime::createFromFormat("Y-m-d H:i:s", $pedidoFormatada);?>
                        <td><?php echo $pedidoFormatada->format('d-m-Y H:i:s');?></td>
                        <td><?php echo $a['nome_local']?></td>
                        <td><?php echo $a['descricao']?></td>
                        <?php $agendaFormatada = $a['data_agendado'];
                        $agendaFormatada = DateTime::createFromFormat("Y-m-d H:i:s", $agendaFormatada);?>
                        <td><?php echo $agendaFormatada->format('d-m-Y H:i:s');?></td>
                        <td>
                            <a href="Andamento.php?id=<?php echo $a['cod_agenda']; ?>"><button class="btn btnPequeno <?php if($a['data_finalizado'] == null){ echo 'btn_verde';} else{echo 'btn_vermelho';}?>" <?php if($a['data_finalizado'] == null){ echo 'enabled';} else{echo "disabled";}?>><?php if($a['data_finalizado'] == null){ echo "Dar Andamento";} else{echo 'Finalizado';}?></button></a>
                            <form method="POST">
                                <button name="btnFimServico" class="btn btnPequeno  <?php if($a['data_finalizado'] == null){ echo 'btn_verde';} else{echo 'btn_vermelho';}?>" <?php if($a['data_finalizado'] == null){ echo 'enabled';} else{echo "disabled";}?> value="<?php echo $a['cod_chamado']; ?>">
                                <?php if($a['data_finalizado'] == null){ echo "Finalizar";} else{echo 'Finalizado';}?></button>
                            </form>
                        <?php if($a['data_finalizado'] == null){?>
                        <a href="CancelarServico.php?id=<?php echo $a['fk_chamado']; ?>"><button class="btn btnPequeno btn_vermelho">Cancelar</button></a>
                        <?php }?>
                        </td>
                    </tr>
                    <?php }?>
                </tbody>
                <!-- ButtonsPages-->
                <?php include "../estruturas/buttonsPages.php";?>
            </table>
        </div>
<!-- Footer - rodapé -->
<?php include "../estruturas/rodape.php";
// -- Fim Footer - rodapé --