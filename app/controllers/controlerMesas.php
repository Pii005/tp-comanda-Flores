<?php

include_once './models/AltaMesa.php';

class ControlerMesas 
{
    public function IngresarMesa($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        // Verificacion parametros
        if (isset($parametros['id'])) {
            $id = $parametros['id'];

            try 
            {
                $alta = new AltaMesa();
                $mensaje = $alta->crearYGuardar($id);
                $payload = json_encode(array("mensaje" => $mensaje));
            } catch (Exception $e) {
                $payload = json_encode(array("Error" => "No se pudo guardar la mesa"));
            }
        } 
        else 
        {
            // Manejar el caso en el que 'id' no está presente
            $payload = json_encode(array("Error" => "Parametro 'id' no encontrado"));
        }

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }


    public function mostrarUnaMesa($request, $response, $args)
    {
        $params = $request->getQueryParams();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') 
        {
            if (isset($params["id"])) 
            {
                $id = $params['id'];
                try 
                {
                    $alta = new AltaMesa();
                    $mesa = $alta->mostrarMesa($id);
                    // $msg = $mesa->mostrar(); // Ahora devuelve un array
                    $payload = json_encode(array("Mensaje" => $mesa), JSON_PRETTY_PRINT); // JSON_PRETTY_PRINT para una salida más legible

                }
                catch (Exception $e)
                {
                    $payload = json_encode(array("Error" => "No se encontró la mesa"));
                }
            }
            else
            {
                $payload = json_encode(array("Error" => "Parámetro 'id' no encontrado"));
            }
        }
        else
        {
            $payload = json_encode(array("Error" => "Método no permitido"));
        }

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function eliminarUnaMesa($request, $response, $args)
    {
        $params = $request->getQueryParams();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') 
        {
            if (isset($params["id"])) 
            {
                $id = $params['id'];
                try 
                {
                    $alta = new AltaMesa();
                    $msg = $alta->eliminarMesa($id);

                    $payload = json_encode(array("Mensaje" => $msg));
                }
                catch (Exception $e)
                {
                    $payload = json_encode(array("Error" => "No se encontró la mesa"));
                }
            }
            else
            {
                $payload = json_encode(array("Error" => "Parámetro 'id' no encontrado"));
            }
        }
        else
        {
            $payload = json_encode(array("Error" => "Método no permitido"));
        }


        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }


}
