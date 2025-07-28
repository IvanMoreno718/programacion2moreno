<?php
class Trabajador {
    public $nombre;
    public $cargo;
    public $salario;
    public function informacion() {
        echo "El nombre del trabajador es {$this->nombre}, tiene el cargo de {$this->cargo} y su salario es de {$this->salario}.";
    }
}

$trabajador = new Trabajador;
$trabajador->nombre = "Juan";
$trabajador->cargo = "gerente";
$trabajador->salario = 3000000;
$trabajador->informacion();


?>