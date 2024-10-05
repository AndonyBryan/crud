<?php
// Nombre del archivo local donde se guardarán los datos
$filename = 'contactos.txt';

// Función para leer los contactos desde el archivo
function leerContactos() {
    global $filename;
    if (file_exists($filename)) {
        $fileData = file_get_contents($filename);
        return json_decode($fileData, true) ?? [];
    }
    return [];
}

// Función para guardar los contactos en el archivo
function guardarContactos($contactos) {
    global $filename;
    file_put_contents($filename, json_encode($contactos, JSON_PRETTY_PRINT));
}

// Manejar las operaciones CRUD
$contactos = leerContactos();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['agregar'])) {
        $nuevoContacto = [
            'matricula' => $_POST['matricula'],
            'nombre' => $_POST['nombre'],
            'carrera' => $_POST['carrera']
        ];

        // Agregar nuevo contacto
        $contactos[] = $nuevoContacto;
        guardarContactos($contactos);
        echo "Contacto agregado correctamente.";
    }

    if (isset($_POST['actualizar'])) {
        $matricula = $_POST['matricula'];
        foreach ($contactos as &$contacto) {
            if ($contacto['matricula'] == $matricula) {
                $contacto['nombre'] = $_POST['nombre'];
                $contacto['carrera'] = $_POST['carrera'];
                guardarContactos($contactos);
                echo "Contacto actualizado correctamente.";
                break;
            }
        }
    }

    if (isset($_POST['eliminar'])) {
        $matricula = $_POST['matricula'];
        $contactos = array_filter($contactos, function($contacto) use ($matricula) {
            return $contacto['matricula'] != $matricula;
        });
        guardarContactos($contactos);
        echo "Contacto eliminado correctamente.";
    }

    if (isset($_POST['buscar'])) {
        $matricula = $_POST['matricula'];
        $contactoEncontrado = array_filter($contactos, function($contacto) use ($matricula) {
            return $contacto['matricula'] == $matricula;
        });

        if (!empty($contactoEncontrado)) {
            $contacto = array_shift($contactoEncontrado);
            echo "Matrícula: " . $contacto['matricula'] . "<br>";
            echo "Nombre: " . $contacto['nombre'] . "<br>";
            echo "Carrera: " . $contacto['carrera'] . "<br>";
        } else {
            echo "No se encontró el contacto.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Contactos (Local)</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f8ff;
        }
        .nav-link {
            font-size: 1.2rem;
            color: white;
        }
        .nav-link.active {
            background-color: white !important;
            color: #007bff !important;
        }
        .content-section {
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .alert-info {
            background-color: #e0f7fa;
            color: #00796b;
        }
        #profile, #contact {
            color: white;
            background-color: #343a40;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center text-primary">Gestión de Contactos</h2>

        <!-- Navegación entre secciones -->
        <ul class="nav nav-pills nav-fill gap-2 p-1 small bg-primary rounded-5 shadow-sm mb-4" id="pillNav2" role="tablist">
            <li class="nav-item" role="presentation">
                <a href="#home" class="nav-link active rounded-5" onclick="showSection('home')">Home</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="#profile" class="nav-link rounded-5" onclick="showSection('profile')">Perfil</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="#contact" class="nav-link rounded-5" onclick="showSection('contact')">Contacto</a>
            </li>
        </ul>

        <!-- Sección de Home -->
        <div id="home" class="content-section">
            <div class="alert alert-info" role="alert">
                Bienvenido al sistema de gestión de contactos. Aquí puedes agregar, actualizar, eliminar y buscar contactos.
            </div>

            <form method="post" action="crud.php">
                <div class="row">
                    <div class="col-xs-12 col-sm-3">
                        <div class="form-group">
                            <label for="textmatricula">Matrícula</label>
                            <input type="text" id="textmatricula" class="form-control" name="matricula">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-3">
                        <div class="form-group">
                            <label for="textnombre">Nombre</label>
                            <input type="text" id="textnombre" class="form-control" name="nombre">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-3">
                        <div class="form-group">
                            <label for="textcarrera">Carrera</label>
                            <input type="text" id="textcarrera" class="form-control" name="carrera">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-3">
                        <div class="form-group mt-4">
                            <button type="submit" name="agregar" class="btn btn-primary">Agregar</button>
                            <button type="submit" name="actualizar" class="btn btn-success">Actualizar</button>
                            <button type="submit" name="eliminar" class="btn btn-danger">Eliminar</button>
                            <button type="submit" name="buscar" class="btn btn-info">Buscar</button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row">
                <div class="col-md-12 mt-4">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Matrícula</th>
                                <th>Nombre</th>
                                <th>Carrera</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($contactos)) {
                                foreach ($contactos as $contacto) {
                                    echo "<tr>
                                            <td>{$contacto['matricula']}</td>
                                            <td>{$contacto['nombre']}</td>
                                            <td>{$contacto['carrera']}</td>
                                        </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3'>No hay contactos registrados.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sección de Perfil -->
        <div id="profile" class="content-section" style="display: none;">
            <h3 class="text-center">Perfil</h3>
            <p><strong>Nombre:</strong> Antony Matuz</p>
            <p><strong>Matricula:</strong> 22887049</p>
            <p><strong>Carrera:</strong> Ingeniería en Informática</p>
            <p><strong>Semestre:</strong> 5º semestre</p>
            <p><strong>Materia:</strong> Sistemas Operativos II</p>
        </div>

        <!-- Sección de Contacto -->
        <div id="contact" class="content-section" style="display: none;">
            <h3 class="text-center">Contacto</h3>
            <p><strong>Correo:</strong> antony@example.com</p>
        </div>
    </div>

    <script>
        function showSection(section) {
            document.getElementById('home').style.display = 'none';
            document.getElementById('profile').style.display = 'none';
            document.getElementById('contact').style.display = 'none';
            document.getElementById(section).style.display = 'block';

            const tabs = document.querySelectorAll('.nav-link');
            tabs.forEach(tab => {
                tab.classList.remove('active');
            });

            document.querySelector(`[href="#${section}"]`).classList.add('active');
        }
    </script>
</body>
</html>
