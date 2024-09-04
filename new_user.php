<?php
//INCLUIR LAS FUNCIONES QUE SE ENCUENTRAS EN LA CLASE
include "funcionesUsuario.php";

 //SI HA INGRESADO DATOS PARA ACTUALIZAR
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
   
    //ACTUALIZAR A USUARIO HA INICIADO SESIÓN
    if (isset($_SESSION["user"]) && !empty($_SESSION["user"])) {

        $ID_Usuario  =  $_SESSION["ID_User"];

        //COMPROBAR QUE NO ESTÉN VACÍAS Y SEAN IGUALES LAS CLASES
        if(!empty($_POST["pass_New"]) && $_POST["pass_New"] == $_POST["pass_Repeat"]){

            //LLAMAR  A LA FUNCIÓN PARA ACTUALIZAR LOS DATOS
            update_User_Information($ID_Usuario,$_POST["user_Name"],$_POST["email"],$_POST["pass_New"]);
        }else{
            $Password_Usuario = $_SESSION["Password_User"];
            //LLAMAR  A LA FUNCIÓN PARA ACTUALIZAR LOS DATOS
            update_User_Information($ID_Usuario,$_POST["user_Name"],$_POST["email"],$Password_Usuario);
        }
    }else{
        //INGRESAR UN NUEVO USUARIO
        new_User();
    }
}
//VARIABLES PARA ALMACENAR DATOS DEL USUARIO
$Id = "";
$Nombre = "";
$Password = "";
$Email = "";

//OBTENER DATOS DEL USUARIO PARA MODIFICARLO
if (isset($_GET["edit_User"])) {

    $id = intval($_GET["edit_User"]);

    //LLAMAR A LA FUNCIÓN PARA OBTENER DATOS DEL USUARIO PARA MODIFICAR
    $Usuario = get_User_Information($id);    

    //COMPROBAR QUE EL USUARIO NO ESTÁ VACÍO
    if (!empty($Usuario) && $Usuario != null) {

        while ($row = mysqli_fetch_array($Usuario)) {

            //VARIABLES PARA ALMACENAR LOS DATOS DEL LIBRO 
            $_SESSION["ID_User"] = $row["Id"];
            $_SESSION["Password_User"] = $row["Pass"];

            $Nombre = $row["Usuario"];           
            $Email = $row["Email"];
        }
    }
}
?>
<html>

<head>
    <title>Registrar Usuario</title>
    <link rel="stylesheet" href="style.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

</head>

<body id="body_Registro">
    <div class="container" id="ctnr_Registro">

        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">

            <div class="container rounded" id="ctnr_Formulario">

                <!-- Nombre usuario -->
                <label>Usuario</label>
                <br>
                <input type="text" name="user_Name" value="<?php echo $Nombre ?>" required>

                <br><br>

                <!-- Email -->
                <label>Email</label>
                <br>
                <input type="email" name="email" value="<?php echo $Email ?>" required>

                <br><br>

                <!-- Contraseña -->
                <label>Contraseña</label>
                <br>
                <?php
                if(isset($_SESSION["user"])){
                    echo "<input type='password' name='pass_New'  > ";
                }else {
                    echo "<input type='password' name='pass_New'  required>";
                }
                ?>

                <br><br>

                <!-- Repetir Contraseña -->
                <label>Repetir Contraseña</label>
                <br>
                <?php
                if(isset($_SESSION["user"])){
                    echo "<input type='password' name='pass_Repeat'  > ";
                }else {
                    echo "<input type='password' name='pass_Repeat'  required>";
                }
                ?>
                <br><br>

                <button class="btn btn-primary">Enviar</button>

                <?php
                    if(isset($_SESSION["user"])){
                        echo   "<a href='listar_usuarios.php' class='btn btn-warning' title='Volver atrás'>Volver</a>";
                    }
                ?>

            </div>

        </form>
    </div>
</body>

</html>