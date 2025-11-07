<?php
require_once '../app/config/init.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $telefone = mysqli_real_escape_string($conn, $_POST['telefone']);
    
    // Validation
    if (empty($nome) || empty($email) || empty($password)) {
        $error = 'Por favor, preencha todos os campos obrigatórios.';
    } elseif ($password !== $confirm_password) {
        $error = 'As senhas não coincidem.';
    } elseif (strlen($password) < 6) {
        $error = 'A senha deve ter pelo menos 6 caracteres.';
    } else {
        // Check if email already exists
        $check_sql = "SELECT id FROM usuarios WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error = 'Este email já está cadastrado.';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $insert_sql = "INSERT INTO usuarios (nome, email, senha, telefone, tipo, status) VALUES (?, ?, ?, ?, 'funcionario', 'pendente')";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ssss", $nome, $email, $hashed_password, $telefone);
            
            if ($insert_stmt->execute()) {
                $success = 'Cadastro realizado com sucesso! Aguarde a aprovação do administrador.';
            } else {
                $error = 'Erro ao realizar cadastro. Tente novamente.';
            }
            
            $insert_stmt->close();
        }
        
        $check_stmt->close();
    }
}

$page_title = 'Cadastro';
?>
<?php include '../app/views/includes/header.php'; ?>

<div class="login-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">
                <div class="card login-card shadow">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <img src="../assets/img/LogoGestCTT.png" alt="GestCTT Logo" class="img-fluid mb-3" style="max-width: 200px;">
                            <h3 class="fw-bold">Criar Conta</h3>
                            <p class="text-muted">Preencha os dados para se cadastrar</p>
                        </div>
                        
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle"></i> <?php echo $success; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <!-- FORMULÁRIO GRID INÍCIO -->
                        <form method="POST" action="" class="needs-validation" novalidate>
                          <div class="row g-3">
                            <div class="col-12">
                              <label for="nome" class="form-label">Nome Completo *</label>
                              <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($nome ?? ''); ?>" required>
                              </div>
                            </div>
                            <div class="col-12 col-md-6">
                              <label for="email" class="form-label">Email *</label>
                              <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                              </div>
                            </div>
                            <div class="col-12 col-md-6">
                              <label for="telefone" class="form-label">Telefone</label>
                              <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="tel" class="form-control" id="telefone" name="telefone" value="<?php echo htmlspecialchars($telefone ?? ''); ?>">
                              </div>
                            </div>
                            <div class="col-12 col-md-6">
                              <label for="password" class="form-label">Senha *</label>
                              <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required minlength="6">
                              </div>
                              <small class="text-muted">Mínimo de 6 caracteres</small>
                            </div>
                            <div class="col-12 col-md-6">
                              <label for="confirm_password" class="form-label">Confirmar Senha *</label>
                              <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                              </div>
                            </div>
                            <div class="col-12">
                              <button type="submit" class="btn btn-primary w-100 btn-custom">
                                <i class="bi bi-person-plus"></i> Cadastrar
                              </button>
                            </div>
                            <div class="col-12 text-center">
                              <hr>
                              <p class="mb-0">Já tem uma conta? <a href="login.php" class="text-decoration-none">Faça login</a></p>
                            </div>
                          </div>
                        </form>
                        <!-- FORMULÁRIO GRID FIM -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../app/views/includes/footer.php'; ?>
