<?php

include_once './csv/csv.php';
include_once './models/AltaComida.php';

class ControlerCsv
{
    function cargarCsv($request, $response, $args)
    {
        $csvContent = (string)$request->getBody();

        // Procesar el archivo CSV
        $lines = explode(PHP_EOL, $csvContent);
        $header = str_getcsv(array_shift($lines));

        $data = [];
        foreach ($lines as $line) {
            if (!empty(trim($line))) {
                $data[] = array_combine($header, str_getcsv($line));
            }
        }

        $jsonData = json_encode($data, JSON_PRETTY_PRINT);
        $jsonFile = __DIR__ . '/archivos/comidas.json';

        // Verificar y crear el directorio uploads si no existe
        $uploadsDir = dirname($jsonFile);
        if (!file_exists($uploadsDir)) {
            mkdir($uploadsDir, 0777, true); // Crea el directorio recursivamente
        }

        // Intentar guardar el archivo JSON
        if (file_put_contents($jsonFile, $jsonData) !== false) {
            return true;
        } else {
            return false; // Internal Server Error
        }
    }


    function cargarDatos($request, $response, $args)
    {
        $archivo = self::cargarCsv($request, $response, $args);
        $jsonFile = __DIR__ . '/archivos/comidas.json';
        if($archivo)
        {
            $jsonContent = file_get_contents($jsonFile);

            $data = json_decode($jsonContent, true);

            if($data != null)
            { $alta = new AltaComida();
                foreach($data as $comida)
                {
                    $mensaje = $alta->crearYGuardar(
                        $comida['nombre'], 
                        $comida['tipoEmpleado'], 
                        $comida['precio'], 
                        $comida['tiempoPreparacion']
                    );

                }
                $payload = json_encode(array("mensaje" => $mensaje));

            }else {
                $payload = json_encode(array("Error" => "al decodificar el archivo JSON"));

            }
        }else {
            $payload = json_encode(array("Error" => "El archivo JSON no existe"));

        }
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    function obtenerCsv()
    {
        $comidas = AltaComida::devolverTodas();

        foreach($comidas as $comida)
        {
            $cadena = $comida['nombre'].",".
                    $comida['tipoEmpleado'].",".
                    $comida['precio'].",".
                    $comida['tiempoPreparacion'];
        }

        

    }

}

