<?php
// Camion extiende Vehiculo (herencia) e implementa todos sus métodos abstractos,
// incluido getEtiqueta() de la interface Vendible.
class Camion extends Vehiculo {

    private float $capacidadCarga; // kg — atributo privado, encapsulamiento

    public function __construct(
        string $marca,
        string $modelo,
        float  $precio,
        float  $capacidadCarga
    ) {
        parent::__construct($marca, $modelo, $precio);
        $this->capacidadCarga = $capacidadCarga;
    }

    // Getter para acceso controlado al atributo privado
    public function getCapacidadCarga(): float {
        return $this->capacidadCarga;
    }

    // Polimorfismo: sobreescribe mostrarDetalles() con representación propia
    public function mostrarDetalles(): string {
        return "Camión: {$this->marca} {$this->modelo} — Capacidad: " .
               number_format($this->capacidadCarga, 0, ',', '.') . " kg";
    }

    // Implementación del método abstracto getEtiqueta() (requerida por Vendible)
    public function getEtiqueta(): string {
        return "🚚 Camión · " . number_format($this->capacidadCarga, 0, ',', '.') . " kg de carga";
    }

    // Implementación del método abstracto obtenerHtml() (requerida por Vehiculo)
    public function obtenerHtml(): string {
        $precioFmt     = number_format($this->precio, 2);
        $descuentoFmt  = number_format($this->calcularDescuento(10), 2);
        $capacidadFmt  = number_format($this->capacidadCarga, 0, ',', '.');

        return "
        <div class='card camion'
             data-tipo='Camion'
             data-precio='{$this->precio}'
             data-nombre='{$this->marca} {$this->modelo}'>
            <div class='card-accent camion-accent'></div>
            <div class='card-icon'>🚚</div>
            <div class='card-body'>
                <h3>{$this->marca} <span>{$this->modelo}</span></h3>
                <span class='badge badge-diesel'>Diésel</span>
                <div class='price'>\${$precioFmt}</div>
                <div class='price-discount'>Con 10% dto: <strong>\${$descuentoFmt}</strong></div>
                <ul class='specs'>
                    <li>📦 {$capacidadFmt} kg de capacidad</li>
                    <li>🛣️ {$this->mostrarDetalles()}</li>
                </ul>
                <div class='card-label'>{$this->getEtiqueta()}</div>
            </div>
        </div>";
    }
}
?>
