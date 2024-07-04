<?php

include_once "./models/Usuario.php";

function PostIngreso()
{
    // echo "entrando...";
    if(isset($_POST['nombre']) && isset($_POST["apellido"])&& isset($_POST["puesto"])
    && isset($_POST["contraseña"]) && isset($_POST["email"]))
    {
        echo "entro";
        $empleado = new Usuario($_POST['nombre'], $_POST['apellido'], $_POST['puesto'],
        $_POST['contraseña'],$_POST['email']);

        $empleado->crearUsuario();
    }
    else
    {
        echo "error al crear empleado";
    }
}


function GetUsuario()
{
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET["email"])) 
        {
            Usuario::ObtenerMostrar($_GET["email"]);
        } else 
        {
            echo "Error en el ingreso";
        }
    } else {
        echo "Error!!";
    }
}


function PostModificador()
{
    if(isset($_POST['Email']) && isset($_POST['modificar']) 
    && isset($_POST['nuevo']))
    {
        echo $_POST['Email'] . " " . ($_POST['modificar']) . " " . ($_POST['nuevo']) . "<br>";
        Usuario::modificarDato($_POST['Email'], ($_POST['modificar']), ($_POST['nuevo']));
        Usuario::ObtenerMostrar($_POST["Email"]);
    }
}


function DeleteUsuario()
{
    if ($_SERVER['REQUEST_METHOD'] == 'DELETE') 
        {
            if (isset($_GET["Email"])) 
            {
                Usuario::borrarUsuario($_GET["Email"]);
            
            } 
            else 
            {
                echo "Error en el ingreso";
            }
        }
        else
        {
            echo "error!!";
        }
}


