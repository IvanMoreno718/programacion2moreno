<?php
class Libro {
    public $titulo;
    public $autor;
    
    public function mostrardatos() {
        echo "El titulo del libro es {$this->titulo} y su autor es {$this->autor}";
    }
}

$Libro = new Libro();
$Libro->titulo = "Libro de Ivan";
$Libro->autor = "Ivan";
$Libro->mostrardatos();
?>