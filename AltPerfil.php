<?php
session_start();
if($_SESSION['logado'] == false){
    header("Refresh: 0;url=Login.php");
    echo '<script type="text/javascript">alert("Porfavor, Faça Login!");</script>';
}
$_SESSION['menu'] = 0;
// pegando o id que deve ser alterado
$id = $_GET['id'];
$_SESSION['showAlert'] = false;

//chamando a conexão com o bd
include_once "estruturas/conexao.php";

//criando uma variavel que guarda a conexão com o banco
$pdo = conectar();

$sqlBusca = "SELECT ra FROM cliente;";
$stmtBusca = $pdo->prepare($sqlBusca);
$stmtBusca->execute();
$identityVerify = $stmtBusca->fetchAll(PDO::FETCH_COLUMN);

$sqlRA = "SELECT ra, acervo FROM cliente WHERE cod_cliente = $id";
$stmtRA = $pdo->prepare($sqlRA);
$stmtRA->execute();
$RAusuario = $stmtRA->fetchAll(PDO::FETCH_COLUMN);
if($RAusuario['acervo'] !== null){
    $conector = "c JOIN setor d ON c.acervo = d.cod_setor ";
}
else{
    $conector = "WHERE cod_cliente = :c;";
}
// sql para buscar somente o registro escolhido
$sql = "SELECT * FROM cliente $conector";

// preparando o sql para não aceitar comandos
$stmtsql = $pdo->prepare($sql);

// trocando a interrogação pelo valor
$stmtsql->bindParam(":c", $id);

// executando o sql no banco
$stmtsql->execute();
$result = $stmtsql->fetch(PDO::FETCH_ASSOC);

$sqlset = "SELECT * FROM setor;";
$stmtset = $pdo->prepare($sqlset);
$stmtset->execute();
$setores = $stmtset->fetchAll();

if (isset($_POST['btnAlterar'])) {
    if($_SESSION['tipo'] == 2){
        

        $nivel = isset($_POST['nivel']) ? $_POST['nivel'] : null;
        if($RAusuario['acervo'] !== null){
            $setor = isset($_POST['setor']) ? $_POST['setor'] : null;
        }
        $ra = isset($_POST['ra']) ? $_POST['ra'] : null;
        if (empty($ra)) {
            $erros[] = "Necessário informar o RA ou MATRICULA";
        }
        if($ra == $RAusuario['ra']){
            //tudo ok
        }
        else{
            if(in_array($ra, $identityVerify)) {
                $erros[] = "O RA já está cadastrado no sistema!";
            }
        } 
        if ($setor === 'is null') {
            $departamento = null;
        }
        else {
            $departamento = $setor;
        }
    }
    $senhaNoHash = isset($_POST['senha']) ? ($_POST['senha']) : null;
    
    if (empty($senhaNoHash)) {
        $erros[] = "Necessário informar a senha";
        $senha = "";
    }
    if (!empty($erros)) {
        // Há erros, vamos construir a mensagem de erro
        unset($_SESSION['alert']);
        $_SESSION['alertTitle'] = "Você nâo preencheu todos os campos";
        $_SESSION['alert'] = implode('<br>', $erros);
        $_SESSION['msgPositiva'] = "Obrigado!";
        $_SESSION['showAlert'] = true;
        unset($erros);
    }
    if($senhaNoHash == $result['senha']){
        $senha = $senhaNoHash;
    }
    else{
        $senha = md5($senhaNoHash);

        if($_SESSION['tipo'] == 0 || $_SESSION['tipo'] == 1){
            $sql = "UPDATE cliente SET senha = :s WHERE cod_cliente = :c;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':s', $senha);
            $stmt->bindParam(':c', $id);
        }

        if($_SESSION['tipo'] == 2){
            $sql = "UPDATE cliente SET tipo = :t, acervo = :s, ra = :u, senha = :p WHERE cod_cliente = :c";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':t', $nivel);
            $stmt->bindParam(':s', $departamento);
            $stmt->bindParam(':u', $ra);
            $stmt->bindParam(':p', $senha);
            $stmt->bindParam(':c', $id);
        }

        if($stmt->execute()) {
            $_SESSION['alertTitle'] = "Registro Alterado";
            $_SESSION['alert'] = "Registro alterado com sucesso!";
            $_SESSION['msgPositiva'] = "OK";
            $_SESSION['showAlert'] = true;
            if($_SESSION['tipo'] == 0 || $_SESSION['tipo'] == 1){
                header("Refresh: 3;url=Perfil.php");
            }
            if($_SESSION['tipo'] == 2){
                if($id == $_SESSION['cod_cliente']){
                    $_SESSION['tipo'] = $nivel;
                }
                header("Refresh: 3;url=admin/Gerenciamento.php");
            }
        }
        else{
            $_SESSION['alertTitle'] = "Erro ao alterar registro";
            $_SESSION['alert'] = "Não foi possivel alterar registro, tente novamente..";
            $_SESSION['msgPositiva'] = "OK";
            $_SESSION['showAlert'] = true;
            header("Refresh: 3;url=admin/Gerenciamento.php");
        }
    }
}
?>
<!-- Estrutura Inicial -->
<?php include "estruturas/estrut.php";?>
<!-- MENU-->
<?php include "estruturas/menu.php";?>
        <div class="dados">
            <form class="tabela_perfil" method="post">
                <?php if($_SESSION['tipo'] == 2) {?>
                    <div>
                        <label for="AcessLevel">Nivel De Acesso: </label>
                        <select id="AcessLevel" name="nivel">
                            <option value="<?php echo $result['tipo'];?>">Atual - <?php if($result['tipo'] == 0){echo "Usuario Comum";} if($result['tipo'] == 1){echo "Funcionario";} if($result['tipo'] == 2){echo "Administrador";}?></option>
                            <?php if($result['tipo'] == 0){?>
                            <option value="2">Administrador</option>
                            <option value="1">Funcionario</option>
                            <?php }?>
                            <?php if($result['tipo'] == 1){?>
                            <option value="2">Administrador</option>
                            <option value="0">Usuario Comum</option>
                            <?php }
                            if($result['tipo'] == 2){?>
                            <option value="1">Funcionario</option>
                            <option value="0">Usuario Comum</option>
                            <?php }?>
                        </select>
                    </div>
                    <div>
                        <label for="departamento">Setor: </label>
                        <select id="departamento" name="setor">
                            <option value="<?php echo $result['cod_setor'];?>">Atual - <?php if($result['nome_setor'] == null){ echo "Nenhum";} else{ echo $result['nome_setor'];}?></option>
                            <?php
                            foreach($setores as $c){
                                if($c['cod_setor'] !== $result['cod_setor']){?>
                                    <option value="<?php echo $c['cod_setor']; ?>"><?php echo $c['nome_setor']; ?></option>
                                <?php }
                            }?>
                            <option value="is null">Nenhum</option>
                        </select>
                    </div>
                    <div>
                        <label for="matricula">RA ou Matricula: </label>
                        <input type="text" id="matricula" name="ra" value="<?php echo $result['ra'];?>">
                    </div>
                <?php }?>
                    <div>
                        <label for="password-field">Senha: </label>
                        <input id="password-field" type="password" name="senha" value="<?php echo $result['senha'];?>">
                    </div>

                <div>
                    <input class="btn btn_verde" type="submit" name="btnAlterar" value="Alterar">
                </div>
            </form>
        </div>
<!-- Footer - rodapé -->
<?php include "estruturas/rodape.php";
// -- Fim Footer - rodapé --
?>