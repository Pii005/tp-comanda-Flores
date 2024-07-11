<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once './JWT/creacionjwt.php';
require_once './Enumerados/tiposEmpleados.php';

//admis - Permiso en todo
//mozos - solo cargar mesas, Pedidos, comidas, cerrrar mesa
//empleados - solo terminar un pendiente
//socios - todo menos analizar en profundida la base de datos

//Acceder a uno:
/*
$dataObject = json_decode($json);

// Accede a la parte "data"
$data = $dataObject->data;

// Ahora puedes acceder a los elementos dentro de "data"
$email = $data->email;
$puesto = $data->puesto;

*/

class VerificadorPuestos
{
    public function verificarAdmis(Request $request, RequestHandler $handler)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        
        $tokenDescode = AutentificadorJWT::ObtenerPayLoad($token);

        // $tokenDescode = '{'.$tokenDescode.'}';

        $data = json_decode(json_encode($tokenDescode), true);
        $datosUser = $data['data'];

        if($datosUser['puesto'] == TiposEmpleados::administrador)
        {
            $response = $handler->handle($request);
        }
        else
        {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'No tiene permiso para esta operacion'));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function verificarMozo(Request $request, RequestHandler $handler)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        
        $tokenDescode = AutentificadorJWT::ObtenerPayLoad($token);

        // $tokenDescode = '{'.$tokenDescode.'}';

        $data = json_decode(json_encode($tokenDescode), true);
        $datosUser = $data['data'];
        var_dump($datosUser['puesto']);
        if($datosUser['puesto'] == TiposEmpleados::mozo || $datosUser['puesto'] == TiposEmpleados::administrador
        || $datosUser['puesto'] == TiposEmpleados::socio)
        {
            $response = $handler->handle($request);
        }
        else
        {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'No tiene permiso para esta operacion'));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function verificarEmpleados(Request $request, RequestHandler $handler)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        
        $tokenDescode = AutentificadorJWT::ObtenerPayLoad($token);

        // $tokenDescode = '{'.$tokenDescode.'}';

        $data = json_decode(json_encode($tokenDescode), true);
        $datosUser = $data['data'];

        if($datosUser['puesto'] == TiposEmpleados::cocinero || $datosUser['puesto'] == TiposEmpleados::bartender
        ||  $datosUser['puesto'] == TiposEmpleados::cervecero || $datosUser['puesto'] == TiposEmpleados::administrador
        || $datosUser['puesto'] == TiposEmpleados::socio)
        {
            $response = $handler->handle($request);
        }
        else
        {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'No tiene permiso para esta operacion'));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function verificarSocios(Request $request, RequestHandler $handler)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        
        $tokenDescode = AutentificadorJWT::ObtenerPayLoad($token);

        // $tokenDescode = '{'.$tokenDescode.'}';

        $data = json_decode(json_encode($tokenDescode), true);
        $datosUser = $data['data'];

        if($datosUser['puesto'] == TiposEmpleados::socio || $datosUser['puesto'] == TiposEmpleados::administrador)
        {
            $response = $handler->handle($request);
        }
        else
        {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'No tiene permiso para esta operacion'));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

}


