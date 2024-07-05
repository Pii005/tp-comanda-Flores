<?php



include_once "./Enumerados/estadoMesa.php";
include_once "./db/AccesoDatos.php";
include_once 'mesa.php';
date_default_timezone_set('America/Argentina/Buenos_Aires'); 


class AltaMesa
{
    private static $acceso;

    private static function obtenerAcceso()
    {
        if (self::$acceso === null) {
            self::$acceso = AccesoDatos::obtenerInstancia();
        }
    }

    public function crearYGuardar($id)
    {
        if(self::validarMesaNoCerrada($id))
        {
            self::obtenerAcceso();
            $mesa = new mesa($id);
            $consulta = self::$acceso->prepararConsulta("INSERT INTO mesas (id, estado, horaLlegada) 
                VALUES (:id, :estado, :horaLlegada)");
            
            $consulta->bindValue(':id', $mesa->getId(), PDO::PARAM_STR);
            $consulta->bindValue(':estado', $mesa->getEstado(), PDO::PARAM_STR);
            $consulta->bindValue(':horaLlegada', $mesa->getHoraLlegada()->format('Y-m-d H:i:s'), PDO::PARAM_STR);
            $consulta->execute();
            return "Mesa ocupada con exito";
        }else
        {
            return "Mesa ocupada, ingrese otro id";
        }
        //echo "Mesa creada<br>";
    }

    public static function buscarMesa($id)
    {
        self::obtenerAcceso();

        $consulta = self::$acceso->prepararConsulta("SELECT id 
        FROM mesas 
        WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        // Verificar si hay resultados
        $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

        if (count($resultados) >= 1) {
            return true;
        } else {
            return false;
        }
    }


    public function ingresoCliente($idMesa)
    {
        self::obtenerAcceso();

        if($this->buscarMesa($idMesa))
        {
            $llegada =  date("Y-m-d H:i:s");
            $consulta = self::$acceso->prepararConsulta("UPDATE mesas 
            SET estado = :estado, horaLlegada = :horaLlegada 
            WHERE id = :idMesa");
            $consulta->bindValue(':estado', estadoMesa::esperando, PDO::PARAM_STR);
            $consulta->bindValue(':horaLlegada', $llegada, PDO::PARAM_STR);
            $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_INT);
            
            $consulta->execute();
            echo "Modificado<br>";
        }else{
            echo "Mesa invalida";
        }
    }


    public function eliminarMesa($id)
    {
        self::obtenerAcceso();

        if(self::buscarMesa($id))
        {
            
            $consulta = self::$acceso->prepararConsulta("DELETE FROM mesas WHERE id = :id");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->execute();
            return "Eliminado con exito<br>";
            // return $consulta->rowCount();
        }else{
            return "La mesa no existe<br>";
        }
    }

    public static function devolverMesa($id)//AJUSTE -  return $consulta->fetchObject('Mesa');
    {
        self::obtenerAcceso();
        if (self::buscarMesa($id)) {
            $consulta = self::$acceso->prepararConsulta("SELECT * FROM mesas WHERE id = :id");
            $consulta->bindValue(':id', $id, PDO::PARAM_STR);
            $consulta->execute();
            return $consulta->fetch(PDO::FETCH_ASSOC);
        } else {
            // echo "Mesa no encontrada<br>";
            return null;
        }
    }

    public static function validarMesaNoCerrada($idMesa)
    {
        
        $mesa = self::devolverMesa($idMesa);

        if($mesa != null)
        {
            if($mesa['estado'] == estadoMesa::cerrada)
            {
                return true;//Mesa cerrada y habilitada a usar
            }
            else{
                return false; //La mesa esta ocupada
            }
        }
        return true;//mesa no existe y se puede usar
        
    }

    public static function validarMesaOcupada($idMesa)//Para hacer el cambio de estado de mesa
    {
        $mesa = self::devolverMesa($idMesa);

        if($mesa != null)//Mesa existe
        {
            if($mesa['estado'] == estadoMesa::cerrada || $mesa['estado'] == estadoMesa::esperando)//esta siendo ocupada y estan esperando
            {
                return true;//La mesa esta libre
            }

        }
        return false;//mesa ocupada

    }

    public function mostrarMesa($id)
    {
        $Mesa = $this->devolverMesa($id);
        if ($Mesa) {
            $MesaObj = new Mesa($id);
            $MesaObj->setEstado($Mesa['estado']);
            $MesaObj->setHoraLlegada($Mesa['horaLlegada']);
            $MesaObj->setHoraSalida($Mesa['horaSalida']);
            $MesaObj->setSocioCerro($Mesa['socioCerro']);

            return $MesaObj->mostrar();
        } else {
            return "Mesa no encontrada";
        }
    }

    public static function modificarEstado($idMesa, $estado)
    {   
        if(self::buscarMesa($idMesa))
        {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            // $estado = estadoMesa::pedido;
            $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET estado = :estado WHERE id = :id");
            $consulta->bindValue(':id', $idMesa, PDO::PARAM_INT);
            $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
            $consulta->execute();

            return true;
        }
        else{
            return false;
        }
    }

}

