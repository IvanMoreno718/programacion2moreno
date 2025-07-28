<?php
class Producto {
    public $nombre;
    public $precio;
    public $stock;
    public function calcularvalor(){
        $valortotal = $this->precio * $this->stock;
        echo "Hay un total de {$this->stock} {$this->nombre} con un precio individual de {$this->precio}, su valor total es {$valortotal}";
    }
}
$producto = new producto;
$producto->nombre = "alfajores";
$producto->precio = 1300;
$producto->stock = 100;
$producto->calcularvalor();


?>