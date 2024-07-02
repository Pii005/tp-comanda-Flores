<?php



abstract class estadoMesa
{
    const esperando = 'Espera a ser atendido';
    const pedido = 'Esperando pedido';
    const comiendo = 'Cliente comiendo';
    const pagando = 'Cliente pagando';
    const cerrada = 'El cliente ya pago';
}