<?php
class Conexion {
    private string $host = "localhost";
    private string $db = "concesionario_db";
    private string $user = "root"; // Usuario por defecto de XAMPP
    private string $password = ""; // Contraseña por defecto de XAMPP (vacía)
    private PDO $conexion;

    public function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db};charset=utf8mb4";
            // Validación mínima del DSN
            if (!is_string($dsn) || trim($dsn) === '') {
                die("DSN inválido: " . var_export($dsn, true));
            }
            // Instanciamos el objeto PDO nativo de PHP con opciones recomendadas
            $this->conexion = new PDO($dsn, $this->user, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die("Error de conexión a la base de datos: " . $e->getMessage() . " -- DSN: " . var_export(isset($dsn) ? $dsn : null, true));
        }
    }

    // Método para exponer la conexión a otras clases
    public function getConexion(): PDO {
        return $this->conexion;
    }
}



// 1. Datos de configuración
//$host = 'localhost';
//$db   = 'nombre_de_tu_bd';
//$user = 'usuario';
//$pass = 'contraseña';
//$charset = 'utf8mb4';

// 2. Definir el DSN (Data Source Name)
//$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// 3. Opciones de configuración de PDO
//$options = [
//    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Activa el reporte de errores
//    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devuelve los datos como array asociativo
//    PDO::ATTR_EMULATE_PREPARES   => false,                  // Desactiva la emulación para mayor seguridad
//];

//try {
    // 4. Intento de conexión
//    $pdo = new PDO($dsn, $user, $pass, $options);
//    echo "Conexión exitosa";
//} catch (\PDOException $e) {
//    // 5. Manejo de errores en caso de fallo
//    throw new \PDOException($e->getMessage(), (int)$e->getCode());
//}


?>