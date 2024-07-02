<?php

include_once "./models/Usuario.php";
include_once "./models/Empleado.php";
include_once "./db/AccesoDatos.php";


class AltaEmpleado
{
    private $acceso;

    public function __construct()
    {
        $this->acceso = AccesoDatos::obtenerInstancia();
    }


    public function buscarEmpleado($id)
    {
        $consulta = $this->acceso->prepararConsulta(
            "SELECT id
            FROM empleados
            WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if (count($resultados) > 0) {
            return true;
        } else {
            return false;
        }   
    }

    public function crearYGuardar($nombre, $apellido, $puesto)
    {
        /*$empleado = new Empleado($nombre, $apellido, $puesto);
        $consulta = $this->acceso->prepararConsulta("INSERT INTO empleados
        (nombre, apellido, puesto)
        VALUES (:nombre, :apellido, :puesto)");

        $consulta->bindValue(':nombre', $empleado->getNombre(), PDO::PARAM_STR);
        $consulta->bindValue(':apellido', $empleado->getApellido(), PDO::PARAM_STR);
        $consulta->bindValue(':puesto', $empleado->getPuesto(), PDO::PARAM_STR);
        $consulta->execute();
        echo "Guardado<br>";
    
*/    }


    public function asignarPendientes($pendiente, $id)
    {
        
    }






}



