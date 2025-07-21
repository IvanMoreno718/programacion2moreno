<?php
class Coche {
    public $marca;
    public $modelo;
    public $color;
    public function detalles() {
        echo "La marca del coche es {$this->marca}, el modelo {$this->modelo} y de color {$this->color}.";
    }
}

$coche = new Coche;
$coche->marca = "Chevrolet";
$coche->modelo = "Camaro";
$coche->color = "Amarillo";
$coche->detalles();
?>