<?php

include_once "./Enumerados/TiposEmpleados.php";
include_once "./db/AccesoDatos.php";

class Comidas{
    
    private $id;//Incremento
    private $nombre;
    private $tipoEmpleado; //el que lo cocina
    private $precio;
    private $tiempoPreparacion;
    

    public function __construct($nombre,  $tipoEmpleado, $precio, $tiempoPreparacion, $id=null)
    {
        $this->nombre = $nombre;
        $this->id = $id;
        $this->tipoEmpleado = $tipoEmpleado;
        $this->precio = $precio;
        $this->tiempoPreparacion = $tiempoPreparacion;
    }

    

    
    public static function validarTipoEmpleado($tipo)
    {
        return in_array($tipo, [
            TiposEmpleados::bartender,
            TiposEmpleados::cervecero,
            TiposEmpleados::cocinero,
            TiposEmpleados::mozo
        ]);
    }

    public static function validacionPrecio($precio)
    {
        return is_numeric($precio);
    }

    public static function validacionTiempo($tiempo)
    {
        return preg_match('/^([01]\d|2[0-3]):([0-5]\d)(:[0-5]\d)?$/', $tiempo);
    }

    public static function ValidacionesCompleta($comida)
    {
        $Vempleado = self::validarTipoEmpleado($comida->getTipoEmpleado());
        $Vprecio = self::validacionPrecio($comida->getPrecio());
        $Vtiempo = self::validacionTiempo($comida->getTiempoPreparacion());

        if (!$Vempleado) {
            echo "<br>Error en tipo de empleado<br>";
        }

        if (!$Vprecio) {
            echo "<br>Error en precio<br>";
        }

        if (!$Vtiempo) {
            echo "<br>Error en tiempo<br>";
        }

        return $Vempleado && $Vprecio && $Vtiempo;
    }

    public function mostrar() {
        
        $data = array(
            "ID" => $this->id,
            "Nombre" => $this->nombre,
            "Precio" => $this->precio,
            "Tiempo de Preparación" => $this->tiempoPreparacion
        );

        return $data;

    }

    

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function getTipoEmpleado()
    {
        return $this->tipoEmpleado;
    }

    public function setTipoEmpleado($tipoEmpleado)
    {
        if (!in_array($tipoEmpleado, [
            TiposEmpleados::bartender,
            TiposEmpleados::cervecero,
            TiposEmpleados::cocinero,
            TiposEmpleados::mozo
        ])) {
            throw new InvalidArgumentException("Tipo de empleado inválido");
        }
        $this->tipoEmpleado = $tipoEmpleado;
    }

    public function getPrecio()
    {
        return $this->precio;
    }

    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }

    public function getTiempoPreparacion()
    {
        return $this->tiempoPreparacion;
    }

    public function setTiempoPreparacion($tiempoPreparacion)
    {
        $this->tiempoPreparacion = $tiempoPreparacion;
    }
}

