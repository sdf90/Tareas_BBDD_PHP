<?php

//INICIAR SESIÓN
session_start();


//COMPROBAR SI NOS LLEGA UN ARRAY CON LOS ID DE LOS ADMINISTRADORES PARA CAMBIAR
if(isset($_POST["array_Admin"])){
    update_User( $_POST["array_Admin"], $_POST["array_No_Admin"] );
}

//ELIMINAR UN USUARIO
if (!empty($_GET['delete_Admin'])) {

    delete_User($_GET['delete_Admin']);
}


//ACTUALIZAR USER PARA SER ADMINISTRADOR
function update_User($list_Admin,$list_No_Admin){

        //ESTABLECE CONEXIÓN
        $conexion_Establecida = crear_Conexion();

        //RECORRER LA LISTAR PARA MARCAR A LOS USUARIOS COMO ADMINISTRADOR
        foreach($list_Admin as $valor){

            //QUERY PARA ACTUALIZAR EL USUARIO PARA QUE SE ADMINISTRADOR
            $queryTrue = 'update usuario set Administrador = true where Id = '. intval($valor);
       
            //EJECUTAR LA CONSULTA
            $resul_queryTrue = $conexion_Establecida->query($queryTrue);
         
            //COMPROBAR QUE LA CONSULTA SE HIZO CORRECTAMENTE
            if (!$resul_queryTrue) {
                echo "Error al ejecutar la consulta " . $conexion_Establecida->error;
            } 
        } 
        
        //RECORRER LA LISTAR PARA MARCAR A LOS USUARIOS COMO NO ADMINISTRADOR
        foreach($list_No_Admin as $valor){
       
            //QUERY PARA ACTUALIZAR EL USUARIO PARA QUE DEJE DE SER ADMINISTRADOR
            $queryFalse = 'update usuario set Administrador = false where Id = '.intval( $valor );

            //EJECUTAR LA CONSULTA
            $resul_queryFalse = $conexion_Establecida->query($queryFalse);

            //COMPROBAR QUE LA CONSULTA SE HIZO CORRECTAMENTE
            if (!$resul_queryFalse) {
                echo "Error al ejecutar la consulta " . $conexion_Establecida->error;
            } 
        } 
        
    
        //CERRAR CONEXIÓN BBDD
        cerrar_Conexion($conexion_Establecida);

}


//ACTUALIZAR LOS DATOS DE UN USUARIO
function update_User_Information($id_User,$usuario,$email,$pass){


     //ESTABLECE CONEXIÓN
     $conexion_Establecida = crear_Conexion();

      //HACEMOS UN HASH A LA CONTRASEÑA
      //$password = password_hash($pass, PASSWORD_BCRYPT);

     //Query para actualizar datos de un usuario
     $query = 'update usuario set Usuario ='. $usuario .', Email ='. $email .', Pass ='. $pass .' where Id = '.intval( $id_User );

     if (!$query) {
        echo "Error al ejecutar la consulta " . $conexion_Establecida->error;
    }else{
           //REDIRECCIONAR LA PÁGINA 
           header("Location:listar_usuarios.php");
           exit();
    }

     //CERRAR CONEXIÓN BBDD
     cerrar_Conexion($conexion_Establecida);

}

//ELIMINAR UN USUARIO
function delete_User($id)
{

    //ESTABLECER CONEXIÓN
    $conexion_Establecida = crear_Conexion();

    //CAMBIAR DE USUARIO AL ADMIN LAS TAREAS QUE TIENE PENDIENTE O EN PROGRESO LAS TAREAS ANTES
    // DE QUE SE ELIMINE EL USUARIO    
    actualizar_usuario_tarea($id);

    //CONSULTA PARA ELIMINAR EL USUARIO
    $query = "delete  from usuario where Id = $id";

    //EJECUTAR LA CONSULTA
    $resul_query = $conexion_Establecida->query($query);

    if (!$resul_query) {
        echo "Error al ejecutar la consulta " . $conexion_Establecida->error;
    } else {
        //REDIRECCIONAR LA PÁGINA 
        header("Location:listar_usuarios.php");
        exit();
    }

    unset($_GET['delete_ID']);

    //CERRAR CONEXIÓN BBDD
    cerrar_Conexion($conexion_Establecida);
}


//ACTUALIZAR A QUIEN PERTENECE LA TAREA
function actualizar_usuario_tarea($id_Admin)
{
    //ESTABLECER CONEXIÓN
    $conexion_Establecida = crear_Conexion();

    //HACER UNA CONSULTA SQL
    $query = "update tarea set Usuario = 1 where Estado_ID = 1 or Estado_ID = 2";

    //EJECUTAR LA CONSULTA SLQ
    $resul_query = $conexion_Establecida->query($query);

        //COMPROBAR QUE NO HAY ERRORES EN LA CONSULTA
    if (!$resul_query) {
        echo "Error al ejecutar la consulta " . $conexion_Establecida->error;
    }
}




//FUNCIONES

//FUNCIÓN PARA ENCONTRAR A UN USUARIO
function get_Usuario()
{

    if (!empty($_POST["usuario"]) && !empty($_POST["password"])) {

        //CREAR VARIABLES PARA ALMACENAR EL VALOR DE LOS CAMPOS
        $usuario = $_POST["usuario"];
        $passw = $_POST["password"];

        //ESTABLECER CONEXIÓN
        $conexion_Establecida = crear_Conexion();

        //ESCAPAR LOS DATOS DEL USUARIO
        $usuario = $conexion_Establecida->real_escape_string($usuario);

        //CONSULTA PREPARADA SQL 
        $stmt = $conexion_Establecida->prepare("select * from usuario where Usuario = ?");
        // bind_param agrega variables a una sentencia preparada
        $stmt->bind_param("s", $usuario);
        //EJECUTA UNA SENTENCIA PREPARADA
        $stmt->execute();

        //OBTENEMOS UN RESULTADO DE UNA SENTENCIA PREPARADA
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            //OBTENEMOS UN FILA DE RESULTADO COMO UN ARRAY ASOCIATIVO
            $row = $result->fetch_assoc();

            //Constraseña válida 
            //COMPRUEBA QUE LA CONTRASEÑA COINCIDA CON UN HASH 
            if (password_verify($passw, $row["Pass"])) {

                //Id del usuario
                $_SESSION["userID"] = $row["Id"];
                //Nombre del usuario
                $_SESSION["user"] = "";
                $_SESSION["user"] = $row["Usuario"];
                //COMPROBAR SI ES ADMINISTRADOR
                $_SESSION["Administrador"] = $row["Administrador"];


                //Regeneral el ID de sesión para evitar el secuestro de sesión
                session_regenerate_id(true);

                session_set_cookie_params(
                    [
                        'lifetime' => 3600,
                        'domain' => 'index.php',
                        'secure' => true,
                        'httponly' => true
                    ]
                );

                header("Location:listar_tareas.php");
                exit();
            } else {
                //MOSTRAR MENSAJE DE ERROR EN CASO DE QUE LA CONTRASEÁ SEA INCORRECTA
                header("Location:index.php?error_Credenciales=Constraseña incorrecto");
                exit();
            }
        } else {
            //MOSTRAR MENSAJE DE ERROR EN CASO DE QUE EL USUARIO SEA INCORRECTO
            header("Location:index.php?error_Credenciales=Usuario incorrecto");
            exit();
        }
        //CERRAR CONEXIÓN
        $stmt->close();
        cerrar_Conexion($conexion_Establecida);
    }
}

//OBTENER INFORMACIÓN COMPLETA DE UN USUARIO PARA MOSTRAR
function get_User_Information($user_ID){

    //ESTABLECER CONEXIÓN
    $conexion_Establecida = crear_Conexion();

    //CONSULTA PREPARADA SQL 
    $stmt = $conexion_Establecida->prepare("select * from usuario where Id = ?");
    // bind_param agrega variables a una sentencia preparada
    $stmt->bind_param("s", $user_ID);
    //EJECUTA UNA SENTENCIA PREPARADA
    $stmt->execute();

    //OBTENEMOS UN RESULTADO DE UNA SENTENCIA PREPARADA
    $result = $stmt->get_result();


    if ($result->num_rows > 0){

        return $result;

    }else{
        return 0;
    }

     //CERRAR CONEXIÓN
     $stmt->close();
     cerrar_Conexion($conexion_Establecida);
}

//REGISTRAR UN NUEVO USUARIO
function new_User()
{

    $user = "";
    $pass = "";
    $repeat_Pass = "";
    $email ="";

    if (!empty($_POST["user_Name"])) {

        $user = $_POST["user_Name"];
    }

    if ($_POST["pass_New"] == $_POST["pass_Repeat"] && !empty($_POST["pass_New"])) {
        $pass = $_POST["pass_New"];
        $repeat_Pass = $_POST["pass_Repeat"];
    }

    if (!empty($_POST["email"])) {

        $email = $_POST["email"];
    }

    //ESTABLECER CONEXIÓN
    $conexion_Establecida = crear_Conexion();    

    if (!empty($user) && !empty($pass)) {

        //ESCAPAR LOS DATOS DEL USUARIO
        $user = $conexion_Establecida->real_escape_string($user);
        $pass = $conexion_Establecida->real_escape_string($pass);

        //VARIABLES
        $vacio = null;

        //FECHA
        $fecha = date("Y-m-d H:i:s");

        //HACEMOS UN HASH A LA CONTRASEÑA
        $pass = password_hash($pass, PASSWORD_BCRYPT);

        $administrdor = false;

        //PREPRAMOS LA CONSULTA SQL
        $query = $conexion_Establecida->prepare("INSERT INTO usuario (Id, Usuario, Pass, Administrador,Fecha_Creacion,Email) values (?,?,?,?,?,?)");
        //Esta línea enlaza los parámetros de la consulta preparada con los valores proporcionados. bind_param se usa para ejecutar una consulta donde los parámetros son variables.
        $query->bind_param("isssss", $vacio, $user, $pass,$administrdor,$fecha,$email);

        // Ejecutar consulta preparada
        if ($query->execute()) { //Aquí se ejecuta la consulta preparada utilizando el método execute() del objeto $stmt. Consulta preparada, esto es una es una consulta SQL precompilada que permite a la base de datos optimizar su ejecución y proteger contra la inyección de SQL
            //REDIRECCIONAR LA PÁGINA 
            header("Location:index.php");
            exit();
        } else {
            echo "Error al ejecutar la consulta: " . $query->error;
            header("Location:new_user.php");
            exit();
        }
        $query->close();
    }

    //CERRAR CONEXIÓN
    cerrar_Conexion($conexion_Establecida);
}



//FUNCIÓN PARA OBTENER TODOS LOS USUARIO
function get_Todos_Usuarios(){

    //ESTABLECE CONEXIÓN
    $conexion_Establecida = crear_Conexion();

    //CONSULTA SQL
    $query = "select * from usuario";

    //EJECUTAR LA CONSULTA
    $resul_query = $conexion_Establecida->query($query);

    //COMPROBAR LA CONSULTA NO TIENE ERRORES
    if (!$resul_query) {
        echo "Error al ejecutar la consulta " . $conexion_Establecida->error;
    } else {
        return $resul_query;
    }

    //CERRAR CONEXIÓN BBDD
    cerrar_Conexion($conexion_Establecida);
}

//CERRAR SESIÓN Y VOLVER AL INDEX
function exit_Session()
{
    //ELIMINAR LA SESION
    unset($_SESSION["user"]);

    //DESTRUIR TODAS LA SESIONES
    session_destroy();

    //REDIRIGIR A LA PAGINA DE INICIO
    header("Location:index.php");
    exit();
}

//CONEXIÓN DE BBDD
function crear_Conexion()
{

    $host = "localhost";
    $db_name = "Tarea_Agenda";
    $username = "root";
    $password = "";

    //ESTABLECER CONEXIÓN   
    $conexion = new mysqli($host, $username, $password, $db_name);

    //VERIFICAR CONEXIÓN
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    return $conexion;
}

//CERRAR CONEXIÓN
function cerrar_Conexion($conexion)
{
    $conexion->close();
}

//FUNCIÓN PARA ERROR DE CONSULTA
function error_consulta($conexion, $query)
{
    //COMPROBAR QUE LA CONSULTA NO TIENE ERRORES
    $resultado =  $conexion->query($query);

    if (!$resultado) {
        return "Error al ejecutar la consulta: " . $conexion->error;
    } else {
        return "";
    }
}

?>