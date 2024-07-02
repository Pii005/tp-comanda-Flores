<?php

include_once ".\models\Pedido.php";
//include_once "./Enumerados/estadoPedido.php";
include_once  ".\models\AltaComida.php";
include_once "./models/AltaMesa.php";
include_once "./models/AltaPendientes.php";
include_once "./Enumerados/estadoMesa.php";

class AltaPedidos
{
    private $acceso;

    public function __construct()
    {
        $this->acceso = AccesoDatos::obtenerInstancia();
    }

    public function buscarPedido($id)
    {
        $consulta = $this->acceso->prepararConsulta(
            "SELECT id, nombreCliente, idMesa, imagen, estadoPedido, tiempoPreparacion, inicioPedido, finalizacionPedido, preparadoEnTiempo
            FROM pedidos
            WHERE id = :id"
        );
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

        
        if (count($resultados) > 0) {
            $resultado = $resultados[0];
            $items = AltaPendientes::buscarPendientes($resultado['id']);
            $pedido = new Pedidos(
                $resultado['nombreCliente'],
                $resultado['idMesa'],
                $items, 
                $resultado['imagen']
            );
            $pedido->setID($resultado['id']);
            $pedido->setEstadoPedido($resultado['estadoPedido']);
            $pedido->setTiempoPreparacion($resultado['tiempoPreparacion']);
            $pedido->setInicioPedido(new DateTime($resultado['inicioPedido']));
            $pedido->setFinalizacionPedido($resultado['finalizacionPedido'] ? new DateTime($resultado['finalizacionPedido']) : null);
            $pedido->setPreparadoEnTiempo($resultado['preparadoEnTiempo']);
            return $pedido;
        } else {
            return null;
        }
    }

    public function obtenerTiempo()
    {
        return new DateTime();
    }

    
    

    public function crearNuevo($idMesa, $nombreCliente, $Comidas, $imagen)
    {
        //validar mesa - Que este ocupando la mesa realmente alguien y este en pendiente

        $validacionMesa = AltaMesa::validarMesaOcupada($idMesa);

        if($validacionMesa)
        {
            // echo "mesa valida <br>";
            $pedido = new Pedidos($nombreCliente, $idMesa, $Comidas, $imagen);
            //si es true - mando a pendientes si esta todo correcto
            $guardadoPendientes = AltaPendientes::ingresoPendientes($pedido->getId(), $Comidas);
            if($guardadoPendientes)
            {
                $estadoCambiado = AltaMesa::modificarEstado($idMesa, estadoMesa::pedido);
                if($estadoCambiado)
                {
                    
                    self::crearYGuardar($pedido);
                }
                
            }
            else
            {
                echo "Error!";
            }
            //si esta todo bien, creo el pedido y lo guardo 

        }

    }

    public function crearYGuardar($pedido)
    {
            $consulta = $this->acceso->prepararConsulta("INSERT INTO pedidos
            (id, nombreCliente, idMesa, imagen, estadoPedido, tiempoPreparacion, inicioPedido)
            VALUES (:id, :nombreCliente, :idMesa, :imagen, :estadoPedido, :tiempoPreparacion, :inicioPedido)");//Query

            $consulta->bindValue(':id', $pedido->getId(), PDO::PARAM_STR);
            $consulta->bindValue(':nombreCliente', $pedido->getNombreCliente(), PDO::PARAM_STR);
            $consulta->bindValue(':idMesa', $pedido->getIdMesa(), PDO::PARAM_STR);
            $consulta->bindValue(':imagen', $pedido->getImagen(), PDO::PARAM_STR);
            $consulta->bindValue(':estadoPedido', $pedido->getEstadoPedido(), PDO::PARAM_STR);
            $consulta->bindValue(':tiempoPreparacion', $pedido->getTiempoPreparacion(), PDO::PARAM_STR);
            $consulta->bindValue(':inicioPedido', $pedido->getInicioPedido()->format('Y-m-d H:i:s'), PDO::PARAM_STR);
            $consulta->execute();

            echo "Guardado<br>";
    }

    


    public function mostrarPedido($id)
    {
        $pedido = self::buscarPedido($id);
        if ($pedido) {
            $pedido->mostrar();
        } else {
            echo "Pedido no encontrado.<br>";
        }
    }  

    

    
    public function eliminarPedido($id)
    {
        if(self::buscarPedido($id))
        {
            echo "El elemento existe<br>";
            $consulta = $this->acceso->prepararConsulta("DELETE FROM pedidos WHERE id = :id");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->execute();
            echo "Eliminado con existo<br>";
            return $consulta->rowCount();
        }else{
            echo "El elemento no existe<br>";
        }
    }


    public function modificarPedido($idPedido, $modificador, $nuevo)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
    
        $camposPermitidos = ['nombreCliente', 'idMesa']; 
        if (!in_array($modificador, $camposPermitidos)) 
        {
            throw new InvalidArgumentException("Campo no permitido para modificar");
        }

        if($modificador == 'idMesa')
        {
            if(AltaMesa::buscarMesa($nuevo)){
                throw new InvalidArgumentException("Tipo de puestos no validos");
            }
        }

        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedido SET $modificador = :nuevo WHERE idPedido = :idPedido");
        $consulta->bindValue(':nuevo', $nuevo, PDO::PARAM_STR);
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
        $consulta->execute();

        echo "Modificado con exito!<br>";
    }

    public function cambiarEstados($idPedido, $estado)
    {
        if(!in_array($estado, [
            EstadoPedido::listo,
            EstadoPedido::servido
        ]))
        {
            throw new InvalidArgumentException("Tipo de estado no valido");
        }

        $objAccesoDato = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET estadoPedido = :estado WHERE id = :id");
        $consulta->bindValue(':estadoPedido', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $idPedido, PDO::PARAM_INT);
        $consulta->execute();

        echo "Modificado con exito!<br>";

    }

    public function pedidoTerminado($idPedido)
    {
        $pendientesTerminados = AltaPendientes::revisarPendientesTerminados($idPedido);
        if($pendientesTerminados)
        {
            $this->cambiarEstados($idPedido, EstadoPedido::listo);
            $this->mostrarPedido($idPedido);
            echo "Pedido terminado, Listo para entregar!!<br>";
            return;
        }
        echo "El pedido aun no esta listo<br>";
    }

}

