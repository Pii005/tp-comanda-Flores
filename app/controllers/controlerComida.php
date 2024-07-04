<?php

include_once './models/AltaComida.php';


class ControlerComida 
{
    public function ingresarComida($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if (isset($parametros["nombre"]) && isset($parametros["tipoEmpleado"]) 
            && isset($parametros["precio"]) && isset($parametros["tiempoPreparacion"]))
        {
            $nombre = $parametros["nombre"];
            $tipoEmpleado = $parametros["tipoEmpleado"];
            $precio = $parametros["precio"];
            $tiempoPreparacion = $parametros["tiempoPreparacion"];

            try
            {
                $alta = new AltaComida();
                $mensaje = $alta->crearYGuardar($nombre, $tipoEmpleado, $precio, $tiempoPreparacion);
                $payload = json_encode(array("mensaje" => $mensaje));
            }
            catch(Exception $e)
            {
                $payload = json_encode(array("Error" => "No se pudo guardar la comida"));
            }
        }
        else 
        {
            $payload = json_encode(array("Error" => "Parametro 'id' no encontrado"));
        }

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function mostrarUnaComida($request, $response, $args)
    {
        $params = $request->getQueryParams();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') 
        {
            if (isset($params["nombre"])) {
                $nombre = $params["nombre"];
                try
                {
                    $alta = new AltaComida();
                    $mensaje = $alta->mostrarComida($nombre);
                    $payload = json_encode(array("mensaje" => $mensaje));
                }
                catch(Exception $e)
                {
                    $payload = json_encode(array("Error" => "No se pudo mostrar la comida"));
                }
            }
        }
        else
        {
            $payload = json_encode(array("Error" => "parametros no validos"));
        }
        
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }


    public function eliminarUnaComida($request, $response, $args)
    {
        $params = $request->getQueryParams();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') 
        {
            if (isset($params["nombre"])) {
                $nombre = $params["nombre"];

                try
                {
                    $alta = new AltaComida();
                    $mensaje = $alta->eliminar($nombre);
                    $payload = json_encode(array("mensaje" => $mensaje));

                }catch(Exception $e)
                {
                    $payload = json_encode(array("Error" => "No se pudo eliminar la comida"));
                }
            }
        }else
        {
            $payload = json_encode(array("Error" => "parametros no validos"));
        }
        
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }
}