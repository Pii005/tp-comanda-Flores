<?php


include_once './models/AltaMesa.php';

class ControlerMesas extends AltaMesa
{
    public function IngresarMesa($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];

        try{
            $alta = new AltaMesa();
            $alta->crearYGuardar($id);
            $payload = json_encode(array("mensaje" => "mesa ocupada con exito"));

        }catch(Exception $e)
        {
            $payload = json_encode(array("Error" => "No se pudo guardar la mesa"));
        }
        
        $response->getBody()->write($payload);

        return $response
        ->withHeader('Content-Type', 'application/json');
    }

}