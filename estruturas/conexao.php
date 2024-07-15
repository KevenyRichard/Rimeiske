<?php

/*
tipo = tipo do banco de dados (ex. mysql, oracle, postsgre, ms sql server)
local = onde esta o banco (online é o endereço e a porta)
dbname = nome do seu banco
usuario

$pdo = new PDO('tipo:host=local;dbname=bancoDados', usuario, senha);

*/
    function conectar(){
        $pdo = new PDO("mysql:dbname=Rimeiski; host=localhost", "root", "");
        return $pdo;
    }

?>