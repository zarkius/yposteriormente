<?php
namespace NAMESPACEWEB\API;

use PDO;
use PDOException;

// Configuración de cabeceras para la API
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$config = include('config.php');
$googleClientId = $config['google_client_id'];
$googleClientSecret = $config['google_client_secret'];
$googleRedirectUri = $config['google_redirect_uri'];

require_once 'vendor/autoload.php';

/**
 * Clase para manejar la conexión a la base de datos
 */
class Database {
    private $host = 'localhost';
    private $db_name = 'u363046794_app1';
    private $username = 'u363046794_zarkius';
    private $password = '11211121aA.,';
    private $conn;

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

    // Obtener todos los usuarios
    public function getUsers() {
        try {
            $query = "SELECT * FROM " . $this->table;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
            exit;
        }
    }

    // Crear un nuevo usuario
    public function createUser($name, $email) {
        try {
            $query = "INSERT INTO " . $this->table . " (name, email) VALUES (:name, :email)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Insert failed: ' . $e->getMessage()]);
            exit;
        }
    }

    // Actualizar un usuario
    public function updateUser($id, $name, $email) {
        try {
            $query = "UPDATE " . $this->table . " SET name = :name, email = :email WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Update failed: ' . $e->getMessage()]);
            exit;
        }
    }

    // Eliminar un usuario
    public function deleteUser($id) {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Delete failed: ' . $e->getMessage()]);
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

    // Manejar solicitudes de la API
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                $this->getUsers();
                break;
            case 'POST':
                $this->createUser();
                break;
            case 'PUT':
                $this->updateUser();
                break;
            case 'DELETE':
                $this->deleteUser();
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
                break;
        }
    }

    // Obtener usuarios
    public function getUsers() {
        $stmt = $this->user->getUsers();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $users_arr = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $users_arr[] = $row;
            }
            echo json_encode($users_arr);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'No users found']);
        }
    }

    // Crear usuario
    public function createUser() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!empty($data['name']) && !empty($data['email'])) {
            $this->user->createUser($data['name'], $data['email']);
            http_response_code(201);
            echo json_encode(['message' => 'User created successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input']);
        }
    }

    // Actualizar usuario
    public function updateUser() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!empty($data['id']) && !empty($data['name']) && !empty($data['email'])) {
            $this->user->updateUser($data['id'], $data['name'], $data['email']);
            http_response_code(200);
            echo json_encode(['message' => 'User updated successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input']);
        }
    }

    // Eliminar usuario
    public function deleteUser() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!empty($data['id'])) {
            $this->user->deleteUser($data['id']);
            http_response_code(200);
            echo json_encode(['message' => 'User deleted successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input']);
        }
    }
}

// Ejecutar la API
try {
    $api = new UserAPI();
    authenticate();
    $api->handleRequest();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Unexpected error: ' . $e->getMessage()]);
}

/**
 * Función para autenticar la solicitud
 */
function authenticate() {
    global $googleClientId, $googleClientSecret, $googleRedirectUri;

    $client = new Google_Client();
    $client->setClientId($googleClientId);
    $client->setClientSecret($googleClientSecret);
    $client->setRedirectUri($googleRedirectUri);
    $client->addScope(['email', 'profile']);

    // Verificar si hay un token de acceso en la cabecera
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['error' => 'No autorizado']);
        exit;
    }

    $accessToken = str_replace('Bearer ', '', $headers['Authorization']);

    try {
        $client->setAccessToken($accessToken);

        // Verificar el token
        if ($client->isAccessTokenExpired()) {
            http_response_code(401);
            echo json_encode(['error' => 'Token expirado']);
            exit;
        }

        $payload = $client->verifyIdToken();
        if (!$payload) {
            http_response_code(401);
            echo json_encode(['error' => 'Token inválido']);
            exit;
        }

        // Opcional: Verificar si el correo electrónico está autorizado
        $authorizedEmails = ['usuario_autorizado@example.com'];
        if (!in_array($payload['email'], $authorizedEmails)) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            exit;
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Error de autenticación: ' . $e->getMessage()]);
        exit;
    }
}
?>