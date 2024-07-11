<?php

include_once './models/AltaPedidos.php';
include_once './models/AltaPendientes.php';


class ControlerPedidos
{
    public function ingresarPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $Archivo = $request->getUploadedFiles();

        if (isset($parametros['idMesa']) && isset($parametros["nombre"]) 
        && isset($parametros["comidas"]) && isset($Archivo["imagen"])) {

            $idMesa = $parametros['idMesa'];
            $nombre = $parametros['nombre'];
            $comidas = str_getcsv($parametros['comidas']); 
            $comidas = array_map('trim', $comidas);
            $imagen = $Archivo["imagen"];
            
            try
            {
                $alta = new AltaPedidos();
                $msg = $alta->crearNuevo($idMesa, $nombre, $comidas, $imagen);

                $payload = json_encode(array("mensaje" => $msg));
            }
            catch(Exception $e)
            {
                $payload = json_encode(array("Error" => "No se pudo guardar el pedido - " . $e->getMessage()));
            }
        }
        else 
        {
            $payload = json_encode(array("Error" => "Parametros no validos"));
        }
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function cambiarAPreparacion($request, $response, $args)
    {
        $params = $request->getParsedBody();
        if(isset($params['idPedido'])){
            $idPedido = $params['idPedido'];

            try{

                $Alta = new AltaPedidos();

                $msg = $Alta->estadoEnPreparacion($idPedido);

                $payload = json_encode(array("mensaje" => $msg));

            }catch(Exception $e){
                $payload = json_encode(array("Error" => "No se pudo cambiar el pedido - " . $e->getMessage()));
            }
        }else {
            $payload = json_encode(array("Error" => "Parametros no validos"));
        }
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }
    

    public function mostrarPedido($request, $response, $args)
    {
        $params = $request->getQueryParams();
        $payload = ""; // Inicializar la variable $payload

        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        {
            if (isset($params["idPedido"])) 
            {
                try
                {
                    $pedido = new AltaPedidos();
                    $msg = $pedido->mostrarPedido($params["idPedido"]);
                    $payload = json_encode($msg);
                }
                catch(Exception $e)
                {
                    $payload = json_encode(array("Error" => "No se encontro el pedido"));
                }
            }
            else 
            {
                $payload = json_encode(array("Error" => "Parametros no validos"));
            }
        }
        else
        {
            $payload = json_encode(array("Error" => "No se logro la conexion"));
        }
        
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function borrarPedido($request, $response, $args)
    {
        $params = $request->getQueryParams();

        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        {
            if (isset($params["idPedido"])) 
            {
                try
                {
                    $pedido = new AltaPedidos();
                    $pedido->eliminarPedido($params["idPedido"]);
                }catch(Exception $e)
                {
                    $payload = json_encode(array("Error" => "No se encontro el pedido"));
                }
            }
            else 
            {
                $payload = json_encode(array("Error" => "Parametros no validos"));
            }
        }else
        {
            $payload = json_encode(array("Error" => "No se logro la conexion"));
        }
        
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }


    public function modificarPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if (isset($parametros['idPedido']) && isset($parametros["modificar"]) 
        && isset($parametros["nuevo"])) {

            $idPedido = $parametros['idPedido'];
            $modificar = $parametros['modificar'];
            $nuevo =  $parametros['nuevo']; // Suponiendo que las comidas se envían como una cadena separada por comas
            
            try
            {
                $pedido = new AltaPedidos();
                $msg = $pedido->modificarPedido($idPedido, $modificar, $nuevo);

                $payload = json_encode(array("mensaje" => $msg));
            }
            catch(Exception $e)
            {
                $payload = json_encode(array("Error" => "No se pudo modificar el pedido"));
            }
        }
        else 
        {
            $payload = json_encode(array("Error" => "Parametros no validos"));
        }
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }


    //MIDDLWARE DE VALIDACION DE PUESTO
    public function terminarPendiente($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if (isset($parametros['idPedido']) && isset($parametros["comida"]))
        {
            $idPedido = $parametros['idPedido'];
            $comida = $parametros["comida"];

            try
            {
                $terminado = AltaPendientes::cambiarTerminado($idPedido, $comida);//cambio estado
                
                if($terminado)
                {
                    $pedido = new AltaPedidos();
                    $msg = $pedido->pedidoTerminado($idPedido);
    
                    $payload = json_encode(array("mensaje" => $msg));
                }
                else
                {
                    $payload = json_encode(array("Error" => "No se logro cambiar el estado"));
                }
            }catch(Exception $e)
            {
                $payload = json_encode(array("Error" => "No se pudo terminar el pendiente"));
            }
        }
        else 
        {
            $payload = json_encode(array("Error" => "Parametros no validos"));
        }
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function entregarPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if (isset($parametros['idPedido']))
        {
            $idPedido = $parametros['idPedido'];

            try
            {
                $pedido = new AltaPedidos();
                $msg = $pedido->pedidoEntregado($idPedido);

                $payload = json_encode(array("mensaje" => $msg));
            }catch(Exception $e)
            {
                $payload = json_encode(array("Error" => "No se pudo entregar el pedido - " . $e->getMessage()));
            }
        }
        else 
        {
            $payload = json_encode(array("Error" => "Parametros no validos"));
        }
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function mostrarPendientePuesto($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if (isset($parametros['puesto']))
        {
            $puesto = $parametros['puesto'];

            try
            {
                $pedido = new AltaPendientes();
                $msg = $pedido->mostrarPendientePuesto($puesto);

                $payload = json_encode(array("mensaje" => $msg));
            }catch(Exception $e)
            {
                $payload = json_encode(array("Error" => "No se pudo entregar el pedido - " . $e->getMessage()));
            }
        }
        else 
        {
            $payload = json_encode(array("Error" => "Parametros no validos"));
        }
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

}



