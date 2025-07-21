<?php
class Rectangulo {
    public $ancho;
    public $largo;

    public function suma() {
    $area = $this->ancho * $this->largo;
        echo "El area del rectangulo es $area.";
    }
}
$Rectangulo = new Rectangulo;
$Rectangulo->largo=5;
$Rectangulo->ancho=7;
$Rectangulo->suma();




?>