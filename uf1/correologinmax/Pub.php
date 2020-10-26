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
        include "Funciones.php";
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_REQUEST["aceptar"])) {
                if ($_REQUEST["aceptar"] == "si"){
                    setcookie("aceptado", 1, time() + 365 * 24 * 60 * 60);
                    header("Location: https://dawjavi.insjoaquimmir.cat/mlarrosa/M07/Pagweb/Pub.php");
                } else {
                    header("location://www.google.es");
                }
            } else {
                if (comprobar_email($_REQUEST["email"]) && comprobar_contra($_REQUEST["contra"])) {
                    if ($_REQUEST["recordar"] == 1) {
                        echo "<p>Hola</p>";
                    } else {
                        $_SESSION['email'] = ($_REQUEST['email']);
                        $_SESSION['contra'] = ($_REQUEST['contra']);
                        header("Location: https://dawjavi.insjoaquimmir.cat/mlarrosa/M07/Pagweb/Priv.php");
                    }
                }else {
                    echo "<p>Datos erroneos</p>";
                }
            }
        }
        if (!isset($_COOKIE["aceptado"])) {
    ?>

<form method="post">
    <label>Esta pagina utiliza cookies, quieres?</label><br>
    <button type="submit" name="aceptar" value="si".></button>
    <button type="submit" name="aceptar" value="No".></button>

</form>
<?php
    } else{
?>

<form method="post">
    <label>Email: </label><input type ="text" name="email"/><br>
    <label>Contraseña: </label><input type ="text" name="contra"/><br>
    <button type="submit">Iniciar sesion</button>
</form>
<?php
    }
?>
</body>
</html>