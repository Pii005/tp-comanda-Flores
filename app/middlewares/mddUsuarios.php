<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

include_once "./models/Usuario.php";
    

class DatosMiddleware
{
    public function crearUserMW(Request $request, RequestHandler $handler)
    {
        $params = $request->getParsedBody();
        
        if (isset($params['nombre']) && isset($params["apellido"]) && isset($params["puesto"]) &&
            isset($params["contraseña"]) && isset($params["email"])) {
            
            $nombre = $params["nombre"];
            $apellido = $params["apellido"];
            $puesto = $params["puesto"];
            $contraseña = $params["contraseña"];
            $email = $params["email"];

            $empleado = new Usuario($nombre, $apellido, $puesto, $contraseña, $email);

            if (Usuario::buscarEmpleado($email)) {
                // Usuario encontrado, devolver error
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "El usuario ya existe")));
                return $response->withStatus(400);
            } else {
                // Usuario no encontrado, crear nuevo usuario
                if ($empleado->crearUsuario()) {
                    // Usuario creado exitosamente, continuar con el siguiente middleware/controlador
                    $response = $handler->handle($request);
                    echo "creado!!!";
                } else {
                    // Error al crear el usuario
                    $response = new Response();
                    $response->getBody()->write(json_encode(array("error" => "El tipo de empleo es incorrecto")));
                    return $response->withStatus(400);
                }
            }
        } else {
            // Faltan parámetros, devolver error
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Faltan parámetros")));
            return $response->withStatus(400);
        }

        return $response;
    }


    public function modificarUserMW(Request $request, RequestHandler $handler)
    {
        $params = $request->getQueryParams();

        if(isset($_POST['Email']) && isset($_POST['modificar']) 
        && isset($_POST['nuevo'])) {
            
            $nuevo = $params["nuevo"];
            $modificar = $params["modificar"];
            $email = $params["Email"];
            
            $camposPermitidos = ['nombre', 'apellido', 'puesto', 'email', 'clave', 'ingreso']; 
            if (!in_array($modificar, $camposPermitidos)) 
            {
                $response = new Response();
                        $response->getBody()->write(json_encode(array("error" => "El nombre de la modificacion no es valida")));
                        return $response->withStatus(400);
            }else{
                if (!in_array($modificar, $camposPermitidos)) {
                    $response = new Response();
                    $response->getBody()->write(json_encode(array("error" => "campos no permitidos")));
                    return $response->withStatus(400);

                } else {
                    
                    if (Usuario::modificarDato($email, $modificar, $nuevo)) {
                        $response = $handler->handle($request);
                        Usuario::ObtenerMostrar($_POST["Email"]);
                    } else {
                        
                        $response = new Response();
                        $response->getBody()->write(json_encode(array("error" => "No se pudo modificar el usuario")));
                        return $response->withStatus(400);
                    }
                }
            }

        } else {
            
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Faltan parámetros")));
            return $response->withStatus(400);
        }

        return $response;
    }



    public function darBajaUserMW(Request $request, RequestHandler $handler)
    {
        $params = $request->getQueryParams();


        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') 
        {
            if(isset($_GET["Email"])) {
                $email = $params["Email"];
                
                if (!Usuario::buscarEmpleado($email)) {
                    $response = new Response();
                    $response->getBody()->write(json_encode(array("error" => "El usuario no existe")));
                    return $response->withStatus(400);
                } else {
                    if (Usuario::borrarUsuario($email)) {
                        $response = $handler->handle($request);
                    } else {
                        $response = new Response();
                        $response->getBody()->write(json_encode(array("error" => "No se pudo dar de baja el usuario")));
                        return $response->withStatus(400);
                    }
                }
            } else {
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Faltan parámetros")));
                return $response->withStatus(400);
            }
        }else
        {
            $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "No se logro la conexion")));
                return $response->withStatus(400);
        }

        return $response;
    }


    public function mostrarUserMW(Request $request, RequestHandler $handler)
    {
        $params = $request->getQueryParams();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($_GET["email"])) 
            {
                $email = $params["Email"];

                if (!Usuario::buscarEmpleado($email)) {
                    $response = new Response();
                    $response->getBody()->write(json_encode(array("error" => "El usuario no existe")));
                    return $response->withStatus(400);
                } else {
                    if (Usuario::ObtenerMostrar($email)) {
                        $response = $handler->handle($request);
                    } else {
                        // Error al crear el usuario
                        $response = new Response();
                        $response->getBody()->write(json_encode(array("error" => "No se pudo mostrar el usuario")));
                        return $response->withStatus(400);
                    }
                }
            }else{
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Faltan parámetros")));
                return $response->withStatus(400);
            }
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "No se logro hacer la conexion")));
            return $response->withStatus(400);
        }

        return $response;
    }



}



