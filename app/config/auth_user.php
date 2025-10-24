<?php
// Inclui o init.php para ter acesso à sessão, $conn e BASE_URL.
// __DIR__ garante que o caminho sempre funcione, pois ambos estão na mesma pasta.
require_once __DIR__ . '/init.php';

// VERIFICAÇÃO: O usuário NÃO está logado?
if (!isset($_SESSION['user_id'])) {
    // Se não estiver logado, limpa qualquer resquício de sessão.
    session_unset();
    session_destroy();
    
    // Manda o usuário para a página de login com uma mensagem clara.
    header('Location: ' . BASE_URL . 'login.php?error=login_required');
    exit(); // Encerra o script. Acesso negado.
}

// Se o script chegou até aqui, o usuário ESTÁ LOGADO.
// Apenas prepara as variáveis para serem usadas na página que incluiu este arquivo.
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_type = $_SESSION['user_type'];

?>
