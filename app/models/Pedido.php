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


    public function __construct($nombre,$idMesa, $items, $imagen)
    {
        $this->id = $this->crearID();
        $this->idMesa = $idMesa;
        $this->nombreCliente = $nombre;
        $this->inicioPedido =  $this->obtenerTiempo();
        $this->imagen = $this->procesarImagen($imagen);
        $this->tiempoPreparacion =  $this->obtenerTiempoPreparacion($items);
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

    public function verificarEnTiempo($inicio, $fin)
    {
        if ($this->finalizacionPedido) {
            
            $diferencia = $inicio->diff($fin);//DateInterval: calcula la diferencia entre los dos objetos 
            $diferenciaEnMinutos = $diferencia->days * 24 * 60;//convierte el número de días en minutos multiplicando (24) y luego los minutos
            $diferenciaEnMinutos += $diferencia->h * 60;//convierte el número de horas
            $diferenciaEnMinutos += $diferencia->i;//suma el número de minutos

            if ($diferenciaEnMinutos <= $this->tiempoPreparacion) {//Compara la diferencia total en minutos
                $this->preparadoEnTiempo = true;
            } else {
                $this->preparadoEnTiempo = false;
            }
        } else {
            $this->preparadoEnTiempo = false;
        }
        return $this->preparadoEnTiempo;
    }

    public function crearID()
    {
        $longitud = 5;
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codigo = '';
        for ($i = 0; $i < $longitud; $i++) {
            $codigo .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }
        return $codigo;
    }

    private  function procesarImagen($imagen)
    {
        $uploadDir = __DIR__ . "/ImagenesClientes/2024/";
        
        // Crear el directorio si no existe
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // $imagen = $this->getImagen();
        $nombreImagen = $this->getId() . "_" . $this->getNombreCliente() . "." . pathinfo($imagen["name"], PATHINFO_EXTENSION);
        $uploadFile = $uploadDir . $nombreImagen;

        if (move_uploaded_file($imagen["tmp_name"], $uploadFile)) {
            return $nombreImagen;
        } else {
            return false;
        }
    }

    public function mostrar()
    {
        echo "<br>ID: " . $this->id . "<br>";
        echo "Nombre del Cliente: " . $this->nombreCliente . "<br>";
        echo "ID de la Mesa: " . $this->idMesa . "<br>";
        //echo "Imagen: <img src='" . $this->imagen . "' alt='Imagen del pedido'><br>";
        echo "Estado del Pedido: " . $this->estadoPedido . "<br>";
        echo "Tiempo de Preparación: " . $this->tiempoPreparacion . " minutos<br>";
        echo "Inicio del Pedido: " . $this->inicioPedido->format('Y-m-d H:i:s') . "<br>";
        echo "Finalización del Pedido: " . ($this->finalizacionPedido ? $this->finalizacionPedido->format('Y-m-d H:i:s') : "N/A") . "<br>";
        echo "Preparado en Tiempo: " . ($this->preparadoEnTiempo ? true : false) . "<br>";
        echo "<br>";
        echo "<br>";

        AltaPendientes::mostrarPendientes($this->id);
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
    public function getTiempoPreparacion() { 
        return $this->tiempoPreparacion;
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



}





