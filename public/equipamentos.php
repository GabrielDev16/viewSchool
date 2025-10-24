<?php
require_once '../app/config/auth_admin.php';
require_once '../app/config/init.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$page_title = 'Gerenciar Equipamentos';
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$equip_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create_equipamento'])) {
        $nome = mysqli_real_escape_string($conn, $_POST['nome']);
        $tipo = mysqli_real_escape_string($conn, $_POST['tipo']);
        $fk_ala = intval($_POST['fk_ala']);
        $descricao = mysqli_real_escape_string($conn, $_POST['descricao']);
        
        // Handle image upload
        $imagem = '';
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
            $upload_dir = '../uploads/equipamentos/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $upload_path)) {
                $imagem = 'uploads/equipamentos/' . $new_filename;
            }
        }
        
        $sql = "INSERT INTO equipamentos (nome, tipo, fk_ala, descricao, imagem, status) VALUES (?, ?, ?, ?, ?, 'ativo')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiss", $nome, $tipo, $fk_ala, $descricao, $imagem);
        
        if ($stmt->execute()) {
            $message = 'Equipamento criado com sucesso!';
            $action = 'list';
        } else {
            $error = 'Erro ao criar equipamento.';
        }
        $stmt->close();
    } elseif (isset($_POST['update_equipamento'])) {
        $id = intval($_POST['id']);
        $nome = mysqli_real_escape_string($conn, $_POST['nome']);
        $tipo = mysqli_real_escape_string($conn, $_POST['tipo']);
        $fk_ala = intval($_POST['fk_ala']);
        $descricao = mysqli_real_escape_string($conn, $_POST['descricao']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        
        // Get current image
        $current_image_sql = "SELECT imagem FROM equipamentos WHERE id = ?";
        $stmt = $conn->prepare($current_image_sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $current_data = $result->fetch_assoc();
        $imagem = $current_data['imagem'];
        $stmt->close();
        
        // Handle new image upload
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
            $upload_dir = '../uploads/equipamentos/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $upload_path)) {
                // Delete old image
                if (!empty($imagem) && file_exists('../' . $imagem)) {
                    unlink('../' . $imagem);
                }
                $imagem = 'uploads/equipamentos/' . $new_filename;
            }
        }
        
        $sql = "UPDATE equipamentos SET nome = ?, tipo = ?, fk_ala = ?, descricao = ?, imagem = ?, status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisssi", $nome, $tipo, $fk_ala, $descricao, $imagem, $status, $id);
        
        if ($stmt->execute()) {
            $message = 'Equipamento atualizado com sucesso!';
            $action = 'list';
        } else {
            $error = 'Erro ao atualizar equipamento.';
        }
        $stmt->close();
    } elseif (isset($_POST['delete_equipamento'])) {
        $id = intval($_POST['id']);
        
        // Soft delete
        $sql = "UPDATE equipamentos SET status = 'inativo' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $message = 'Equipamento desativado com sucesso!';
            $action = 'list';
        } else {
            $error = 'Erro ao desativar equipamento.';
        }
        $stmt->close();
    }
}

// Get equipment data for edit/view
$equip_data = null;
if (($action == 'edit' || $action == 'view') && $equip_id > 0) {
    $sql = "SELECT e.*, a.nome as ala_nome FROM equipamentos e 
            LEFT JOIN ala a ON e.fk_ala = a.id 
            WHERE e.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $equip_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $equip_data = $result->fetch_assoc();
    $stmt->close();
}

// Get all equipment
$equipamentos_sql = "SELECT e.*, a.nome as ala_nome 
                     FROM equipamentos e 
                     LEFT JOIN ala a ON e.fk_ala = a.id 
                     WHERE e.status IN ('ativo', 'problema')
                     ORDER BY e.nome";
$equipamentos_result = $conn->query($equipamentos_sql);

// Get all alas for dropdown
$alas_sql = "SELECT id, nome FROM ala WHERE status = 'ativo' ORDER BY nome";
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
                        <h2><i class="bi bi-tools"></i> Gerenciar Equipamentos</h2>
                        <p class="text-muted">Gerencie os equipamentos da instituição</p>
                    </div>
                    <?php if ($action == 'list'): ?>
                        <a href="?action=create" class="btn btn-primary btn-custom">
                            <i class="bi bi-plus-circle"></i> Novo Equipamento
                        </a>
                    <?php else: ?>
                        <a href="equipamentos.php" class="btn btn-secondary btn-custom">
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
                                    <h5 class="mb-0">Lista de Equipamentos</h5>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="searchInput" placeholder="Buscar equipamento..." onkeyup="searchTable('searchInput', 'equipamentosTable')">
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if ($equipamentos_result->num_rows > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover" id="equipamentosTable">
                                        <thead>
                                            <tr>
                                                <th>Imagem</th>
                                                <th>Nome</th>
                                                <th>Tipo</th>
                                                <th>Ala</th>
                                                <th>Status</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($equip = $equipamentos_result->fetch_assoc()): ?>
                                                <tr>
                                                    <td>
                                                        <?php if (!empty($equip['imagem']) && file_exists('../' . $equip['imagem'])): ?>
                                                            <img src="../<?php echo htmlspecialchars($equip['imagem']); ?>" alt="<?php echo htmlspecialchars($equip['nome']); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                                        <?php else: ?>
                                                            <img src="../assets/img/LogoGestCTT.png" alt="Sem imagem" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><strong><?php echo htmlspecialchars($equip['nome']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars($equip['tipo']); ?></td>
                                                    <td><?php echo htmlspecialchars($equip['ala_nome']); ?></td>
                                                    <td>
                                                        <?php if ($equip['status'] == 'ativo'): ?>
                                                            <span class="badge bg-success">Ativo</span>
                                                        <?php elseif ($equip['status'] == 'problema'): ?>
                                                            <span class="badge bg-danger">Problema</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Inativo</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="?action=view&id=<?php echo $equip['id']; ?>" class="btn btn-sm btn-info">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="?action=edit&id=<?php echo $equip['id']; ?>" class="btn btn-sm btn-warning">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form method="POST" style="display:inline;" onsubmit="return confirmDelete('Deseja realmente desativar este equipamento?');">
                                                            <input type="hidden" name="id" value="<?php echo $equip['id']; ?>">
                                                            <button type="submit" name="delete_equipamento" class="btn btn-sm btn-danger">
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
                                <p class="text-center text-muted">Nenhum equipamento cadastrado.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                <?php elseif ($action == 'create'): ?>
                    <!-- Create Form -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Novo Equipamento</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nome" class="form-label">Nome do Equipamento *</label>
                                        <input type="text" class="form-control" id="nome" name="nome" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="tipo" class="form-label">Tipo *</label>
                                        <select class="form-select" id="tipo" name="tipo" required>
                                            <option value="">Selecione...</option>
                                            <option value="Ar Condicionado">Ar Condicionado</option>
                                            <option value="Lâmpada">Lâmpada</option>
                                            <option value="Tomada">Tomada</option>
                                            <option value="Interruptor">Interruptor</option>
                                            <option value="Ventilador">Ventilador</option>
                                            <option value="Projetor">Projetor</option>
                                            <option value="Computador">Computador</option>
                                            <option value="Outro">Outro</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="fk_ala" class="form-label">Ala (Local) *</label>
                                    <select class="form-select" id="fk_ala" name="fk_ala" required>
                                        <option value="">Selecione...</option>
                                        <?php 
                                        $alas_result->data_seek(0);
                                        while ($ala = $alas_result->fetch_assoc()): 
                                        ?>
                                            <option value="<?php echo $ala['id']; ?>"><?php echo htmlspecialchars($ala['nome']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="descricao" class="form-label">Descrição</label>
                                    <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="imagem" class="form-label">Imagem</label>
                                    <input type="file" class="form-control" id="imagem" name="imagem" accept="image/*" onchange="previewImage(this, 'imagePreview')">
                                    <img id="imagePreview" src="#" alt="Preview" style="display:none; margin-top: 10px; max-width: 200px; border-radius: 5px;">
                                </div>
                                
                                <button type="submit" name="create_equipamento" class="btn btn-primary btn-custom">
                                    <i class="bi bi-save"></i> Salvar
                                </button>
                                <a href="equipamentos.php" class="btn btn-secondary btn-custom">
                                    <i class="bi bi-x-circle"></i> Cancelar
                                </a>
                            </form>
                        </div>
                    </div>
                    
                <?php elseif ($action == 'edit' && $equip_data): ?>
                    <!-- Edit Form -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Editar Equipamento</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                                <input type="hidden" name="id" value="<?php echo $equip_data['id']; ?>">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nome" class="form-label">Nome do Equipamento *</label>
                                        <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($equip_data['nome']); ?>" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="tipo" class="form-label">Tipo *</label>
                                        <select class="form-select" id="tipo" name="tipo" required>
                                            <option value="">Selecione...</option>
                                            <option value="Ar Condicionado" <?php echo ($equip_data['tipo'] == 'Ar Condicionado') ? 'selected' : ''; ?>>Ar Condicionado</option>
                                            <option value="Lâmpada" <?php echo ($equip_data['tipo'] == 'Lâmpada') ? 'selected' : ''; ?>>Lâmpada</option>
                                            <option value="Tomada" <?php echo ($equip_data['tipo'] == 'Tomada') ? 'selected' : ''; ?>>Tomada</option>
                                            <option value="Interruptor" <?php echo ($equip_data['tipo'] == 'Interruptor') ? 'selected' : ''; ?>>Interruptor</option>
                                            <option value="Ventilador" <?php echo ($equip_data['tipo'] == 'Ventilador') ? 'selected' : ''; ?>>Ventilador</option>
                                            <option value="Projetor" <?php echo ($equip_data['tipo'] == 'Projetor') ? 'selected' : ''; ?>>Projetor</option>
                                            <option value="Computador" <?php echo ($equip_data['tipo'] == 'Computador') ? 'selected' : ''; ?>>Computador</option>
                                            <option value="Outro" <?php echo ($equip_data['tipo'] == 'Outro') ? 'selected' : ''; ?>>Outro</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="fk_ala" class="form-label">Ala (Local) *</label>
                                        <select class="form-select" id="fk_ala" name="fk_ala" required>
                                            <option value="">Selecione...</option>
                                            <?php 
                                            $alas_result->data_seek(0);
                                            while ($ala = $alas_result->fetch_assoc()): 
                                            ?>
                                                <option value="<?php echo $ala['id']; ?>" <?php echo ($equip_data['fk_ala'] == $ala['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($ala['nome']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">Status *</label>
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="ativo" <?php echo ($equip_data['status'] == 'ativo') ? 'selected' : ''; ?>>Ativo</option>
                                            <option value="problema" <?php echo ($equip_data['status'] == 'problema') ? 'selected' : ''; ?>>Problema</option>
                                            <option value="inativo" <?php echo ($equip_data['status'] == 'inativo') ? 'selected' : ''; ?>>Inativo</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="descricao" class="form-label">Descrição</label>
                                    <textarea class="form-control" id="descricao" name="descricao" rows="3"><?php echo htmlspecialchars($equip_data['descricao']); ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="imagem" class="form-label">Imagem</label>
                                    <?php if (!empty($equip_data['imagem']) && file_exists('../' . $equip_data['imagem'])): ?>
                                        <div class="mb-2">
                                            <img src="../<?php echo htmlspecialchars($equip_data['imagem']); ?>" alt="Imagem atual" style="max-width: 200px; border-radius: 5px;">
                                            <p class="text-muted small">Imagem atual</p>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" id="imagem" name="imagem" accept="image/*" onchange="previewImage(this, 'imagePreview')">
                                    <img id="imagePreview" src="#" alt="Preview" style="display:none; margin-top: 10px; max-width: 200px; border-radius: 5px;">
                                </div>
                                
                                <button type="submit" name="update_equipamento" class="btn btn-primary btn-custom">
                                    <i class="bi bi-save"></i> Atualizar
                                </button>
                                <a href="equipamentos.php" class="btn btn-secondary btn-custom">
                                    <i class="bi bi-x-circle"></i> Cancelar
                                </a>
                            </form>
                        </div>
                    </div>
                    
                <?php elseif ($action == 'view' && $equip_data): ?>
                    <!-- View Details -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Detalhes do Equipamento</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 text-center mb-3">
                                    <?php if (!empty($equip_data['imagem']) && file_exists('../' . $equip_data['imagem'])): ?>
                                        <img src="../<?php echo htmlspecialchars($equip_data['imagem']); ?>" alt="<?php echo htmlspecialchars($equip_data['nome']); ?>" class="img-fluid rounded">
                                    <?php else: ?>
                                        <img src="../assets/img/LogoGestCTT.png" alt="Sem imagem" class="img-fluid rounded">
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-8">
                                    <h3><?php echo htmlspecialchars($equip_data['nome']); ?></h3>
                                    <hr>
                                    <p><strong>Tipo:</strong> <?php echo htmlspecialchars($equip_data['tipo']); ?></p>
                                    <p><strong>Ala:</strong> <?php echo htmlspecialchars($equip_data['ala_nome']); ?></p>
                                    <p><strong>Status:</strong> 
                                        <?php if ($equip_data['status'] == 'ativo'): ?>
                                            <span class="badge bg-success">Ativo</span>
                                        <?php elseif ($equip_data['status'] == 'problema'): ?>
                                            <span class="badge bg-danger">Problema</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inativo</span>
                                        <?php endif; ?>
                                    </p>
                                    <p><strong>Descrição:</strong></p>
                                    <p><?php echo nl2br(htmlspecialchars($equip_data['descricao'])); ?></p>
                                    
                                    <div class="mt-4">
                                        <a href="?action=edit&id=<?php echo $equip_data['id']; ?>" class="btn btn-warning btn-custom">
                                            <i class="bi bi-pencil"></i> Editar
                                        </a>
                                        <a href="equipamentos.php" class="btn btn-secondary btn-custom">
                                            <i class="bi bi-arrow-left"></i> Voltar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php include '../app/views/includes/footer.php'; ?>
