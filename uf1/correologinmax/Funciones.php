<?php
function comprobar_unidad($texto){
    $texto = trim($texto);
    $texto = stripslashes($texto);
    $texto = htmlspecialchars($texto);
    return $texto;

}
function comprobar_email($email) {
    $email = comprobar_unidad($email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = False;
    } else {
        $emailError = True;
    }
    return $emailError;
}
function comprobar_contra($contra) {
    $contra = comprobar_unidad($contra);
    if (!preg_match("/[a-zA-Z\d]/",$contra)) {
        $emailError = False;
    } else {
        $emailError = True;
    }
    return $emailError;
}
?>