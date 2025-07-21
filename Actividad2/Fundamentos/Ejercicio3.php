<?php
class Estudiante {
    public $nombre;
    public $edad;
    public $matricula;
    public function mostrardatos() {
        echo "El nombre del estudiante es {$this->nombre}, tiene una edad de {$this->edad} años y su matricula es {$this->matricula}.";
    }
}

$Estudiante = new Estudiante;
$Estudiante->nombre = "Juan";
$Estudiante->edad = 20;
$Estudiante->matricula = "profesional";
$Estudiante->mostrardatos();

?>