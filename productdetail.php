<?php

$dsn = 'mysql:dbname=shopweb;host=127.0.0.1';
$usuario = 'root';
$clave = '';
$cn = new PDO($dsn, $usuario, $clave);

if (isset($_GET["cat"])) {
    $cat = $_GET["cat"];

    $sql = "SELECT * FROM vista_productos WHERE categoria_id = :cat";
    $rs = $cn->prepare($sql);
    $rs->bindParam(':cat', $cat);
    $rs->execute();

    $rows = $rs->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);
    exit;
}

if (isset($_GET["id"])) {
    $id = $_GET["id"];

    $sql = "SELECT * FROM vista_productos WHERE id = :id";
    $rs = $cn->prepare($sql);
    $rs->bindParam(':id', $id);
    $rs->execute();

    echo json_encode($rs->fetch(PDO::FETCH_ASSOC));
    exit;
}

echo json_encode(["error" => "Solicitudes incorrectas"]);

