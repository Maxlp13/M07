<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_destroy();
    header("Location: Pub.php");

}   else{
    if (isset($_SESSION["email"]) && isset($_SESSION["contra"])) {
        echo "<p>Hola" .$_SESSION["email"]. "!</p>";
    
    }
    else{
        header("Location: Pub.php");

    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="post">
        <button type="submit">Salir</button>
    
</body>
</html>


<?php
}
?>
