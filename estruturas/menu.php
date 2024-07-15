</head>
<body>
    <div id="magic-box">
        <header id="header">
            <div class="menu-animated" onclick="menuAnimated(this)">
                <div class="bar1"></div>
                <div class="bar2"></div>
                <div class="bar3"></div>
            </div>
            <nav id="tb-menu">
                <div id="logo">
                    <img src="
                    <?php if($_SESSION['menu'] == 1){?>
                        ../<?php }?>img/rimeiski sem fundo.png" width="40px" height="40px" alt="LOGO">
                    <p><a href="<?php if($_SESSION['menu'] == 1){ echo "../";}?>index.php" class="logo-nome">Rimeiski</a></p>
                </div>
                <ul class="menu_vertical">
                    <!-- MENU INICIAL NVL-0 Deslogado-->
                    <?php if($_SESSION['logado'] === false){?>
                    <li><a href="index.php"><img src="img/IconLogin.png" alt="Icone Login"> Login e Cadastro</a></li>
                    <li class="avaliacao"><a href="<?php if($_SESSION['menu'] == 1){ echo "../";}?>Avaliacao.php" class="avaliacao"><img src="img/IconAvaliation.png" alt="Icone Avaliação"> Avalie nosso sistema</a></li>
                    <?php }?>
                    
                    <!-- MENU NVL-0 LOGADO -->
                    <?php if($_SESSION['logado'] === true and $_SESSION['tipo'] == 0){?>
                    <li><a href="Perfil.php"><img src="img/IconProfileRED.png" alt="Icone Perfil"> Perfil</a></li>
                    <li><a href="VerChamados.php"><img src="img/IconList.png" alt="Icone Solicitações"> Suas Solicitações</a></li>
                    <li><a href="SolicitarServico.php"><img src="img/IconSolicitation.png" alt="Icone Chamado"> Solicitar Serviço</a></li>
                    <li class="avaliacao"><a href="<?php if($_SESSION['menu'] == 1){ echo "../";}?>avaliacao.php"><img src="img/IconAvaliation.png" alt="Icone Avaliação"> Avalie nosso sistema</a></li>
                    <?php }?>

                    <!-- MENU NVL-1 LOGADO -->
                    <?php if($_SESSION['logado'] === true and $_SESSION['tipo'] == 1){?>
                    <li><a href="<?php if($_SESSION['menu'] == 1){ echo "../";}?>Perfil.php"><img src="<?php if($_SESSION['menu'] == 1){?>../<?php }?>img/IconProfileRED.png" alt="Icone Perfil"> Perfil</a></li>
                    <li><a href="<?php if($_SESSION['menu'] == 0){ echo "admin/";}?>Estoque.php"><img src="<?php if($_SESSION['menu'] == 1){?>../<?php }?>img/IconStorage.png" alt="Icone Estoque"> Estoque</a></li>
                    <li><a href="<?php if($_SESSION['menu'] == 0){ echo "admin/";}?>Agenda.php"><img src="<?php if($_SESSION['menu'] == 1){?>../<?php }?>img/IconAvaliation.png" alt="Icone Agenda"> Agenda</a></li>
                    <li><a href="<?php if($_SESSION['menu'] == 1){ echo "../";}?>VerChamados.php"><img src="<?php if($_SESSION['menu'] == 1){?>../<?php }?>img/IconList.png" alt="Icone Solicitações"> Suas Solicitações</a></li>
                    <li><a href="<?php if($_SESSION['menu'] == 0){ echo "admin/";}?>VerServicos.php"><img src="<?php if($_SESSION['menu'] == 1){?>../<?php }?>img/IconServices.png" alt="Icone Serviços"> Ver Serviços</a></li>
                    <li><a href="<?php if($_SESSION['menu'] == 1){ echo "../";}?>SolicitarServico.php"><img src="<?php if($_SESSION['menu'] == 1){?>../<?php }?>img/IconSolicitation.png" alt="Icone Chamado"> Solicitar Serviço</a></li>
                    <li class="avaliacao"><a href="<?php if($_SESSION['menu'] == 1){ echo "../";}?>avaliacao.php"><img src="<?php if($_SESSION['menu'] == 1){?>../<?php }?>img/IconAvaliation.png" alt="Icone Avaliação"> Avalie nosso sistema</a></li>
                    <?php }?>

                    <!-- MENU NVL-2 LOGADO -->
                     <?php if($_SESSION['logado'] === true and $_SESSION['tipo'] == 2){?>
                    <li><a href="<?php if($_SESSION['menu'] == 1){ echo "../";}?>Perfil.php"><img src="<?php if($_SESSION['menu'] == 1){?>../<?php }?>img/IconProfileRED.png" alt="Icone Perfil"> Perfil</a></li>
                    <li><a href="<?php if($_SESSION['menu'] == 0){ echo "admin/";}?>Estoque.php"><img src="<?php if($_SESSION['menu'] == 1){?>../<?php }?>img/IconStorage.png" alt="Icone Estoque"> Estoque</a></li>
                    <li><a href="<?php if($_SESSION['menu'] == 0){ echo "admin/";}?>Agenda.php"><img src="<?php if($_SESSION['menu'] == 1){?>../<?php }?>img/IconAvaliation.png" alt="Icone Agenda"> Agenda</a></li>
                    <li><a href="<?php if($_SESSION['menu'] == 0){ echo "admin/";}?>Gerenciamento.php"><img src="<?php if($_SESSION['menu'] == 1){?>../<?php }?>img/IconManegement.png" alt="Icone Gerenciamento"> Gerenciar Usuarios</a></li>
                    <li><a href="<?php if($_SESSION['menu'] == 1){ echo "../";}?>VerChamados.php"><img src="<?php if($_SESSION['menu'] == 1){?>../<?php }?>img/IconList.png" alt="Icone Solicitações"> Suas Solicitações</a></li>
                    <li><a href="<?php if($_SESSION['menu'] == 0){ echo "admin/";}?>VerServicos.php"><img src="<?php if($_SESSION['menu'] == 1){?>../<?php }?>img/IconServices.png" alt="Icone Serviços"> Ver Serviços</a></li>
                    <li><a href="<?php if($_SESSION['menu'] == 1){ echo "../";}?>SolicitarServico.php"><img src="<?php if($_SESSION['menu'] == 1){?>../<?php }?>img/IconSolicitation.png" alt="Icone Chamado"> Solicitar Serviço</a></li>
                    <li><a href="<?php if($_SESSION['menu'] == 1){ echo "../";}?>index.php"><img src="<?php if($_SESSION['menu'] == 1){?>../<?php }?>img/IconLogin.png" alt="Icone Login"> Login e Cadastro</a></li>
                    <li class="avaliacao"><a href="<?php if($_SESSION['menu'] == 1){ echo "../";}?>avaliacao.php"><img src="<?php if($_SESSION['menu'] == 1){?>../<?php }?>img/IconAvaliation.png" alt="Icone Avaliação"> Avalie nosso sistema</a></li>
                    <?php }?>
                </ul>
            </nav>
        </header>
        <?php
        if($_SESSION['showAlert'] == true){?>
        <div id="box-alerta" class="alertas">
            <div class="mensagem">
                <h2><?php echo $_SESSION['alertTitle'];?></h2>
                <p><?php echo $_SESSION['alert'];?></p>
                <div class="btns">
                    <form method="POST">
                        <?php if($_SESSION['msgPositiva'] !== null){?>
                        <button type="submit" class="btn btn_verde" name="btnOK" onclick="closeAlert()"><?php echo $_SESSION['msgPositiva'];?></button>
                        <?php }
                        if($_SESSION['msgNegativa'] !== null){?>
                        <button type="submit" class="btn btn_vermelho" name="btnCancel" onclick="closeAlert()"><?php echo $_SESSION['msgNegativa'];?></button>
                        <?php }?>
                    </form>
                </div>
            </div>
        </div>
        <?php }?>
        <div id="container">