<!DOCTYPE html>
<?php
session_start();
require_once "estruturas/conexao.php";
$pdo = conectar();

$_SESSION['menu'] = 0;

if (isset($_SESSION['logado'])) {
  //tudo certo
}
else {
  $_SESSION['logado'] = false;
}
if (isset($_SESSION['alert'])) {
  //tudo certo
}
else {
  $_SESSION['alert'] = null;
}
if (isset($_SESSION['alertTitle'])) {
  //tudo certo
}
else {
  $_SESSION['alertTitle'] = null;
}
$_SESSION['showAlert'] = false;
$erros = [];
$_SESSION['msgPositiva'] = null;
$_SESSION['msgNegativa'] = null;
// -- PHP para login no sistema --
if (isset($_POST['btnLogin'])) {
  $ra = isset($_POST['ra']) ? $_POST['ra'] : null;
  $senhaNoHash = isset($_POST['senha']) ? ($_POST['senha']) : null;
  if (empty($ra)) {
    $erros[] = "Necessário informar o RA ou MATRICULA";
  }

  if (empty($senhaNoHash)) {
    $erros[] = "Necessário informar a senha";
  }

  if(!empty($senhaNoHash)){
    $senha = md5($senhaNoHash);
  }

  if (!empty($erros)) {
      // Há erros, vamos construir a mensagem de erro
      unset($_SESSION['alert']);
      $_SESSION['alert'] = implode('<br>', $erros);
      $_SESSION['alertTitle'] = "Erro no Login";
      $_SESSION['msgPositiva'] = "OK";
      $_SESSION['showAlert'] = true;
      unset($erros);
  }
  else{
    $_SESSION['showAlert'] = false;
    $sql = "SELECT * FROM cliente WHERE ra = :u AND senha = :s;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':u', $ra);
    $stmt->bindParam(':s', $senha);
    $stmt->execute();
    $user=$stmt->fetch();

    if ($stmt->rowCount() > 0) {
      $_SESSION['cod_cliente'] = $user['cod_cliente'];
      $_SESSION['nome_cliente'] = $user['nome_cliente'];
      $_SESSION['tipo'] = $user['tipo'];

      $_SESSION['logado'] = true;
      $_SESSION['alertTitle'] = "Bem Vindo";
      $_SESSION['alert'] = $_SESSION['nome_cliente'];
      $_SESSION['showAlert'] = true;

      if ($_SESSION['tipo'] == 2) {
        $_SESSION['setor'] = $user['acervo'];
        header("Refresh: 2;url=admin/VerServicos.php");
      }
      elseif ($_SESSION['tipo'] == 1) {
        $_SESSION['setor'] = $user['acervo'];
        header("Refresh: 2;url=admin/VerServicos.php");
      }
      else {
        header("Refresh: 2;url=SolicitarServico.php");
      }
    }
    else{
      $_SESSION['alertTitle'] = "Erro No Login";
      $_SESSION['alert'] = "Usuário ou senha invalidos";
      $_SESSION['msgPositiva'] = "OK";
      $_SESSION['showAlert'] = true;
    }
  }
}
?>

<!-- Estrutura Inicial -->
<?php include "estruturas/estrut.php";?>
<!-- MENU-->
<?php include "estruturas/menu.php";?>

    <div class="painel_login">
        <form method="POST">
          <div class="login_usuario">
            <label for="cp_ra"><b>RA ou Matricula</b></label>
            <br>
            <input class="cp_login" type="text" id="cp_ra" name="ra">
          </div>
            
          <div class="login_senha">
            <label for="cp_senha"><b>Senha</b></label>
            <br>
            <input type="password" name="senha" id="cp_senha" class="cp_login">
          </div>
            
            <div class="botoes_login">
              <a href="Cadastro.php" class="btn btn_cadastro" name="btnCadastro">Cadastrar</a>
              <button type="submit" id="ativar-botao" class="btn btn_login" name="btnLogin">Entrar</button>
            </div>
        </form>
    </div>
    <div class="video_introducao">
    	<iframe src="https://www.youtube.com/embed/7rucFcRuEbI" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		  <div class="texto_introducao">
        	<p>Faça Seu Campus Melhor, Relate Problemas Para Resolvermos</p>
        	<p>Todos Conectados Em Razão De Uma Universidade Melhor</p>
		  </div>
	  </div>
  </div>
<!-- Footer - rodapé -->
<?php include "estruturas/rodape.php";
// -- Fim Footer - rodapé --
?>