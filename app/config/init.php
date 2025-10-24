<?php
// Include database connection
require_once __DIR__ . '/database.php';
// ... (código do init.php) ...

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// === LÓGICA DE AUTO-LOGIN VIA COOKIE "LEMBRAR-ME" ===
// Verifica se o usuário NÃO está logado na sessão, MAS tem um cookie 'remember_me'
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];

    // Procura o usuário que possui este token e verifica se ele não expirou
    $sql_token = "SELECT * FROM usuarios WHERE remember_token = ? AND remember_token_expires_at > NOW()";
    $stmt_auto_login = $conn->prepare($sql_token);
    $stmt_auto_login->bind_param("s", $token);
    $stmt_auto_login->execute();
    $result_auto_login = $stmt_auto_login->get_result();

    if ($result_auto_login->num_rows == 1) {
        // Token válido! Loga o usuário criando a sessão.
        $user = $result_auto_login->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nome'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_type'] = $user['tipo'];
    } else {
        // Token inválido ou expirado. Destrói o cookie.
        setcookie('remember_me', '', time() - 3600, '/');
    }
    $stmt_auto_login->close();
}
// === FIM DA LÓGICA DE AUTO-LOGIN ===


// Define base URL
define('BASE_URL', 'http://localhost/viewSchool/public/'); // Adjust this based on your server configuration

// Autoload classes (if using a more complex structure with classes)
spl_autoload_register(function ($class_name) {
    $file = __DIR__ . '/../models/' . $class_name . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
    $file = __DIR__ . '/../controllers/' . $class_name . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

?>
