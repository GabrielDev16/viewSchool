<?php
require_once '../app/config/init.php'; // Precisa do $conn e da sessão

// Limpa o token do banco de dados, se o usuário estiver logado
if (isset($_SESSION['user_id'])) {
    $stmt_clear = $conn->prepare("UPDATE usuarios SET remember_token = NULL, remember_token_expires_at = NULL WHERE id = ?");
    $stmt_clear->bind_param("i", $_SESSION['user_id']);
    $stmt_clear->execute();
    $stmt_clear->close();
}

// Destrói o cookie no navegador
if (isset($_COOKIE['remember_me'])) {
    unset($_COOKIE['remember_me']);
    setcookie('remember_me', '', time() - 3600, '/'); // Define um tempo no passado
}

// Destrói a sessão
session_start();
session_unset();
session_destroy();

// Redireciona para o login
header('Location: ' . BASE_URL . 'login.php?logout=success');
exit();
?>
