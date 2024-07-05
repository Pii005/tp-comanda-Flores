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
            "SELECT *
            FROM pedidos
            WHERE id = :id"
        );
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

        if (count($resultados) > 0) {
            $resultado = $resultados[0];
            // var_dump($resultado['id']);
            $items = AltaPendientes::buscarPendientes($resultado['id']);
            $pedido = new Pedidos(
                $resultado['id'],
                $resultado['nombreCliente'],
                $resultado['idMesa'],
                $resultado['tiempoPreparacion'],
                $resultado['imagen']
            );

            $pedido->setItems( $items);
            $pedido->setEstadoPedido($resultado['estadoPedido']);
            $pedido->setInicioPedido(new DateTime($resultado['inicioPedido']));
            $pedido->setFinalizacionPedido(new DateTime($resultado['finalizacionPedido']));
            $pedido->setPreparadoEnTiempo($resultado['preparadoEnTiempo']);

            return $pedido;
        } else {
            return null;
        }
    }

    private function procesarImagen($imagen, $idPedido, $nombreCliente)
    {
        $uploadDir = __DIR__ . "/ImagenesClientes/2024/";

        // Crear el directorio si no existe
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Obtener el nombre y la extensión del archivo subido
        if ($imagen instanceof \Slim\Psr7\UploadedFile) {
            $nombreOriginal = $imagen->getClientFilename();
            $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);

            // Crear un nuevo nombre para la imagen
            $nombreImagen = $idPedido . "_" . $nombreCliente . "." . $extension;
            $uploadFile = $uploadDir . $nombreImagen;

            // Mover el archivo subido al directorio de destino
            $imagen->moveTo($uploadFile);

            return $nombreImagen;
        } else {
            throw new InvalidArgumentException("El archivo de imagen no es válido.");
        }
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
    

    public function crearNuevo($idMesa, $nombreCliente, $Comidas, $imagen)
    {
        //validar mesa - Que este ocupando la mesa realmente alguien y este en pendiente
        try{

            $validacionMesa = AltaMesa::validarMesaOcupada($idMesa);
    
            if($validacionMesa)
            {
                $idPedido = self::crearID();
                
                //si es true - mando a pendientes si esta todo correcto
                $guardadoPendientes = AltaPendientes::enviarComidas($idPedido, $Comidas);
                
                if($guardadoPendientes)
                {
                    $tiempoPreparacion = AltaComida::sumarTiempos($Comidas);
                    $rutaImagen = self::procesarImagen($imagen, $idPedido, $nombreCliente);
                    $pedido = new Pedidos($idPedido, $nombreCliente, $idMesa, $tiempoPreparacion, $rutaImagen);

                    $estadoCambiado = AltaMesa::modificarEstado($idMesa, estadoMesa::pedido);
                    if($estadoCambiado)
                    {
                        
                        $msg = self::crearYGuardar($pedido);
                        return $msg;
                    }
                }
                else
                {
                    return "No se pudieron guardar los pendientes";
                }
                //si esta todo bien, creo el pedido y lo guardo 
    
            }
            else
            {
                return "La mesa no esta libre o no esta cargada - Ingrese la mesa";
            }
        }
        catch(Exception $e)
        {
            throw new Exception("error en creanNuevo - AltaPedidos: " . $e->getMessage());
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

            return "Pedido guardado con exito";
    }

    


    public function mostrarPedido($id)
    {
        $array = [];
        $pedido = self::buscarPedido($id);
        if ($pedido) {
            $array['pedido'] = $pedido->mostrar();

            $pendientes = AltaPendientes::mostrarPendientes($pedido->getId());

            if ($pendientes) {
                $array['Pendientes'] = $pendientes;
            }
            return $array;

        } else {
            return "Pedido no encontrado";
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
            // echo "Eliminado con existo<br>";
            return "Pedido eliminado con existo";;
        }else{
            return "El pedido no existe";
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

        return "Pedido modificado con exito";
    }

    public function cambiarEstados($idPedido, $estado)
    {
        try
        {
            if($estado != EstadoPedido::servido && $estado != EstadoPedido::listo &&
            $estado != EstadoPedido::pagado)
            {
                throw new JsonException("Tipo de estado no valido CambiarEstados-AltaPedidos");
            }

            $objAccesoDato = AccesoDatos::obtenerInstancia();

            $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET estadoPedido = :estado WHERE id = :id");
            $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
            $consulta->bindValue(':id', $idPedido, PDO::PARAM_INT);
            $consulta->execute();

        }
        catch (Exception $e)
        {
            throw new JsonException("error en CambiarEstados-AltaPedidos");
        }
        // echo "Modificado con exito!<br>";

    }

    //reviso si el pedido tiene todos los pendientes terminados o no
    public function pedidoTerminado($idPedido)
    {
        try
        {
            $pendientesTerminados = AltaPendientes::revisarPendientesTerminados($idPedido);
            if($pendientesTerminados)
            {
                $this->cambiarEstados($idPedido, EstadoPedido::listo);
                
                return "Pedido terminado, Listo para entregar";
            }
            return "El pedido aun no esta listo";
        }
        catch (Exception $e)
        {
            return "Error en los cambios de estado del pedido";
        }
    }

    
// 	$fechaInicio = new DateTime("2020-03-09 17:55:15");
// 	$fechaFin = new DateTime("2022-01-01 17:45:25");
// 	$intervalo = $fechaInicio->diff($fechaFin);

// 	echo "La diferencia entre  " . $fechaInicio->format('Y-m-d h:i:s') . " y " . $fechaFin->format('Y-m-d h:i:s') . " es de: <br> 
//   " . $intervalo->h . " horas, " . $intervalo->i . " minutos y " . $intervalo->s . " segundos";  

public function verificarEnTiempo($idPedido, $fin)
{
    $pedido = self::buscarPedido($idPedido);

    var_dump("Inicio: ". $pedido->getInicioPedido()->format('Y-m-d H:i:s'));
    var_dump("estimado: ");
    var_dump($pedido->getTiempoPreparacion());
    var_dump("fin: ".$fin->format('Y-m-d H:i:s'));

    $inicio = $pedido->getInicioPedido();

    // Calcula la diferencia entre el inicio y el fin del pedido
    $diferencia = $inicio->diff($fin);

    // Calcula la diferencia total en segundos
    $totalSegundos = $diferencia->days * 24 * 60 * 60; // Días a segundos
    $totalSegundos += $diferencia->h * 60 * 60;        // Horas a segundos
    $totalSegundos += $diferencia->i * 60;             // Minutos a segundos
    $totalSegundos += $diferencia->s;                  // Segundos

    $tiempoMinimoSegundos = $pedido->getTiempoPreparacion() * 60;

    if ($totalSegundos <= $tiempoMinimoSegundos) {
        return false; // fuera del tiempo estimado
    } 
    return true; // dentro del tiempo estimado
}




    public function pedidoEntregado($idPedido)
    {
        $pedido = self::buscarPedido($idPedido);
        self::establecerFinalizar($idPedido);
        if($pedido->getEstadoPedido() == EstadoPedido::listo)
        {
            $this->cambiarEstados($idPedido, EstadoPedido::servido);
            
            //cambiar estadoMesa;
            AltaMesa::modificarEstado($pedido->getIdMesa(), estadoMesa::comiendo);
            
            
            return "Pedido entregado";
        }
        if($pedido->getEstadoPedido() == EstadoPedido::servido 
        || $pedido->getEstadoPedido() == EstadoPedido::pagado )
        {
            return "Este pedido ya fue entregado";
        }
        return "El pedido aun no esta listo";
    }
    
    function establecerFinalizar($idPedido)
    {
        try
        {
            $finalizacion = new DateTime();
            $preparadoEnTiempo = self::verificarEnTiempo($idPedido, $finalizacion);

            // var_dump($finalizacion);
            // var_dump($preparadoEnTiempo);

            $objAccesoDato = AccesoDatos::obtenerInstancia();

            $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos 
            SET finalizacionPedido = :finalizacionPedido, preparadoEnTiempo = :preparadoEnTiempo
            WHERE id = :id");
            $consulta->bindValue(':id', $idPedido, PDO::PARAM_STR);
            $consulta->bindValue(':finalizacionPedido', $finalizacion->format('Y-m-d H:i:s'), PDO::PARAM_STR);
            $consulta->bindValue(':preparadoEnTiempo', $preparadoEnTiempo, PDO::PARAM_STR);
            $consulta->execute();

        }
        catch (Exception $e)
        {
            throw new JsonException("error en establecerFinalizar-AltaPedidos - ". $e->getMessage());
        }
    }

    function cerrarMesa($idPedido)
    {
        $pedido = self::buscarPedido($idPedido);

        if($pedido)
        {
            if($pedido->getEstadoPedido() != estadoMesa::pagando)
            {
                //cambiar estado - pedido
                $this->cambiarEstados($idPedido, EstadoPedido::pagado);
                //cambiar estado - mesa
                AltaMesa::modificarEstado($pedido->getIdMesa(), estadoMesa::pagando);
                return "Pedido cerrado - retirar dinero";
            }
            return "El pedido ya esta cerrado";
        }
        return "El pedido no existe";

    }


}

