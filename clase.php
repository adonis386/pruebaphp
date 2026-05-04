<?php
require_once 'conexion.php';

// ─── Interface ────────────────────────────────────────────────────────────────
// Contrato que todos los vehículos vendibles deben cumplir
interface Vendible {
    public function calcularDescuento(float $porcentaje): float;
    public function getEtiqueta(): string;
}

// ─── Motor ────────────────────────────────────────────────────────────────────
// Composición: Coche HAS-A Motor (no hereda, lo contiene)
class Motor {
    private string $tipo;

    public function __construct(string $tipo) {
        $this->tipo = $tipo;
    }

    public function arrancar(): string {
        return "Motor {$this->tipo} rugiendo...";
    }

    public function getTipo(): string {
        return $this->tipo;
    }
}

// ─── Vehiculo (Clase Abstracta) ───────────────────────────────────────────────
// Define la estructura común e implementa la interface Vendible
abstract class Vehiculo implements Vendible {

    // Propiedad estática: cuenta todos los objetos Vehiculo instanciados
    protected static int $totalCreados = 0;

    // Atributos protegidos: accesibles por subclases (herencia)
    protected string $marca;
    protected string $modelo;
    protected float  $precio;

    public function __construct(string $marca, string $modelo, float $precio) {
        $this->marca  = $marca;
        $this->modelo = $modelo;
        $this->precio = $precio;
        self::$totalCreados++;
    }

    // Método estático: accesible sin instanciar la clase
    public static function getTotalCreados(): int {
        return self::$totalCreados;
    }

    // ── Getters (Encapsulamiento) ─────────────────────────────────────────────
    public function getMarca(): string  { return $this->marca; }
    public function getModelo(): string { return $this->modelo; }
    public function getPrecio(): float  { return $this->precio; }

    // ── Implementación concreta de Vendible::calcularDescuento ────────────────
    // Misma lógica para todos los vehículos; las subclases no necesitan sobreescribirla
    public function calcularDescuento(float $porcentaje): float {
        return round($this->precio * (1 - $porcentaje / 100), 2);
    }

    // Método con comportamiento por defecto, sobreescribible (polimorfismo)
    public function mostrarDetalles(): string {
        return "{$this->marca} {$this->modelo} — Precio: $" . number_format($this->precio, 2);
    }

    // ── Métodos abstractos: cada subclase DEBE implementarlos ─────────────────
    abstract public function getEtiqueta(): string;
    abstract public function obtenerHtml(): string;
}

// ─── Coche ────────────────────────────────────────────────────────────────────
class Coche extends Vehiculo {

    private int   $puertas;
    private Motor $motor; // Composición

    public function __construct(
        string $marca,
        string $modelo,
        float  $precio,
        int    $puertas,
        string $tipoMotor = 'Gasolina'
    ) {
        parent::__construct($marca, $modelo, $precio);
        $this->puertas = $puertas;
        $this->motor   = new Motor($tipoMotor);
    }

    public function mostrarDetalles(): string {
        return "Coche: {$this->marca} {$this->modelo} — {$this->puertas} puertas — {$this->motor->arrancar()}";
    }

    public function getEtiqueta(): string {
        return "🚗 Coche · {$this->puertas} puertas · {$this->motor->getTipo()}";
    }

    public function obtenerHtml(): string {
        $precioFmt     = number_format($this->precio, 2);
        $descuentoFmt  = number_format($this->calcularDescuento(10), 2);
        $motorTipo     = $this->motor->getTipo();
        $badgeClass    = match($motorTipo) {
            'Eléctrico' => 'badge-electric',
            'Híbrido'   => 'badge-hybrid',
            'Diésel'    => 'badge-diesel',
            default     => 'badge-gas',
        };

        return "
        <div class='card coche'
             data-tipo='Coche'
             data-precio='{$this->precio}'
             data-nombre='{$this->marca} {$this->modelo}'>
            <div class='card-accent coche-accent'></div>
            <div class='card-icon'>🚗</div>
            <div class='card-body'>
                <h3>{$this->marca} <span>{$this->modelo}</span></h3>
                <span class='badge {$badgeClass}'>{$motorTipo}</span>
                <div class='price'>\${$precioFmt}</div>
                <div class='price-discount'>Con 10% dto: <strong>\${$descuentoFmt}</strong></div>
                <ul class='specs'>
                    <li>🚪 {$this->puertas} puertas</li>
                    <li>⚡ {$this->motor->arrancar()}</li>
                </ul>
                <div class='card-label'>{$this->getEtiqueta()}</div>
            </div>
        </div>";
    }
}

// ─── Motocicleta ──────────────────────────────────────────────────────────────
class Motocicleta extends Vehiculo {

    private int $cilindrada;

    public function __construct(string $marca, string $modelo, float $precio, int $cilindrada) {
        parent::__construct($marca, $modelo, $precio);
        $this->cilindrada = $cilindrada;
    }

    public function mostrarDetalles(): string {
        return "Moto: {$this->marca} {$this->modelo} — {$this->cilindrada}cc";
    }

    public function getEtiqueta(): string {
        return "🏍️ Moto · {$this->cilindrada}cc";
    }

    public function obtenerHtml(): string {
        $precioFmt    = number_format($this->precio, 2);
        $descuentoFmt = number_format($this->calcularDescuento(10), 2);

        return "
        <div class='card moto'
             data-tipo='Motocicleta'
             data-precio='{$this->precio}'
             data-nombre='{$this->marca} {$this->modelo}'>
            <div class='card-accent moto-accent'></div>
            <div class='card-icon'>🏍️</div>
            <div class='card-body'>
                <h3>{$this->marca} <span>{$this->modelo}</span></h3>
                <span class='badge badge-gas'>Gasolina</span>
                <div class='price'>\${$precioFmt}</div>
                <div class='price-discount'>Con 10% dto: <strong>\${$descuentoFmt}</strong></div>
                <ul class='specs'>
                    <li>🔧 {$this->cilindrada}cc de cilindrada</li>
                    <li>🛣️ Lista para la ruta</li>
                </ul>
                <div class='card-label'>{$this->getEtiqueta()}</div>
            </div>
        </div>";
    }
}

// ─── Concesionario ────────────────────────────────────────────────────────────
class Concesionario {

    private array $inventario = [];

    // Carga todos los vehículos desde la BD; soporta Coche, Moto y Camion
    public function cargarDesdeBaseDeDatos(): void {
        $conexion   = (new Conexion())->getConexion();
        $stmt       = $conexion->query("SELECT * FROM vehiculos ORDER BY tipo, precio");
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($resultados as $fila) {
            // match() — PHP 8: expresión de coincidencia estricta
            $vehiculo = match($fila['tipo']) {
                'Coche'  => new Coche(
                    $fila['marca'], $fila['modelo'],
                    (float) $fila['precio'], (int) $fila['atributo_especial'],
                    $fila['tipo_motor']
                ),
                'Camion' => new Camion(
                    $fila['marca'], $fila['modelo'],
                    (float) $fila['precio'], (float) $fila['atributo_especial']
                ),
                default  => new Motocicleta(
                    $fila['marca'], $fila['modelo'],
                    (float) $fila['precio'], (int) $fila['atributo_especial']
                ),
            };

            $this->inventario[] = $vehiculo;
        }
    }

    public function agregarVehiculo(Vehiculo $vehiculo): void {
        $this->inventario[] = $vehiculo;
    }

    public function getInventario(): array {
        return $this->inventario;
    }

    // Filtra el inventario por nombre de clase (ej. 'Coche', 'Motocicleta', 'Camion')
    public function filtrarPorTipo(string $tipo): array {
        return array_values(array_filter(
            $this->inventario,
            fn(Vehiculo $v) => get_class($v) === $tipo
        ));
    }

    // Devuelve el inventario ordenado por precio (sin modificar el original)
    public function ordenarPorPrecio(bool $ascendente = true): array {
        $copia = $this->inventario;
        usort($copia, fn(Vehiculo $a, Vehiculo $b) =>
            $ascendente
                ? $a->getPrecio() <=> $b->getPrecio()
                : $b->getPrecio() <=> $a->getPrecio()
        );
        return $copia;
    }

    // Calcula métricas del inventario completo
    public function getEstadisticas(): array {
        if (empty($this->inventario)) {
            return ['total' => 0, 'coches' => 0, 'motos' => 0, 'camiones' => 0,
                    'precio_min' => 0, 'precio_max' => 0, 'precio_avg' => 0];
        }

        $precios = array_map(fn(Vehiculo $v) => $v->getPrecio(), $this->inventario);

        return [
            'total'      => count($this->inventario),
            'coches'     => count($this->filtrarPorTipo('Coche')),
            'motos'      => count($this->filtrarPorTipo('Motocicleta')),
            'camiones'   => count($this->filtrarPorTipo('Camion')),
            'precio_min' => min($precios),
            'precio_max' => max($precios),
            'precio_avg' => array_sum($precios) / count($precios),
        ];
    }
}
?>
