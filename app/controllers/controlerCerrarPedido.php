<?php

include_once './models/AltaPedidos.php';

class ControlerCerrarPedido
{
    function cerrarPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if (isset($parametros['idPedido']) && isset($parametros['socioCerro']))
        {
            $idPedido = $parametros['idPedido'];
            $socioCerro = $parametros['socioCerro'];

            try
            {
                $pedido = new AltaPedidos();
                $alta = new AltaMesa();

                $msg = $pedido->cerrarPedido($idPedido, $socioCerro);
                $alta->cerrarMesa($idPedido);

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

    // function cerrarMesa($request, $response, $args)
    // {
    //     $parametros = $request->getParsedBody();
    //     if (isset($parametros['idPedido']) && isset($parametros['socioEmail']))
    //     {
    //         $idPedido = $parametros['idPedido'];
    //         $socioCerro = $parametros['socioEmail'];

    //         try
    //         {
    //             $alta = new AltaMesa();
    //             $msg = $alta->cerrarMesa($idPedido);

    //             $payload = json_encode(array("mensaje" => $msg));
    //         }catch(Exception $e)
    //         {
    //             $payload = json_encode(array("Error" => "No se pudo cerrar el pedido"));
    //         }
    //     }
    //     else 
    //     {
    //         $payload = json_encode(array("Error" => "Parametros no validos"));
    //     }
    //     $response->getBody()->write($payload);

    //     return $response->withHeader('Content-Type', 'application/json');
    // }
}

