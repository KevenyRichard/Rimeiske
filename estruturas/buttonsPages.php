<th colspan="9">
    <div class="buttons_solicitacoes">
        <?php
        if ($paginaAtual > 1){?>
            <a id="PagAnt" href="?pagina=<?php echo $paginaAtual - 1; ?>" class="btn btnPequeno btn_verde">Anterior</a>
        <?php }?>

        <?php
        for ($i = 1; $i <= $totalPaginas; $i++){?>
            <a id="indicePag" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php }?>

        <?php
        if ($paginaAtual < $totalPaginas){ ?>
            <a id="PagProx" href="?pagina=<?php echo $paginaAtual + 1; ?>" class="btn btnPequeno btn_verde">Próxima</a>
        <?php }?>

    </div>
</th>