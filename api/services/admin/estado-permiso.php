<?php
// Se incluye la clase del modelo.
require_once('../models/data/estado-permiso-data.php');

const POST_ID = "idEstadoPermiso";
const POST_ESTPERMISO = "EstadoPermiso";
const POST_ESTADO = "estadoEstadoPermiso";


// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $EstadoPermiso = new EstadoPermisoData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'fileStatus' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'searchRows':
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $EstadoPermiso->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;
            case 'createRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$EstadoPermiso->setEstPermiso($_POST[POST_ESTPERMISO]) or
                    !$EstadoPermiso->setEstado($_POST[POST_ESTADO])
                ) {
                    $result['error'] = $EstadoPermiso->getDataError();
                } elseif ($EstadoPermiso->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Idioma creado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al crear el idioma';
                }
                break;
            case 'readAll':
                if ($result['dataset'] = $EstadoPermiso->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen idiomas registrados';
                }
                break;
            case 'readOne':
                if (!$EstadoPermiso->setId($_POST[POST_ID])) {
                    $result['error'] = $EstadoPermiso->getDataError();
                } elseif ($result['dataset'] = $EstadoPermiso->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Idioma inexistente';
                }
                break;
            case 'updateRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$EstadoPermiso->setId($_POST[POST_ID]) or
                    !$EstadoPermiso->setEstPermiso($_POST[POST_ESTPERMISO]) or
                    !$EstadoPermiso->setEstado($_POST[POST_ESTADO])
                ) {
                    $result['error'] = $EstadoPermiso->getDataError();
                } elseif ($EstadoPermiso->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Idioma modificado correctamente';
                    // Se asigna el estado del archivo después de actualizar.
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el idioma';
                }
                break;
            case 'deleteRow':
                if (
                    !$EstadoPermiso->setId($_POST[POST_ID])
                ) {
                    $result['error'] = $EstadoPermiso->getDataError();
                } elseif ($EstadoPermiso->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Idioma eliminado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar el idioma';
                }
                break;
            default:
                $result['error'] = 'Acción no disponible dentro de la sesión';
        }
        // Se obtiene la excepción del servidor de base de datos por si ocurrió un problema.
        $result['exception'] = Database::getException();
        // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
        header('Content-type: application/json; charset=utf-8');
        // Se imprime el resultado en formato JSON y se retorna al controlador.
        print(json_encode($result));
    } else {
        print(json_encode('Acceso denegado'));
    }
} else {
    print(json_encode('Recurso no disponible'));
}