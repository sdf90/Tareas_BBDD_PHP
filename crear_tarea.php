<?php

$Id = "";
$nombre = "";
$descripcion = "";
$estado = "";
$fechaInicio = "";
$fechaFin = "";

include "tareas.php";

// OBTENER TODOS LOS ESTADOS DE LAS TAREAS
$estado_Tarea = estado_Tarea();

// OBTENER TODOS LOS EMPLEADOS
$empleados_Gestion = get_Todos_Usuarios();

//OBTENER LA SESIÓN DEL USUARIO
$user = "__";
if (!empty($_SESSION["user"])) {
    $user = $_SESSION["user"];
}

//COMPROBAR QUE TIENE DATOS LOS CAMPOS ANTES DE AGREGAR EN LA BBDD
if (!empty($_POST["nombre"]) && !empty($_POST["descripcion"])  && !empty($_POST["estado_ID"]) && empty($_GET["edit_ID"])) {
    agregar();
}


//ACTUALIZAR DATOS DE LA TAREA
if (!empty($_GET["edit_ID"])) {

    if (!empty($_POST["nombre"]) && !empty($_POST["descripcion"])  && !empty($_POST["estado_ID"])) {
        editar_tarea($_GET["edit_ID"]);
    } else {

        //LLAMAR A LA FUNCIO PARA OBTENER LA TAREA QUE QUEREMOS
        $editar_Tarea =  buscar_tarea($_GET["edit_ID"]);

        //COMPROBAR QUE HAY UN TAREA
        if (!empty($editar_Tarea)) {

            //RECORRER LOS DATOS DE LA TAREA
            while ($row = mysqli_fetch_array($editar_Tarea)) {

                //VARIABLES PARA ALMACENAR LOS DATOS DEL LIBRO 
                $Id = $row["Id"];
                $nombre = $row["Nombre"];
                $descripcion = $row["Descripcion"];
                $estado = $row["Estado_ID"];
                $fechaInicio = $row["Fecha_Inicio"];
                $fechaFin = $row["Fecha_Fin"];
                $Usuario_Asigando = $row["Usuario"];
            }
        }
    }
}

//MENSAJE DE ERROR
$mensaje_FechaIncorrecta = "";
if (isset($_GET["error_Fecha_Incorrecta"]) &&  $_GET["error_Fecha_Incorrecta"] != "") {
    $mensaje_FechaIncorrecta = $_GET["error_Fecha_Incorrecta"];
}

?>

<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Tareas</title>
    <link rel="stylesheet" href="style_Tarea.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- <script type="text/javascript" src="/assets/JavaScript/funciones.js">  </script> -->
    <script src="https://code.jquery.com/jquery-3.7.1.slim.js" integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" crossorigin="anonymous"></script>
</head>

<body id="body_">

    <div class="container border border-black rounded" id="contain_body">

        <h1>Tarea para añadir</h1>

        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">

            <div class="container " id="contain_estado">
                <label><strong>Estado de la tarea</strong></label>

                <!-- SELECTOR DEL ESTADO DE LA TAREA -->
                <select name="estado_ID" id="selectEstado" required>

                    <?php

                    echo "<option value=''>---Selecionar---</option>";

                    // MOSTRARA TODOS LOS ESTADOS QUE TIENE UNA TAREA
                    while ($row = mysqli_fetch_array($estado_Tarea)) {

                        if ($row["Id"] == $estado) {

                            echo  '<option value=' . $row['Id'] . ' selected >' . $row["Estado"] . '</option>';
                        } else {

                            echo  '<option value=' . $row['Id'] . '>' . $row['Estado'] . '</option>';
                        }
                    }

                    ?>
                </select>

                <!-- SELECTOR PARA SABER A QUIEN ESTÁ ASIGNADO LA TAREA -->
                <?php
                // COMPROBAR QUE ES UN ADMINISTRADOR
                if ($_SESSION["Administrador"] == true) {

                    echo "<label><strong>Empleado asignado</strong></label>";
                    echo "<select name='selector_Empleado'>";

                    echo "<option value='0'>---Selecionar---</option>";

                    // MOSTRAR TODOS LOS EMPLEADOS QUE EXISTE
                    while ($row = mysqli_fetch_array($empleados_Gestion)) {

                        if ($row["Id"] == $Usuario_Asigando) {

                            echo  '<option value=' . $row['Id'] . ' selected >' . $row["Usuario"] . '</option>';
                        } else {

                            echo  '<option value=' . $row['Id'] . '>' . $row['Usuario'] . '</option>';
                        }
                    }

                    echo "</select>";
                }
                ?>
            </div>

            <div class="container" id="ctnr_Fechas">

                <!-- Fecha inicio tarea -->
                <div class="container">
                    <label for="start" class="lbl_Inicio"><strong>Fecha Inicio</strong></label>
                    <br>
                    <input type="date" class="start" name="trip-start" value="<?php echo $fechaInicio ?>" required />
                </div>

                <!-- Fecha fin tarea -->
                <div class="container">
                    <label for="start" class="lbl_Inicio"><strong>Fecha Fin</strong></label>
                    <br>
                    <input type="date" class="start" name="trip-stop" value="<?php echo $fechaFin ?>" required />
                </div>


            </div>
            <!-- Nombre tarea -->
            <div class="container " id="contain_nombre">
                <label><strong> Nombre de la Tarea </strong></label>
                <br>
                <input type="text" name="nombre" class="rounded" value="<?php echo $nombre ?>" required>

            </div>

            <!-- Descripcion de la tarea -->
            <div class="container" id="contain_descripcion">
                <label><strong>Descripción de la tarea</strong></label>
                <br>
                <textarea id="tarea_" name="descripcion" rows="3" cols="60" required placeholder="Descripcion de la tarea ..."><?php echo trim($descripcion); ?></textarea>

            </div>

            <?php
            if ($Id != null) {
                echo  "<button id='btn_guardar' class='btn btn-success btn-lg' type='submit'>Guardar</button>";
            } else {
                echo  "<button id='btn_guardar' class='btn btn-success btn-lg' type='submit'>Añadir</button>";
            }
            ?>

            <a href='listar_tareas.php' class='btn btn-primary btn-lg'>Cancelar</a>


        </form>

        <!-- MENSAJE DE ERROR -->
        <?php
        if ($mensaje_FechaIncorrecta != "") {
            echo '<p id="mensaje_error"><strong>' .  $mensaje_FechaIncorrecta . '</strong></p>';
        } ?>



    </div>

</body>

</html>
