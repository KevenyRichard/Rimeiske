<?php 
function formatarCPF($cpf) {
    $doc = preg_replace("/[^0-9]/", "", $cpf);
    $qtd = strlen($cpf);

    if($qtd === 11) {
        $cpfFormatado = substr($doc, 0, 3) . '.' .
                        substr($doc, 3, 3) . '.' .
                        substr($doc, 6, 3) . '-' .
                        substr($doc, 9, 2);
        return $cpfFormatado;
    }
    else {
        return 0;
    }
}
?>