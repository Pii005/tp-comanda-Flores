<?php
require_once './models/Usuario.php';

//app\models\Usuario.php
class UsuarioController 
{
  public function ingresarUsuario($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    if(isset($parametros['nombre']) && isset($parametros["apellido"]) 
    && isset($parametros["puesto"]) && isset($parametros["clave"]) 
    && isset($parametros["email"]))
    {
      $nombre = $parametros['nombre'];
      $apellido = $parametros["apellido"];
      $puesto = $parametros["puesto"];
      $contraseña = $parametros["clave"];
      $email = $parametros["email"];
      
      try
      {
        $empleado = new Usuario($parametros['nombre'], $parametros["apellido"], $parametros["puesto"],
        $parametros["clave"], $parametros["email"]);
        
          $msg = $empleado->crearUsuario();
          $payload = json_encode(array("mensaje" => $msg));
      }
      catch(Exception $e)
      {
        $payload = json_encode(array("Error" => "No se pudo crear el usuario"));
      }
    }
    else 
    {
        $payload = json_encode(array("Error" => "Los parametros son incorrectos"));
    }

    $response->getBody()->write($payload);

    return $response->withHeader('Content-Type', 'application/json');
  }

  public function mostrarUnUsuario($request, $response, $args)
  {
    $params = $request->getQueryParams();

      if ($_SERVER['REQUEST_METHOD'] === 'GET') 
      {
        if (isset($params["email"])) 
        {
          $email = $params["email"];
          try
          {
            $msg = Usuario::ObtenerMostrar($email);
            $payload = json_encode(array("mensaje" => $msg));
          }
          catch(Exception $e)
          {
            $payload = json_encode(array("Error" => "No se pudo crear el usuario"));
          }
        }
      }else 
      {
        $payload = json_encode(array("Error" => "Los parametros son incorrectos"));
      }

    $response->getBody()->write($payload);

    return $response->withHeader('Content-Type', 'application/json');
  }

  public function bajaUnUsuario($request, $response, $args)
  {
    $params = $request->getQueryParams();

      if ($_SERVER['REQUEST_METHOD'] === 'GET') 
      {
        if (isset($params["email"])) 
        {
          $email = $params["email"];
          try
          {
            $msg = Usuario::borrarUsuario($email);
            $payload = json_encode(array("mensaje" => $msg));
          }
          catch(Exception $e)
          {
            $payload = json_encode(array("Error" => "No se pudo eliminar el usuario"));
          }
        }
      }else 
      {
        $payload = json_encode(array("Error" => "Los parametros son incorrectos"));
      }

    $response->getBody()->write($payload);

    return $response->withHeader('Content-Type', 'application/json');
  }

  public function Altadenuevo($request, $response, $args)
  {
    $params = $request->getQueryParams();

      if ($_SERVER['REQUEST_METHOD'] === 'GET') 
      {
        if (isset($params["email"])) 
        {
          $email = $params["email"];
          try
          {
            $msg = Usuario::restaurarUser($email);
            $payload = json_encode(array("mensaje" => $msg));
          }
          catch(Exception $e)
          {
            $payload = json_encode(array("Error" => "No se pudo dar de alta al usuario"));
          }
        }
      }else 
      {
        $payload = json_encode(array("Error" => "Los parametros son incorrectos"));
      }

    $response->getBody()->write($payload);

    return $response->withHeader('Content-Type', 'application/json');

  }

  public function modificarUsuario($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    if(isset($parametros['email']) && isset($parametros["modificar"]) 
    && isset($parametros["nuevo"]))
    {
      $modificar = $parametros['modificar'];
      $nuevo = $parametros["nuevo"];
      $email = $parametros["email"];
      
      try
      {
        $verificado = Usuario::modificarDato($email, $modificar, $nuevo);
        if($verificado)
        {
          $msg = Usuario::ObtenerMostrar($email);
  
          $payload = json_encode(array("usuario modificado" => $msg));
        }
        else
        {
          $payload = json_encode(array("Error" => "EL usuario este dado de baja"));

        }
      }
      catch(Exception $e)
      {
        $payload = json_encode(array("Error" => "No se pudo crear el usuario"));
      }
    }
    else 
    {
        $payload = json_encode(array("Error" => "Los parametros son incorrectos"));
    }

    $response->getBody()->write($payload);

    return $response->withHeader('Content-Type', 'application/json');
  }



}
