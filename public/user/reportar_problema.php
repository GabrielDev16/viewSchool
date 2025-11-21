<?php
// Protege a página e nos dá a conexão $conn e o ID do usuário logado ($user_id)
require_once '../../app/config/auth_user.php';

// --- Validação do Equipamento ---
// Verifica se um ID de equipamento foi passado pela URL
if (!isset($_GET['equipamento_id']) || !is_numeric($_GET['equipamento_id'])) {
    header('Location: alas.php'); // Se não, volta para a seleção de salas
    exit();
}
$equipamento_id = (int)$_GET['equipamento_id'];

// Busca os detalhes do equipamento para mostrar na página
$stmt_equip = $conn->prepare("SELECT nome FROM equipamentos WHERE id = ?");
$stmt_equip->bind_param("i", $equipamento_id);
$stmt_equip->execute();
$result_equip = $stmt_equip->get_result();
if ($result_equip->num_rows === 0) {
    header('Location: alas.php'); // Se o equipamento não existe, volta
    exit();
}
$equipamento = $result_equip->fetch_assoc();
$nome_equipamento = $equipamento['nome'];


// --- Processamento do Formulário ---
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);

    // Validação simples
    if (empty($titulo) || empty($descricao)) {
        $error_message = 'Por favor, preencha o título e a descrição do problema.';
    } else {
        // Insere o problema no banco de dados
        $sql_insert = "INSERT INTO problemas (fk_equipamento, fk_usuario, titulo, descricao) VALUES (?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        // Usa o $user_id que vem do auth_user.php
        $stmt_insert->bind_param("iiss", $equipamento_id, $user_id, $titulo, $descricao);

        if ($stmt_insert->execute()) {
            // Opcional: Atualiza o status do equipamento para 'problema'
            $conn->query("UPDATE equipamentos SET status = 'problema' WHERE id = $equipamento_id");
            
            $success_message = 'Problema reportado com sucesso! Um administrador será notificado.';
        } else {
            $error_message = 'Ocorreu um erro ao salvar o problema. Tente novamente.';
        }
        $stmt_insert->close();
    }
}


$page_title = 'Reportar Problema em ' . htmlspecialchars($nome_equipamento);
include_once '../../app/views/includes/header.php';

?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3><i class="bi bi-exclamation-triangle"></i> Reportar Problema</h3>
                </div>
                <div class="card-body">
                    <h5 class="card-subtitle mb-2 text-muted">Equipamento: <?php echo htmlspecialchars($nome_equipamento); ?></h5>
                    <p>Por favor, descreva o problema encontrado com o máximo de detalhes possível.</p>
                    <hr>

                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success">
                            <?php echo $success_message; ?>
                            <div class="mt-3">
                                <a href="index.php" class="btn btn-primary">Voltar ao Painel</a>
                                <a href="alas.php" class="btn btn-secondary">Reportar Outro Problema</a>
                            </div>
                        </div>
                    <?php elseif (!empty($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>

                    <?php if (empty($success_message)): // Mostra o formulário apenas se ainda não foi enviado com sucesso ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="titulo" class="form-label">Título do Problema</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Ex: Computador não liga" required>
                            </div>
                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição Detalhada</label>
                                <textarea class="form-control" id="descricao" name="descricao" rows="5" placeholder="Ex: Tentei ligar o computador pressionando o botão, mas nada acontece. A tela permanece preta e não há nenhum barulho vindo da CPU." required></textarea>
                            </div>
                            <div class="d-flex justify-content-end">
                                <!-- 
                                botão para reornar com id que tinha, sistema que era usado antes do retornar para o arquivo anterior
                                <a href="equipamentos.php?ala_id=<?php echo $ala_id_do_equipamento_se_tiver; ?>" class="btn btn-secondary me-2">Cancelar</a> -->
                                
                                <a href="equipamentos.php" class="btn btn-secondary me-2">Cancelar</a>
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-send-fill"></i> Enviar Relatório
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once '../../app/views/includes/footer.php';
?>
