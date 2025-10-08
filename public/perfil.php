<?php
require_once '../app/config/init.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$page_title = 'Meu Perfil';
$message = '';
$error = '';

// Get user data
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $telefone = mysqli_real_escape_string($conn, $_POST['telefone']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Update basic info
    $update_sql = "UPDATE usuarios SET nome = ?, telefone = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssi", $nome, $telefone, $user_id);
    
    if ($update_stmt->execute()) {
        $_SESSION['user_name'] = $nome;
        $message = 'Perfil atualizado com sucesso!';
        
        // Update password if provided
        if (!empty($current_password) && !empty($new_password)) {
            if (password_verify($current_password, $user_data['senha'])) {
                if ($new_password === $confirm_password) {
                    if (strlen($new_password) >= 6) {
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $pass_sql = "UPDATE usuarios SET senha = ? WHERE id = ?";
                        $pass_stmt = $conn->prepare($pass_sql);
                        $pass_stmt->bind_param("si", $hashed_password, $user_id);
                        
                        if ($pass_stmt->execute()) {
                            $message .= ' Senha alterada com sucesso!';
                        } else {
                            $error = 'Erro ao alterar senha.';
                        }
                        $pass_stmt->close();
                    } else {
                        $error = 'A nova senha deve ter pelo menos 6 caracteres.';
                    }
                } else {
                    $error = 'As senhas não coincidem.';
                }
            } else {
                $error = 'Senha atual incorreta.';
            }
        }
        
        // Refresh user data
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_data = $result->fetch_assoc();
        $stmt->close();
    } else {
        $error = 'Erro ao atualizar perfil.';
    }
    
    $update_stmt->close();
}
?>

<?php include '../app/views/includes/header.php'; ?>
<?php include '../app/views/includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <main class="col-md-12 ms-sm-auto px-md-4">
            <div class="content-wrapper">
                <div class="page-header">
                    <h2><i class="bi bi-person-circle"></i> Meu Perfil</h2>
                    <p class="text-muted">Gerencie suas informações pessoais</p>
                </div>
                
                <?php if (!empty($message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Informações Pessoais</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" class="needs-validation" novalidate>
                                    <div class="mb-3">
                                        <label for="nome" class="form-label">Nome Completo *</label>
                                        <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($user_data['nome']); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" disabled>
                                        <small class="text-muted">O email não pode ser alterado.</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="telefone" class="form-label">Telefone</label>
                                        <input type="tel" class="form-control" id="telefone" name="telefone" value="<?php echo htmlspecialchars($user_data['telefone']); ?>">
                                    </div>
                                    
                                    <hr class="my-4">
                                    
                                    <h5 class="mb-3">Alterar Senha</h5>
                                    <p class="text-muted small">Deixe em branco se não deseja alterar a senha.</p>
                                    
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Senha Atual</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">Nova Senha</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" minlength="6">
                                        <small class="text-muted">Mínimo de 6 caracteres</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirmar Nova Senha</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6">
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary btn-custom">
                                        <i class="bi bi-save"></i> Salvar Alterações
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Informações da Conta</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Tipo de Conta:</strong></p>
                                <?php if ($user_data['tipo'] == 'admin'): ?>
                                    <span class="badge bg-danger mb-3">Administrador</span>
                                <?php else: ?>
                                    <span class="badge bg-primary mb-3">Funcionário</span>
                                <?php endif; ?>
                                
                                <p class="mt-3"><strong>Status:</strong></p>
                                <?php if ($user_data['status'] == 'ativo'): ?>
                                    <span class="badge bg-success mb-3">Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary mb-3"><?php echo ucfirst($user_data['status']); ?></span>
                                <?php endif; ?>
                                
                                <p class="mt-3"><strong>Membro desde:</strong></p>
                                <p class="text-muted"><?php echo date('d/m/Y', strtotime($user_data['created_at'])); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../app/views/includes/footer.php'; ?>
