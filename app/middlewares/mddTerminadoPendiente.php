<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class MddTerminadoPendiente
{
    public function __invoke(Request $request, RequestHandler $handler) : Response
    {
        $parametros = $request->getParsedBody();
        $response = new Response();
        
        if (isset($parametros["comida"])) {
            $header = $request->getHeaderLine('Authorization');
            $token = trim(explode("Bearer", $header)[1]);

            $tokenDescode = AutentificadorJWT::ObtenerPayLoad($token);

            $data = json_decode(json_encode($tokenDescode), true);
            $datosUser = $data['data'];
            
            $puestoUser = $datosUser['puesto'];
            $comida = AltaComida::devolverComida($parametros['comida']);

            if ($comida->getTipoEmpleado() == $puestoUser) {
                $response = $handler->handle($request);
            } else {
                $payload = json_encode(['mensaje' => 'No tiene permiso para esta operación']);
                $response->getBody()->write($payload);
            }
        } else {
            $payload = json_encode(['Error' => 'Parámetros incorrectos']);
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}
