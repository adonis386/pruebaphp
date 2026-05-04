<?php
require_once 'conexion.php';

// Una interface es como un contrato: las clases que la implementen
// están obligadas a tener estos dos métodos sí o sí
interface Vendible {
    public function calcularDescuento(float $porcentaje): float;
    public function getEtiqueta(): string;
}

// Clase Motor — la uso dentro de Coche en vez de heredar,
// porque un coche "tiene" un motor, no "es" un motor
class Motor {
    private string $tipo;

    public function __construct(string $tipo) {
        $this->tipo = $tipo;
    }

    public function arrancar(): string {
        return "Motor {$this->tipo} rugiendo...";
    }

    // Necesito este getter para poder leer el tipo desde fuera de la clase
    public function getTipo(): string {
        return $this->tipo;
    }
}

// Clase abstracta: no se puede crear un objeto "Vehiculo" directamente,
// solo sirve de base para Coche, Motocicleta y Camion.
// Además implementa la interface Vendible para que todas las subclases la cumplan
abstract class Vehiculo implements Vendible {

    // Variable estática: es compartida por toda la clase, no por cada objeto.
    // La uso para llevar la cuenta de cuántos vehículos se han creado en total
    protected static int $totalCreados = 0;

    // Con protected las subclases también pueden acceder a estos atributos
    protected string $marca;
    protected string $modelo;
    protected float  $precio;

    public function __construct(string $marca, string $modelo, float $precio) {
        $this->marca  = $marca;
        $this->modelo = $modelo;
        $this->precio = $precio;
        self::$totalCreados++; // cada vez que se crea un vehículo, sumamos 1
    }

    // Con static puedo llamar a este método sin crear ningún objeto:
    // Vehiculo::getTotalCreados()
    public static function getTotalCreados(): int {
        return self::$totalCreados;
    }

    // Getters: sirven para leer los atributos desde fuera sin poder modificarlos
    public function getMarca(): string  { return $this->marca; }
    public function getModelo(): string { return $this->modelo; }
    public function getPrecio(): float  { return $this->precio; }

    // El cálculo del descuento es igual para todos los vehículos,
    // así que lo pongo aquí y no tengo que repetirlo en cada subclase
    public function calcularDescuento(float $porcentaje): float {
        return round($this->precio * (1 - $porcentaje / 100), 2);
    }

    // Este método tiene una versión genérica, pero cada subclase
    // puede sobreescribirlo para mostrar su propia información (polimorfismo)
    public function mostrarDetalles(): string {
        return "{$this->marca} {$this->modelo} — Precio: $" . number_format($this->precio, 2);
    }

    // Estos dos métodos son abstractos: no tienen cuerpo aquí,
    // cada subclase tiene que definirlos a su manera o PHP dará error
    abstract public function getEtiqueta(): string;
    abstract public function obtenerHtml(): string;
}

// Coche hereda de Vehiculo y además tiene su propio atributo (puertas)
// y un objeto Motor dentro (eso se llama composición)
class Coche extends Vehiculo {

    private int   $puertas;
    private Motor $motor; // aquí guardo el objeto Motor que le pertenece a este coche

    public function __construct(
        string $marca,
        string $modelo,
        float  $precio,
        int    $puertas,
        string $tipoMotor = 'Gasolina' // si no me pasan el motor, pongo Gasolina por defecto
    ) {
        parent::__construct($marca, $modelo, $precio); // llamo al constructor del padre
        $this->puertas = $puertas;
        $this->motor   = new Motor($tipoMotor); // creo el objeto Motor con el tipo que me pasan
    }

    // Sobreescribo mostrarDetalles() para que muestre info del coche específicamente
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

        // Según el tipo de motor le asigno un color distinto al badge
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

// Motocicleta también hereda de Vehiculo, pero su atributo especial es la cilindrada
class Motocicleta extends Vehiculo {

    private int $cilindrada;

    public function __construct(string $marca, string $modelo, float $precio, int $cilindrada) {
        parent::__construct($marca, $modelo, $precio);
        $this->cilindrada = $cilindrada;
    }

    // Sobreescribo mostrarDetalles() igual que en Coche — esto es el polimorfismo:
    // el mismo método se comporta distinto según el objeto que lo llame
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

// Esta clase maneja el inventario completo del concesionario
class Concesionario {

    // Array donde voy guardando todos los vehículos que se cargan
    private array $inventario = [];

    // Aquí conecto a la base de datos y creo los objetos según el tipo
    // que viene guardado en la columna "tipo" de cada fila
    public function cargarDesdeBaseDeDatos(): void {
        $conexion   = (new Conexion())->getConexion();
        $stmt       = $conexion->query("SELECT * FROM vehiculos ORDER BY tipo, precio");
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($resultados as $fila) {
            // match() funciona como un switch pero más limpio:
            // dependiendo del tipo creo el objeto correspondiente
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
                // si no es ni Coche ni Camion, asumo que es Motocicleta
                default  => new Motocicleta(
                    $fila['marca'], $fila['modelo'],
                    (float) $fila['precio'], (int) $fila['atributo_especial']
                ),
            };

            $this->inventario[] = $vehiculo;
        }
    }

    // Permite añadir un vehículo que no viene de la BD (por ejemplo, el camión manual)
    public function agregarVehiculo(Vehiculo $vehiculo): void {
        $this->inventario[] = $vehiculo;
    }

    public function getInventario(): array {
        return $this->inventario;
    }

    // Devuelve solo los vehículos del tipo que se pide, por ejemplo 'Coche'
    public function filtrarPorTipo(string $tipo): array {
        return array_values(array_filter(
            $this->inventario,
            fn(Vehiculo $v) => get_class($v) === $tipo
        ));
    }

    // Ordena por precio — trabajo sobre una copia para no alterar el array original
    public function ordenarPorPrecio(bool $ascendente = true): array {
        $copia = $this->inventario;
        usort($copia, fn(Vehiculo $a, Vehiculo $b) =>
            $ascendente
                ? $a->getPrecio() <=> $b->getPrecio()
                : $b->getPrecio() <=> $a->getPrecio()
        );
        return $copia;
    }

    // Junto en un array varios datos útiles del inventario para mostrarlos arriba de la página
    public function getEstadisticas(): array {
        if (empty($this->inventario)) {
            return ['total' => 0, 'coches' => 0, 'motos' => 0, 'camiones' => 0,
                    'precio_min' => 0, 'precio_max' => 0, 'precio_avg' => 0];
        }

        // Saco solo los precios en un array para poder calcular min, max y promedio
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
