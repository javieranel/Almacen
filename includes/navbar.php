<body>

<!-- 🔷 NAVBAR -->
<nav class="navbar navbar-expand-lg shadow-sm">
    <div class="container-fluid">

        <!-- LOGO -->
        <span class="navbar-brand d-flex align-items-center text-white">
            <img src="../img/logo.png" class="logo">
            CONTROL DE SALIDA DE INSUMOS Y HERRAMIENTAS
        </span>

        <!-- BOTÓN RESPONSIVE -->
        <button class="navbar-toggler bg-light" data-bs-toggle="collapse" data-bs-target="#menu">
            ☰
        </button>

        <!-- MENÚ -->
        <div class="collapse navbar-collapse justify-content-end" id="menu">
    <div class="d-flex flex-column flex-lg-row gap-2 mt-2 mt-lg-0">

        <!-- DASHBOARD -->
        <a href="../dashboard.php" class="menu-link">📊 Dashboard</a>

        <!-- INSUMOS -->
        <div class="dropdown">
            <a class="menu-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                📦 Insumos
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="./salidas.php">📦 Salidas</a></li>
                <li><a class="dropdown-item" href="./historial.php">📋 Historial</a></li>
                <li><a class="dropdown-item" href="./insumos.php">🧰 Ingresar</a></li>
            </ul>
        </div>

        <!-- HERRAMIENTAS -->
        <div class="dropdown">
            <a class="menu-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                🛠 Herramientas
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="./herramientas_salida.php">📦 Salidas</a></li>
                <li><a class="dropdown-item" href="./herramientas_historial.php">📋 Historial</a></li>
                <li><a class="dropdown-item" href="./herramientas.php">🧰 Ingresar</a></li>
            </ul>
        </div>

        <!-- PERSONAL -->
        <a href="./personas.php" class="menu-link">👤 Personal</a>

    </div>
</div>

    </div>
</nav>