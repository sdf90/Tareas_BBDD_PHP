<?php

//INCLUIR LA LLAMADA A LA CLASE DONDE SE ENCUENTRA LOS MÉTODOS
include "funcionesUsuario.php";

//ELIMINAR UNA TAREA
if (!empty($_GET['delete_ID'])) {

    eliminar_tarea($_GET['delete_ID']);
}


//COMPROBAMOS SI VAMOS A CERRAR SESSION
if (isset($_GET["logout"]) && $_GET["logout"] == true) {

    exit_Session();
}


//AÑADIR UNA NUEVA TAREA
function agregar()
{

    // OBTENER LOS DATOS DEL FORMULARIO Y COMPROBAR SI ESTÁN VACIOS O NO

    //TIPO DE ESTADO DE LA TAREA
    $estado_Tarea = 0;
    if ($_POST['estado_ID'] > 0) {
        $estado_Tarea = $_POST['estado_ID'];
    }

    //NOMBRE DE LA TAREA
    $nombre_Tarea = '';
    if (!empty($_POST['nombre'])) {
        $nombre_Tarea = $_POST['nombre'];
    }

    //DESCRIPCIÓN DE LA TAREA
    $descripcion_Tarea = '';
    if (!empty($_POST['descripcion'])) {
        $descripcion_Tarea = $_POST['descripcion'];
    }

    //FECHA DE INICIO DE LA TAREA
    $fecha_Inicio = "";
    if (!empty($_POST['trip-start'])) {
        $fecha_Inicio =  $_POST['trip-start'];
    }

    //FECHA FIN DE LA TAREA
    $fecha_Fin = "";
    if (!empty($_POST['trip-stop'])) {
        $fecha_Fin = $_POST['trip-stop'];
    }


    //COMPROBAR QUE LA FECHA FIN SEA MAYOR QUE LA DE INICIO
    $error_Fecha_Incorrecta = "";
    if ($fecha_Inicio > $fecha_Fin) {

        $error_Fecha_Incorrecta = "La fecha fin debe ser superior a la fecha de inicio";
    }

    //ENVIAR MENSAJE DE ERRORES A LA VISTA DE CREAR TAREA
    if ($error_Fecha_Incorrecta != "") {

        header("Location:crear_tarea.php?error_Fecha_Incorrecta");
        exit();
    } else {

        //ESTABLECER CONEXIÓN
        $conexion_Establecida = crear_Conexion();

        //OBTENER EL ID DEL USUARIO QUE HA INICIADO SESIÓN
        $id_user  = 1;
        if (isset($_SESSION["userID"])) {
            // EN CASO DE QUE LA TAREA SEA CREADO POR UN EMPLEADO SE LA ASIGNA A EL 
            if ($_SESSION["Administrador"] != true) {
                $id_user = $_SESSION["userID"];
            } else {
                // SI LA TAREA ES CREADO POR EL ADMIN A QUIEN ASIGNE EL ADMINISTRADOR
                if ($_POST["selector_Empleado"] != 0) {
                    $id_user = $_POST["selector_Empleado"];
                }
            }
        }

        //VARIABLES PARA EL SQL
        //VALOR NULL PARA QUE PRODUZCA POR DEFECTO EL AUTOINCREMENTO DEL ID AL INGRESAR UNA NUEVA TAREA
        $id = null;
        //VARIABLE PARA LA FECHA EN QUE SE CREÓ LA TAREA
        $fecha = date("Y-m-d H:i:s");


        //ESCAPAR LOS DATOS DEL USUARIO PARA COMPROBAR QUE NO INTRODUZCA CODIGO MALICIOS
        $nombre_Tarea = $conexion_Establecida->real_escape_string($nombre_Tarea);
        $descripcion_Tarea = $conexion_Establecida->real_escape_string($descripcion_Tarea);


        //Aquí se prepara una consulta SQL para insertar una nueva fila en la tabla Tareas con los valores proporcionados para la descripción y el estado. Se utiliza una consulta preparada para evitar la inyección SQL y garantizar la seguridad de la aplicación.
        $stmt = $conexion_Establecida->prepare("INSERT INTO tarea (Id,Nombre,Descripcion,Fecha_Creacion,Fecha_Inicio,Fecha_Fin,Estado_Id,Usuario) VALUES (?,?,?,?,?,?,?,?)");
        //Esta línea enlaza los parámetros de la consulta preparada con los valores proporcionados. bind_param se usa para ejecutar una consulta donde los parámetros son variables.
        $stmt->bind_param("ssssssii", $id, $nombre_Tarea, $descripcion_Tarea, $fecha, $fecha_Inicio, $fecha_Fin, $estado_Tarea, $id_user);

        // Ejecutar consulta preparada
        if ($stmt->execute()) { //Aquí se ejecuta la consulta preparada utilizando el método execute() del objeto $stmt. Consulta preparada, esto es una es una consulta SQL precompilada que permite a la base de datos optimizar su ejecución y proteger contra la inyección de SQL
            //REDIRECCIONAR LA PÁGINA 
            header("Location:listar_tareas.php");
            //FINALIZAR DE EJECUTAR EL SCRIPT
            exit();
        } else {
            header("Location:crear_tarea.php?errorConsulta=$stmt->error");
            //FINALIZAR DE EJECUTAR EL SCRIPT
            exit();
            //echo "Error al ejecutar la consulta: " . $stmt->error;
        }

         //CERRAR CONEXIONES
        $stmt->close();       
        cerrar_Conexion($conexion_Establecida);
    }
}


//LISTAR TODAS LAS TAREAS
function listar_Tareas()
{
    //Nombre del usuario
    $usuario = $_SESSION["user"];

    //ESTABLECER CONEXIÓN
    $conexion_Establecida = crear_Conexion();


    //CONSULTAS SQL
    //MOSTRAR SOLA LAS TAREAS QUE TENGA ASOCIADO ESE USUARIO EN CASO DE QUE NO SEA ADMINISTRADOR
    if ($_SESSION["Administrador"] == false) {
        $query = "  select tar.Id,tar.nombre,tar.descripcion,tar.fecha_creacion,tar.fecha_inicio,tar.fecha_fin,estd.estado,us.usuario
        from tarea as tar
        inner join estado_tarea as estd on tar.Estado_Id = estd.Id 
        inner join usuario as us on tar.Usuario = us.Id
        where us.Usuario like '$usuario'";
    } else {
        //CONSULTA QUE DEVOLVERÁ TODAS LAS TAREAS QUE EXISTA EN CASO DE QUE SEAS UN ADMINISTRADOR
        $query = "select tar.Id,tar.nombre,tar.descripcion,tar.fecha_creacion,tar.fecha_inicio,tar.fecha_fin,estd.estado,us.usuario
        from tarea as tar
        inner join estado_tarea as estd on tar.Estado_Id = estd.Id 
        inner join usuario as us on tar.Usuario = us.Id";
    }

    //OBTENER LA CONSULTA SQL
    $resul_query = $conexion_Establecida->query($query);

    //COMPROBAR QUE NO SE HAN PRODUCIDO ERRORES
    if (!$resul_query) {
        echo "Error al ejecutar la consulta " . $conexion_Establecida->error;
    } else {
        return $resul_query;
    }

    //CERRAR CONEXIÓN BBDD
    cerrar_Conexion($conexion_Establecida);
}

//EDITAR TAREA
function editar_tarea($id)
{
    // Obtener datos del formulario
    //ESTADO DE LA TAREA
    $estado_Tarea = 0;
    if (isset($_POST['estado_ID']) && !empty($_POST['estado_ID'])) {
        $estado_Tarea = intval($_POST['estado_ID']);
    }

    //NOMBRE DE LA TAREA
    $nombre_Tarea = '';
    if (!empty($_POST['nombre'])) {
        $nombre_Tarea = $_POST['nombre'];
    }

    //DESCRIPCION DE LA TAREA
    $descripcion_Tarea = '';
    if (isset($_POST['descripcion']) && !empty($_POST['descripcion'])) {
        $descripcion_Tarea = trim($_POST['descripcion']);
    }

    //FECHA DE INICIO DE LA TAREA
    $fecha_Inicio = "";
    if (!empty($_POST['trip-start'])) {
        $fecha_Inicio =  $_POST['trip-start'];
    }

    //FECHA FIN DE LA TAREA
    $fecha_Fin = "";
    if (!empty($_POST['trip-stop'])) {
        $fecha_Fin = $_POST['trip-stop'];
    }

    //ESTABLECER CONEXIÓN
    $conexion_Establecida = crear_Conexion();

    
    $id_user = $id;
    // EN CASO DE QUE LA TAREA SEA CREADO POR UN EMPLEADO SE LA ASIGNA A EL 
    if ($_SESSION["Administrador"] != true) {
        $user_Tarea = $id;
    } else {
        // SI LA TAREA ES CREADO POR EL ADMIN A QUIEN ASIGNE EL ADMINISTRADOR
        if ($_POST["selector_Empleado"] != 0) {
            $user_Tarea =intval( $_POST["selector_Empleado"] );
        }
    } 
    

    //ESCAPAR LOS DATOS DEL USUARIO
    $nombre_Tarea = $conexion_Establecida->real_escape_string($nombre_Tarea);
    $descripcion_Tarea = $conexion_Establecida->real_escape_string($descripcion_Tarea);

    //PREPARAMOS LA CONSULTA SQL
    $stmt = $conexion_Establecida->prepare("update tarea set Nombre= ? ,Descripcion = ?,Fecha_Inicio= ?, Fecha_Fin = ?, Estado_ID = ?, Usuario = ? where Id = ? "); //Aquí se prepara una consulta SQL para insertar una nueva fila en la tabla Tareas con los valores proporcionados para la descripción y el estado. Se utiliza una consulta preparada para evitar la inyección SQL y garantizar la seguridad de la aplicación.
    //AÑADIMOS LOS VALORES A LA CONSULTA PARA EJECUTARLA
    $stmt->bind_param("ssssiii", $nombre_Tarea, trim($descripcion_Tarea), $fecha_Inicio, $fecha_Fin, $estado_Tarea,$user_Tarea, $id_user); //Esta línea enlaza los parámetros de la consulta preparada con los valores proporcionados. bind_param se usa para ejecutar una consulta donde los parámetros son variables.

    // Ejecutar consulta preparada
    if ($stmt->execute()) { //Aquí se ejecuta la consulta preparada utilizando el método execute() del objeto $stmt. Consulta preparada, esto es una es una consulta SQL precompilada que permite a la base de datos optimizar su ejecución y proteger contra la inyección de SQL
        //REDIRECCIONAR LA PÁGINA 
        header("Location:listar_tareas.php");
        exit();
    } else {
        echo "Error al ejecutar la consulta: " . $stmt->error;
    }
    //CERRAR CONEXIONES
    $stmt->close();
    cerrar_Conexion($conexion_Establecida);
}

//SELECCIONAR UNA TAREA
function buscar_tarea($id_Tarea)
{
    //ESTABLECER CONEXIÓN
    $conexion_Establecida = crear_Conexion();

    //CONSULTA SQL
    $query = "select * from tarea where ID = $id_Tarea";

    //EJECUTAR LA CONSULTA
    $resul_query = $conexion_Establecida->query($query);

    //COMPROBAR QUE NO HAY ERRORES EN LA EJECUCIÓN DE LA CONSULTA
    if (!$resul_query) {
        echo "Error al ejecutar la consulta " . $conexion_Establecida->error;
    } else {
        return $resul_query;
    }

    //CERRAR CONEXIÓN BBDD
    cerrar_Conexion($conexion_Establecida);
}

//ELIMINAR UNA TAREA
function eliminar_tarea($id)
{

    //ESTABLECER CONEXIÓN
    $conexion_Establecida = crear_Conexion();

    //CONSULTA SQL
    $query = "delete  from tarea where Id = $id";

    //EJECUTAR LA CONSULTA
    $resul_query = $conexion_Establecida->query($query);

     //COMPROBAR QUE NO HAY ERRORES EN LA EJECUCIÓN DE LA CONSULTA
    if (!$resul_query) {
        echo "Error al ejecutar la consulta " . $conexion_Establecida->error;
    } else {
        //REDIRECCIONAR LA PÁGINA 
        header("Location:listar_tareas.php");
        exit();
    }

    //ELIMINAR LA VARIABLE
    unset($_GET['delete_ID']);

    //CERRAR CONEXIÓN BBDD
    cerrar_Conexion($conexion_Establecida);
}


//OBTENER TODOS LOS ESTADOS DE LA TAREAS
function estado_Tarea()
{

    //ESTABLECER CONEXIÓN
    $conexion_Establecida = crear_Conexion();

    //CONSULTA SQL
    $query = "select * from estado_Tarea";

    //EJECUTAR LA CONSULTA
    $resul_query = $conexion_Establecida->query($query);

    //COMPROBAR QUE NO HAY ERRORES EN LA EJECUCIÓN DE LA CONSULTA
    if (!$resul_query) {
        echo "Error al ejecutar la consulta " . $conexion_Establecida->error;
    } else {
        return $resul_query;
    }

    //CERRAR CONEXIÓN BBDD
    cerrar_Conexion($conexion_Establecida);
}
