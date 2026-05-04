<?php
// Esta clase se encarga de conectarse a la base de datos.
// La uso en otras clases para no repetir el código de conexión en cada archivo
class Conexion {
    private string $host     = "localhost";
    private string $db       = "concesionario_db";
    private string $user     = "root"; // usuario por defecto de XAMPP
    private string $password = "";    // en XAMPP la contraseña de root viene vacía
    private PDO $conexion;

    public function __construct() {
        try {
            // El DSN es básicamente la "dirección" de la base de datos
            $dsn = "mysql:host={$this->host};dbname={$this->db};charset=utf8mb4";

            // Me aseguro de que el DSN sea válido antes de conectar
            if (!is_string($dsn) || trim($dsn) === '') {
                die("DSN inválido: " . var_export($dsn, true));
            }

            // Creo la conexión PDO con algunas opciones que mejoran el manejo de errores
            $this->conexion = new PDO($dsn, $this->user, $this->password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // que lance excepciones si hay error
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // que devuelva arrays asociativos
                PDO::ATTR_EMULATE_PREPARES   => false,                  // mejor seguridad
            ]);
        } catch (PDOException $e) {
            // Si algo falla, muestro el error para saber qué pasó
            die("Error de conexión a la base de datos: " . $e->getMessage() . " -- DSN: " . var_export(isset($dsn) ? $dsn : null, true));
        }
    }

    // Con este método le paso la conexión a cualquier clase que la necesite
    public function getConexion(): PDO {
        return $this->conexion;
    }
}
?>
