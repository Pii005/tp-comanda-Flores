<?php

include_once "./models/Comidas.php";
include_once "./models/Pedido.php";

class Pendientes
{
    private $idEmpleado; //Busco en la listo el q' menos tareas tiene
    private $puesto; //Puesto que fue enviada la comida
    private $idPedido; // Pedido que se tiene q entregar la comida
    private $comida; //Lo que tiene que ser preparado
    private $horaLlegada;
    private $terminado; //false: sigue en preparacion true: finalizado

    public function __construct($idEmpleado, $puesto, $idPedido, $comida)
    {
        $this->idEmpleado = $idEmpleado;
        $this->puesto = $puesto;
        $this->idPedido = $idPedido;
        $this->comida = $comida;
        $this->horaLlegada = $this->obtenerTiempo();
        $this->terminado = false;
    }

    public function obtenerTiempo()
    {
        return new DateTime();
    }

    public function Mostrar()
    {
        $datos = [
            'Id Empleado a cargo' => $this->idEmpleado,
            'Puesto' => $this->puesto,
            'ID pedido' => $this->idPedido,
            'Comida' => $this->comida,
            'Hora de llegada' => $this->horaLlegada->format('Y-m-d H:i:s'),
            'Estado del Pendiente' => $this->terminado ? "Pendiente terminado" : "Pendiente sin terminar"
        ];

        return $datos;
    }


    public function getIdEmpleado()
    {
        return $this->idEmpleado;
    }

    public function setIdEmpleado($idEmpleado)
    {
        $this->idEmpleado = $idEmpleado;
    }

    public function getPuesto()
    {
        return $this->puesto;
    }

    public function setPuesto($puesto)
    {
        $this->puesto = $puesto;
    }

    public function getIdPedido()
    {
        return $this->idPedido;
    }

    public function setIdPedido($idPedido)
    {
        $this->idPedido = $idPedido;
    }

    public function getComida()
    {
        return $this->comida;
    }

    public function setComida($comida)
    {
        $this->comida = $comida;
    }

    public function getHoraLlegada()
    {
        return $this->horaLlegada;
    }

    public function setHoraLlegada($horaLlegada)
    {
        $this->horaLlegada = $horaLlegada;
    }

    public function getTerminado()
    {
        return $this->terminado;
    }

    public function setTerminado($terminado)
    {
        $this->terminado = $terminado;
    }
}





