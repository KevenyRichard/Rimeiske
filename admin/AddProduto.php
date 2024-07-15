<?php
session_start();
require_once "../estruturas/conexao.php";
$pdo = conectar();

if($_SESSION['tipo'] == 0){
    header("Refresh: 0;url=../index.php");
    echo ("ERRO!");
}

$_SESSION['showAlert'] = false;

$sqlloc = "SELECT * FROM local;";
$stmtloc = $pdo->prepare($sqlloc);
$stmtloc->execute();
$locais = $stmtloc->fetchAll();

//DATA MINIMA E MAXIMA DO INPUT
$sql = "SELECT CURRENT_TIMESTAMP;";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$CurrentData = $stmt->fetchAll();
$DataBanco = $CurrentData[0]['CURRENT_TIMESTAMP'];

$sqlBusca = "SELECT patrimonio FROM produto;";
$stmtBusca = $pdo->prepare($sqlBusca);
$stmtBusca->execute();
$patrimonioVerify = $stmtBusca->fetchAll(PDO::FETCH_COLUMN);

$dataMin = new DateTime($DataBanco);
$dataMax = new DateTime($DataBanco);
$dataMin->modify('-25 years');
$garantiaMin = $dataMin->format('Y-m-d');
$garantiaMax = $dataMax->format('Y-m-d');

if (isset($_POST['btnAdd'])) { 
    $produto = isset($_POST['produto']) ? $_POST['produto'] : null;
    $quantidade = isset($_POST['quantidade']) ? $_POST['quantidade'] : null;
    $local = isset($_POST['local']) ? $_POST['local'] : null;
    $data = isset($_POST['data_entrada']) ? $_POST['data_entrada'] : null;
    // validando os dados vindos (opcional)
    if (empty($produto)) {
        $erros[] = "Necessário informar o nome do produto";
    }
    if (empty($quantidade)) {
        $erros[] = "Necessário informar a quantidade do produto";
    }
    if (empty($local)) {
        $erros[] = "Necessário informar o local do produto";
    }
    if (empty($data)) {
        $erros[] = "Necessário informar a data de entrada do produto no local";
    }
    if (!empty($erros)) {
        // Há erros, vamos construir a mensagem de erro
        unset($_SESSION['alert']);
        $_SESSION['alert'] = implode('<br>', $erros);
        $_SESSION['alertTitle'] = "Erro ao adicionar produto";
        $_SESSION['msgPositiva'] = "OK";
        $_SESSION['showAlert'] = true;
        unset($erros);
    }
    else{
        $sql = "INSERT INTO produto (nome_produto, categoria, quantidade, ativo) VALUES (:c, 1, :a, 1);";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':c', $produto);
        $stmt->bindParam(':a', $quantidade);
        if ($stmt->execute()) {
            $cod_prod = $pdo->lastInsertId();
            $sqladd = "INSERT INTO produto_local (fk_produto, local_atual, local_original, data_entrada) VALUES (:b, :d, :d, :e);";
            $stmtadd = $pdo->prepare($sqladd);
            $stmtadd->bindParam(':b', $cod_prod);
            $stmtadd->bindParam(':d', $local);
            $stmtadd->bindParam(':e', $data);
            if ($stmtadd->execute()) {
                $_SESSION['alertTitle'] = "Produto Adicionado";
                $_SESSION['alert'] = "Sucesso ao adicionar o produto!";
                $_SESSION['msgPositiva'] = "OK";
                $_SESSION['showAlert'] = true;
                header("Refresh: 2;url=EstoqueTemp.php");        
            }
            else{
                $_SESSION['alertTitle'] = "Erro ao adicionar o produto";
                $_SESSION['alert'] = "Não foi possivel Inserir o produto, tente novamente!..";
                $_SESSION['msgPositiva'] = "OK";
                $_SESSION['showAlert'] = true;
                header("Refresh: 2;url=EstoqueTemp.php");
            }
        }
    }
}
?>
<!-- Estrutura Inicial -->
<?php include "../estruturas/estrut.php";?>
<!-- MENU-->
<?php include "../estruturas/menu.php";?>
        <div class="dados">
            <form class="tabela_modif" method="POST">
                <div>
                    <label for="nome_produto">Nome do Produto: </label>
                    <input type="text" id="nome_produto" name="produto" method="POST">
                </div>
                <div>
                    <label for="quant_prod">Quantidade: </label>
                    <input type="number" id="quant_prod" name="quantidade" method="POST">
                </div>
                <div>
                    <label for="loc_prod">Local Do Produto</label>
                    <br>
                    <select class="local" id="loc_prod" name="local" method="POST">
                        <?php foreach($locais as $l){?>
                        <option value="<?php echo $l['cod_local']; ?>"><?php echo $l['nome_local'];?></option>
                        <?php }?>
                    </select>
                </div>
                <div>
                    <label for="data_entrada_loc">Data de Entrada do Item no Local<br><label>
                    <input type="date" id="data_entrada_loc" name="data_entrada" min="<?php echo $garantiaMin;?>" max="<?php echo $garantiaMax;?>">
                </div>
                <div>
                    <input class="btn btn_verde" id="buttonAdicionar" type="submit" name="btnAdd">
                </div>
            </form>
        </div>
<!-- Footer - rodapé -->
<?php include "../estruturas/rodape.php";
// -- Fim Footer - rodapé --