<?php
require_once '../../app/config/init.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$page_title = 'Gerenciar Usuários';
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['approve_user'])) {
        $id = intval($_POST['id']);
        $sql = "UPDATE usuarios SET status = 'ativo' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $message = 'Usuário aprovado com sucesso!';
        } else {
            $error = 'Erro ao aprovar usuário.';
        }
        $stmt->close();
    } elseif (isset($_POST['reject_user'])) {
        $id = intval($_POST['id']);
        $sql = "UPDATE usuarios SET status = 'rejeitado' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $message = 'Usuário rejeitado.';
        } else {
            $error = 'Erro ao rejeitar usuário.';
        }
        $stmt->close();
    } elseif (isset($_POST['delete_user'])) {
        $id = intval($_POST['id']);
        $sql = "UPDATE usuarios SET status = 'inativo' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $message = 'Usuário desativado com sucesso!';
        } else {
            $error = 'Erro ao desativar usuário.';
        }
        $stmt->close();
    }
}

// Get pending users
$pending_sql = "SELECT * FROM usuarios WHERE status = 'pendente' ORDER BY id DESC";
$pending_result = $conn->query($pending_sql);

// Get active users
$active_sql = "SELECT * FROM usuarios WHERE status = 'ativo' ORDER BY nome";
$active_result = $conn->query($active_sql);
?>

<?php include '../../app/views/includes/header.php'; ?>
<?php include '../../app/views/includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <main class="col-md-12 ms-sm-auto px-md-4">
            <div class="content-wrapper">
                <div class="page-header">
                    <h2><i class="bi bi-people"></i> Gerenciar Usuários</h2>
                    <p class="text-muted">Gerencie os usuários do sistema</p>
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
                
                <!-- Pending Users -->
                <?php if ($pending_result->num_rows > 0): ?>
                    <div class="card mb-4">
                        <div class="card-header bg-warning">
                            <h5 class="mb-0"><i class="bi bi-hourglass-split"></i> Solicitações Pendentes (<?php echo $pending_result->num_rows; ?>)</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th>Telefone</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($user = $pending_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $user['id']; ?></td>
                                                <td><?php echo htmlspecialchars($user['nome']); ?></td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td><?php echo htmlspecialchars($user['telefone']); ?></td>
                                                <td>
                                                    <form method="POST" style="display:inline;">
                                                        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                        <button type="submit" name="approve_user" class="btn btn-sm btn-success">
                                                            <i class="bi bi-check-circle"></i> Aprovar
                                                        </button>
                                                    </form>
                                                    <form method="POST" style="display:inline;">
                                                        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                        <button type="submit" name="reject_user" class="btn btn-sm btn-danger">
                                                            <i class="bi bi-x-circle"></i> Rejeitar
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Active Users -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Usuários Ativos</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($active_result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th>Telefone</th>
                                            <th>Tipo</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($user = $active_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $user['id']; ?></td>
                                                <td><?php echo htmlspecialchars($user['nome']); ?></td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td><?php echo htmlspecialchars($user['telefone']); ?></td>
                                                <td>
                                                    <?php if ($user['tipo'] == 'admin'): ?>
                                                        <span class="badge bg-danger">Admin</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-primary">Funcionário</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                        <form method="POST" style="display:inline;" onsubmit="return confirmDelete('Deseja realmente desativar este usuário?');">
                                                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                            <button type="submit" name="delete_user" class="btn btn-sm btn-danger">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-center text-muted">Nenhum usuário ativo.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../../app/views/includes/footer.php'; ?>
