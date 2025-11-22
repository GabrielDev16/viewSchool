<?php
require_once '../app/config/auth_admin.php';
require_once '../app/config/init.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Dashboard stats
$page_title = 'Dashboard';

$total_alas = $conn->query("SELECT COUNT(*) as total FROM ala WHERE status='ativo'")->fetch_assoc()['total'];
$total_equipamentos = $conn->query("SELECT COUNT(*) as total FROM equipamentos WHERE status='ativo'")->fetch_assoc()['total'];
$equipamentos_problema = $conn->query("SELECT COUNT(*) as total FROM equipamentos WHERE status='problema'")->fetch_assoc()['total'];
$total_usuarios = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE status='ativo'")->fetch_assoc()['total'];

// Recent equipment
$recent_equipamentos = $conn->query("SELECT e.*, a.nome as ala_nome FROM equipamentos e LEFT JOIN ala a ON e.fk_ala=a.id WHERE e.status='ativo' ORDER BY e.id DESC LIMIT 6");

// Gráfico 1: Equipamentos por status
$equip_status = $conn->query("SELECT status, COUNT(*) AS total FROM equipamentos GROUP BY status");
$labelsEquip = [];
$dataEquip = [];
while ($row = $equip_status->fetch_assoc()) {
    $labelsEquip[] = ucfirst($row['status']);
    $dataEquip[] = (int)$row['total'];
}
$labelsEquip_json = json_encode($labelsEquip);
$dataEquip_json = json_encode($dataEquip);

// Gráfico 2: Evolução mensal dos equipamentos
$equip_mes = $conn->query("SELECT DATE_FORMAT(created_at,'%Y-%m') as mes, status, COUNT(*) as total FROM equipamentos GROUP BY mes, status ORDER BY mes");
$meses = [];
$dadosMes = ['ativo' => [], 'inativo' => [], 'problema' => []];
while ($row = $equip_mes->fetch_assoc()) {
    $mes = $row['mes'];
    $status = $row['status'];
    $total = (int)$row['total'];

    if (!in_array($mes, $meses)) {
        $meses[] = $mes;
        foreach ($dadosMes as $s => $arr) {
            $dadosMes[$s][$mes] = 0;
        }
    }
    $dadosMes[$status][$mes] = $total;
}
$labelsMes_json = json_encode($meses);
$ativo_json = json_encode(array_values($dadosMes['ativo']));
$inativo_json = json_encode(array_values($dadosMes['inativo']));
$problema_json = json_encode(array_values($dadosMes['problema']));
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

                <!-- Gráficos -->
                <div class="container mb-2">
                    <div class="row">
                        <!-- Gráfico 1 -->
                        <div class="col-md-6 col-12 card d-flex justify-content-center align-items-center mb-4">
                            <h3 class="text-center mt-2">Situação por Equipamentos</h3>
                            <div class="container m-5">
                                <div class="row d-flex justify-content-center align-items-center">
                                    <div class="col-md-6 col-12">
                                        <canvas id="statusChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Gráfico 2 -->
                        <div class="col-md-6 col-12 card d-flex justify-content-center align-items-center mb-4">
                            <h3 class="text-center mt-2">Evolução Mensal</h3>
                            <div class="container m-5">
                                <div class="row d-flex justify-content-center align-items-center">
                                    <div class="col-md-10 col-12">
                                        <canvas id="evolucaoChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    // Gráfico 1: Situação por Equipamentos
                    const ctxStatus = document.getElementById('statusChart').getContext('2d');
                    const statusChart = new Chart(ctxStatus, {
                        type: 'pie',
                        data: {
                            labels: <?php echo $labelsEquip_json; ?>,
                            datasets: [{
                                label: 'Quantidade de Equipamentos',
                                data: <?php echo $dataEquip_json; ?>,
                                backgroundColor: [
                                    'rgba(75, 192, 192, 0.6)',
                                    'rgba(255, 205, 86, 0.6)',
                                    'rgba(255, 99, 132, 0.6)'
                                ],
                                borderColor: [
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(255, 205, 86, 1)',
                                    'rgba(255, 99, 132, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });

                    // Gráfico 2: Evolução Mensal
                    const ctxEvolucao = document.getElementById('evolucaoChart').getContext('2d');
                    const evolucaoChart = new Chart(ctxEvolucao, {
                        type: 'line',
                        data: {
                            labels: <?php echo $labelsMes_json; ?>,
                            datasets: [{
                                    label: 'Ativo',
                                    data: <?php echo $ativo_json; ?>,
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    fill: false,
                                    tension: 0.2
                                },
                                {
                                    label: 'Inativo',
                                    data: <?php echo $inativo_json; ?>,
                                    borderColor: 'rgba(255, 205, 86, 1)',
                                    backgroundColor: 'rgba(255, 205, 86, 0.2)',
                                    fill: false,
                                    tension: 0.2
                                },
                                {
                                    label: 'Problema',
                                    data: <?php echo $problema_json; ?>,
                                    borderColor: 'rgba(255, 99, 132, 1)',
                                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                    fill: false,
                                    tension: 0.2
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                </script>


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