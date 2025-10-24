<?php
require_once '../../app/config/auth_admin.php';
require_once '../../app/config/init.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$page_title = 'Gerenciar Prestadores';
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$prestador_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create_prestador'])) {
        $nome = mysqli_real_escape_string($conn, $_POST['nome']);
        $empresa = mysqli_real_escape_string($conn, $_POST['empresa']);
        $telefone = mysqli_real_escape_string($conn, $_POST['telefone']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $especialidade = mysqli_real_escape_string($conn, $_POST['especialidade']);
        
        $sql = "INSERT INTO prestadores (nome, empresa, telefone, email, especialidade, status) VALUES (?, ?, ?, ?, ?, 'ativo')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $nome, $empresa, $telefone, $email, $especialidade);
        
        if ($stmt->execute()) {
            $message = 'Prestador criado com sucesso!';
            $action = 'list';
        } else {
            $error = 'Erro ao criar prestador.';
        }
        $stmt->close();
    } elseif (isset($_POST['update_prestador'])) {
        $id = intval($_POST['id']);
        $nome = mysqli_real_escape_string($conn, $_POST['nome']);
        $empresa = mysqli_real_escape_string($conn, $_POST['empresa']);
        $telefone = mysqli_real_escape_string($conn, $_POST['telefone']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $especialidade = mysqli_real_escape_string($conn, $_POST['especialidade']);
        
        $sql = "UPDATE prestadores SET nome = ?, empresa = ?, telefone = ?, email = ?, especialidade = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $nome, $empresa, $telefone, $email, $especialidade, $id);
        
        if ($stmt->execute()) {
            $message = 'Prestador atualizado com sucesso!';
            $action = 'list';
        } else {
            $error = 'Erro ao atualizar prestador.';
        }
        $stmt->close();
    } elseif (isset($_POST['delete_prestador'])) {
        $id = intval($_POST['id']);
        
        $sql = "UPDATE prestadores SET status = 'inativo' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $message = 'Prestador desativado com sucesso!';
            $action = 'list';
        } else {
            $error = 'Erro ao desativar prestador.';
        }
        $stmt->close();
    }
}

// Get prestador data for edit
$prestador_data = null;
if ($action == 'edit' && $prestador_id > 0) {
    $sql = "SELECT * FROM prestadores WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $prestador_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $prestador_data = $result->fetch_assoc();
    $stmt->close();
}

// Get all prestadores
$prestadores_sql = "SELECT * FROM prestadores WHERE status = 'ativo' ORDER BY nome";
$prestadores_result = $conn->query($prestadores_sql);
?>

<?php include '../../app/views/includes/header.php'; ?>
<?php include '../../app/views/includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <main class="col-md-12 ms-sm-auto px-md-4">
            <div class="content-wrapper">
                <div class="page-header d-flex justify-content-between align-items-center">
                    <div>
                        <h2><i class="bi bi-person-badge"></i> Gerenciar Prestadores</h2>
                        <p class="text-muted">Gerencie os prestadores de serviço</p>
                    </div>
                    <?php if ($action == 'list'): ?>
                        <a href="?action=create" class="btn btn-primary btn-custom">
                            <i class="bi bi-plus-circle"></i> Novo Prestador
                        </a>
                    <?php else: ?>
                        <a href="prestadores.php" class="btn btn-secondary btn-custom">
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
                            <h5 class="mb-0">Lista de Prestadores</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($prestadores_result->num_rows > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nome</th>
                                                <th>Empresa</th>
                                                <th>Telefone</th>
                                                <th>Email</th>
                                                <th>Especialidade</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($prestador = $prestadores_result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo $prestador['id']; ?></td>
                                                    <td><strong><?php echo htmlspecialchars($prestador['nome']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars($prestador['empresa']); ?></td>
                                                    <td><?php echo htmlspecialchars($prestador['telefone']); ?></td>
                                                    <td><?php echo htmlspecialchars($prestador['email']); ?></td>
                                                    <td><span class="badge bg-info"><?php echo htmlspecialchars($prestador['especialidade']); ?></span></td>
                                                    <td>
                                                        <a href="?action=edit&id=<?php echo $prestador['id']; ?>" class="btn btn-sm btn-warning">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form method="POST" style="display:inline;" onsubmit="return confirmDelete('Deseja realmente desativar este prestador?');">
                                                            <input type="hidden" name="id" value="<?php echo $prestador['id']; ?>">
                                                            <button type="submit" name="delete_prestador" class="btn btn-sm btn-danger">
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
                                <p class="text-center text-muted">Nenhum prestador cadastrado.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                <?php elseif ($action == 'create'): ?>
                    <!-- Create Form -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Novo Prestador</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="needs-validation" novalidate>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nome" class="form-label">Nome *</label>
                                        <input type="text" class="form-control" id="nome" name="nome" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="empresa" class="form-label">Empresa</label>
                                        <input type="text" class="form-control" id="empresa" name="empresa">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="telefone" class="form-label">Telefone *</label>
                                        <input type="tel" class="form-control" id="telefone" name="telefone" required>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email">
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="especialidade" class="form-label">Especialidade *</label>
                                        <input type="text" class="form-control" id="especialidade" name="especialidade" required>
                                    </div>
                                </div>
                                
                                <button type="submit" name="create_prestador" class="btn btn-primary btn-custom">
                                    <i class="bi bi-save"></i> Salvar
                                </button>
                                <a href="prestadores.php" class="btn btn-secondary btn-custom">
                                    <i class="bi bi-x-circle"></i> Cancelar
                                </a>
                            </form>
                        </div>
                    </div>
                    
                <?php elseif ($action == 'edit' && $prestador_data): ?>
                    <!-- Edit Form -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Editar Prestador</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="needs-validation" novalidate>
                                <input type="hidden" name="id" value="<?php echo $prestador_data['id']; ?>">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nome" class="form-label">Nome *</label>
                                        <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($prestador_data['nome']); ?>" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="empresa" class="form-label">Empresa</label>
                                        <input type="text" class="form-control" id="empresa" name="empresa" value="<?php echo htmlspecialchars($prestador_data['empresa']); ?>">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="telefone" class="form-label">Telefone *</label>
                                        <input type="tel" class="form-control" id="telefone" name="telefone" value="<?php echo htmlspecialchars($prestador_data['telefone']); ?>" required>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($prestador_data['email']); ?>">
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="especialidade" class="form-label">Especialidade *</label>
                                        <input type="text" class="form-control" id="especialidade" name="especialidade" value="<?php echo htmlspecialchars($prestador_data['especialidade']); ?>" required>
                                    </div>
                                </div>
                                
                                <button type="submit" name="update_prestador" class="btn btn-primary btn-custom">
                                    <i class="bi bi-save"></i> Atualizar
                                </button>
                                <a href="prestadores.php" class="btn btn-secondary btn-custom">
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

<?php include '../../app/views/includes/footer.php'; ?>
