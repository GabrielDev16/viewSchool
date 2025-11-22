<?php
require_once '../../app/config/auth_user.php';
require_once '../../app/config/init.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Busca os dados do usuário
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

$page_title = "Meu Perfil";
?>

<?php include '../../app/views/includes/header.php'; ?>
<?php include '../../app/views/includes/navbar_user.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-person-circle"></i> Meu Perfil
                    </h4>
                </div>

                <div class="card-body">

                    <div class="text-center mb-4">
                        <i class="bi bi-person-circle" style="font-size: 80px;"></i>
                        <h5 class="mt-2"><?php echo htmlspecialchars($usuario['nome']); ?></h5>

                        <span class="badge 
                            <?php
                                if ($usuario['status'] == 'ativo') echo 'bg-success';
                                elseif ($usuario['status'] == 'pendente') echo 'bg-warning text-dark';
                                elseif ($usuario['status'] == 'rejeitado') echo 'bg-danger';
                                else echo 'bg-secondary';
                            ?>">
                            <?php echo strtoupper($usuario['status']); ?>
                        </span>
                    </div>

                    <hr>

                    <h6 class="fw-bold">Informações Pessoais</h6>

                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($usuario['nome']); ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">E-mail</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($usuario['email']); ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Telefone</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($usuario['telefone'] ?? 'Não informado'); ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipo de Usuário</label>
                        <input type="text" class="form-control" value="<?php echo strtoupper($usuario['tipo']); ?>" readonly>
                    </div>

                    <hr>

                    <h6 class="fw-bold">Segurança</h6>

                    <div class="d-grid gap-2">
                        <a href="../alterar_senha.php" class="btn btn-warning">
                            <i class="bi bi-key"></i> Alterar Senha
                        </a>
                    </div>

                </div>

                <div class="card-footer text-end">
                    <small class="text-muted">Última atualização: 
                        <?php echo date('d/m/Y H:i', strtotime($usuario['updated_at'])); ?>
                    </small>
                </div>

            </div>

        </div>
    </div>
</div>

<?php include '../../app/views/includes/footer.php'; ?>
