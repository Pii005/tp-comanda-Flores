<?php

class ControlerClientes
{
    public function obtenerTiempoPreparacion($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if (isset($parametros['idPedido'])) 
        {
            $idPedido = $parametros['idPedido'];
            
            try
            {
                $alta = new AltaPedidos();
                $msg = $alta->obtenerTiempoPreparacion($idPedido);
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
}