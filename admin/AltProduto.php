<?php
session_start();
//chamando a conexão com o bd
include_once "../estruturas/conexao.php";

//criando uma variavel que guarda a conexão com o banco
$pdo = conectar();

if($_SESSION['tipo'] == 0){
    header("Refresh: 0;url=../index.php");
    echo ("ERRO!");}

// pegando o id que deve ser alterado
$id = $_GET['id'];
$_SESSION['showAlert'] = false;

// sql para buscar somente o registro escolhido
$sqlprod = "SELECT * FROM produto p JOIN produto_local f ON f.fk_produto = p.cod_produto JOIN local l ON f.local_atual = l.cod_local WHERE cod_produto = :c;";
$stmtprod = $pdo->prepare($sqlprod);
$stmtprod->bindParam(":c", $id);
$stmtprod->execute();
$result = $stmtprod->fetch(PDO::FETCH_ASSOC);

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

$dataMin = new DateTime($DataBanco);
$dataMin1 = new DateTime($DataBanco);
$dataMax = new DateTime($DataBanco);
$dataMin1->modify('-25 years');
$dataMax->modify('+25 years');
$dataEnt = $dataMin1->format('Y-m-d');
$garantiaMin = $dataMin->format('Y-m-d');
$garantiaMax = $dataMax->format('Y-m-d');

// pegando os dados correspondente ao id selecionado
// PDO::FETCH_ASSOC = $variavel['campo']
// PDO::FETCH_OBJ = $variavel->campo

    if (isset($_POST['btnAlterar'])) {
        $nome = isset($_POST['produto']) ? $_POST['produto'] : null;
        $quantidade = isset($_POST['quantidade']) ? $_POST['quantidade'] : null;
        $status = isset($_POST['status']) ? $_POST['status'] : null;
        $garantia = isset($_POST['garantia']) ? $_POST['garantia'] : null;
        $local = isset($_POST['local']) ? $_POST['local'] : null;
        $data = isset($_POST['data_entrada']) ? $_POST['data_entrada'] : null;
    
        $sql = "UPDATE produto SET nome_produto = :c, quantidade = :d, ativo = :f, garantia = :h WHERE cod_produto = :g";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':c', $nome);
        $stmt->bindParam(':d', $quantidade);
        $stmt->bindParam(':f', $status);
        $stmt->bindParam(':h', $garantia);
        $stmt->bindParam(':g', $id);
        if ($stmt->execute()) {
            $sqladd = "UPDATE produto_local SET local_atual = :d, data_entrada = :e WHERE fk_produto = :b;";
            $stmtadd = $pdo->prepare($sqladd);
            $stmtadd->bindParam(':b', $id);
            $stmtadd->bindParam(':d', $local);
            $stmtadd->bindParam(':e', $data);
            if ($stmtadd->execute()) {
                $_SESSION['alertTitle'] = "Produto Alterado";
                $_SESSION['alert'] = "Produto alterado com sucesso!";
                $_SESSION['msgPositiva'] = "OK";
                $_SESSION['showAlert'] = true;
                if($result['categoria'] == 1){
                    header("Refresh: 3;url=EstoqueTemp.php");
                }
                header("Refresh: 3;url=Estoque.php");
            }
            else{
                $_SESSION['alertTitle'] = "Erro ao alterar o produto";
                $_SESSION['alert'] = "Não foi possivel alterar o produto!";
                $_SESSION['msgPositiva'] = "OK";
                $_SESSION['showAlert'] = true;
                header("Refresh: 3;url=EstoqueTemp.php");
            }
        }
    }
?>
<!-- Estrutura Inicial -->
<?php include "../estruturas/estrut.php";?>
<!-- MENU-->
<?php include "../estruturas/menu.php";?>

        <div class="dados">
            <form class="tabela_modif" method="post">
                <div>
                    <label>Nome Do Produto: </label>
                    <input type="text" name="produto" value="<?php echo $result['nome_produto']; ?>">
                </div>
                <div>
                    <label for=""><b>Quantidade Em Estoque: </b></label>
                    <input type="number" name="quantidade" value="<?php echo $result['quantidade']; ?>">
                </div>
                <div>
                    <label for="cp_status"><b>Status: </b></label>
                    <select name="status" id="cp_status" method="POST">
                        <option value="<?php echo $result['ativo']; ?>">Atual - <?php if($result['ativo'] == 0){echo "Inativo";} if($result['ativo'] == 1){echo "Ativo";}?></option>
                        <?php if($result['ativo'] == 0){?>
                        <option value="1" method="POST">Ativo</option>
                        <?php }
                        if($result['ativo'] == 1){?>
                        <option value="0" method="POST">Inativo</option>
                        <?php }?>
                    </select>
                </div>
                <div>
                    <label for="cp_locais"><b>Local Do Produto</b></label>
                    <br>
                    <select class="local" name="local" id="cp_locais" method="POST">
                        <option value="<?php echo $result['cod_local']; ?>">Atual - <?php echo $result['nome_local'];?></option>
                        <?php foreach($locais as $l){?>
                            <option value="<?php echo $l['cod_local']; ?>"><?php echo $l['nome_local'];?></option>
                        <?php }?>
                    </select>
                </div>
                <div>
                    <label><b>Data de Entrada do Item no Local<br></b><label>
                    <input type="date" name="data_entrada" value="<?php echo $result['data_entrada']; ?>" min="<?php echo $dataEnt;?>" max="<?php echo $garantiaMin;?>">
                </div>
                <div>
                    <label><b>Data fim da Garantia</b></label>
                    <input class="garantia" type="date" name="garantia" value="<?php if($result['garantia'] !== null){ echo $result['garantia'];} else {echo "is null";}?>" min="<?php echo $garantiaMin;?>" max="<?php echo $garantiaMax;?>" method="POST">
                </div>
                <div>
                    <input class="btn btn_verde" type="submit" name="btnAlterar" value="Alterar">
                </div>
            </form>
        </div>
<!-- Footer - rodapé -->
<?php include "../estruturas/rodape.php";
// -- Fim Footer - rodapé --