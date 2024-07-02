<?php


include_once "./models/AltaPedidos.php";

function PostIngresoPedido() {
    if (isset($_POST['idMesa']) && isset($_POST["nombre"]) && isset($_POST["comidas"]) && isset($_FILES["imagen"])) {
        // Obtener los datos del POST ------           $_FILES["imagen"]))
        $idMesa = $_POST['idMesa'];
        $nombre = $_POST['nombre'];
        $comidas = explode(',', $_POST['comidas']); // Suponiendo que las comidas se envían como una cadena separada por comas
        $imagen = $_FILES["imagen"];

        // Crear el pedido
        $pedido = new AltaPedidos();
        $pedido->crearNuevo($idMesa, $nombre, $comidas, $imagen);

        //echo "Pedido guardado!";
    } else {
        echo "Error en parametros!";
    }
}


function GetMostrarPedido()
{
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET["idPedido"])) 
        {
            $pedido = new AltaPedidos();
            $pedido->mostrarPedido($_GET["idPedido"]);
        }else{
            echo "Error en ingreso de datos";
        }
    }
    else{
        echo "Error!";
    }
}


function DeleteBorrarPedido()
{
    if ($_SERVER['REQUEST_METHOD'] == 'DELETE') 
    {
        if (isset($_GET["id"])) 
        {
            $pedido = new AltaPedidos();
            $pedido->eliminarPedido($_GET["idPedido"]);
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



function PostModificarPedido()
{
    //Modificacion nombreCliete, mesa, estado pedido - Entregado

    if(isset($_POST['idPedido']) && isset($_POST['modificar']) 
    && isset($_POST['nuevo']))
    {
        $pedido = new AltaPedidos();
        $pedido->modificarPedido($_POST['idPedido'], $_POST['modificar'], $_POST['nuevo']);
    }
    else 
    {
        echo "Error en el ingreso<br>";
    }
}


//cambiar 'terminado' de Pendientes
function PostCambiarTerminado()
{
    if(isset($_POST['idPedido']) && isset($_POST['comida']))
    {
        AltaPendientes::cambiarTerminado($_POST['idPedido'], $_POST['comida']);

        $pedido = new AltaPedidos();
        $pedido->pedidoTerminado($_POST['idPedido']);

    }else
    {
        echo "error!!";
    }

}



//revisar si todos sus pendientes estan terminados y cambiar a listo para servir
function PostCambiarListo()
{
    if(isset($_POST['idPedido']))
    {
        $pedido = new AltaPedidos();
        $pedido->pedidoTerminado($_POST['idPedido']);
    }
    else 
    {
        echo "Error en el ingreso<br>";
    }
}



//cerrar pedido
//function PostCerrarPedido();






