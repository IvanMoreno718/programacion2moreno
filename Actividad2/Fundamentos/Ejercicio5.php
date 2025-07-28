<?php
class Persona {
    public $nombre;
    public $edad;
    public $adulto;

    public function esAdulto() {
        if ($this->edad >= 18){
        $this->adulto = true;
        echo "El nombre de esta pesona es {$this->nombre}, y tiene {$this->edad} años, es mayor de edad";}
        else {$this->adulto = false;
        echo "El nombre de esta pesona es {$this->nombre}, y tiene {$this->edad} años, no es mayor de edad";}
    }

}


$persona = new Persona;
$persona->nombre = "Uriel";
$persona->edad = 18;
$persona->esAdulto();

?>