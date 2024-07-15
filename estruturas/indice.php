<?php
// Número de itens por página
$itensPorPagina = 10;

// Obter o número total de páginas
$totalPaginas = ceil(count($itens) / $itensPorPagina);

// Verificar a página atual
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($paginaAtual < 1) {
    $paginaAtual = 1;
} elseif ($paginaAtual > $totalPaginas) {
    $paginaAtual = $totalPaginas;
}

// Calcular o índice inicial e final dos itens a serem exibidos na página atual
$indiceInicial = ($paginaAtual - 1) * $itensPorPagina;
$indiceFinal = $indiceInicial + $itensPorPagina - 1;

// Extrair os itens para exibição na página atual
$itensPagina = array_slice($itens, $indiceInicial, $itensPorPagina);
?>