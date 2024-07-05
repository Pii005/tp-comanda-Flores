<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

include_once './models/AltaPedidos.php';
include_once './models/AltaReseña.php';


class ValidarResenia
{
    public function __invoke(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $response = new Response();
        if(isset($parametros["idPedido"]))
        {
            $id = $parametros["idPedido"];

            $alta = new AltaPedidos();
            $pedido = $alta->buscarPedido($id);
    
            if($pedido->getEstadoPedido() == EstadoPedido::pagado)
            {
                return $handler->handle($request);
            }
            else
            {
                $payload = json_encode(array("Error" => "El pedido aun no esta cerrado"));
            }
        }else
        {
            $payload = json_encode(array("Error" => "parametros no validos"));
        }  

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


}