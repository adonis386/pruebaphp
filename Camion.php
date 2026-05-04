<?php
// Camion hereda de Vehiculo igual que Coche y Motocicleta.
// Su atributo especial es la capacidad de carga en kilogramos
class Camion extends Vehiculo {

    private float $capacidadCarga; // lo pongo private para que nadie lo cambie desde fuera

    public function __construct(
        string $marca,
        string $modelo,
        float  $precio,
        float  $capacidadCarga
    ) {
        parent::__construct($marca, $modelo, $precio); // inicializo los datos del padre
        $this->capacidadCarga = $capacidadCarga;
    }

    // Como el atributo es privado, necesito un getter para poder leerlo desde fuera
    public function getCapacidadCarga(): float {
        return $this->capacidadCarga;
    }

    // Sobreescribo mostrarDetalles() para que muestre la capacidad en lugar de puertas o cilindrada
    public function mostrarDetalles(): string {
        return "Camión: {$this->marca} {$this->modelo} — Capacidad: " .
               number_format($this->capacidadCarga, 0, ',', '.') . " kg";
    }

    // Este método lo exige la interface Vendible a través de Vehiculo,
    // devuelve una etiqueta con el tipo y la capacidad del camión
    public function getEtiqueta(): string {
        return "🚚 Camión · " . number_format($this->capacidadCarga, 0, ',', '.') . " kg de carga";
    }

    // Genero el HTML de la tarjeta para mostrarlo en la página principal
    public function obtenerHtml(): string {
        $precioFmt     = number_format($this->precio, 2);
        $descuentoFmt  = number_format($this->calcularDescuento(10), 2); // calculo el 10% de descuento
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
