<?php
session_start();
require_once "../estruturas/conexao.php";
$pdo = conectar();

if($_SESSION['tipo'] == 0){
    header("Refresh: 0;url=../VerServicos.php");
    echo ("ERRO!");
}

$_SESSION['menu'] = 1;
$id = $_GET['id'];
$_SESSION['showAlert'] = false;

$sql = "SELECT CURRENT_TIMESTAMP;";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$CurrentData = $stmt->fetchAll();
$DataBanco = $CurrentData[0]['CURRENT_TIMESTAMP'];

$dataMin = new DateTime($DataBanco);
$dataMax = new DateTime($DataBanco);
$dataMin->modify('-5 years');
$dataMax->modify('+5 years');
$garantiaMin = $dataMin->format('Y-m-d');
$garantiaMax = $dataMax->format('Y-m-d');
?>
<!-- Estrutura Inicial -->
<?php include "../estruturas/estrut.php";?>
<!-- MENU-->
<?php include "../estruturas/menu.php";?>

        <div class="dados">
            <form class="tabela_modif" method="POST">
                <div>
                    <label for="dt_garantia">Data fim da Garantia</label>
                    <input id="dt_garantia" class="garantia" type="date" name="garantia" min="<?php echo $garantiaMin;?>" max="<?php echo $garantiaMax;?>" method="POST">
                </div>
                <div>
                    <input class="btn btn-success" id="buttonAdicionar" type="submit" name="btnAdd" method="POST">
                </div>
            </form>
                <div>
            <?php
            if (isset($_POST['btnAdd'])) { 
                $garantia = isset($_POST['garantia']) ? $_POST['garantia'] : null;
                // validando os dados vindos (opcional)
                if (empty($garantia)) {
                    $_SESSION['alert'] = "Necessário informar uma data para o fim da garantia";
                    $_SESSION['alertTitle'] = "Você Não Preencheu Todos Os Campos";
                    $_SESSION['showAlert'] = true;
                }
                $sqlgar = "UPDATE produto SET garantia = :p WHERE cod_produto = :c;";
                $stmtgar = $pdo->prepare($sqlgar);
                $stmtgar->bindParam(':p', $garantia);
                $stmtgar->bindParam(':c', $id);
                if ($stmtgar->execute()) {
                    $_SESSION['alert'] = "Sucesso ao adicionar garantia do produto!";
                    $_SESSION['alertTitle'] = "Garantia Adicionada!";
                    $_SESSION['showAlert'] = true;    
                    header("Refresh: 2;url=Estoque.php");
                }
                else{
                    $_SESSION['alert'] = "Não foi possivel Inserir a garantia do produto, tente novamente!..";
                    $_SESSION['alertTitle'] = "Ocorreu um erro ao adicionar uma garantia";
                    $_SESSION['showAlert'] = true;
                    header("Refresh: 2;url=Estoque.php");
                }
            }
            ?>
        </div>        
    </div>
<!-- Footer - rodapé -->
<?php include "../estruturas/rodape.php";
// -- Fim Footer - rodapé --