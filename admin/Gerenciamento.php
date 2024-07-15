<!DOCTYPE html>
<?php
session_start();
require_once "../estruturas/conexao.php";
$pdo = conectar();

if($_SESSION['tipo'] == 0){
  header("Refresh: 0;url=../index.php");
  echo ("ERRO!");
}
if($_SESSION['tipo'] == 1){
    header("Refresh: 0;url=../index.php");
    echo ("ERRO!");
}

$_SESSION['menu'] = 1;
$_SESSION['showAlert'] = false;

$sql = "SELECT * FROM cliente c LEFT JOIN setor s ON c.acervo = s.cod_setor";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$perfis = $stmt->fetchAll();
$itens = $perfis;
include "../estruturas/indice.php";

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
      $_SESSION['alertTitle'] = "Registro Excluido";
      $_SESSION['alert'] = "O Registro Foi Excluído Com Sucesso!";
      $_SESSION['msgPositiva'] = "OK";
      $_SESSION['showAlert'] = true;
      header("Refresh: 3;url=Gerenciamento.php");
    }
  }
  
  else {
    $_SESSION['alertTitle'] = "Erro Ao Excluir Registro";
    $_SESSION['alert'] = "Não foi possivel excluir o registro, tente novamente..";
    $_SESSION['msgPositiva'] = "OK";
    $_SESSION['showAlert'] = true;
    header("Refresh: 3;url=Gerenciamento.php");
  }
}
// FIM DELETAR PERFIL
?>
<!-- Estrutura Inicial -->
<?php include "../estruturas/estrut.php";?>
<!-- MENU-->
<?php include "../estruturas/menu.php";?>

    <div class="dados_gerenciamento">
      <table class="tabela_gerenciamento">
        <thead>
          <th colspan="9"><b>TODOS OS USUARIOS</b></th>
          <tr class="gerenciamento-info">
            <th id="nome_usuario">Nome</th>
            <th id="cpf_usuario">CPF</th>
            <th id="ra_usuario">Matricula / RA</th>
            <th id="nivel_usuario">Nivel de Acesso</th>
            <th id="setor_usuario">Setor</th>
            <th id="alt_usuario">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach($itens as $p){ ?>
            <tr class="informacoes">
              <td><?php echo $p['nome_cliente'] ?></td>
                <td><?php echo $p['cpf'] ?></td>
                <td><?php echo $p['ra'] ?></td>
                <td><?php if ($p['tipo'] == 2){ echo "Administrador";} if ($p['tipo'] == 1){ echo "Funcionario";} if($p['tipo'] == 0){echo "Usuario Comum";};?></td>
                <td><?php if($p['nome_setor'] !== null){ echo $p['nome_setor'];} else { echo null;}?></td>
                <td class="botoes_usuarios">
                  <a href="../AltPerfil.php?id=<?php echo $p['cod_cliente']; ?>"><button class="btn btnPequeno btnAltPerf <?php if($_SESSION['cod_cliente'] == $p['cod_cliente'] or $p['tipo'] == 2){ echo 'btn_vermelho';} else{echo 'btn_verde';}?>" <?php if($_SESSION['cod_cliente'] == $p['cod_cliente'] or $p['tipo'] == 2){ echo 'disabled';} else{echo "enabled";}?>><?php if($_SESSION['cod_cliente'] == $p['cod_cliente'] or $p['tipo'] == 2){ echo "Indisponivel";} else{echo 'Alterar';}?></button></a>
                  <form method="POST"><button type="submit" class="btn btnPequeno <?php if($_SESSION['cod_cliente'] == $p['cod_cliente'] or $p['tipo'] == 2){ echo 'btn_amarelo';} else{echo 'btn_vermelho';}?>" name="btnExc" value="<?php echo $p['cod_cliente']; ?>" <?php if($_SESSION['cod_cliente'] == $p['cod_cliente'] or $p['tipo'] == 2){ echo 'disabled';} else{echo "enabled";}?>><?php if($_SESSION['cod_cliente'] == $p['cod_cliente'] or $p['tipo'] == 2){ echo "Indisponivel";} else{echo 'Excluir';}?></button></form>
                </td>
            </tr>
            <?php }?>
        </tbody>
        <!-- ButtonsPages-->
        <?php include "../estruturas/buttonsPages.php";?>
      </table>
    </div>
<!-- Footer - rodapé -->
<?php include "../estruturas/rodape.php";
// -- Fim Footer - rodapé --
?>