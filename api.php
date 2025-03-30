<?php
namespace NAMESPACEWEB\API;

use PDO;
use PDOException;

// Configuración de cabeceras para la API
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Clase para manejar la conexión a la base de datos
 */
class Database {
    private $host = 'localhost';
    private $db_name = 'u363046794_app1';
    private $username = 'u363046794_zarkius';
    private $password = '11211121aA.,';
    private $conn;

    /**
     * Conectar a la base de datos
     */
    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Manejo de errores de conexión
            http_response_code(500);
            echo json_encode(['error' => 'Connection failed: ' . $e->getMessage()]);
            exit;
        }

        return $this->conn;
    }
}

/**
 * Clase para manejar operaciones relacionadas con los usuarios
 */
class User {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Obtener todos los usuarios de la tabla
     */
    public function getUsers() {
        try {
            $query = "SELECT * FROM " . $this->table;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            // Manejo de errores en la consulta
            http_response_code(500);
            echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
            exit;
        }
    }
}

/**
 * Clase para manejar la API de usuarios
 */
class UserAPI {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->user = new User($this->db);
    }

    /**
     * Obtener y devolver los usuarios en formato JSON
     */
    public function getUsers() {
        $stmt = $this->user->getUsers();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $users_arr = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $users_arr[] = $row; // Agregar cada fila al array
            }
            echo json_encode($users_arr);
        } else {
            // Si no hay usuarios, devolver un mensaje
            http_response_code(404);
            echo json_encode(['message' => 'No users found']);
        }
    }
}

// Ejecutar la API
try {
    $api = new UserAPI();
    $api->getUsers();
} catch (Exception $e) {
    // Manejo de errores generales
    http_response_code(500);
    echo json_encode(['error' => 'Unexpected error: ' . $e->getMessage()]);
}
?>