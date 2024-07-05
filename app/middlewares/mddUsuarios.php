<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

include_once "./models/Usuario.php";
    

class verificacionUsuarioMW
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $parametros = $request->getParsedBody();

        $response = new Response();
        if(isset($parametros["puesto"]))
        {
            $tipo = $parametros["puesto"];

            if(in_array($tipo,
            [
                TiposEmpleados::bartender,
                TiposEmpleados::cervecero,
                TiposEmpleados::cocinero,
                TiposEmpleados::mozo,
                TiposEmpleados::socio,
                TiposEmpleados::administrador
            ]))
            {
                return $handler->handle($request);
            }
            else
            {
                $payload = json_encode(array("Error" => "Puesto no valido"));
            }
        }else
        {
            $payload = json_encode(array("Error" => "parametros no validos"));
        }
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    



}



