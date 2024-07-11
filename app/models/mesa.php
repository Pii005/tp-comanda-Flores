<?php

include_once "./Enumerados/estadoMesa.php";
include_once "./db/AccesoDatos.php";


class mesa
{
    private $id;//único 
    private $estado;//Default Libre 
    private $horaLlegada;//un time now 
    // private $horaSalida;//un now cuando el cliente cierra mesa
    // private $socioCerro; //Socio que cerro la mesa

    public function __construct($id)
    {
        $this->estado = estadoMesa::esperando;
        $this->id = $id;
        $this->horaLlegada = $this->obtenerTiempo();
    }

    


    public function mostrar()
{
    $data = array(
        "ID" => $this->id,
        "Estado" => $this->estado,
        "Hora llegada" => $this->horaLlegada,
    );

    // if ($this->horaSalida != "0000-00-00 00:00:00" && $this->horaSalida != null) {
    //     $data["Hora que el cliente se fue"] = $this->horaSalida;
    // }
    // if ($this->socioCerro != null) {
    //     $data["Socio que cerro la mesa"] = $this->socioCerro;
    // }

    return $data;
}


    public function obtenerTiempo()
    {
        return new DateTime();
    }


    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        
    }

    // Getter y Setter para $estado
    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }

    // Getter y Setter para $horaLlegada
    public function getHoraLlegada() {
        return $this->horaLlegada;
    }

    public function setHoraLlegada($horaLlegada) {
        $this->horaLlegada = $horaLlegada;
    }

}


