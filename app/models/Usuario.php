<?php

class Usuario
{
    public $id;
    public $nombre;
    public $apellido;
    public $puesto;
    public $alta;
    public $baja;
    public $contraseña;
    public $email;

    public function __construct($nombre, $apellido, $puesto, $Contraseña, $email)
    {
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->puesto = $puesto;
        $this->alta = $this->obtenerTiempo();
        $this->contraseña = $Contraseña;
        $this->email = $email;
    }

    public function obtenerTiempo()
    {
        return new DateTime();
    }

    public function Mostrar()
    {
        $data = array(
            "ID" => $this->id,
            "nombre" => $this->nombre,
            "puesto" => $this->puesto,
            "Ingreso" => $this->alta
        );
        
        if($this-> baja != "0000-00-00")
        {
            $data["Baja"] =  $this->baja;
        }
        return $data;
    }

    public static function ObtenerMostrar($email)
    {
        $empleado = self::obtenerUsuario($email);
        if($empleado)
        {
            return $empleado->Mostrar();
        }
        return false;
    }

    public static function buscarEmpleado($email)
    {
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT Email
            FROM empleados
            WHERE Email = :Email");
        $consulta->bindValue(':Email', $email, PDO::PARAM_STR);
        $consulta->execute();

        $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if (count($resultados) > 0) {
            return true;
        }  
        
        return false;
    }

    public static function buscarPuesto($puesto)
{
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, apellido, puesto, ingreso, baja, clave, Email
        FROM empleados WHERE puesto = :puesto");
    $consulta->bindValue(':puesto', $puesto, PDO::PARAM_STR);
    $consulta->execute();

    // Fetch all results as associative arrays
    $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

    // Create an array to hold Usuario objects
    $usuarios = [];

    // Loop through each result and create a Usuario object
    foreach ($resultados as $resultado) {
        $usuario = new Usuario($resultado['nombre'], $resultado['apellido'], $resultado['puesto'], $resultado['clave'], $resultado['Email']);
        $usuario->setId($resultado['id']);
        $usuario->setAlta(new DateTime($resultado['ingreso']));

        // Optionally set the 'baja' field if needed
        if ($resultado['baja'] !== null) {
            $usuario->baja = new DateTime($resultado['baja']);
        }

        $usuarios[] = $usuario;
    }

    return $usuarios;
}

    public function crearUsuario()
    {
        if(!Usuario::buscarEmpleado($this->email))
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO empleados (nombre, apellido, puesto, ingreso, clave, email)
            VALUES (:nombre, :apellido, :puesto, :ingreso, :clave, :Email)");

            $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
            $consulta->bindValue(':puesto', $this->puesto, PDO::PARAM_STR);
            $consulta->bindValue(':ingreso', $this->alta->format('Y-m-d H:i:s'), PDO::PARAM_STR);

            $claveHash = password_hash($this->contraseña, PASSWORD_DEFAULT);
            $consulta->bindValue(':clave', $claveHash);

            $consulta->bindValue(':Email', $this->email, PDO::PARAM_STR);
            $consulta->execute();
            // echo "creado con exito";
            return "Usuario creado";
        }
        else
        {
            return "El usuario ya existe";
        }
        //return $objAccesoDatos->obtenerUltimoId();
    
        
    }



    public static function obtenerUsuario($email)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT *
        FROM empleados WHERE Email = :Email");
        $consulta->bindValue(':Email', $email, PDO::PARAM_STR);
        $consulta->execute();
        
        $user = $consulta->fetch(PDO::FETCH_ASSOC);

        if($user)
        {
            $userNew = new Usuario(
                $user['nombre'],
                $user['apellido'],
                $user['puesto'],
                $user['clave'],
                $user['Email']
            );

            $userNew->setId($user['id']);
            $userNew->setAlta($user['ingreso']);
            $userNew->setBaja($user['baja']);
            return $userNew;
        }
        return null;
    }

    public static function verificarBaja($email)
    {   
        $user = self::obtenerUsuario($email);

        if($user)
        {
            if($user->getBaja() == "0000-00-00")
            {   
                return true;
            }
        }
        return false;
    }

    public static function modificarDato($Email, $modificador, $nuevo) 
    {
        //verificar que no este de BAJA!!!
        if(self::verificarBaja($Email))
        {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
        
            if ($modificador === 'clave') 
            {
                $nuevo = password_hash($nuevo, PASSWORD_DEFAULT);
            }

            if ($modificador == "puesto" && !in_array($nuevo, [
                TiposEmpleados::bartender,
                TiposEmpleados::cervecero,
                TiposEmpleados::cocinero,
                TiposEmpleados::mozo,
                TiposEmpleados::socio,
                TiposEmpleados::administrador
            ])) {
                return false;
            }

            $consulta = $objAccesoDato->prepararConsulta("UPDATE empleados SET $modificador = :nuevo WHERE Email = :Email");
            $consulta->bindValue(':nuevo', $nuevo, PDO::PARAM_STR);
            $consulta->bindValue(':Email', $Email, PDO::PARAM_INT);
            $consulta->execute();

            return true;

        }
        return false;
    }
    



    public static function borrarUsuario($email)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE empleados SET baja = :baja WHERE Email = :Email");
        $fecha = new DateTime(date("d-m-Y"));

        $consulta->bindValue(':Email', $email, PDO::PARAM_INT);
        $consulta->bindValue(':baja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
        return "Usuario dado de baja con exito";
    }

    public static function restaurarUser($email)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE empleados SET ingreso = :ingreso ,baja = :baja WHERE Email = :Email");
        $fecha = new DateTime(date("d-m-Y"));
        $baja = "0000-00-00";

        $consulta->bindValue(':Email', $email, PDO::PARAM_INT);
        $consulta->bindValue(':ingreso', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->bindValue(':baja', $baja);
        $consulta->execute();
        return "usuario dado de alta denuevo";
    }


    
    public function getNombre()
    {
        return $this->nombre;
    }

    public function getid()
    {
        return $this->id;
    }

    public function getApellido()
    {
        return $this->apellido;
    }

    public function getPuesto()
    {
        return $this->puesto;
    }

    public function getIngreso()
    {
        return $this->alta;
    }

    public function getAlta()
    {
        return $this->alta;
    }

    public function getBaja()
    {
        return $this->baja;
    }

    public function getContraseña()
    {
        return $this->contraseña;
    }

    public function setAlta($a)
    {
        $this->alta = $a;
    }
    public function setbaja($a)
    {
        $this->baja = $a;
    }

    public function setId($a)
    {
        $this->id = $a;
    }
}