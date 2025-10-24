<?php
require_once '../app/config/auth_admin.php';
require_once '../app/config/init.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$page_title = 'Gerenciar Alas';
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$ala_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create_ala'])) {
        $nome = mysqli_real_escape_string($conn, $_POST['nome']);
        $descricao = mysqli_real_escape_string($conn, $_POST['descricao']);
        
        $sql = "INSERT INTO ala (nome, descricao, status) VALUES (?, ?, 'ativo')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $nome, $descricao);
        
        if ($stmt->execute()) {
            $message = 'Ala criada com sucesso!';
            $action = 'list';
        } else {
            $error = 'Erro ao criar ala.';
        }
        $stmt->close();
    } elseif (isset($_POST['update_ala'])) {
        $id = intval($_POST['id']);
        $nome = mysqli_real_escape_string($conn, $_POST['nome']);
        $descricao = mysqli_real_escape_string($conn, $_POST['descricao']);
        
        $sql = "UPDATE ala SET nome = ?, descricao = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $nome, $descricao, $id);
        
        if ($stmt->execute()) {
            $message = 'Ala atualizada com sucesso!';
            $action = 'list';
        } else {
            $error = 'Erro ao atualizar ala.';
        }
        $stmt->close();
    } elseif (isset($_POST['delete_ala'])) {
        $id = intval($_POST['id']);
        
        // Soft delete
        $sql = "UPDATE ala SET status = 'inativo' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $message = 'Ala desativada com sucesso!';
            $action = 'list';
        } else {
            $error = 'Erro ao desativar ala.';
        }
        $stmt->close();
    }
}

// Get ala data for edit
$ala_data = null;
if ($action == 'edit' && $ala_id > 0) {
    $sql = "SELECT * FROM ala WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ala_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ala_data = $result->fetch_assoc();
    $stmt->close();
}

// Get all alas
$alas_sql = "SELECT a.*, COUNT(e.id) as total_equipamentos 
             FROM ala a 
             LEFT JOIN equipamentos e ON a.id = e.fk_ala AND e.status = 'ativo'
             WHERE a.status = 'ativo'
             GROUP BY a.id
             ORDER BY a.nome";
$alas_result = $conn->query($alas_sql);
?>

<?php include '../app/views/includes/header.php'; ?>
<?php include '../app/views/includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <main class="col-md-12 ms-sm-auto px-md-4">
            <div class="content-wrapper">
                <div class="page-header d-flex justify-content-between align-items-center">
                    <div>
                        <h2><i class="bi bi-building"></i> Gerenciar Alas</h2>
                        <p class="text-muted">Gerencie as alas (salas/locais) da instituição</p>
                    </div>
                    <?php if ($action == 'list'): ?>
                        <a href="?action=create" class="btn btn-primary btn-custom">
                            <i class="bi bi-plus-circle"></i> Nova Ala
                        </a>
                    <?php else: ?>
                        <a href="alas.php" class="btn btn-secondary btn-custom">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                    <?php endif; ?>
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
                
                <?php if ($action == 'list'): ?>
                    <!-- List View -->
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="mb-0">Lista de Alas</h5>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="searchInput" placeholder="Buscar ala..." onkeyup="searchTable('searchInput', 'alasTable')">
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if ($alas_result->num_rows > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover" id="alasTable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nome</th>
                                                <th>Descrição</th>
                                                <th>Equipamentos</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($ala = $alas_result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo $ala['id']; ?></td>
                                                    <td><strong><?php echo htmlspecialchars($ala['nome']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars($ala['descricao']); ?></td>
                                                    <td>
                                                        <span class="badge bg-info"><?php echo $ala['total_equipamentos']; ?> equipamentos</span>
                                                    </td>
                                                    <td>
                                                        <a href="?action=edit&id=<?php echo $ala['id']; ?>" class="btn btn-sm btn-warning">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form method="POST" style="display:inline;" onsubmit="return confirmDelete('Deseja realmente desativar esta ala?');">
                                                            <input type="hidden" name="id" value="<?php echo $ala['id']; ?>">
                                                            <button type="submit" name="delete_ala" class="btn btn-sm btn-danger">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-center text-muted">Nenhuma ala cadastrada.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                <?php elseif ($action == 'create'): ?>
                    <!-- Create Form -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Nova Ala</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome da Ala *</label>
                                    <input type="text" class="form-control" id="nome" name="nome" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="descricao" class="form-label">Descrição</label>
                                    <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                                </div>
                                
                                <button type="submit" name="create_ala" class="btn btn-primary btn-custom">
                                    <i class="bi bi-save"></i> Salvar
                                </button>
                                <a href="alas.php" class="btn btn-secondary btn-custom">
                                    <i class="bi bi-x-circle"></i> Cancelar
                                </a>
                            </form>
                        </div>
                    </div>
                    
                <?php elseif ($action == 'edit' && $ala_data): ?>
                    <!-- Edit Form -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Editar Ala</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="needs-validation" novalidate>
                                <input type="hidden" name="id" value="<?php echo $ala_data['id']; ?>">
                                
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome da Ala *</label>
                                    <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($ala_data['nome']); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="descricao" class="form-label">Descrição</label>
                                    <textarea class="form-control" id="descricao" name="descricao" rows="3"><?php echo htmlspecialchars($ala_data['descricao']); ?></textarea>
                                </div>
                                
                                <button type="submit" name="update_ala" class="btn btn-primary btn-custom">
                                    <i class="bi bi-save"></i> Atualizar
                                </button>
                                <a href="alas.php" class="btn btn-secondary btn-custom">
                                    <i class="bi bi-x-circle"></i> Cancelar
                                </a>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php include '../app/views/includes/footer.php'; ?>
