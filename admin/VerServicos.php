<?php
session_start();
if($_SESSION['logado'] == false){
    echo '<script type="text/javascript">alert("Porfavor, Faça Login!");</script>';
    header("Refresh: 0;url=../index.php");
}
require_once "../estruturas/conexao.php";
$pdo = conectar();

$_SESSION['showAlert'] = false;

//valida se o nivel de acesso na sessao é o mesmo que esta no banco de dados
$sqlValidate = "SELECT tipo FROM cliente WHERE cod_cliente = :c;";
$stmtValidate = $pdo->prepare($sqlValidate);
$stmtValidate->bindParam(":c", $_SESSION['cod_cliente']);
$stmtValidate->execute();
$resultValidate = $stmtValidate->fetch(PDO::FETCH_COLUMN);
if($resultValidate === $_SESSION['tipo']) {
    //tudo certo
}
else{
    $_SESSION['tipo'] = $resultValidate;
}
//validando se quem esta logado é funcionario ou administrador
if($_SESSION['tipo'] == 0){
    echo '<script type="text/javascript">alert("Faça Login Como Funcionario ou Administrador!");</script>';
    header("Refresh: 0;url=../index.php");
}
//define o nivel de acesso da pagina para 1
$_SESSION['menu'] = 1;

$sqlserv = "SELECT * FROM chamado s INNER JOIN cliente c ON s.fk_cliente = c.cod_cliente INNER JOIN local l ON s.fk_local = l.cod_local INNER JOIN setor a ON s.fk_setor = a.cod_setor WHERE s.fk_setor = :c ORDER BY s.data_agendado";
$stmtserv = $pdo->prepare($sqlserv);
$stmtserv->bindParam(':c', $_SESSION['setor']);
$stmtserv->execute();
$servicos = $stmtserv->fetchAll();

$itens = $servicos;

if (isset($_POST['btnAceitar'])) {
    $id = isset($_POST['btnAceitar']) ? $_POST['btnAceitar'] : null;
    $sqlagn = ("INSERT INTO agenda (fk_chamado, fk_func) VALUES (:c,:n);");
    $stmtagn = $pdo->prepare($sqlagn); 
    $stmtagn->bindParam(':c', $id);
    $stmtagn->bindParam(':n', $_SESSION['cod_cliente']);
    if ($stmtagn->execute()) {
        $sqlupd = "UPDATE chamado SET funcionario = :c, nome_func = :d WHERE cod_chamado = :g;";
        $stmtupd = $pdo->prepare($sqlupd);
        $stmtupd->bindParam(':c', $_SESSION['cod_cliente']);
        $stmtupd->bindParam(':d', $_SESSION['usuario']);
        $stmtupd->bindParam(':g', $id);
        if($stmtupd->execute()){
            $_SESSION['alertTitle'] = "Serviço Aceito";
            $_SESSION['alert'] = "Serviço Adicionado a Sua Agenda Com Sucesso!";
            $_SESSION['msgPositiva'] = "Entendido";
            $_SESSION['showAlert'] = true;
        }
    }
    else{
        $_SESSION['alertTitle'] = "Erro ao aceitar o serviço";
        $_SESSION['alert'] = "Não foi possivel aceitar este serviço, tente novamente!..";
        $_SESSION['msgPositiva'] = "Entendido";
        $_SESSION['showAlert'] = true;
    }
    header("Refresh: 2;url=VerServicos.php");
}

//INDICE DOS BOTÕES DE SELEÇÃO DE PAGINA
include "../estruturas/indice.php";
?>
<!-- Estrutura Inicial -->
<?php include "../estruturas/estrut.php";?>
<!-- MENU-->
<?php include "../estruturas/menu.php";?>

        <div class="dados_servicos">
            <table class="tabela_servicos">
                <thead>
                    <th colspan="9"><b>Solicitações</b></th>
                    <tr class="chamados-info">
                        <th id="nome_cli">Nome Do Solicitante</th>
                        <th id="data_pedido">Data Pedido</th>
                        <th id="nome_setor">Setor Responsavel</th>
                        <th id="local_serv">Local Do Serviço</th>
                        <th id="desc_problema">Descrição Do Pedido</th>
                        <th id="agenda">Data Para Realização</th>
                        <th id="conclusao">Conclusão</th>
                        <th id="confirmacao">Deseja Aceitar?</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($itens as $s){?>
                    <tr class="informacoes">
                        <?php
                        if($s['data_finalizado'] == null and $s['fk_cliente'] !== $_SESSION['cod_cliente']){?>
                            <td><?php echo $s['nome_cliente']?></td>
                            <?php $pedidoFormatada = $s['data_pedido'];
                            $pedidoFormatada = DateTime::createFromFormat("Y-m-d H:i:s", $pedidoFormatada);?>
                            <td><?php echo $pedidoFormatada->format('d-m-Y H:i:s');?></td>
                            <td><?php echo $s['nome_setor']?></td>
                            <td><?php echo $s['nome_local']?></td>
                            <td><?php echo $s['descricao']?></td>
                            <?php $agendaFormatada = $s['data_agendado'];
                            $agendaFormatada = DateTime::createFromFormat("Y-m-d H:i:s", $agendaFormatada);?>
                            <td><?php echo $agendaFormatada->format('d-m-Y H:i:s');?></td>
                            <td class="<?php if($s['data_finalizado'] == null){ echo 'bg-danger';} else{echo 'bg-success';}?>">
                            <?php if($s['data_finalizado'] != null){
                                    $finalFormatada = $s['data_finalizado'];
                                    $finalFormatada = DateTime::createFromFormat("Y-m-d H:i:s", $finalFormatada);?>
                                    <?php echo $finalFormatada->format('d-m-Y H:i:s');?></td><?php }
                                else{?></td><?php }?>
                            <td>
                                <form method="POST">
                                    <button name="btnAceitar" class="btn btnPequeno btn_servicos <?php if($s['data_finalizado'] == null and $s['funcionario'] == null){ echo 'btn_verde';} else{echo 'btn_vermelho';}?>" <?php if($s['data_finalizado'] == null and $s['funcionario'] == null){ echo 'enabled';} else{echo "disabled";}?> value="<?php echo $s['cod_chamado'];?>">
                                    <?php if($s['funcionario'] == null){ echo "Aceitar";} else{echo 'Indisponivel';}?></button>
                                </form>
                            </td>
                        <?php }?>
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