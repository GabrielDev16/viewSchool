<?php
// Protege a página e nos dá a conexão $conn.
require_once '../../app/config/auth_user.php';

// Verificar se o ID da ala foi fornecido na URL e é um número.
if (!isset($_GET['ala_id']) || !is_numeric($_GET['ala_id'])) {
    header('Location: alas.php');
    exit();
}

$ala_id = (int)$_GET['ala_id'];

// Buscar o nome da ala selecionada para exibir no título.
$stmt_ala = $conn->prepare("SELECT nome FROM ala WHERE id = ?");
$stmt_ala->bind_param("i", $ala_id);
$stmt_ala->execute();
$result_ala = $stmt_ala->get_result();
if ($result_ala->num_rows === 0) {
    header('Location: alas.php'); // Se a ala não for válida, volta.
    exit();
}
$ala = $result_ala->fetch_assoc();
$nome_ala = $ala['nome'];

// CONSULTA CORRIGIDA: Usa a tabela 'equipamentos', a coluna 'fk_ala' e não busca 'numero_serie'.
$stmt_equip = $conn->prepare("SELECT id, nome, descricao FROM equipamentos WHERE fk_ala = ? AND status = 'ativo' ORDER BY nome ASC");
$stmt_equip->bind_param("i", $ala_id);
$stmt_equip->execute();
$equipamentos = $stmt_equip->get_result()->fetch_all(MYSQLI_ASSOC);

$page_title = 'Equipamentos em ' . htmlspecialchars($nome_ala);

include_once '../../app/views/includes/header.php';

?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-pc-display"></i> Equipamentos em "<?php echo htmlspecialchars($nome_ala); ?>"</h2>
        <a href="alas.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Trocar de Sala</a>
    </div>

    <p class="text-muted">Selecione o equipamento para reportar um problema.</p>

    <div class="list-group shadow-sm">
        <?php if (count($equipamentos) > 0): ?>
            <?php foreach ($equipamentos as $equipamento): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1"><?php echo htmlspecialchars($equipamento['nome']); ?></h5>
                        <!-- HTML CORRIGIDO: Exibe a 'descricao' em vez de 'numero_serie' -->
                        <small class="text-muted"><?php echo htmlspecialchars($equipamento['descricao'] ?: 'Sem descrição'); ?></small>
                    </div>
                    <!-- Este link levará para o formulário de reporte, passando o ID do equipamento -->
                    <a href="reportar_problema.php?equipamento_id=<?php echo $equipamento['id']; ?>" class="btn btn-danger">
                        <i class="bi bi-exclamation-triangle-fill"></i> Reportar Problema
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="list-group-item">
                <p class="text-center text-muted mb-0">Nenhum equipamento ativo encontrado nesta sala.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
include_once '../../app/views/includes/footer.php';
?>
