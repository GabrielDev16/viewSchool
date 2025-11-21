<?php
// Apenas esta linha é necessária no topo.
require_once '../../app/config/auth_user.php'; 

$page_title = 'Painel do Usuário';

// Caminhos corrigidos para subir dois níveis (de /public/user/ para a raiz)
include_once '../../app/views/includes/header.php';

?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center p-5">
                    <!-- A variável $user_name já é definida dentro de auth_user.php -->
                    <h1 class="display-5">Olá, <?php echo htmlspecialchars($user_name); ?>!</h1>
                    <p class="lead">Bem-vindo ao sistema de gestão de equipamentos.</p>
                    <hr class="my-4">
                    <p>Para começar, clique no botão abaixo para encontrar a sala e o equipamento que precisa de atenção.</p>
                    <a class="btn btn-primary btn-lg" href="alas.php" role="button">
                        <i class="bi bi-door-open-fill"></i> Reportar um Problema
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Caminho corrigido
include_once '../../app/views/includes/footer.php';
?>
