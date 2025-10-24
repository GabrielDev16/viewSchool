<?php
// Inclui o init.php para ter acesso à BASE_URL, $conn e à sessão.
require_once '../app/config/init.php';

// Redireciona usuários que JÁ ESTÃO LOGADOS para seus respectivos painéis.
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin') {
        header('Location: ' . BASE_URL . 'admin/index.php');
    } else {
        header('Location: ' . BASE_URL . 'user/index.php');
    }
    exit();
}

$error = '';

// Processa o formulário quando ele é enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    // Procura por um usuário com o email fornecido E que tenha o status 'ativo'
    $sql = "SELECT * FROM usuarios WHERE email = ? AND status = 'ativo'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Verifica se encontrou exatamente um usuário
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verifica se a senha fornecida corresponde à senha hashada no banco de dados
        if (password_verify($password, $user['senha'])) {
            // SUCESSO NO LOGIN!

            // 1. Cria a sessão padrão do usuário
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nome'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_type'] = $user['tipo'];

            // 2. LÓGICA DO "LEMBRAR-ME"
            if (isset($_POST['remember'])) {
                $token = bin2hex(random_bytes(32));
                $expires_at = date('Y-m-d H:i:s', time() + (86400 * 30)); // Expira em 30 dias

                // Salva o token no banco de dados
                $stmt_token = $conn->prepare("UPDATE usuarios SET remember_token = ?, remember_token_expires_at = ? WHERE id = ?");
                $stmt_token->bind_param("ssi", $token, $expires_at, $user['id']);
                $stmt_token->execute();
                $stmt_token->close();

                // Cria o cookie no navegador do usuário
                setcookie('remember_me', $token, time() + (86400 * 30), "/");
            }
            
            // 3. Redireciona o usuário para o painel correto
            if ($user['tipo'] == 'admin') {
                header('Location: ' . BASE_URL . 'admin/paineladm.php');
            } else {
                header('Location: ' . BASE_URL . 'user/index.php'); 
            }
            exit(); // Encerra o script após o redirecionamento

        } else {
            // Senha incorreta
            $error = 'Email ou senha incorretos.';
        }
    } else {
        // Usuário não encontrado ou não está 'ativo'
        $error = 'Email ou senha incorretos, ou usuário inativo/pendente.';
    }
    
    $stmt->close();
}

$page_title = 'Login';
include '../app/views/includes/header.php';
?>

<div class="login-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card login-card shadow">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <img src="../assets/img/LogoGestCTT.png" alt="GestCTT Logo" class="img-fluid mb-3" style="max-width: 200px;">
                            <h3 class="fw-bold">Bem-vindo ao GestCTT</h3>
                            <p class="text-muted">Sistema de Gestão de Equipamentos</p>
                        </div>
                        
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="login.php" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Senha</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Lembrar-me</label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 btn-custom">
                                <i class="bi bi-box-arrow-in-right"></i> Entrar
                            </button>
                        </form>
                        
                        <hr class="my-4">
                        
                        <div class="text-center">
                            <p class="mb-0">Não tem uma conta? <a href="cadastro.php" class="text-decoration-none">Cadastre-se</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../app/views/includes/footer.php'; ?>
