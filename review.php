<?php
    $dsn = 'mysql:dbname=shopweb;host=127.0.0.1';
    $usuario = 'root';
    $clave = '';
    $cn = new PDO($dsn, $usuario, $clave);
    $cn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Recibir datos

$producto   = $_POST["producto"];
$comentario = $_POST["comentario"];
$usuario_id = $_POST["usuario_id"];
$etiquetas  = $_POST["etiquetas"]; 

// --- SUBIR IMAGEN ---
$nombreImg = null;

if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === 0) {

    $carpeta = "uploads/";
    if (!file_exists($carpeta)) {
        mkdir($carpeta, 0777, true);
    }

    $nombreImg = time() . "_" . basename($_FILES["imagen"]["name"]);
    $rutaDestino = $carpeta . $nombreImg;

    move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino);
}

// Guardamos en base de datos
$sql = "INSERT INTO reseñas (usuario_id, producto, comentario, imagen, etiquetas)
        VALUES (:usuario, :producto, :comentario, :imagen, :etiquetas)";

$stmt = $cn->prepare($sql);

$stmt->bindValue(":usuario", $usuario_id);
$stmt->bindValue(":producto", $producto);
$stmt->bindValue(":comentario", $comentario);
$stmt->bindValue(":imagen", $nombreImg);
$stmt->bindValue(":etiquetas", $etiquetas);

$stmt->execute();

echo json_encode(["success" => true]);
?>

