<?php

include_once "./models/Usuario.php";
include_once "./Enumerados/TiposEmpleados.php";

class Empleado extends Usuario
{
    private $pendientes = [];

    public function __construct($nombre, $apellido, $puesto, $alta, $contraseña, $email)
    {
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->puesto = $puesto;
        $this->alta = $alta;
        $this->contraseña = $contraseña;
        $this->email = $email;
    }

    public function verificarEmpleado()
    {
        if(in_array(self::getPuesto(),
        [
            TiposEmpleados::bartender,
            TiposEmpleados::cervecero,
            TiposEmpleados::cocinero,
            TiposEmpleados::mozo,
            TiposEmpleados::socio,
            TiposEmpleados::administrador
        ]))
        {
            return true;
        }else{
            return false;
        }
    }


    public function mostrarPendientes()
    {
        $pendientes = self::getPendientes();
        foreach($pendientes as $p)
        {
            echo $p;
        }
    }

    public function getPendientes()
    {
        return $this->pendientes;
    }
}


