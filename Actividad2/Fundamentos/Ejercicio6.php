<?php
class Cuenta {
    public $saldo;
    public $monto;
    public function ingresar() {
        $resultado = $this->saldo + $this->monto;
        echo "Su saldo es de {$resultado}";
    }
    public function retirar() {
        $resultado = $this->saldo - $this->monto;
        echo "Su saldo es de {$resultado}";
    }
}

$cuenta = new Cuenta;
$cuenta->saldo=5000;
$cuenta->monto=1500;
$cuenta->ingresar();
//$cuenta->retirar();

?>