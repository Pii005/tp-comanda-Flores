<?php



class Reseña
{
    private $idPedido;
    private $mesa;
    private $restaurante;
    private $mozo;
    private $cocinero;
    private $promedio;

    public function __construct($idPedido,$mesa, $restaurante, $mozo, $cocinero, $promedio) {
        $this->idPedido = $idPedido;
        $this->mesa = $mesa;
        $this->restaurante = $restaurante;
        $this->mozo = $mozo;
        $this->cocinero = $cocinero;
        $this->promedio = $promedio;
    }


    public function mostrar()
    {
        $data = [
            'Pedido' => $this->getIdPedido(),
            'Puntuacion de la mesa' => $this->getMesa(),
            'Puntuacion del restaurante' => $this->getRestaurante(),
            'Puntuacion del mozo' => $this->getMozo(),
            'Puntuacion del cocinero' => $this->getCocinero()
        ];

        $suma = $this->getMesa() + $this->getRestaurante() + 
        $this->getMozo() + $this->getCocinero();

        $promedio = $suma / 4;

        $data['Promedio'] = $promedio;

        return $data;
    }

    

    public function getIdPedido() {
        return $this->idPedido;
    }

    public function getMesa() {
        return $this->mesa;
    }
    public function getRestaurante() {
        return $this->restaurante;
    }
    public function getMozo() {
        return $this->mozo;
    }
    public function getCocinero() {
        return $this->cocinero;
    }
    public function getPromedio() {
        return $this->promedio;
    }


}