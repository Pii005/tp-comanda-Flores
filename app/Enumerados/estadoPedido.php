<?php


abstract class EstadoPedido
{
    const esperando = 'Para preparar';
    const preparando = 'En preparacion';
    const listo = 'Listo para servir';
    const servido = 'Entregado';
    const pagado = 'Pedido pagado y cerrado';

}