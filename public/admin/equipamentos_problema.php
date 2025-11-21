<?php
require_once '/..app/config/auth_admin.php';
require_once '../app/config/init.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$page_title = 'Equipamentos com Problema';
$message = '';
$error = '';

// Soft delete (desativar equipamento)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_equipamento'])) {
    $id = intval($_POST['id']);
    $sql = "UPDATE equipamentos SET status = 'inativo' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = 'Equipamento desativado com sucesso!';
    } else {
        $error = 'Erro ao desativar equipamento.';
    }
    $stmt->close();
}

// Filtro por Ala
$selected_ala = isset($_GET['ala']) ? intval($_GET['ala']) : 0;
$filter_sql = '';
if ($selected_ala > 0) {
    $filter_sql = " AND e.fk_ala = $selected_ala";
}

// Buscar equipamentos com status 'problema'
$equipamentos_sql = "SELECT e.*, a.nome as ala_nome 
                     FROM equipamentos e 
                     LEFT JOIN ala a ON e.fk_ala = a.id 
                     WHERE e.status = 'problema' $filter_sql
                     ORDER BY e.nome";
$equipamentos_result = $conn->query($equipamentos_sql);

// Buscar todas as alas para o filtro
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
                        <h2><i class="bi bi-exclamation-triangle"></i> Equipamentos com Problema</h2>
                        <p class="text-muted">Lista de equipamentos marcados como problema</p>
                    </div>
                    <a href="equipamentos.php" class="btn btn-secondary btn-custom">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>

```
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

            <!-- Filtro por Ala -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" class="row g-2">
                        <div class="col-md-4">
                            <select class="form-select" name="ala" onchange="this.form.submit()">
                                <option value="0">Todas as Alas</option>
                                <?php while ($ala = $alas_result->fetch_assoc()): ?>
                                    <option value="<?php echo $ala['id']; ?>" <?php echo ($ala['id'] == $selected_ala) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($ala['nome']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Equipamentos -->
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
                                        <th>Tombamento</th>
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
                                                <span class="badge bg-danger">Problema</span>
                                            </td>
                                            <td><?php echo htmlspecialchars($equip['numero_tombamento']); ?></td>
                                            <td>
                                                <a href="equipamentos.php?action=view&id=<?php echo $equip['id']; ?>" class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="equipamentos.php?action=edit&id=<?php echo $equip['id']; ?>" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" style="display:inline;" onsubmit="return confirm('Deseja realmente desativar este equipamento?');">
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
                        <p class="text-center text-muted">Nenhum equipamento com problema.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </main>
</div>
```

</div>

<?php include '../app/views/includes/footer.php'; ?>
