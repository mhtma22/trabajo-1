<?php

    $dsn = 'mysql:dbname=shopweb;host=127.0.0.1';
    $usuario = 'root';
    $clave = '';
    $cn = new PDO($dsn, $usuario, $clave);

    $sql = "SELECT * FROM productos";
    $rs = $cn->prepare($sql);
    $rs->execute();

    $rows = $rs->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);

?>