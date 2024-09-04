<?php
//INCLUIR LA CLASE TAREAS PARA LLAMAR A LAS FUNCIONES QUE TENDA DENTRO
include "tareas.php";

//OBTENEMOS TODOS LOS USUARIOS REGISTRADOS
$lista_Usuario = get_Todos_Usuarios();

//OBTENER EL USUARIO LOGEADO
$user = "__";
if (!empty($_SESSION["user"])) {
    $user = $_SESSION["user"];
}

?>


<!Doctype html>
<html lang="es">

<head>
    <title>Administrar Usuarios</title>

    <link rel="stylesheet" href="style.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.slim.js" integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" crossorigin="anonymous"></script>

    <!-- libreria para cargar ajax -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js" type="text/javascript"></script>
</head>

<body>
    <!-- CONTENEDOR DONDE SE MOSTRA INFORMACION DE LA PAGINA -->
    <div class="container" id="cntr_cabecera_user">

        <div class="container" id="cntr_cabecera">
            <h1>Listar Usuarios</h1>
            <a href="tareas.php?logout=true" class='btn btn-warning' title="Cerrar Sesión"><i class="bi bi-person-x-fill"></i></a>
        </div>

        <!-- ELEMENTOS PARA VOLVER A LA ANTERIOR PAGINA Y GUARDAR LOS CAMBIOS DE PRIVILEGIOS -->
        <div class="container" id="ctnr_crearTarea">
            <a href="listar_tareas.php" class='btn btn-warning' title="VolVer atrás">Volver</a>

            <button id="guardar" class="btn btn-primary" onclick="guardarCambios()">Guardar Cambios</button>

            <!-- <label><strong> Usuario: <?php echo $user ?></strong></label> -->

        </div>

        <!-- Container para mostrar usuario y cerrar sesión -->
        <div class="container" id="ctnr_Label_Usuario">

            <label><strong> Usuario: <?php echo $user ?></strong></label>

        </div>

    </div>



    <!-- MOSTRAR UNA TABLA CON TODOS LOS libros -->
    <div class="container" id="tablalibross">

        <table class="table table-warning table-striped border border-dark">

            <thead>

                <tr class="table-dark">
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Administrador</th>
                    <th>Fecha Creacion</th>
                    <th>Email</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>

                <?php

                $id = 0;
                //Mostrar todos los datos obtenidos de la consults sql
                while ($row = mysqli_fetch_array($lista_Usuario)) {
                    $id = $row["Id"];
                    echo "<tr class='tarea' data-id='$id'> ";
                    echo  '<td>' . $row["Id"] . '</td>';
                    echo  '<td>' . $row["Usuario"] . '</td>';
                    //SI ERES ADMINISTRADOR MOSTRAR COMO ACTIVADO EL CHECKBOX
                    if ($row["Administrador"] == true) {
                        echo  '<td><input type="checkbox" checked value=' . $row["Id"] . '  class="activado"></td>';
                    } else {
                        echo  '<td><input type="checkbox" value=' . $row["Id"] . '  class="activado"></td>';
                    }
                    echo  '<td>' . $row["Fecha_Creacion"] . '</td>';
                    echo  '<td>' . $row["Email"] . '</td>';
                    echo "<td> <a href='new_user.php?edit_User=$id' class='btn btn-warning'>Editar</a>";
                    echo "   <a href='funcionesUsuario.php?delete_Admin=$id' class='btn btn-danger'>Eliminar</a>
                    </td>";
                    echo "</tr>";
                }

                ?>
            </tbody>

        </table>
    </div>

</body>

</html>
<script>
    //BOTÓN GUARDA CAMBIOS ESTÉ OCULTO POR DEFECTO
    $("#guardar").hide();

    //ARRAY PARA ALMACENAR LOS ID DE LOS ADMINITRADORES
    let list_Admin = new Array();

    //ARRAY PARA ALMACENAR TODOS LOS ID DE LOS ADMINISTRADORES QUE POR DEFECTO YA ESTÉN MARCADOS SUS CHECBOX
    let list_Ckb_Activados = document.getElementsByClassName('activado');

    //OBTENEMOS TODOS LOS ID DE LOS ADMIN QUE ESTEN ACTIVADOS SUS CHECKBOX
    for (i = 0; i < list_Ckb_Activados.length; i++) {
        if (list_Ckb_Activados[i].checked == true) {
            // console.log(list_Ckb_Activados[i].value);
            list_Admin.push(list_Ckb_Activados[i].value);
        }
    }

    //MODIFICAR LOS PRIVILEGIOS DE LOS EMPLEADOS
    //PODER HACER ADMINISTRADORES O NO
    $('.activado').on('click', function() {

        //MOSTRAR EL BOTÓN DE GUARDA CAMBIOS
        $("#guardar").show();

        //comprobar si ya está metido o no el id del administrador

        if (list_Admin.length > 0) {
            //COMPRUEBA SI YA ESTA METIDO EN EL ARRAY EL ID
            if (list_Admin.includes($(this).val()) == false && $(this).prop("checked") == true) {
                //ALMACENAMOS EN LA LISTA EL ID DEL EMPLEADO
                list_Admin.push($(this).val());
            } else {
                if (list_Admin.includes($(this).val()) == true && $(this).prop("checked") == false) {
                    //OBTENEMOS EN QUE POSICION SE ENCUENTRA EL ID QUE QUEREMOS ELIMINAR DE LA LISTA
                    let position = list_Admin.indexOf($(this).val());
                    //QUITAR DEL ARRAY EL ELEMENTO EN LA POSICION EN LA QUE ESTÉ
                    list_Admin.splice(position, position + 1);
                }
            }
        } else {
            list_Admin.push($(this).val());
        }        

    });

    //FUNCION PARA GUARDAR CAMBIOS EN LOS PRIVILEGIOS DE ADMINISTRADOR
    function guardarCambios() {

          //ARRAY PARA ALMACENAR LOS ID DE LOS ADMINITRADORES
    let list_No_Admin = new Array();

    //OBTENEMOS TODOS LOS ID DE LOS ADMIN QUE NO ESTEN ACTIVADOS SUS CHECKBOX
    for (i = 0; i < list_Ckb_Activados.length; i++) {
        if (list_Ckb_Activados[i].checked == false) {
            list_No_Admin.push(list_Ckb_Activados[i].value);
        }
    }

        //ENVIAR UNA LISTA DE LOS USUARIOS QUE SE HAN MODIFICADOS LOS PRIVILEGIOS
        $.ajax({
            type: "POST",
            url: "funcionesUsuario.php",
            data: {
                array_Admin: list_Admin,
                array_No_Admin : list_No_Admin
            },
            success: function() {
                //EN CASO DE SE ACTUALICE CORRECTAMENTE VAMOS A LA SIGUIENT PÁGINA
                location.href = "listar_tareas.php";
            }
        });

    }
</script>