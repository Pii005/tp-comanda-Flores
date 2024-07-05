<?php

include_once './models/AltaPedidos.php';
include_once './models/AltaReseña.php';


class ControlerResenia
{
    public function ingresarResenia($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros["idPedido"]) && isset($parametros["mozo"]) && isset($parametros["mesa"])
        && isset($parametros["restaurante"]) && isset($parametros["cocina"]))
        {
            $id = $parametros["idPedido"];
            $mozo = $parametros["mozo"];
            $mesa = $parametros["mesa"];
            $restaurante = $parametros["restaurante"];
            $cocina = $parametros["cocina"];

            $msg = AltaReseña::ingresarReseña($id, $mozo, $mesa, $restaurante, $cocina);

            $payload = json_encode(array("mensaje" => $msg));
        }else 
        {
            // Manejar el caso en el que 'id' no está presente
            $payload = json_encode(array("Error" => "Parametros no validos"));
        }

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function mostrarResenia($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros["idPedido"]))
        {
            $id = $parametros["idPedido"];
            
            $msg = AltaReseña::mostrarResenia($id);

            $payload = json_encode(array("mensaje" => $msg));
        }else 
        {
            // Manejar el caso en el que 'id' no está presente
            $payload = json_encode(array("Error" => "Parametro 'id' no encontrado"));
        }

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function mejoresResenias($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        try{
            $msg = AltaReseña::mejoresResenias();
    
            $payload = json_encode(array("mensaje" => $msg));
        }
        catch(Exception $e)
        {
            $payload = json_encode(array("Error" => "Parametro 'id' no encontrado"));
        }

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }
}