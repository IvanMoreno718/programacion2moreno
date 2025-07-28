<?php
class Circulo {
    public $radio;
    public function calcularperimetro() {
        $perimetro = 2 * pi() * $this->radio;
        echo "El perimetro del circulo es {$perimetro}";

    }
}

$circulo = new Circulo;
$circulo->radio = 20;
$circulo->calcularperimetro();

?>