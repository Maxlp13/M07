<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php
    include "funcions.php";

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_REQUEST["volver"])) {
            header("Location: UF1-A6-ExerciciPublic.php");
        }
?>
<form method="post">
    <h3>Volver a la pagina principal: <button type="submit" name="volver" value="Si">Menu</button></h3>
</form>

<?php
        info_producto($_REQUEST["idProductes"]);
?>
<h3>Las Categorias:</h3>
<?php
        ver_categorias($_REQUEST["idProductes"]);
?>
<br><h3>Imagenes del producto:</h3>
<?php    
        tabla_imagenes($_REQUEST["idProductes"]);
    } else {
        header("Location: UF1-A6-ExerciciPublic.php");
    }
?>
</body>
</html>