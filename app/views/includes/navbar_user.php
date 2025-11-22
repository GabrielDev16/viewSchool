<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo BASE_URL; ?>index.php">
            <img src="<?php echo BASE_URL; ?>assets/img/LogoGestCTT.png" alt="GestCTT Logo">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarUser" aria-controls="navbarUser" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarUser">
            <ul class="navbar-nav ms-auto">

                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'user'): ?>

                    <!-- Início -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>user/index.php">
                            <i class="bi bi-house-door"></i> Início
                        </a>
                    </li>

                    <!-- Alas -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>user/alas.php">
                            <i class="bi bi-houses"></i> Alas
                        </a>
                    </li>

                    <!-- Equipamentos -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>user/equipamentos.php">
                            <i class="bi bi-tools"></i> Equipamentos
                        </a>
                    </li>

                    <!-- Reportar Problema -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>user/reportar_problema.php">
                            <i class="bi bi-exclamation-octagon"></i> Reportar Problema
                        </a>
                    </li>

                    <!-- Menu do Usuário -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdownMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> 
                            <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdownMenu">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>perfil.php">Meu Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>logout.php">Sair</a></li>
                        </ul>
                    </li>

                <?php else: ?>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>login.php">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </li>

                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>