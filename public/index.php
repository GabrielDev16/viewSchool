<?php
require_once '../app/config/auth_admin.php';
require_once '../app/config/init.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$page_title = 'Dashboard';

// Get statistics
$total_alas_sql = "SELECT COUNT(*) as total FROM ala WHERE status = 'ativo'";
$total_alas = $conn->query($total_alas_sql)->fetch_assoc()['total'];

$total_equipamentos_sql = "SELECT COUNT(*) as total FROM equipamentos WHERE status = 'ativo'";
$total_equipamentos = $conn->query($total_equipamentos_sql)->fetch_assoc()['total'];

$equipamentos_problema_sql = "SELECT COUNT(*) as total FROM equipamentos WHERE status = 'problema'";
$equipamentos_problema = $conn->query($equipamentos_problema_sql)->fetch_assoc()['total'];

$total_usuarios_sql = "SELECT COUNT(*) as total FROM usuarios WHERE status = 'ativo'";
$total_usuarios = $conn->query($total_usuarios_sql)->fetch_assoc()['total'];

// Get recent equipment
$recent_equipamentos_sql = "SELECT e.*, a.nome as ala_nome FROM equipamentos e 
                           LEFT JOIN ala a ON e.fk_ala = a.id 
                           WHERE e.status = 'ativo' 
                           ORDER BY e.id DESC LIMIT 6";
$recent_equipamentos = $conn->query($recent_equipamentos_sql);
?>

<?php include '../app/views/includes/header.php'; ?>
<?php include '../app/views/includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <main class="col-md-12 ms-sm-auto px-md-4">
            <div class="content-wrapper">
                <div class="page-header">
                    <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
                    <p class="text-muted">Bem-vindo, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Total de Alas</h6>
                                        <h3 class="mb-0"><?php echo $total_alas; ?></h3>
                                    </div>
                                    <div class="text-primary">
                                        <i class="bi bi-building" style="font-size: 2.5rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card stats-card success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Equipamentos Ativos</h6>
                                        <h3 class="mb-0"><?php echo $total_equipamentos; ?></h3>
                                    </div>
                                    <div class="text-success">
                                        <i class="bi bi-tools" style="font-size: 2.5rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card stats-card danger">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Com Problema</h6>
                                        <h3 class="mb-0"><?php echo $equipamentos_problema; ?></h3>
                                    </div>
                                    <div class="text-danger">
                                        <i class="bi bi-exclamation-triangle" style="font-size: 2.5rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card stats-card warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Usuários Ativos</h6>
                                        <h3 class="mb-0"><?php echo $total_usuarios; ?></h3>
                                    </div>
                                    <div class="text-warning">
                                        <i class="bi bi-people" style="font-size: 2.5rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-lightning"></i> Ações Rápidas</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <a href="equipamentos.php?action=create" class="btn btn-outline-primary w-100 btn-custom">
                                            <i class="bi bi-plus-circle"></i> Novo Equipamento
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <a href="alas.php?action=create" class="btn btn-outline-success w-100 btn-custom">
                                            <i class="bi bi-plus-circle"></i> Nova Ala
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <a href="equipamentos.php" class="btn btn-outline-info w-100 btn-custom">
                                            <i class="bi bi-list"></i> Ver Equipamentos
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <a href="alas.php" class="btn btn-outline-warning w-100 btn-custom">
                                            <i class="bi bi-list"></i> Ver Alas
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Equipment -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Equipamentos Recentes</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($recent_equipamentos->num_rows > 0): ?>
                                    <div class="row">
                                        <?php while ($equip = $recent_equipamentos->fetch_assoc()): ?>
                                            <div class="col-md-4 mb-3">
                                                <div class="card equipment-card">
                                                    <?php if (!empty($equip['imagem']) && file_exists('../' . $equip['imagem'])): ?>
                                                        <img src="../<?php echo htmlspecialchars($equip['imagem']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($equip['nome']); ?>">
                                                    <?php else: ?>
                                                        <img src="../assets/img/LogoGestCTT.png" class="card-img-top" alt="Sem imagem">
                                                    <?php endif; ?>
                                                    <div class="card-body">
                                                        <h5 class="card-title"><?php echo htmlspecialchars($equip['nome']); ?></h5>
                                                        <p class="card-text">
                                                            <small class="text-muted">
                                                                <i class="bi bi-building"></i> <?php echo htmlspecialchars($equip['ala_nome']); ?>
                                                            </small>
                                                        </p>
                                                        <a href="equipamentos.php?action=view&id=<?php echo $equip['id']; ?>" class="btn btn-sm btn-primary">
                                                            <i class="bi bi-eye"></i> Ver Detalhes
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted text-center">Nenhum equipamento cadastrado ainda.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../app/views/includes/footer.php'; ?>