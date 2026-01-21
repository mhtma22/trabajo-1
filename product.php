<?php
    $dsn = 'mysql:dbname=shopweb;host=127.0.0.1';
    $usuario = 'root';
    $clave = '';
    $cn = new PDO($dsn, $usuario, $clave);

    if( isset($_GET["cat"]) && isset($_GET["id"])){
        $cat = $_GET["cat"];
        $id = $_GET["id"];

        $sql = "SELECT * FROM vista_productos
                where categoria_id=".$cat." and id = ". $id;

    } elseif (isset($_GET["cat"])){
        $cat = $_GET["cat"];

        $sql = "SELECT * FROM vista_productos WHERE categoria_id=".$cat ;  

    } else {
        $sql = "SELECT * FROM vista_productos";
    }

    $rs = $cn->prepare($sql);
    $rs->execute();
    //almanacenar la data en $rows
    $rows = $rs->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);
?>



