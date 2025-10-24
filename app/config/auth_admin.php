<?php
// Inclui o init.php PRIMEIRO para ter acesso à BASE_URL e à sessão.
require_once __DIR__ . '/init.php';

// 1. Verifica se o usuário NÃO está logado
// 2. Verifica se o usuário logado NÃO é um 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    // Limpa a sessão para garantir um logout seguro.
    session_unset();
    session_destroy();
    
    // Redireciona para o login usando a URL base, que sempre funciona.
    header('Location: ' . BASE_URL . 'login.php?error=access_denied');
    exit();
}
?>
