<?php
class Triangulo {
    public $base;
    public $altura;
    public function area() {
        $resultado = $this->base * $this->altura / 2;
        echo "el area del rectangulo es {$resultado}";
    }
}

$triangulo = new triangulo;
$triangulo->base = 10;
$triangulo->altura = 15;
$triangulo->area();

?>