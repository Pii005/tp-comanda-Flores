<?php

include_once "./models/Comidas.php";
include_once "./db/AccesoDatos.php"; // Asegúrate de incluir el archivo de AccesoDatos

class AltaComida 
{
    private static $acceso;

    private static function obtenerAcceso()
    {
        if (self::$acceso === null) {
            self::$acceso = AccesoDatos::obtenerInstancia();
        }
    }

    public function crearYGuardar($nombre, $tipoEmpleado, $precio, $tiempoPreparacion)
    {
        
        if (!self::buscarComida($nombre)) {
            echo "Es nuevo<br>";
            $this->crearNuevo($nombre, $tipoEmpleado, $precio, $tiempoPreparacion);
        } else {
            echo "Se encontró uno igual<br>";
            echo "Error en el proceso<br>";
        }
    }

    public function crearNuevo($nombre, $tipoEmpleado, $precio, $tiempoPreparacion)
    {
        self::obtenerAcceso();
        $comida = new Comidas($nombre, $tipoEmpleado, $precio, $tiempoPreparacion);
        if (Comidas::ValidacionesCompleta($comida)) {
            $consulta = self::$acceso->prepararConsulta("INSERT INTO comidas (nombre, tipoEmpleado, precio, tiempoPreparacion) 
            VALUES (:nombre, :tipoEmpleado, :precio, :tiempoPreparacion)");

            $consulta->bindValue(':nombre', $comida->getNombre(), PDO::PARAM_STR);
            $consulta->bindValue(':tipoEmpleado', $comida->getTipoEmpleado(), PDO::PARAM_STR);
            $consulta->bindValue(':precio', $comida->getPrecio(), PDO::PARAM_STR);
            $consulta->bindValue(':tiempoPreparacion', $comida->getTiempoPreparacion(), PDO::PARAM_INT);
            $consulta->execute();
            echo "Guardado<br>";
        } else {
            echo "Error en validaciones <br>";
        }
    }

    public static function buscarComida($nombre)
    {
        self::obtenerAcceso();
        $consulta = self::$acceso->prepararConsulta("SELECT nombre FROM comidas WHERE nombre = :nombre");
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->execute();
        $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
        return count($resultados) > 0;
    }

    public static function devolverComida($nombre)
    {
        self::obtenerAcceso();
        if (self::buscarComida($nombre)) {
            //echo "Comida encontrada<br>";
            $consulta = self::$acceso->prepararConsulta("SELECT * FROM comidas WHERE nombre = :nombre");
            $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
            $consulta->execute();
            $comida = $consulta->fetch(PDO::FETCH_ASSOC);
            return $comida;
        } else {
            echo "Comida no encontrada<br>";
            return null;
        }
    }

    public static function devolverPuesto($nombreComida)
    {
        $Comida = self::devolverComida($nombreComida);
        if($Comida != null)
        {
            return $Comida['tipoEmpleado'];
        }
        return null;
    }

    public static function sumarTiempos($comidasArray)
    {
        $sumaSegundos = 0;

        foreach ($comidasArray as $nombreComida) {
            $comida = self::devolverComida($nombreComida);
            if ($comida != null) {
                $tiempoPreparacion = $comida['tiempoPreparacion']; // Tiempo en formato HH:MM:SS

                // Convertir tiempo de preparación a segundos
                list($horas, $minutos, $segundos) = explode(':', $tiempoPreparacion);
                $totalSegundos = $horas * 3600 + $minutos * 60 + $segundos;
                
                // Sumar los segundos totales
                $sumaSegundos += $totalSegundos;
            }
            else
            {
                echo "Error en validaciones!!<br>";
            }
        }

        // Convertir la suma total de segundos a formato HH:MM:SS
        $horas = floor($sumaSegundos / 3600);
        $minutos = floor(($sumaSegundos % 3600) / 60);
        $segundos = $sumaSegundos % 60;

        return sprintf("%02d:%02d:%02d", $horas, $minutos, $segundos);
    }

    public function modificar($nombre, $nuevoNombre, $nuevoTipoEmpleado, $nuevoPrecio, $nuevoTiempoPreparacion)
    {
        self::obtenerAcceso();
        if (self::buscarComida($nombre)) {
            $consulta = self::$acceso->prepararConsulta("UPDATE comidas 
            SET nombre = :nuevoNombre, tipoEmpleado = :nuevoTipoEmpleado, precio = :nuevoPrecio, tiempoPreparacion = :nuevoTiempoPreparacion 
            WHERE nombre = :nombre");
            $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
            $consulta->bindValue(':nuevoNombre', $nuevoNombre, PDO::PARAM_STR);
            $consulta->bindValue(':nuevoTipoEmpleado', $nuevoTipoEmpleado, PDO::PARAM_STR);
            $consulta->bindValue(':nuevoPrecio', $nuevoPrecio, PDO::PARAM_STR);
            $consulta->bindValue(':nuevoTiempoPreparacion', $nuevoTiempoPreparacion, PDO::PARAM_INT);
            $consulta->execute();
            echo "Modificado<br>";
        } else {
            echo "No existe<br>";
        }
    }

    public function eliminar($nombre)
    {
        self::obtenerAcceso();
        if (self::buscarComida($nombre)) {
            echo "El elemento existe<br>";
            $consulta = self::$acceso->prepararConsulta("DELETE FROM comidas WHERE nombre = :nombre");
            $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
            $consulta->execute();
            echo "Eliminado con éxito<br>";
            return $consulta->rowCount();
        } else {
            echo "El elemento no existe<br>";
        }
    }

    public static function mostrarComida($nombre)
    {
        $comida = self::devolverComida($nombre);
        if ($comida) {
            $comidaObj = new Comidas(
                $comida['nombre'],
                $comida['tipoEmpleado'],
                $comida['precio'],
                $comida['tiempoPreparacion'],
                $comida['id']
            );
            echo $comidaObj->mostrar();
        } else {
            echo "Comida no encontrada";
        }
    }
    
}
