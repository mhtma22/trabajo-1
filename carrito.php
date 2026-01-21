<?php
header("Content-Type: application/json");

// Conexión a BD
$dsn = 'mysql:dbname=shopweb;host=127.0.0.1';
$usuario = 'root';
$clave = '';
$cn = new PDO($dsn, $usuario, $clave);

// VERIFICAR LA ACCIÓN
$accion = $_GET["accion"] ?? null;

//  LISTAR CARRITO
if ($accion === "listar") {
    $usuario_id = $_GET["usuario_id"];

    $sql = $cn->prepare("CALL sp_listar_carrito(:usuario_id)");
    $sql->bindParam(":usuario_id", $usuario_id);
    $sql->execute();

    echo json_encode($sql->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

//  AGREGAR PRODUCTO
if ($accion === "agregar") {
    $usuario_id = $_POST["usuario_id"];
    $producto_id = $_POST["producto_id"];
    $talla = $_POST["talla"];
    $color = $_POST["color"];

    $sql = $cn->prepare("CALL sp_agregar_carrito(:usuario_id, :producto_id, :talla, :color)");
    $sql->bindParam(":usuario_id", $usuario_id);
    $sql->bindParam(":producto_id", $producto_id);
    $sql->bindParam(":talla", $talla);
    $sql->bindParam(":color", $color);
    $sql->execute();

    echo json_encode(["mensaje" => "Producto agregado al carrito"]);
    exit;
}


// ACTUALIZAR PRODUCTO

if ($accion === "actualizar") {
    $id = $_POST["id"];
    $talla = $_POST["talla"];
    $color = $_POST["color"];

    $sql = $cn->prepare("CALL sp_actualizar_carrito(:id, :talla, :color)");
    $sql->bindParam(":id", $id);
    $sql->bindParam(":talla", $talla);
    $sql->bindParam(":color", $color);
    $sql->execute();

    echo json_encode(["mensaje" => "Producto actualizado"]);
    exit;
}

//  ELIMINAR PRODUCTO
if ($accion === "eliminar") {
    $id = $_GET["id"];

    $sql = $cn->prepare("CALL sp_eliminar_carrito(:id)");
    $sql->bindParam(":id", $id);
    $sql->execute();

    echo json_encode(["mensaje" => "Producto eliminado del carrito"]);
    exit;
}

echo json_encode(["error" => "Acción no válida"]);
?>
