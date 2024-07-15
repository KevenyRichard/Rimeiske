<!DOCTYPE html>
<?php
session_start();
if($_SESSION['logado'] == false){
  echo '<script type="text/javascript">alert("Porfavor, Faça Login!");</script>';
  header("Refresh: 0;url=index.php");
}
require_once "estruturas/conexao.php";
$pdo = conectar();

$_SESSION['menu'] = 0;
$_SESSION['showAlert'] = false;

if($_SESSION['tipo'] == 1 || $_SESSION['tipo'] == 2){ 
  $sql = "SELECT * FROM cliente c JOIN setor s ON s.cod_setor = c.acervo WHERE cod_cliente = :c;";
}
else{
  $sql = "SELECT * FROM cliente WHERE cod_cliente = :c;";
}
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':c', $_SESSION['cod_cliente']);
$stmt->execute();
$p = $stmt->fetch();

if (isset($_POST['btnSair'])) {
  session_destroy();
  echo '<script type="text/javascript">alert("Deslogado!");</script>';
  header("Refresh: 0;url=index.php");
}
?>
<!-- Estrutura Inicial -->
<?php include "estruturas/estrut.php";?>
<!-- MENU-->
<?php include "estruturas/menu.php";?>

    <div class="dados">
      <table>
        <tr>
          <div class="campo_perfil">
              Nome: <?php echo $p['nome_cliente'];?>
          </div>
        </tr>
        <tr>
          <div class="campo_perfil">
              CPF: <?php echo $p['cpf'];?>
          </div>
        </tr>
        <tr>
          <div class="campo_perfil">
          <?php
              if ($p['tipo'] == 2 or $p['tipo'] == 1){ echo "Matricula: ";}
              else { echo "RA: ";}
              echo $p['ra'];?>
          </div>
        </tr>
        <tr>
          <div class="campo_perfil">
              Nivel de Acesso: <?php echo $p['tipo'];
              if ($p['tipo'] == 2){ echo "  -  Administrador";}
              if ($p['tipo'] == 1){ echo "  -  Funcionario";}
              if ($p['tipo'] == 0){echo "  -  Usuario Comum";}?>
          </div>
        </tr>
        <?php if($_SESSION['tipo'] == 1 || $_SESSION['tipo'] == 2) {?>
        <tr>
          <div class="campo_perfil">
            Setor: <?php echo $p['nome_setor'];?>
          </div>
        </tr>
        <?php }?>
        <tr>
          <div class="botoes_perfil">
            <a class="btn btn_alterar" href="AltPerfil.php?id=<?php echo $_SESSION['cod_cliente'];?>">Alterar Informações</a>
            <form method="POST">
              <button type="submit" class="btn btn_sair btn_amarelo" name="btnSair" method="POST">Sair</button>
              <button type="submit" class="btn btn_deletarPerfil btn_vermelho" value="<?php echo $_SESSION['cod_cliente'];?>" name="btnExc">Excluir Conta</button>
            </form>
          </div>
          <?php
          // INICIO DELETAR PERFIL
          if(isset($_POST['btnExc'])) {
            $id = $_POST['btnExc'];
            $sqldado = "SELECT * FROM chamado s JOIN agenda a ON s.cod_chamado = a.fk_chamado WHERE s.fk_cliente = :c or s.funcionario = :c;";
            $stmtdado = $pdo->prepare($sqldado);
            $stmtdado->bindParam(':c', $id);
            if($stmtdado->execute()){
              $dado = $stmtdado->fetchAll();
              foreach ($dado as $d) {
                if($d['data_finalizado'] == null and $d['funcionario'] == null){
                  $sqlserv = "DELETE FROM chamado WHERE fk_cliente = :a;";
                  $stmtserv = $pdo->prepare($sqlserv);
                  $stmtserv->bindParam(':a', $id);
                  $stmtserv->execute();
                }
                elseif($d['fk_func'] == null){
                  $sqlagn = "DELETE FROM agenda WHERE cod_agenda = :l;";
                  $stmtagn = $pdo->prepare($sqlagn);
                  $stmtagn->bindParam(':l', $d['cod_agenda']);
                  $stmtagn->execute();
                }
                else{
                  $sqlserv = "UPDATE chamado SET fk_cliente = null WHERE cod_chamado = :c";
                  $stmtserv = $pdo->prepare($sqlserv);
                  $stmtserv->bindParam(':c', $d['cod_chamado']);
                  $stmtserv->execute();
                }
              }
              $sqlperf = "DELETE FROM cliente WHERE cod_cliente = :p;";
              $stmtperf = $pdo->prepare($sqlperf);
              $stmtperf->bindParam(":p", $id);
              if ($stmtperf->execute()) {
                header("Refresh: 3;url=index.php");
                session_destroy();?>
                <div class="sucesso">Registro excluído com sucesso!</div>
              <?php }
            }
            
            else {
                header("Refresh: 3;url=Perfil.php");?>
                <div class="fracasso">Não foi possivel excluir o registro, tente novamente..</div>
            <?php
            }
          }
          // FIM DELETAR PERFIL
          ?>
        </tr>
      </table>
    </div>
<!-- Footer - rodapé -->
<?php include "estruturas/rodape.php";
// -- Fim Footer - rodapé --