<?php

//INCLUIR LA CLASE TAREAS PARA LLAMAR A LAS FUNCIONES QUE TENDA DENTRO
include "tareas.php";

//LLAMADA A LA FUNCIÓN PARA TENER UN LISTADO DE LAS TAREAS
$result = listar_Tareas();


if (empty($result)) {
    $result = "";
}

// SABER QUIEN HA INICIADO SESSION
$user = "__";
if (!empty($_SESSION["user"])) {
    $user = $_SESSION["user"];
}


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Listar Tareas</title>
    <link rel="stylesheet" href="style_Tarea.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/draggable/1.0.0/sortable.min.js"> </script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.8/axios.min.js"> </script>

</head>

<body>
    <!-- CONTENEDOR DONDE SE MOSTRA INFORMACION DE LA PAGINA -->
    <div class="container " id="cntr_cabecera_listar">

        <div class="container" id="cntr_cabecera">
            <h1>Listar tareas</h1>
            <a href="tareas.php?logout=true" class='btn btn-warning' title="Cerrar Sesión"><i class="bi bi-person-x-fill"></i></a>
        </div>


        <div class="container" id="ctnr_crear_cerrar">

            <!-- Container para el botón de crear tarea -->
            <div class="container" id="ctnr_crearTarea">

                <a href='crear_tarea.php' class='btn btn-info' title="Añadir Tarea"><i class="bi bi-journal-plus"></i></a>
                <?php 
                //MOSTRAR EL BOTÓN DE CONFIGURACIÓN DE USUARIO SI ERES UN ADMINISTRADOR
                    if($_SESSION["Administrador"]  == true){
                        echo "<a href='listar_usuarios.php' class='btn btn-warning' title='Configuración de Usuarios'><i class='bi bi-person-fill-gear'></i></a>";
                    }
                ?>

            </div>
   
        </div>
    </div>


    <!-- MOSTRAR UNA TABLA CON TODOS LOS libros -->
    <div class="container" id="tablalibross">

        <table class="table table-warning table-striped border border-dark">

            <thead>

                <tr class="table-dark">
                    <th>Nombre Tarea</th>
                    <th>Descripción</th>
                    <th>Fecha Creacion</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Estado</th>
                    <?php
                    //MOSTRAR EN LA TABLA LOS USUARIO SI ERES ADMINISTRADOR
                    if ($_SESSION["Administrador"]  == true) {
                        echo "<th>Usuario</th>";
                    }
                    ?>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>

                <?php

                $id = 0;
                //Mostrar todos los datos obtenidos de la consults sql
                while ($row = mysqli_fetch_array($result)) {
                    $id = $row["Id"];
                    echo "<tr class='tarea' data-id='$id'> ";
                    echo  '<td>' . $row["nombre"] . '</td>';
                    echo  '<td>' . $row["descripcion"] . '</td>';
                    echo  '<td>' . $row["fecha_creacion"] . '</td>';
                    echo  '<td>' . $row["fecha_inicio"] . '</td>';
                    echo  '<td>' . $row["fecha_fin"] . '</td>';
                    echo  '<td>' . $row["estado"] . '</td>';
                    //SI ERES ADMINISTRADOR MOSTRAR EL USUARIO
                    if ($_SESSION["Administrador"]  == true) {
                        echo  '<td>' . $row["usuario"] . '</td>';
                    }
                    echo "<td>
                    <a href='crear_tarea.php?edit_ID=$id' class='btn btn-warning'>Editar</a>";
                    //MOSTRAR LA OPCION DE ELIMINAR SI ERES ADMINISTRADOR
                    if ($_SESSION["Administrador"] == true) { 
                        echo " <a href='tareas.php?delete_ID=$id' class='btn btn-danger'>Eliminar</a>
                    </td>";
                    } else {
                        echo "</td>";
                    }

                    echo "</tr>";
                }

                ?>
            </tbody>

        </table>
    </div>

</body>

</html>

