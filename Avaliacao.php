<?php
session_start();
if (isset($_SESSION['logado'])) {
  //tudo certo
}
else {
  $_SESSION['logado'] = false;
}

if($_SESSION['logado'] == false){
  echo '<script type="text/javascript">alert("Porfavor, Faça Login!");</script>';
  header("Refresh: 0;url=index.php");
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

$_SESSION['alert'] = null;
if(isset($_POST['btnAvaliar'])){
  $avaliacao = isset($_POST['avaliacao']) ? $_POST['avaliacao'] : null;
  $sql = "INSERT INTO avaliacao (descricao_avaliacao, fk_avaliador) VALUES (:a, :c);";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':a', $avaliacao);
  $stmt->bindParam(':c', $_SESSION['cod_cliente']);
  if($stmt->execute()){
    $_SESSION['alert'] = "Muito Obrigado Pela Sua Avaliação!";
    header("Refresh: 3;url=VerChamados.php");
  }
  else{
    $_SESSION['alert'] = "Sentimos Muito, Ocorreu Um Erro!";
    header("Refresh: 3;url=VerChamados.php");
  }
}
?>
<!-- Estrutura Inicial -->
<?php include "estruturas/estrut.php";?>
<!-- IMPORTS -->
<!-- FIM IMPORTS -->
<!-- MENU-->
<?php include "estruturas/menu.php";?>
      <div class="painel_avaliacao">
        <p>Faça Sua Avaliação Do Nosso Sistema</p>
        <form class="form_avaliacao" method="POST">
          <div>
            <label for="cp_avaliacao">Encontrou Algum Erro No Sistema?<br>Explique Abaixo<br></label>
            <textarea id="cp_avaliacao" name="avaliacao" rows="4" cols="50"></textarea>
          </div>
          
          <div class="botoes_form">
            <a href="<?php if($_SESSION['tipo'] !== 2){ echo "VerChamados.php";} else{ echo "admin/Gerenciamento.php";}?>" class="btn" id="button_retornar">Voltar</a>
            <button type="submit" class="btn" id="button_avaliar" name="btnAvaliar">Avaliar</button>
          </div>
        </form>
      </div>
<!-- Footer - rodapé -->
<?php include "estruturas/rodape.php";
// -- Fim Footer - rodapé --