<?php
// Apenas esta linha é necessária no topo.
// Ela protege a página e já inclui o 'init.php', nos dando acesso à variável $conn.
require_once '../../app/config/auth_user.php';

// Buscar todas as alas/salas do banco de dados
$query = "SELECT id, nome, descricao FROM ala ORDER BY nome ASC";
$result = mysqli_query($conn, $query); // $conn funciona pois já foi incluída
$alas = mysqli_fetch_all($result, MYSQLI_ASSOC);

$page_title = 'Selecionar Sala';

// Os caminhos dos includes de views também precisam subir dois níveis
include_once '../../app/views/includes/header.php';

?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-door-open"></i> Selecione a Sala</h2>
        <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Voltar ao Painel</a>
    </div>

    <div class="list-group shadow-sm">
        <?php if (count($alas) > 0): ?>
            <?php foreach ($alas as $ala): ?>
                <a href="equipamentos.php?ala_id=<?php echo $ala['id']; ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1"><?php echo htmlspecialchars($ala['nome']); ?></h5>
                        <p class="mb-1 text-muted"><?php echo htmlspecialchars($ala['descricao']); ?></p>
                    </div>
                    <i class="bi bi-chevron-right"></i>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="list-group-item">
                <p class="text-center text-muted mb-0">Nenhuma sala encontrada. Por favor, contate um administrador.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Caminho corrigido aqui também
include_once '../../app/views/includes/footer.php';
?>
