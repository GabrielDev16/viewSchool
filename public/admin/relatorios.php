<?php
require_once '../../app/config/init.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$page_title = 'Relatórios';

// Get statistics
$total_alas = $conn->query("SELECT COUNT(*) as total FROM ala WHERE status = 'ativo'")->fetch_assoc()['total'];
$total_equipamentos = $conn->query("SELECT COUNT(*) as total FROM equipamentos WHERE status = 'ativo'")->fetch_assoc()['total'];
$equipamentos_problema = $conn->query("SELECT COUNT(*) as total FROM equipamentos WHERE status = 'problema'")->fetch_assoc()['total'];
$equipamentos_inativos = $conn->query("SELECT COUNT(*) as total FROM equipamentos WHERE status = 'inativo'")->fetch_assoc()['total'];
$total_usuarios = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE status = 'ativo'")->fetch_assoc()['total'];
$usuarios_pendentes = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE status = 'pendente'")->fetch_assoc()['total'];
$total_prestadores = $conn->query("SELECT COUNT(*) as total FROM prestadores WHERE status = 'ativo'")->fetch_assoc()['total'];

// Equipment by type
$equip_by_type_sql = "SELECT tipo, COUNT(*) as total FROM equipamentos WHERE status = 'ativo' GROUP BY tipo ORDER BY total DESC";
$equip_by_type = $conn->query($equip_by_type_sql);

// Equipment by ala
$equip_by_ala_sql = "SELECT a.nome, COUNT(e.id) as total 
                     FROM ala a 
                     LEFT JOIN equipamentos e ON a.id = e.fk_ala AND e.status = 'ativo'
                     WHERE a.status = 'ativo'
                     GROUP BY a.id, a.nome
                     ORDER BY total DESC";
$equip_by_ala = $conn->query($equip_by_ala_sql);

// Recent activity (last 10 equipment added)
$recent_activity_sql = "SELECT e.nome, e.tipo, a.nome as ala_nome, e.created_at 
                        FROM equipamentos e 
                        LEFT JOIN ala a ON e.fk_ala = a.id 
                        ORDER BY e.created_at DESC 
                        LIMIT 10";
$recent_activity = $conn->query($recent_activity_sql);
?>

<?php include '../../app/views/includes/header.php'; ?>
<?php include '../../app/views/includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <main class="col-md-12 ms-sm-auto px-md-4">
            <div class="content-wrapper">
                <div class="page-header">
                    <h2><i class="bi bi-graph-up"></i> Relatórios</h2>
                    <p class="text-muted">Visão geral do sistema</p>
                </div>
                
                <!-- Statistics Overview -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Estatísticas Gerais</h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-3 mb-3">
                                        <div class="p-3 border rounded">
                                            <h3 class="text-primary"><?php echo $total_alas; ?></h3>
                                            <p class="mb-0 text-muted">Alas Ativas</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="p-3 border rounded">
                                            <h3 class="text-success"><?php echo $total_equipamentos; ?></h3>
                                            <p class="mb-0 text-muted">Equipamentos Ativos</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="p-3 border rounded">
                                            <h3 class="text-danger"><?php echo $equipamentos_problema; ?></h3>
                                            <p class="mb-0 text-muted">Com Problema</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="p-3 border rounded">
                                            <h3 class="text-warning"><?php echo $total_usuarios; ?></h3>
                                            <p class="mb-0 text-muted">Usuários Ativos</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <!-- Equipment by Type -->
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Equipamentos por Tipo</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($equip_by_type->num_rows > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Tipo</th>
                                                    <th class="text-end">Quantidade</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = $equip_by_type->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($row['tipo']); ?></td>
                                                        <td class="text-end"><strong><?php echo $row['total']; ?></strong></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted text-center">Nenhum dado disponível.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Equipment by Ala -->
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-building"></i> Equipamentos por Ala</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($equip_by_ala->num_rows > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Ala</th>
                                                    <th class="text-end">Quantidade</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = $equip_by_ala->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($row['nome']); ?></td>
                                                        <td class="text-end"><strong><?php echo $row['total']; ?></strong></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted text-center">Nenhum dado disponível.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Atividades Recentes</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($recent_activity->num_rows > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Equipamento</th>
                                                    <th>Tipo</th>
                                                    <th>Ala</th>
                                                    <th>Data de Cadastro</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = $recent_activity->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($row['nome']); ?></td>
                                                        <td><span class="badge bg-info"><?php echo htmlspecialchars($row['tipo']); ?></span></td>
                                                        <td><?php echo htmlspecialchars($row['ala_nome']); ?></td>
                                                        <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted text-center">Nenhuma atividade recente.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Export Options -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-download"></i> Exportar Dados</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Exporte os dados do sistema para análise externa.</p>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                                        <i class="bi bi-printer"></i> Imprimir Relatório
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../../app/views/includes/footer.php'; ?>
