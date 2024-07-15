<?php
session_start();
//chamando a conexão com o bd
include_once "estruturas/conexao.php";

//criando uma variavel que guarda a conexão com o banco
$pdo = conectar();

$_SESSION['menu'] = 0;
$_SESSION['showAlert'] = false;

// pegando o id que deve ser alterado
$id = $_GET['id'];

// sql para buscar somente o registro escolhido
$sql = "SELECT * FROM chamado WHERE cod_chamado = :c";

// preparando o sql para não aceitar comandos
$stmtsql = $pdo->prepare($sql);

// trocando a interrogação pelo valor
$stmtsql->bindParam(":c", $id);

// executando o sql no banco
$stmtsql->execute();

// pegando os dados correspondente ao id selecionado
// PDO::FETCH_ASSOC = $variavel['campo']
// PDO::FETCH_OBJ = $variavel->campo

$result = $stmtsql->fetch(PDO::FETCH_ASSOC);
?>
<!-- Estrutura Inicial -->
<?php include "estruturas/estrut.php";?>
<!-- MENU-->
<?php include "estruturas/menu.php";?>

        <div class="dados">
            <form class="tabela_modif" method="post">
                <div>
                    <label for="altDescricao">Descrição: </label>
                    <input type="text" id="altDescricao" name="descricao" value="<?php echo $result['descricao']; ?>">
                </div>
                <div>
                    <label for="altData">Data Para Realização: </label>
                    <input type="datetime-local" id="altData" name="data_agendado" value="<?php echo $result['data_agendado']; ?>">
                </div>
                <div>
                    <input class="btn btn_verde" type="submit" name="btnAlterar" value="Alterar">
                </div>
                <div>
                    <?php
                        if (isset($_POST['btnAlterar'])) {
                            $descricao = isset($_POST['descricao']) ? $_POST['descricao'] : null;
                            $data_agendado = isset($_POST['data_agendado']) ? $_POST['data_agendado'] : null;
                            $sql = "UPDATE chamado SET descricao = :c, data_agendado = :d WHERE cod_chamado = :f";

                            $stmt = $pdo->prepare($sql);
                            $stmt->bindParam(':c', $descricao);
                            $stmt->bindParam(':d', $data_agendado);
                            $stmt->bindParam(':f', $id);
                            if ($stmt->execute()) {
                                header("Refresh: 3;url=VerChamados.php");?>
                                <div class="sucesso"><h1><b><center>Registro alterado com sucesso!</center></b></h1></div>
                            <?php
                            }
                            else{
                                header("Refresh: 3;url=VerChamados.php");?>
                                <div class="fracasso"><h1><b><center>Não foi possivel alterar registro, tente novamente..</center></b></h1></div>
                            <?php
                            }
                        }
                    ?>
                </div>
            </form>
        </div>
<!-- Footer - rodapé -->
<?php include "estruturas/rodape.php";
// -- Fim Footer - rodapé --