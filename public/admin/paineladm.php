<?php
// PASSO 1: Proteger a página
// Esta linha garante que apenas administradores logados possam ver esta página.
require_once '../../app/config/auth_admin.php';

// Define o título da página para a aba do navegador
$page_title = 'Painel Administrativo';

// PASSO 2: Incluir os templates visuais
// Inclui o cabeçalho (com <head>, CSS, etc.) e a barra de navegação.
include_once '../../app/views/includes/header.php';
include_once '../../app/views/includes/navbar.php';
?>

<!-- Início do conteúdo principal da página -->
<div class="container mt-5">
    
    <!-- Cabeçalho de Boas-Vindas -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-5">Painel Administrativo</h1>
            <!-- Pega o nome do usuário da sessão para uma saudação personalizada -->
            <p class="lead">Bem-vindo, <?php echo htmlspecialchars($_SESSION['user_name']); ?>. Gerencie o sistema a partir daqui.</p>
        </div>
    </div>

    <!-- Linha com os Cartões de Menu -->
    <div class="row">

        <!-- Cartão para Gerenciar Usuários -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <i class="bi bi-people-fill" style="font-size: 3rem; color: #0d6efd;"></i>
                    <h5 class="card-title mt-3">Gerenciar Usuários</h5>
                    <p class="card-text">Aprove, edite ou remova as contas de usuários do sistema.</p>
                    <!-- O `stretched-link` faz todo o cartão ser clicável -->
                    <a href="usuarios.php" class="btn btn-primary stretched-link">Acessar</a>
                </div>
            </div>
        </div>

        <!-- Cartão para Visualizar Relatórios -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <i class="bi bi-file-earmark-text-fill" style="font-size: 3rem; color: #198754;"></i>
                    <h5 class="card-title mt-3">Visualizar Relatórios</h5>
                    <p class="card-text">Veja os problemas e chamados reportados pelos funcionários.</p>
                    <a href="relatorios.php" class="btn btn-success stretched-link">Acessar</a>
                </div>
            </div>
        </div>

        <!-- Cartão para Gerenciar Prestadores -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <i class="bi bi-person-badge-fill" style="font-size: 3rem; color: #ffc107;"></i>
                    <h5 class="card-title mt-3">Gerenciar Prestadores</h5>
                    <p class="card-text">Adicione ou edite as informações dos prestadores de serviço.</p>
                    <a href="prestadores.php" class="btn btn-warning stretched-link">Acessar</a>
                </div>
            </div>
        </div>
        
        <!-- Adicione outros cartões aqui conforme precisar, por exemplo, para equipamentos, alas, etc. -->

    </div>
</div>
<!-- Fim do conteúdo principal -->

<?php
// Inclui o rodapé da página (com scripts JS, etc.)
include_once '../../app/views/includes/footer.php';
?>
