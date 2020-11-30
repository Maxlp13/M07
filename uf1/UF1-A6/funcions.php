<?php

function comprovar_campo($campo) {
    $campo = trim($campo);
    $campo = stripslashes($campo);
    $campo = htmlspecialchars($campo);
    return $campo;
}

function comprovar_email($email) {
    $email = comprovar_campo($email);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = True;
    } else {
        $emailError = False;
    }
    return $emailError;
}

function comprovar_contra($contra) {
    $contra = comprovar_campo($contra);
    if (!preg_match("/[^a-zA-Z\d]/",$contra)) {
        $contraError = True;
    } else {
        $contraError = False;
    }
    return $contraError;
}

function crear_cookie($aceptado) {
    if ($aceptado == "Si") {
        setcookie("aceptado", 1, time() + 365 * 24 * 60 * 60);
        header("Location: UF1-A6-ExerciciPublic.php");
    } else {
        header("Location: https://www.google.es");
    }
}

function cerrar_session() {
    session_unset();
    session_regenerate_id();
    session_destroy();
    header("Location: UF1-A6-ExerciciPublic.php");
}

function iniciar_sesion($user, $password) {
    if (comprovar_email($user) && comprovar_contra($password)) {
        $conn = new mysqli("localhost", "mlarrosa", "mlarrosa", "mlarrosa_Actividad05");
                
        $sql = "SELECT * FROM Usuario WHERE User='$user' and Password='$password'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $usuario = $result->fetch_assoc();
            $_SESSION["user"] = comprovar_campo($usuario["user"]);
            $_SESSION["password"] = comprovar_campo($usuario["password"]);

            $conn->close();
            if ($usuario["idrol"] == 1) {
                header("Location: UF1-A6-ExerciciPrivatAdmin.php");
            } else if ($usuario["idrol"] == 2) {
                header("Location: UF1-A6-ExerciciPrivat.php");
            }
        } else {
            echo "<p>Datos incorrectos</p>";
        }
    } else {
        echo "<p>Has escrito el correo la contraseña incorrectamente</p>";
    }
}

function registrar_usuario($user, $password, $rol) {
    if (!comprovar_email($user)) {
        echo "<p>No has escrito el correo correctamente!</p>";
    } else if (!comprovar_contra($password)) {
        echo "<p>No has escrito la contraseña correctamente!</p>";
    } else {
        $conn = new mysqli("localhost", "mlarrosa", "mlarrosa", "mlarrosa_Actividad05");
        
        $sql = "SELECT * FROM Usuario WHERE User='$user'";
        $result = $conn->query($sql);

        if ($result->num_rows == 0) {
            $sql = "INSERT INTO Usuario VALUES ('$user','$password',$rol, NULL)";
            if ($conn->query($sql) === TRUE) {
                echo "<p>Usuario registrado con exito!</p>";
            } else {
                echo "Error: ".$sql."<br>".$conn->error;
            }
        } else {
            echo "<p>Ya existe un usuario con ese correo</p>";
        }
        $conn->close();
    }
}

function modificar_usuario($user, $password, $newuser ,$newpassword) {
    if ($password != $_SESSION["password"]) {
        echo "<p>La contraseña no coincide!</p>";
    } else if (!comprovar_email($newuser)) {
        echo "<p>No has escrito el correo a modificar correctamente</p>";
    } else if (!comprovar_contra($newpassword)) {
        echo "<p>No has escrito la nueva contraseña correctamente</p>";
    } else {
        $conn = new mysqli("localhost", "mlarrosa", "mlarrosa", "mlarrosa_Actividad05");

        $sql = "SELECT user FROM Usuario WHERE User='$newuser'";
        $result = $conn->query($sql);

        if ($result->num_rows == 0) {
            $sql = "SELECT * FROM Usuario WHERE User='$user'";
            $result = $conn->query($sql);
            $usuario = $result->fetch_assoc();
            $id = $usuario["id"];

            $sql = "UPDATE Usuario SET User='$newuser' WHERE id=$id";
            $result = $conn->query($sql);
            $sql = "UPDATE Usuario SET Password='$newpassword' WHERE id=$id";
            $result = $conn->query($sql);

            $conn->close();

            $_SESSION["user"] = $newuser;
            $_SESSION["password"] = $newpassword;
            echo "Usuario actualizado!";
        } else {
            echo "<p>Ya existe un usuario con ese correo</p>";
        }
    }
}

function generar_tabla_admin() {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>CORREO</th><th>CONTRASEÑA</th><th>ROL</th></th><th colspan='2'>ACCION</th></tr>";
    $conn = new mysqli("localhost", "mlarrosa", "mlarrosa", "mlarrosa_Actividad05");
    
    $nombres = Array();
    $rol = Array();
    $id = Array();

    $sql = "SELECT id, User, role FROM usuario";
    $result = $conn->query($sql);

    while($usuario = $result->fetch_assoc()) {
        $nombres[] = $usuario["user"];
        $rol[] = $usuario["role"];
        $id[] = $usuario["id"];
    }

    for ($n=0; $n < count($nombres); $n++) { 
        echo "<form method='post'>";
        echo "<tr>
            <td><input size='1' type='text' name='id' value='".$id[$n]."' readonly></td>
            <td><input type='text' name='newuser' value='".$nombres[$n]."'></td>
            <td><input type='password' name='newpassword'></td>";

                if ($n == 0) {
                    echo "<td><select name='newrol' disabled><option value='1' selected>Administrador</option><option value='2'>Usuario</option></select></td>";
                    echo "<input type='hidden' name='newrol' value='1' />";
                } else {
                    if ($rol[$n] == 1) {
                        echo "<td><select name='newrol'><option value='1' selected>Administrador</option><option value='2'>Usuario</option></select></td>";
                    } elseif ($rol[$n] == 2) {
                        echo "<td><select name='newrol'><option value='1'>Administrador</option><option value='2' selected>Usuario</option></select></td>";
                    }
                }

                if ($n == 0) {
                    echo "<td colspan='2' style='text-align: center;'><button type='submit' name='modificar' value='si'>Modificar</button></td>";
                } else if ($n != 0) {
                    echo "<td><button type='submit' name='modificar' value='si'>Modificar</button></td>
                        <td><button type='submit' name='borrar' value='si'>Eliminar</button></td>";
                }
        echo "</tr>";
        echo "</form>";
    }
    echo "</table><br>";

    echo "<h3>Crear usuario:</h3>";
    echo "<table border='1'>
            <form method='post'>
                <tr><th>CORREO</th><th>CONTRASEÑA</th><th>ROL</th></th><th>ACCION</th></tr>
                <td><input type='text' name='reguser'></td>
                <td><input type='password' name='regpassword'></td>
                <td><select name='regrol'><option value='1'>Administrador</option><option value='2' selected>Usuario</option></select></td>
                <td><button type='submit' name='crear' value='si'>Crear</button></td>
            </form>
        </table>";
}

function borrar_usuario($user) {
    $conn = new mysqli("localhost", "mlarrosa", "mlarrosa", "mlarrosa_Actividad05");

    $sql = "SELECT id FROM Usuario WHERE User='$user'";
    $result = $conn->query($sql);
    $usuario = $result->fetch_assoc();
    $id = $usuario["id"];

    $sql = "DELETE FROM Usuario WHERE id='$id'";
    $result = $conn->query($sql);
    $conn->close();

    echo "Usuario eliminado!";
}

function modificar_usuario_admin($id, $user, $password, $rol) {
    if (!comprovar_email($user)) {
        echo "<p>No has escrito el correo a modificar correctamente</p>";
    } else if (empty($password)) {
        echo "<p>No has escrito la nueva contraseña</p>";
    } else if (!comprovar_contra($password)) {
        echo "<p>No has escrito la nueva contraseña correctamente</p>";
    } else {
        $conn = new mysqli("localhost", "mlarrosa", "mlarrosa", "mlarrosa_Actividad05");

        $sql = "SELECT user FROM Usuario WHERE User='$user'";
        $result = $conn->query($sql);

        if ($result->num_rows == 0) {
            $sql = "UPDATE Usuario SET User='$user' WHERE id=$id";
            $result = $conn->query($sql);
            $sql = "UPDATE Usuario SET Password='$password' WHERE id=$id";
            $result = $conn->query($sql);
            $sql = "UPDATE Usuario SET role='$rol' WHERE id=$id";
            $result = $conn->query($sql);

            $conn->close();

            echo "Usuario actualizado!";
        } else {
            echo "Ya existe un usuario con ese correo!";
        }
    }
}
function tabla_productos_usuario($user) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>NOMBRE</th><th>DESCRIPCION</th><th>PRECIO</th><th colspan='3'>ACCION</th></tr>";
    $conn = new mysqli("localhost", "mlarrosa", "mlarrosa", "mlarrosa_Actividad05");

    $sql = "SELECT * FROM Usuarios WHERE user='$user'";
    $result = $conn->query($sql);
    $usuario = $result->fetch_assoc();
    $id = $usuario["id"];

    $sql = "SELECT * FROM Productos WHERE idusuari='$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($producto = $result->fetch_assoc()) {
            $nombre[] = $producto["nom"];
            $desc[]  = $producto[ "descripcion"];
            $precio[] = $producto["preu"];
            $ids[] = $producto["id"];
        }
    
        for ($n=0; $n < count($nombre); $n++) {
            echo "<form method='post'>";
            echo "<tr>
                <td><input size='1' type='text' name='idProductes' value='".$ids[$n]."' readonly></td>
                <td><input type='text' name='nomprod' value='".$nombre[$n]."'></td>
                <td><textarea style='resize: none;' name='descprod' cols='60' rows='3'>".$desc[$n]."</textarea></td>
                <td><input type='text' name='preuprod' value='".$precio[$n]."€'></td>
                <td><button type='submit' name='modificarprod' value='si'>Modificar producto</button></td>
                <td><button type='submit' name='borrarprod' value='si'>Eliminar producto</button></td></form>
                <form action='UF1-A6-ExerciciImatges.php' method='post'>
                    <input type='hidden' name='idProductes' value='".$ids[$n]."'>
                    <td><button type='submit' name='imagenesprod' value='si'>Imagenes/Categorias</button></td>
                </form>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5' style='text-align: center;'><p>No tienes productos!</p></td></tr>";
    }
    
    echo "</table><br>";
}

function Tbuscador($buscar) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>NOMBRE</th><th>DESCRIPCION</th><th>PRECIO</th><th>ACCION</th></tr>";
    $conn = new mysqli("localhost", "mlarrosa", "mlarrosa", "mlarrosa_Actividad05");

    $sql = "SELECT * FROM Productos WHERE LOWER(nom) LIKE '%{$buscar}%'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($producto = $result->fetch_assoc()) {
            $nombre[] = $producto["nom"];
            $desc[]  = $producto[ "descripcion"];
            $precio[] = $producto["preu"];
            $ids[] = $producto["id"];
        }
    
        for ($n=0; $n < count($nombre); $n++) {
            echo "<form action='UF1-A6-ExerciciProducte.php' method='post'>";
            echo "<tr>
                <td><input size='1' type='text' name='idprod' value='".$ids[$n]."' readonly></td>
                <td><input type='text' name='nomprod' value='".$nombre[$n]."' readonly></td>
                <td><textarea style='resize: none;' name='descprod' cols='60' rows='3' readonly>".$desc[$n]."</textarea></td>
                <td><input type='text' name='preuprod' value='".$precio[$n]."€' readonly></td>
                <td><button type='submit' name='info' value='Si'>Mas informacion</button></td>";
            echo "</tr>";
            echo "</form>";
        }
    } else {
        echo "<tr><td colspan='5' style='text-align: center;'><p>No hay productos registrados.</p></td></tr>";
    }
    
    echo "</table><br>";
}

function tabla_productes() {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>NOMBRE</th><th>DESCRIPCION</th><th>PRECIO</th><th colspan='3'>ACCION</th></tr>";
    $conn = new mysqli("localhost", "mlarrosa", "mlarrosa", "mlarrosa_Actividad05");

    $sql = "SELECT * FROM Productos";
    $result = $conn->query($sql);


    if ($result->num_rows > 0) {
        while($producto = $result->fetch_assoc()) {
            $nombre[] = $producto["nom"];
            $desc[]  = $producto[ "descripcio"];
            $precio[] = $producto["preu"];
            $ids[] = $producto["id"];
        }
        for ($n=0; $n < count($nombre); $n++) {
            echo "<form action='UF1-A6-ExerciciProducte.php' method='post'>";
            echo "<tr>
                <td><input size='1' type='text' name='idprod' value='".$ids[$n]."' readonly></td>
                <td><input type='text' name='nomprod' value='".$nombre[$n]."' readonly></td>
                <td><textarea style='resize: none;' name='descprod' cols='60' rows='3' readonly>".$desc[$n]."</textarea></td>
                <td><input type='text' name='preuprod' value='".$precio[$n]."€' readonly></td>
                <td><button type='submit' name='info' value='Si'>Mas informacion</button></td>";
            echo "</tr>";
            echo "</form>";
        } 
    } else{
        echo "<tr><td colspan='5' style='text-align: center;'><p>No tienes productos</p></td></tr>";
    }
    echo "</table><br>";
}
function info_producto($id) {
    echo "<table border='1'>";
    echo "<tr><th>NOMBRE</th><th>DESCRIPCION</th><th>PRECIO</th></tr>";
    $conn = new mysqli("localhost", "mlarrosa", "mlarrosa", "mlarrosa_Actividad05");

    $sql = "SELECT * FROM Productos WHERE id='$id'";
    $result = $conn->query($sql);

    while($producto = $result->fetch_assoc()) {
        $nombre[] = $producto["nom"];
        $desc[]  = $producto[ "descripcion"];
        $precio[] = $producto["preu"];
        $ids[] = $producto["id"];
    }

    for ($n=0; $n < count($nombre); $n++) {
        echo "<form method='post'>";
        echo "<tr>
            <td><input type='text' name='nomprod' value='".$nombre[$n]."' readonly></td>
            <td><textarea style='resize: none;' name='descprod' cols='60' rows='3' readonly>".$desc[$n]."</textarea></td>
            <td><input type='text' name='preuprod' value='".$precio[$n]."€' readonly></td>";
        echo "</tr>";
        echo "</form>";
    }
    
    echo "</table><br>";
}

function tabla_imagenes($id) {
    $conn = new mysqli("localhost", "mlarrosa", "mlarrosa", "mlarrosa_Actividad05");

    $sql = "SELECT * FROM Imagenes WHERE idproducte='$id'";
    $result = $conn->query($sql);

    echo "<table border='1'><tr>";
    if ($result->num_rows > 0) {
        while($producto = $result->fetch_assoc()) {
            $ruta[] = $producto["ruta"];
        }
        for ($n=0; $n < count($ruta); $n++) {
            echo "<td><img src='".$ruta[$n]."'  width='300px'  height='300px'></td>";
            if ($n == 4) {
                echo "</tr><tr>";
            }
        }
    } else {
        echo "<td>No hay imagenes para el producto seleccionado</td>";
    }
    echo "</tr></table'>";
}

function subir_imagen($id) {
    $conn = new mysqli("localhost", "mlarrosa", "mlarrosa", "mlarrosa_Actividad05");
    $dir_subida = "imagenes/";
    $nombre = $_FILES['myfile']['name'];
    $ruta = $dir_subida.$nombre;

    $sql = "SELECT * FROM Imagenes WHERE idproducte='$id' and ruta='$ruta'";
    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        $fichero_subido = $dir_subida.basename($_FILES['myfile']['name']);
        if (move_uploaded_file($_FILES['myfile']['tmp_name'], $fichero_subido)) {
            $sql = "INSERT INTO Imagenes VALUES (NULL,'$nombre','$ruta', $id)";
            if ($conn->query($sql) === TRUE) {
                    echo "<p>Imagen guardada</p>";
            }
            $conn->close();
        } else {
            echo "La imagen no se subió.";
        }
    } else {
        echo "<p>No se ha podido subir la imagen, es posible que ya se haya subido o que contenga el mismo nombre que una ya subida.</p>";
    }
}

function tabla_img_usuario($id) {
    $conn = new mysqli("localhost", "mlarrosa", "mlarrosa", "mlarrosa_Actividad05");

    $sql = "SELECT * FROM Imagenes WHERE idproducte='$id'";
    $result = $conn->query($sql);

    echo "<table border='1'>";
    if ($result->num_rows > 0) {
        while($producto = $result->fetch_assoc()) {
            $ruta[] = $producto["ruta"];
            $idimg[] = $producto["id"];
        }

        for ($n=0; $n < count($ruta); $n++) {
            echo "<tr><td><img src='".$ruta[$n]."'  width='300px'  height='300px'></td>
                <form method='post'>
                    <input type='hidden' name='idprod' value='".$_REQUEST["idprod"]."'>
                    <input type='hidden' name='idimg' value='".$idimg[$n]."'>
                    <input type='hidden' name='ruta' value='".$ruta[$n]."'>
                    <td><button type='submit' name='borrar' value='si'>Borrar imagen</button></td>
                </form></tr>";
        }
    } else {
        echo "<td>No hay imagenes para el producto</td>";
    }
    echo "</tr></table'>";
}

function crear_categoria($nom) {
    $conn = new mysqli"localhost", "mlarrosa", "mlarrosa", "mlarrosa_Actividad05");

    $sql = "INSERT INTO Categorias VALUES (NULL,'$nom')";

    if ($conn->query($sql) === TRUE) {
            echo "<p>Categoria creada con exito!</p>";
    } else {
            echo "Error: ".$sql."<br>".$conn->error;
    }

    $conn->close();
}

function producto_categoria($producte, $idcategoria) {
    $conn = new mysqli("localhost", "mlarrosa", "mlarrosa", "mlarrosa_Actividad05");

    $sql = "INSERT INTO Categorias_Productos VALUES ('$idcategoria','$producte')";

    if ($conn->query($sql) === TRUE) {
        echo "<p>Categoria añadida al producto con exito!</p>";
    } else {
        echo "<p>Este producto ya tiene esta categoria!</p>";
    }

    $conn->close();
}

function comprovar_existe_categoria($idproducte, $categoria) {
    $conn = new mysqli("localhost", "mlarrosa", "mlarrosa", "mlarrosa_Actividad05");

    $categorialower = strtolower($categoria);
    $sql = "SELECT * FROM Categorias WHERE LOWER(nom) LIKE '%{$categorialower}%'";
    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        crear_categoria($categoria);
    }

    $sql = "SELECT * FROM Categorias WHERE LOWER(nom) LIKE '%{$categorialower}%'";
    $result = $conn->query($sql);
    while($categoria = $result->fetch_assoc()) {
        $id[] = $categoria["id"];
    }
    producto_categoria($idproducte, $id[0]);
}

function ver_categorias_usuario($id) {
    $conn = new mysqli("localhost", "mlarrosa", "mlarrosa", "mlarrosa_Actividad05");

    $sql = "SELECT * FROM Categorias_Productos WHERE idproducte='$id'";
    $result = $conn->query($sql);

    echo "<table border='1'>";
    if ($result->num_rows > 0) {
        while($producto = $result->fetch_assoc()) {
            $idcategoria[] = $producto["idcategoria"];
        }

        for ($n=0; $n < count($idcategoria); $n++) {
            $idcat = $idcategoria[$n];

            $sql = "SELECT * FROM Categorias WHERE id='$idcat'";
            $result = $conn->query($sql);

            while($categoria = $result->fetch_assoc()) {
                $nomcategoria[] = $categoria["nom"];
                echo "<form method='post'><tr>
                    <input type='hidden' name='idcat' value='".$idcat."'>
                    <input type='hidden' name='idprod' value='".$_REQUEST["idprod"]."'>
                    <td><input type='text' name='categoria' value='".$nomcategoria[$n]."' readonly></td>
                    <td><button type='submit' name='borrcat' value='si'>Borrar categoria</button></td>
                </tr></form>"; 
            }
        }  
    } else {
        echo "<td>Este producto no tiene categorias!</td>";
    }
    echo "</table>";
}

function borrar_categoria_producto($id) {
    $conn = new mysqli("localhost", "mlarrosa", "mlarrosa", "mlarrosa_Actividad05");

    $sql = "DELETE FROM Categorias_Productos WHERE idcategoria='$id'";
    $result = $conn->query($sql);
    $conn->close();

    echo "Categoria eliminada del producto!";
}

function ver_categorias($id) {
    $conn = new mysqli("localhost", "mlarrosa", "mlarrosa", "mlarrosa_Actividad05");

    $sql = "SELECT * FROM Categorias_Productos WHERE idproducte='$id'";
    $result = $conn->query($sql);

    echo "<table border='1'><tr>";
    if ($result->num_rows > 0) {
        while($producto = $result->fetch_assoc()) {
            $idcategoria[] = $producto["idcategoria"];
        }

        for ($n=0; $n < count($idcategoria); $n++) {
            $idcat = $idcategoria[$n];
            $sql = "SELECT * FROM Categorias WHERE id='$idcat'";
            $result = $conn->query($sql);
            while($categoria = $result->fetch_assoc()) {
                $nomcategoria[] = $categoria["nom"];
                echo "<td><input type='text' name='categoria' value='".$nomcategoria[$n]."' readonly></td>"; 
            }

            if ($n == 7) {
                echo "</tr><tr>";
            }
        }  
    } else {
        echo "<td>Este producto no tiene categorias!</td>";
    }
    echo "</tr></table>";
}

function todas_categorias() {
    $conn = new mysqli("localhost", "mlarrosa", "mlarrosa", "mlarrosa_Actividad05");

    $sql = "SELECT * FROM Categorias";
    $result = $conn->query($sql);
    echo "<option hidden selected value='' readonly></option>";
    if ($result->num_rows > 0) {
        while($categoria = $result->fetch_assoc()) {
            $idcategoria[] = $categoria["id"];
            $nomcategoria[] = $categoria["nom"];
        }

        for ($n=0; $n < count($nomcategoria); $n++) {
            echo "<option value='".$idcategoria[$n]."'>".$nomcategoria[$n]."</option>";
        }
    }
    $conn->close();
}

function tabla_buscador_categoria($categoria) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>NOMBRE</th><th>DESCRIPCION</th><th>PRECIO</th><th>ACCION</th></tr>";
    $conn = new mysqli("localhost", "mlarrosa", "mlarrosa", "mlarrosa_Actividad05");

    $sql = "SELECT * FROM Productos WHERE id IN (SELECT idproducte FROM Categorias_Productos WHERE idcategoria='$categoria')";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($producto = $result->fetch_assoc()) {
            $nombre[] = $producto["nom"];
            $desc[] = $producto["descripcio"];
            $precio[] = $producto["preu"];
            $ids[] = $producto["id"];
        }
    
        for ($n=0; $n < count($nombre); $n++) {
            echo "<form action='ExerciciProducte.php' method='post'>";
            echo "<tr>
                <td><input size='1' type='text' name='idprod' value='".$ids[$n]."' readonly></td>
                <td><input type='text' name='nomprod' value='".$nombre[$n]."' readonly></td>
                <td><textarea style='resize: none;' name='descprod' cols='60' rows='3' readonly>".$desc[$n]."</textarea></td>
                <td><input type='text' name='preuprod' value='".$precio[$n]."€' readonly></td>
                <td><button type='submit' name='info' value='Si'>Mas informacion</button></td>";
            echo "</tr>";
            echo "</form>";
        }
    } else {
        echo "<tr><td colspan='5' style='text-align: center;'><p>No hay productos registrados!</p></td></tr>";
    }
    
    echo "</table><br>";
}

?>
