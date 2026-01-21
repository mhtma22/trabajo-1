<?php
header("Content-Type: application/json");
session_start();

$dsn = 'mysql:dbname=shopweb;host=127.0.0.1';
$usuario = 'root';
$clave = '';
$cn = new PDO($dsn, $usuario, $clave);

$user = $_POST["usuario"];
$pass = $_POST["clave"];

$sql = $cn->prepare("SELECT * FROM usuarios WHERE usuario = :u AND clave = :c");
$sql->bindParam(":u", $user);
$sql->bindParam(":c", $pass);
$sql->execute();

$data = $sql->fetch(PDO::FETCH_ASSOC);

if ($data) {
    $_SESSION["usuario_id"] = $data["id"];
    $_SESSION["usuario"] = $data["usuario"];
    $_SESSION["nombre"] = $data["nombre"];

    echo json_encode([
        "success" => true,
        "usuario_id" => $data["id"],
        "nombre" => $data["nombre"]
    ]);
} else {
    echo json_encode(["success" => false]);
}
