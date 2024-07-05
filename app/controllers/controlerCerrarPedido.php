<?php

include_once './models/AltaPedidos.php';

class ControlerCerrarPedido
{
    function cerrarPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if (isset($parametros['idPedido']))
        {
            $idPedido = $parametros['idPedido'];

            try
            {
                $pedido = new AltaPedidos();
                $msg = $pedido->cerrarMesa($idPedido);

                $payload = json_encode(array("mensaje" => $msg));
            }catch(Exception $e)
            {
                $payload = json_encode(array("Error" => "No se pudo cerrar el pedido"));
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

