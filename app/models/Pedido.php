<?php


include_once "./Enumerados/estadoPedido.php";

include_once "./models/AltaPendientes.php";

class Pedidos
{
    private $id;//5 digitos
    private $nombreCliente;
    private $idMesa;//Numero mesa
    private $imagen;//
    private $estadoPedido;//enum default Pidiendo/esperando 
    private $items = []; //array de la class comidas
    private $tiempoPreparacion;
    private $inicioPedido;//time now
    private $finalizacionPedido;//default en null

    private $preparadoEnTiempo; //default en null y cuando llego a finalizacion se asigna true o false


    public function __construct($id ,$nombre,$idMesa, $tiempoPreparacion, $imagen)
    {
        $this->id = $id;
        $this->idMesa = $idMesa;
        $this->nombreCliente = $nombre;
        $this->inicioPedido =  $this->obtenerTiempo();
        $this->imagen = $imagen;
        $this->tiempoPreparacion =  $tiempoPreparacion;
        $this->estadoPedido = EstadoPedido::preparando;
        
    }

    public function obtenerTiempo()
    {
        return new DateTime();
    }

    public function obtenerTiempoPreparacion($Comidas)
    {
        return AltaComida::sumarTiempos($Comidas);
    }

    public function mostrar()
    {
        $datos = [
            'ID' => $this->id,
            'Nombre del Cliente' => $this->nombreCliente,
            'ID de la Mesa' => $this->idMesa,
            'ruta de foto' => $this->imagen,
            'Estado del Pedido' => $this->estadoPedido,
            'Tiempo de Preparación' => $this->tiempoPreparacion . " minutos",
            'Inicio del Pedido' => $this->inicioPedido->format('Y-m-d H:i:s'),
        ];
        if($this->finalizacionPedido->format('Y-m-d H:i:s') != "-0001-11-30 00:00:00")
        {
            $datos['Finalización del Pedido'] = $this->finalizacionPedido->format('Y-m-d H:i:s');
        }
        
        if($this->preparadoEnTiempo != false)
        {
            $datos['Preparado en Tiempo'] = $this->preparadoEnTiempo ? true : false;
        }
        
        // $pendientes = AltaPendientes::mostrarPendientes($this->id);

        // if ($pendientes) {
        //     $datos['Pendientes'] = $pendientes;
        // }

        return $datos;
    }


    public function getId() 
    { 
        return $this->id;
    }
    public function getNombreCliente() 
    { 
        return $this->nombreCliente;
    }
    public function getIdMesa() 
    { 
        return $this->idMesa; 
    }
    public function getImagen() { 
        return $this->imagen; 
    }
    public function getEstadoPedido() {
        return $this->estadoPedido;
    }
    public function getItems() {
        return $this->items;
    }
    public function getTiempoPreparacion()
    {
        return (int) $this->tiempoPreparacion;
    }
    public function getInicioPedido() { 
        return $this->inicioPedido; 
    }
    public function getFinalizacionPedido() { 
        return $this->finalizacionPedido; 
    }
    public function getPreparadoEnTiempo() { 
        return $this->preparadoEnTiempo; 
    }
    public function setID($id) { 
        $this->id = $id; 
    }
    public function setTiempoPreparacion($tiempoPreparacion) { 
        $this->tiempoPreparacion = $tiempoPreparacion; 
    }
    public function setInicioPedido($inicioPedido) { 
        $this->inicioPedido = $inicioPedido; 
    }
    public function setFinalizacionPedido($finalizacionPedido) { 
        $this->finalizacionPedido = $finalizacionPedido; 
    }
    public function setPreparadoEnTiempo($preparadoEnTiempo) { 
        $this->preparadoEnTiempo = $preparadoEnTiempo; 
    }
    public function setEstadoPedido($estadoPedido) { 
        $this->estadoPedido = $estadoPedido;
    }
    public function setNombreCliente($nombreCliente) { 
        $this->nombreCliente = $nombreCliente; 
    }
    public function setIdMesa($idMesa) { 
        $this->idMesa = $idMesa; 
    }
    public function setImagen($imagen) { 
        $this->imagen = $imagen;
    }

    public function setItems($a) { 
        $this->items = $a;
    }

}





