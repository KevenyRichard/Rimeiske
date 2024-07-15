<?php
session_start();
if($_SESSION['logado'] == false){
  header("Refresh: 0;url=Login.php");
  echo '<script type="text/javascript">alert("Porfavor, Faça Login!");</script>';
}
require_once "estruturas/conexao.php";
$pdo = conectar();

$_SESSION['menu'] = 0;
$_SESSION['showAlert'] = false;

$sqldepto = "SELECT DISTINCT departamento FROM local;";
$stmtdepto = $pdo->prepare($sqldepto);
$stmtdepto->execute();
$departamento = $stmtdepto->fetchAll(PDO::FETCH_COLUMN);

$sqlloc = "SELECT * FROM local ORDER BY nome_local ASC;";
$stmtloc = $pdo->prepare($sqlloc);
$stmtloc->execute();
$locais = $stmtloc->fetchAll();

$sql = "SELECT CURRENT_TIMESTAMP;";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$CurrentData = $stmt->fetchAll();
$DataBanco = $CurrentData[0]['CURRENT_TIMESTAMP'];

$dataMin = new DateTime($DataBanco);
$dataMax = new DateTime($DataBanco);
$dataMax->modify('+2 months');
$prazoMin = $dataMin->format('Y-m-d H:i');
$prazoMax = $dataMax->format('Y-m-d H:i');

if (isset($_POST['btnSalvar'])) {
    // buscar o conteudo dos inputs
    $selecionar_local = isset($_POST['selecionar_local']) ? $_POST['selecionar_local'] : null;
    $descricao_problema = isset($_POST['descricao_problema']) ? $_POST['descricao_problema'] : null;
    $data_agendado = isset($_POST['data_agendado']) ? $_POST['data_agendado'] : null;

    $_SESSION['cod_cliente'];

    $sqlchamad = "SELECT * FROM chamado WHERE data_agendado = :data_agendado AND fk_cliente = :cod_cliente";
    $stmtchamad = $pdo->prepare($sqlchamad);
    $stmtchamad->bindParam(':data_agendado', $data_agendado, PDO::PARAM_STR);
    $stmtchamad->bindParam(':cod_cliente', $_SESSION['cod_cliente'], PDO::PARAM_INT);
    $stmtchamad->execute();
    $chamado = $stmtchamad->fetchAll(PDO::FETCH_COLUMN);
    // validando os dados vindos (opcional)
    if(!empty($chamado)){
      $erros[] = "Parece que este chamado ja foi realizado por você, aguarde um momento e tente novamente..";
    }
    if (empty($selecionar_local)) {
      $erros[] = "Necessário informar o local do serviço";
    }
    if (empty($descricao_problema)) {
      $erros[] = "Necessário informar uma descrição do problema";
    }
    if (empty($data_agendado)){
      $erros[] = "Necessário informar uma data para a realização do serviço";
    }

    if (!empty($erros)) {
      // Há erros, vamos construir a mensagem de erro
      unset($_SESSION['alert']);
      $_SESSION['alert'] = implode('<br>', $erros);
      $_SESSION['alertTitle'] = "Você nâo preencheu todos os campos";
      if($erros[0] == "Parece que este chamado ja foi feito por outra pessoa, aguarde um momento e tente novamente.."){
        $_SESSION['alertTitle'] = "Chamado Já Solicitado";
        header("Refresh: 2;url=VerChamados.php");
      }
      $_SESSION['msgPositiva'] = "OK";
      $_SESSION['showAlert'] = true;
      unset($erros);
  }
  else{
  // criando a sql
    $sql = ("INSERT INTO chamado (descricao, fk_local, fk_cliente, fk_setor, data_agendado, nome_cli) VALUES (:c,:n,:w,1,:d,:s);");
  // preparando o sql para receber os dados
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':c', $descricao_problema);
    $stmt->bindParam(':n', $selecionar_local);
    $stmt->bindParam(':w', $_SESSION['cod_cliente']);
    $stmt->bindParam(':d', $data_agendado);
    $stmt->bindParam(':s', $_SESSION['nome_cliente']);
    $stmt->execute();
    $user=$stmt->fetch();
    if ($stmt->rowCount() > 0) {
      $_SESSION['alertTitle'] = "Chamado Realizado";
      $_SESSION['alert'] = "Solicitação de chamado realizada com sucesso!";
      $_SESSION['msgPositiva'] = null;
      $_SESSION['showAlert'] = true;
      header("Refresh: 2;url=VerChamados.php");
    }
    else{
      $_SESSION['alertTitle'] = "Erro na solicitação";
      $_SESSION['alert'] = "O chamado não foi realizado!";
      $_SESSION['msgPositiva'] = "Entendido";
      $_SESSION['showAlert'] = true;
    }
  }
}
?>
<!-- Estrutura Inicial -->
<?php include "estruturas/estrut.php";?>
<!-- MENU-->
<?php include "estruturas/menu.php";?>

      <form class="chamado" method="POST">
        <div class="campo_chamado1">
          <br>
          <?php foreach($departamento as $d){?>
            <input type="radio" class="campo_dpto" name="departamento" id="<?php echo "cp_departamento" . $d;?>" value="<?php echo $d;?>">
            <label for="<?php echo "cp_departamento" . $d;?>">
              <?php if($d == 0){ echo "Salas De Aula";}
              elseif($d == 1){ echo "Departamentos";}
              elseif($d == 2){ echo "Locais";}?>
            </label>
          <?php }?>
        </div>
        
        <?php foreach($departamento as $d){?>
          <div class="campo_chamado">
            <label class="cp_serv" id="label_serv_<?php echo $d;?>" for="local_serv_<?php echo $d;?>" style="display: none;">Local Do Serviço</label>
            <select class="cp_local" id="local_serv_<?php echo $d;?>" name="selecionar_local" style="display:none";>
              <?php foreach($locais as $l){
                if($l['departamento'] == $d){?>
                  <option value="<?php echo $l['cod_local']; ?>"><?php echo $l['nome_local'];?></option>
                <?php }
              }?>
            </select>
          </div>
        <?php }?>
        
        <div class="campo_chamado">
          <label for="desc_serv">Descrição Do Serviço</label>
          <input class="campo_insercao" type="text" id="desc_serv" name="descricao_problema">
        </div>
  
        <div class="campo_chamado">
          <label for="prazo_serv">Prazo Para Realização Do Serviço<label>
          <input class="campo_insercao" type="datetime-local" id="prazo_serv" name="data_agendado" min="<?php echo $prazoMin;?>" max="<?php echo $prazoMax;?>">
        </div>
        <div class="campo_chamado">
          <input class="btn btn_chamado btn_vermelho" id="buttonLimpar" type="reset" name="btnLimpar">
          <input class="btn btn_chamado btn_verde" id="buttonSalvar" type="submit" name="btnSalvar">
        </div>
      </form>
<!-- Footer - rodapé -->
<?php include "estruturas/rodape.php";
// -- Fim Footer - rodapé --