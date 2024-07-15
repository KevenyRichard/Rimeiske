<?php
session_start();
require_once "../estruturas/conexao.php";
$pdo = conectar();

if($_SESSION['tipo'] == 0){
    header("Refresh: 0;url=../VerServicos.php");
    echo ("ERRO!");
}
$_SESSION['showAlert'] = false;
$id = $_GET['id'];

$sqlserv = "SELECT cod_agenda FROM agenda WHERE fk_chamado = :c;";
$stmtserv = $pdo->prepare($sqlserv);
$stmtserv->bindParam(':c', $id);
$stmtserv->execute();
$delServ = $stmtserv->fetch();
?>
<!-- Estrutura Inicial -->
<?php include "../estruturas/estrut.php";?>
<!-- MENU-->
<?php include "../estruturas/menu.php";?>

            <div class="dados">
                <form class="tabela_modif" method="POST">
                    <div>
                        <label>Motivo do Cancelamento: </label>
                        <input type="text" name="cancelamento" method="POST">
                    </div>
                    <div>
                        <button class="btn btn_vermelho" id="buttonCancelar" type="submit" name="btnCancel">Cancelar Atendimento</button>
                    </div>
                </form>
            <div>
                <?php
                if (isset($_POST['btnCancel'])) {
                    $motivo = isset($_POST['cancelamento']) ? $_POST['cancelamento'] : null;
                    if (empty($motivo)) {
                        $_SESSION['alertTitle'] = "Você não preencheu todos os campos";
                        $_SESSION['alert'] = "Necessário informar o motivo do cancelamento!";
                        $_SESSION['msgPositiva'] = "Entendido";
                        $_SESSION['showAlert'] = true;
                    }
                    else{
                        $sql = "SELECT ativo, cod_produto FROM produto p JOIN andamento a ON a.fk_cod_produto = p.cod_produto WHERE a.fk_agenda = :c";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(":c", $delServ['cod_agenda']);
                        if ($stmt->execute()) {
                            $status = $stmt->fetch();
                            if($status['ativo'] == 0){
                                $sql = "UPDATE produto SET ativo = 1 WHERE cod_produto = :f";
                                $stmt = $pdo->prepare($sql);
                                $stmt->bindParam(':f', $status['cod_produto']);
                                if($stmt->execute()){
                                    $_SESSION['alert'] = "O item da agenda foi ativado novamente";
                                    $_SESSION['alertTitle'] = "Item ativo novamente";
                                    $_SESSION['msgPositiva'] = "Entendido";
                                    $_SESSION['showAlert'] = true;
                                }
                                else{
                                    $_SESSION['alert'] = "Não foi possivel ativar o item novamente";
                                    $_SESSION['alertTitle'] = "Erro ao ativar item";
                                    $_SESSION['msgPositiva'] = "Entendido";
                                    $_SESSION['showAlert'] = true;
                                }
                            }
                        }
                        $sql = "DELETE FROM andamento WHERE fk_agenda = :c";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(":c", $delServ['cod_agenda']);
                        if ($stmt->execute()) {
                            $sqlagn = ("DELETE FROM agenda WHERE cod_agenda = :c;");
                            $stmtagn = $pdo->prepare($sqlagn); 
                            $stmtagn->bindValue(':c', $delServ['cod_agenda']);
                            if ($stmtagn->execute()) {
                                $sqlupd = "UPDATE chamado SET funcionario = :c, nome_func = :d, cancelamento = :s WHERE cod_chamado = :g;";
                                $stmtupd = $pdo->prepare($sqlupd);
                                $stmtupd->bindValue(':c', null);
                                $stmtupd->bindValue(':d', null);
                                $stmtupd->bindParam(':s', $motivo);
                                $stmtupd->bindValue(':g', $id);
                                if ($stmtupd->execute()) {
                                    $_SESSION['alert'] = "O serviço foi removido da sua agenda!";
                                    $_SESSION['alertTitle'] = "Serviço Removido Da Sua Agenda";
                                    $_SESSION['msgPositiva'] = "Entendido";
                                    $_SESSION['showAlert'] = true;
                                    header("Refresh: 3;url=Agenda.php");
                                }
                            }
                        }
                        else{
                            $_SESSION['alert'] = "Não foi possivel cancelar o serviço da sua agenda!";
                            $_SESSION['alertTitle'] = "Erro ao remover um serviço da sua agenda";
                            $_SESSION['msgPositiva'] = "Entendido";
                            $_SESSION['showAlert'] = true;
                            header("Refresh: 3;url=Agenda.php");
                        }
                    }
                }?>
            </div>
            </div>
        </div>        
    </div>
<!-- Footer - rodapé -->
<?php include "../estruturas/rodape.php";?>
<!-- Fim Footer - rodapé -->