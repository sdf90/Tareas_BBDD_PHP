<?php

// COMPROBAR SI HA RECARGADO LA MISMA PÁGINA
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // INCLUIR LA PAGINA DONDE SE ENCUENTRA LAS FUNCIONES
    include "funcionesUsuario.php";

    // LLAMAR A LA FUNCIÓN
    get_Usuario();
}

// MENSAJE DE ERROR EN CASO DE QUE LOS CREDENCIALES SEAN INCORRECTOS
$mensaje_Error = "";
if (isset($_GET["error_Credenciales"]) &&  $_GET["error_Credenciales"] != "") {
    $mensaje_Error = $_GET["error_Credenciales"];
}

?>


<!Doctype html>
<html>

<head>
    <title>Registrar Usuario</title>
    <link rel="stylesheet" href="style.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>

<body>

    <!-- CONTENEDOR DONDE SE ENCUENTRA EL BOTÓN DE REGISTRAR UN NUEVO USUARIO -->
    <div class="container" id="ctn_Registrar">

        <a id="btn_Registro" class="btn btn-success" onclick="registro()" title="Nuevo usuario"><i class="bi bi-person-fill-add"></i></a>

    </div>

    <!-- FORMULARIO PARA INICIAR SESIÓN -->
    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">

        <div class="container rounded" id="cnt_Login_Total">

            <div class="container" id="cnt_Login">

                <label><strong> Usuario </strong>  </label>
                <br>
                <input type="text" name="usuario" >
                <br>
                <label><strong>Contraseña </strong>  </label>
                <br>
                <input type="password" name="password">             

            </div>

            <!-- MOSTRAR MENSAJE DE ERROR -->
            <?php if ($mensaje_Error != "") {
                echo '<p id="mensaje_error"><strong>'. $mensaje_Error . '</strong></p>';
            } ?>            

            <button class="btn btn-primary" id="btn_Enviar">Enviar</button>

        </div>

    </form>


</body>

</html>
<script>
    //Funcion para redigerir a la página de registro
    function registro() {

        location.href = "new_user.php";
    }
</script>