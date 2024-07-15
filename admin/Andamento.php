<?php
session_start();
require_once "../estruturas/conexao.php";
$pdo = conectar();

if($_SESSION['tipo'] == 0){
    header("Refresh: 0;url=../Login.php");
    echo ("ERRO!");}

$_SESSION['menu'] = 1;
$_SESSION['showAlert'] = false;
$id = $_GET['id'];

// criando um select para pegar todos os serviços que tem na tabela.
$sqlserv = "SELECT cod_chamado, funcionario FROM chamado s JOIN agenda a ON s.cod_chamado = :c JOIN cliente d ON d.cod_cliente = a.fk_func;";
$stmtserv = $pdo->prepare($sqlserv);
$stmtserv->bindValue(':c', $id);
$stmtserv->execute();
$andamento = $stmtserv->fetch();

$sqlprodutos = "SELECT * FROM produto;";
$stmtprodutos = $pdo->prepare($sqlprodutos);
$stmtprodutos->execute();
$produtos = $stmtprodutos->fetchAll();

if (isset($_POST['btnAdd'])) { 
    $saida = isset($_POST['saida']) ? $_POST['saida'] : null;
    $retorno = isset($_POST['retorno']) ? $_POST['retorno'] : null;
    $produto = isset($_POST['produto']) ? $_POST['produto'] : null;
    $descricao = isset($_POST['descricao']) ? $_POST['descricao'] : null;
    // validando os dados vindos (opcional)
    if (empty($produto)) {
        $erros[] = "Necessário informar o produto que esta sendo retirado do local";
    }
    if (empty($saida)) {
        $erros[] = "Necessário informar a data de saída do produto retirado do local";
    }
    if (empty($retorno)) {
        $erros[] = "Necessário informar a data de retorno do produto retirado do local";
    }
    if (!empty($erros)) {
        // Há erros, vamos construir a mensagem de erro
        unset($_SESSION['alert']);
        $_SESSION['alert'] = implode('<br>', $erros);
        $_SESSION['alertTitle'] = "Erro ao dar o andamento do serviço";
        $_SESSION['msgPositiva'] = "OK";
        $_SESSION['showAlert'] = true;
        unset($erros);
    }
    $sql = "INSERT INTO andamento (saida_produto, retorno_produto, descricao_chamado, fk_cod_produto, fk_agenda, fk_cod_chamado, fk_cod_func) VALUES (:c, :a, :f, :j, :s, :d, :r);";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':c', $saida);
    $stmt->bindParam(':a', $retorno);
    $stmt->bindParam(':f', $descricao);
    $stmt->bindParam(':j', $produto);
    $stmt->bindParam(':s', $id);
    $stmt->bindParam(':d', $andamento['cod_chamado']);
    $stmt->bindParam(':r', $andamento['funcionario']);
    if($stmt->execute()) {
        $sql1 = "SELECT ativo FROM produto WHERE cod_produto = :f";
        $stmt1 = $pdo->prepare($sql1);
        $stmt1->bindParam(':f', $produto);
        if ($stmt1->execute()) {
            $status = $stmt1->fetch();
            if($status == 1){
                $sql2 = "UPDATE produto SET ativo = 0 WHERE cod_produto = :f";
                $stmt2 = $pdo->prepare($sql2);
                $stmt2->bindParam(':f', $produto);
                if($stmt2->execute()){
                    $_SESSION['alertTitle'] = "Produto Desativado";
                    $_SESSION['alert'] = "Sucesso no andamento do serviço!";
                    $_SESSION['msgPositiva'] = "OK";
                    $_SESSION['showAlert'] = true;
                    header("Refresh: 3;url=Agenda.php");
                }
            }
            else{
                $_SESSION['alertTitle'] = "Andamento do serviço";
                $_SESSION['alert'] = "Sucesso no andamento do serviço!";
                $_SESSION['msgPositiva'] = "OK";
                $_SESSION['showAlert'] = true;
                header("Refresh: 3;url=Agenda.php");
            }
        }
    }
    else{
        $_SESSION['alertTitle'] = "Erro ao dar andamento do serviço";
        $_SESSION['alert'] = "Não foi possivel Inserir os dados de andamento do serviço, tente novamente!..";
        $_SESSION['msgPositiva'] = "OK";
        $_SESSION['showAlert'] = true;
        header("Refresh: 3;url=Agenda.php");
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
                    <label>Produto do Serviço Designado</label>
                    <select name="produto" method="POST">
                    <?php foreach($produtos as $p){ ?>
                        <option method="POST" value="<?php echo $p['cod_produto']; ?>"><?php echo $p['nome_produto']; ?></option>
                        <?php }?>
                    </select>
                </div>
                <div>
                    <label>Descrição do Serviço Realizado</label>
                    <input type="text" name="descricao" method="POST">
                </div>
                <div>
                    <label>Saida do Local</label>
                    <input type="datetime-local" name="saida" method="POST">
                </div>
                <div>
                    <label>Retorno ao Local</label>
                    <input type="datetime-local" name="retorno" method="POST">
                </div>
                <div>
                    <button class="btn btn_verde" id="buttonAdd" type="submit" name="btnAdd">Adicionar Andamento</button>
                </div>
            </form>
        </div>        
<!-- Footer - rodapé -->
<?php include "../estruturas/rodape.php";
// -- Fim Footer - rodapé --