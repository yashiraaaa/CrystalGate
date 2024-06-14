<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../helpers/database.php');
/*
 *  Clase para manejar el comportamiento de los datos de la tabla administrador.
 */
class AdministradorHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id = null;
    protected $id_tipo_administrador = null;
    protected $nombre = null;
    protected $apellido = null;
    protected $correo = null;
    protected $clave = null;

    /*
     *  Métodos para gestionar la cuenta del administrador.
     */
    public function checkUser($email, $password)
    {
        $sql = 'SELECT id_administrador, correo, clave FROM tb_Administradores WHERE correo = ?';
        $params = array($email);
        if (!($data = Database::getRow($sql, $params))) {
            return false;
        } elseif (password_verify($password, $data['clave_administrador'])) {
            $_SESSION['idAdministrador'] = $data['id_administrador'];
            $_SESSION['correoAdministrador'] = $data['correo'];
            return true;
        } else {
            return false;
        }
    }

    public function checkPassword($password)
    {
        $sql = 'SELECT * FROM tb_Administradores WHERE id_administrador = ?';
        $params = array($_SESSION['idAdministrador']);
        $data = Database::getRow($sql, $params);
        // Se verifica si la contraseña coincide con el hash almacenado en la base de datos.
        if (password_verify($password, $data['clave_administrador'])) {
            return true;
        } else {
            return false;
        }
    }

    public function changePassword()
    {
        $sql = 'UPDATE tb_Administradores SET clave = ? WHERE id_administrador = ?';
        $params = array($this->clave, $_SESSION['idAdministrador']);
        return Database::executeRow($sql, $params);
    }

    public function readProfile()
    {
        $sql = 'SELECT a.id_administrador, ta.tipo_administrador, a.nombre, a.apellido, a.correo
                FROM tb_administradores a INNER JOIN tb_tipos_administradores ta ON a.id_tipo_administrador = ta.id_tipo_administrador
                WHERE a.id_administrador = ?;';
        $params = array($_SESSION['idAdministrador']);
        return Database::getRow($sql, $params);
    }

    public function editProfile()
    {
        $sql = 'UPDATE tb_administradores SET nombre = ?, apellido = ?, correo = ? WHERE id_administrador = ?';
        $params = array($this->nombre, $this->apellido, $this->correo, $_SESSION['idAdministrador']);
        return Database::executeRow($sql, $params);
    }

    /*
     *  Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT * FROM tb_administradores WHERE id_administrador LIKE ?, tipo_administrador LIKE ?, nombre LIKE ?, apellido LIKE ?';
        $params = array($value, $value, $value, $value);
        return Database::getRows($sql, $params);
    }

    public function createRow()
    {
        $sql = 'INSERT INTO tb_administradores(id_tipo_administrador, nombre, apellido, correo, clave) VALUES(?, ?, ?, ?, ?)';
        $params = array($this->id_tipo_administrador, $this->nombre, $this->apellido, $this->correo, $this->clave);
        return Database::executeRow($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT id_administrador, id_tipo_administrador, nombre, apellido, clave, correo
                FROM tb_administradores
                ORDER BY apellido';
        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT id_administrador, id_tipo_administrador, nombre, apellido, clave, correo
                FROM tb_administradores
                WHERE id_administrador = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE tb_administradores
                SET id_tipo_administrador = ?, nombre = ?, apellido = ?, correo = ?
                WHERE id_administrador = ?';
        $params = array($this->id_tipo_administrador, $this->nombre, $this->apellido, $this->correo, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM tb_administradores
                WHERE id_administrador = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }
}