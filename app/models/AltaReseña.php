<?php

include_once './models/AltaPedidos.php';
include_once './Enumerados/estadoPedido.php';
include_once './models/reseña.php';


class AltaReseña
{

    static function ingresarReseña($idPedido, $mozo, $mesa, $restaurante, $cocinero)
    {
        $guardado = self::guardarReseña($idPedido, $mozo, $mesa, $restaurante, $cocinero);
        if($guardado == true)
        {
            return "Resenia guardada con exito";
        }
        return "Error";
        
    }

    static function guardarReseña($idPedido, $mozo, $mesa, $restaurante, $cocinero)
    {
        try
        {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
    
            $promedio = self::obtenerPromedio($mozo, $mesa, $restaurante, $cocinero);
    
            $consulta = $objAccesoDato->prepararConsulta("INSERT INTO resenias
            (id, puntuacionMozo, puntuacionMesa, puntuacionRestaurante, puntuacionCocinero, promedio)
            VALUES (:id, :puntuacionMozo, :puntuacionMesa, :puntuacionRestaurante, :puntuacionCocinero, :promedio)");//Query
    
            $consulta->bindValue(':id', $idPedido, PDO::PARAM_INT);
            $consulta->bindValue(':puntuacionMozo', $mozo, PDO::PARAM_INT);
            $consulta->bindValue(':puntuacionMesa', $mesa, PDO::PARAM_INT);
            $consulta->bindValue(':puntuacionRestaurante', $restaurante, PDO::PARAM_INT);
            $consulta->bindValue(':puntuacionCocinero', $cocinero, PDO::PARAM_INT);
            $consulta->bindValue(':promedio', $promedio, PDO::PARAM_INT);
            $consulta->execute();
            
            // var_dump("Guardado...");
            return true;
        }
        catch(Exception $e)
        {
            throw new Exception("Error guardando resenia: " . $e->getMessage());
        }
    }

    static function obtenerPromedio($mozo, $mesa, $restaurante, $cocinero)
    {
        $suma = $mozo + $mesa + $restaurante + $cocinero;

        $promedio = $suma / 4;

        return $promedio;
    }

    static function buscarResenia($idPedido)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta(
            "SELECT *
            FROM resenias
            WHERE id = :id"
        );
        $consulta->bindValue(':id', $idPedido, PDO::PARAM_STR);
        $consulta->execute();

        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        $resenia = new Reseña(
            $resultado['id'],
            $resultado['puntuacionMesa'],
            $resultado['puntuacionRestaurante'],
            $resultado['puntuacionMozo'],
            $resultado['puntuacionCocinero'],
            $resultado['promedio']
        );

        return $resenia;
    }

    static function buscarTodasResenia()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta(
            "SELECT *
            FROM resenias"
        );
        
        $consulta->execute();

        $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

        $reseniasTotal = [];

        foreach($resultados as $result)
        {
            // Verifica si 'promedio' está definido en el resultado antes de usarlo
            $promedio = isset($result['promedio']) ? $result['promedio'] : null;

            $resenia = new Reseña(
                $result['id'],
                $result['puntuacionMesa'],
                $result['puntuacionRestaurante'],
                $result['puntuacionMozo'],
                $result['puntuacionCocinero'],
                $promedio // Usa la variable $promedio que puede ser null si 'promedio' no está definido
            );
            $reseniasTotal[] = $resenia;
        }

        return $reseniasTotal;
    }


    static function mostrarResenia($idPedido)
    {
        $resenia = self::buscarResenia($idPedido);

        return $resenia->mostrar();
    }

    static function mejoresResenias()
    {
        $resenias = self::buscarTodasResenia();
        $mejores = [];

        foreach($resenias as $resenia)
        {
            if($resenia->getPromedio() >= 7)
            {
                $mejores[] = $resenia->mostrar();
            }
        }

        return $mejores;
    }

}