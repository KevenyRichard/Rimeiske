<?php
session_start();
if (isset($_SESSION['logado'])) {
  //tudo certo
}
else {
  $_SESSION['logado'] = false;
}

if (isset($_SESSION['tipo'])) {
  //tudo certo
}
else {
  $_SESSION['tipo'] = null;
}
$_SESSION['menu'] = 0;
$_SESSION['showAlert'] = false;
require_once "estruturas/conexao.php";
$pdo = conectar();

$sqlset = "SELECT * FROM setor;";
$stmtset = $pdo->prepare($sqlset);
$stmtset->execute();
$setores = $stmtset->fetchAll();

$sqlBusca = "SELECT ra, cpf FROM cliente;";
$stmtBusca = $pdo->prepare($sqlBusca);
$stmtBusca->execute();
$identityVerify = $stmtBusca->fetchAll(PDO::FETCH_COLUMN);

if($_SESSION['tipo'] == 0 || $_SESSION['tipo'] == 1 || $_SESSION['tipo'] == null) {
  if (isset($_POST['btnCadastro'])) {
    $nome = isset($_POST['nome']) ? $_POST['nome'] : null;
    $cpf = isset($_POST['cpf']) ? $_POST['cpf'] : null;
    $ra = isset($_POST['ra']) ? $_POST['ra'] : null;
    $celular = isset($_POST['celular']) ? $_POST['celular'] : null;
    $senha = isset($_POST['senha']) ? md5($_POST['senha']) : null;
    
    if (empty($nome)) {
      echo "Necessário informar o nome completo";
      exit();
    }
    if (empty($cpf)) {
      $alert = "Necessário informar o CPF";
      exit();
    }
    elseif (strlen($cpf) !== 11 || !is_numeric($cpf)) {
		  $alert = "Formato de CPF inválido!";
      exit();
    }
    else {
      if (in_array($cpf, $identityVerify)) {
        echo '<script type="text/javascript">alert("O CPF já está cadastrado, Faça Login!");</script>';
        header("Refresh: 0;url=index.php");
        exit();
      }
    }
    if (empty($ra)) {
      $alerta = "Necessário informar o RA ou Matricula";
      exit();
    }
    if (in_array($ra, $identityVerify)) {
      echo '<script type="text/javascript">alert("Este RA ou Matricula Já Esta Cadastrado, Faça Login!");</script>';
      header("Refresh: 0;url=index.php");
      exit();
    }
    if (empty($celular)) {
      $alerta = "Necessário informar um telefone para contato";
      exit();
    }
    elseif (strlen($celular) !== 11 || !is_numeric($celular)) {
		  $alert = "Formato de numero inválido!";
    }
    if (empty($senha)) {
      echo "Necessário informar a senha";
      exit();
    }
    $sql = "INSERT INTO cliente (nome_cliente, cpf, ra, celular, senha) VALUES
    (:n,:c,:r,:l,:s);";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':n', $nome);
    $stmt->bindParam(':c', $cpf);
    $stmt->bindParam(':r', $ra);
    $stmt->bindParam(':l', $celular);
    $stmt->bindParam(':s', $senha);
    $stmt->execute();
    $user=$stmt->fetch();
    if ($stmt->rowCount() > 0) {
      header("Refresh: 2;url=index.php");
      echo ("Bem Vindo ".$nome);
    }
    else{
      echo ("Erro No Cadastro!!");
      exit();
    }
  }
}

if($_SESSION['tipo'] == 2 and $_SESSION['logado'] == true){
  if (isset($_POST['btnCadastro'])) {
    $nome = isset($_POST['nome']) ? $_POST['nome'] : null;
    $cpf = isset($_POST['cpf']) ? $_POST['cpf'] : null;
    $ra = isset($_POST['ra']) ? $_POST['ra'] : null;
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : null;
    $setor = isset($_POST['setor']) ? $_POST['setor'] : null;
    $senha = isset($_POST['senha']) ? md5($_POST['senha']) : null;

    if (empty($nome)) {
      echo "Necessário informar o nome completo";
      exit();
    }
    if (empty($cpf)) {
      echo "Necessário informar o CPF";
      exit();
    }
    elseif (strlen($cpf) !== 11 || !is_numeric($cpf)) {
      $alert = "Formato de CPF inválido!";
      exit();
    }
    else {
      if (in_array($cpf, $identityVerify)) {
        echo '<script type="text/javascript">alert("O CPF já está cadastrado, Faça Login!");</script>';
        header("Refresh: 0;url=index.php");
        exit();
      }
    }
    if (empty($ra)) {
      echo "Necessário informar o RA";
      exit();
    }
    if (in_array($ra, $identityVerify)) {
      echo '<script type="text/javascript">alert("Este RA ou Matricula Já Esta Cadastrado, Faça Login!");</script>';
      header("Refresh: 0;url=index.php");
      exit();
    }
    if (empty($setor)) {
      echo "Necessário informar o setor";
      exit();
    }
    if (empty($senha)) {
      echo "Necessário informar a senha";
      exit();
    }

      $sql = "INSERT INTO cliente (nome_cliente, cpf, ra, tipo, acervo, senha) VALUES
      (:n,:c,:r,:t,:g,:s);";
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':n', $nome);
      $stmt->bindParam(':c', $cpf);
      $stmt->bindParam(':r', $ra);
      $stmt->bindParam(':t', $tipo);
      $stmt->bindParam(':g', $setor);
      $stmt->bindParam(':s', $senha);
      $stmt->execute();
      if ($stmt->rowCount() > 0) {
        header("Refresh: 3;url=index.php");
      echo ("Usuario ".$nome." Cadastrado Com Sucesso!");
      }
      else{
        echo ("Erro No Cadastro!!");
        exit();
      }
  }
}
?>
<!-- Estrutura Inicial -->
<?php include "estruturas/estrut.php";?>
<!-- IMPORTS -->
<!-- FIM IMPORTS -->
<!-- MENU-->
<?php include "estruturas/menu.php";?>

      <div class="painel_cadastro">
        <p>Cadastro</p>
        <form class="form_cadastro" method="POST">
          <div class="campo_nome">
            <label for="nome_cadastro" class="label_cadastro">Nome Completo</label>
            <br>
            <input type="text" id="nome_cadastro" name="nome" class="input_cadastro" required>
          </div>

          <div class="campo_cpf">
            <label for="cpf_cadastro" class="label_cadastro">CPF</label>
            <br>
            <input type="text" id="cpf_cadastro" name="cpf" class="input_cadastro" minlength="11" maxlength="11" placeholder="Somente Numeros" required>
          </div>
          
          <div class="campo_celular">
            <label for="celular_cadastro" class="label_cadastro">Celular</label>
            <br>
            <input type="number" id="celular_cadastro" name="celular" class="input_cadastro" maxlength="11" placeholder="Digite um numero para contato" required>
          </div>

          <div class="campo_ra">
            <label for="matricula_cadastro" class="label_cadastro">RA ou MATRICULA</label>
            <br>
            <input type="number" id="matricula_cadastro" name="ra" class="input_cadastro" maxlength="8" required>
          </div>
          <?php
          if($_SESSION['tipo'] == 2){?>
            <div class="campo_tipo">
              <label for="nivel" class="label_cadastro">Tipo de usuario</label>
              <br>
              <select id="nivel" name="tipo" class="input_cadastro">
                <option value="">Selecione</option>
                <option value="2">Administrador</option>
                <option value="1">Funcionario</option>
                <option value="0">Usuario Comum</option>
              </select>
            </div>

            <div class="campo_setor">
              <label for="setores" class="label_cadastro">Setor:</label>
              <br>
              <select id="setores" name="setor" class="input_cadastro">
                <option value="<?php echo 'is null';?>">Nenhum</option>
                <?php
                  foreach($setores as $c){
                ?>
                    <option value="<?php echo $c['cod_setor']; ?>"><?php echo $c['nome_setor']; ?></option>
                  <?php }?>
              </select>
            </div>
          <?php }?>
            
          <div class="campo_senha">
            <label for="password-field" class="label_cadastro">Senha</label>  
            <br>          
            <input id="password-field" type="password" name="senha" class="input_cadastro" minlength="8" class="campo_senha" required>
            <span toggle="#password-field"></span>
          </div> 
          
          <div class="botoes_form">
            <a href="<?php if($_SESSION['tipo'] !== 2){ echo "index.php";} else{ echo "admin/Gerenciamento.php";}?>" class="btn" id="button_retornar">Voltar</a>
            <button type="submit" class="btn" id="button_cadastro" name="btnCadastro">Cadastrar</button>
          </div>
        </form>
      </div>
<!-- Footer - rodapé -->
<?php include "estruturas/rodape.php";
// -- Fim Footer - rodapé --