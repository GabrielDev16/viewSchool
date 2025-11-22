<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">

        <a class="navbar-brand" href="<?php echo BASE_URL; ?>user/index.php">
            <img src="<?php echo BASE_URL; ?>../assets/img/LogoGestCTT.png" alt="GestCTT Logo" style="height:40px;">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarUser" aria-controls="navbarUser"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarUser">
            <ul class="navbar-nav ms-auto">

                <!-- Página inicial do usuário -->
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


                <!-- Menu do usuário -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown"
                       role="button" data-bs-toggle="dropdown" aria-expanded="false">
                       <i class="bi bi-person-circle"></i> 
                       <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuário'); ?>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>user/perfil.php">
                                Meu Perfil
                            </a>
                        </li>

                        <li><hr class="dropdown-divider"></li>

                        <li>
                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>logout.php">
                                Sair
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>

    </div>
</nav>
