<?php
// Configurações do banco de dados para XAMPP
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'findash_pro');

// Criar conexão
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Erro na conexão: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

// Iniciar sessão
session_start();

// Verificar se usuário está logado
function verificarLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit();
    }
}

// Obter dados do usuário logado
function getUsuarioLogado() {
    if (!isset($_SESSION['usuario_id'])) {
        return null;
    }
    
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT id, nome, cargo, email FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    
    return $usuario;
}
?>
