<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Ejecutivo</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap y Chart -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            background: linear-gradient(135deg, #eef2f7, #e3eaf3);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* NAVBAR */
        .navbar {
            background: linear-gradient(90deg, #1f2d3d, #2c3e50);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .logo {
            height: 40px;
            margin-right: 10px;
        }

        /* MENU */
        .menu-link {
            color: #cfd8dc;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .menu-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .menu-active {
            background: #17a2b8;
            color: #fff !important;
        }

        /* KPI CARDS */
        .kpi-card {
            border-radius: 15px;
            padding: 20px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .kpi-blue {
            background: linear-gradient(135deg, #17a2b8, #138496);
        }

        .kpi-green {
            background: linear-gradient(135deg, #28a745, #1e7e34);
        }

        .metric {
            font-size: 36px;
            font-weight: bold;
        }

        .kpi-icon {
            position: absolute;
            right: 15px;
            bottom: 10px;
            font-size: 50px;
            opacity: 0.2;
        }

        /* CARDS */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        /* LISTA */
        .list-group-item {
            border: none;
            border-bottom: 1px solid #eee;
        }

        /* FOOTER */
        .footer {
            background: #1f2d3d;
            color: #cfd8dc;
            padding: 15px;
            text-align: center;
            font-size: 13px;
            margin-top: 40px;
        }
    </style>

</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg shadow-sm">
        <div class="container-fluid">

            <!-- LOGO -->
            <span class="navbar-brand d-flex align-items-center text-white">
                <img src="img/logo.png" class="logo">
                CONTROL DE SALIDA DE INSUMOS DE ALMACEN
            </span>

            <!-- BOTÓN RESPONSIVE -->
            <button class="navbar-toggler bg-light" data-bs-toggle="collapse" data-bs-target="#menu">
                ☰
            </button>

            <!-- MENÚ -->
            <div class="collapse navbar-collapse justify-content-end" id="menu">
                <div class="d-flex flex-column flex-lg-row gap-2 mt-2 mt-lg-0">

                    <!-- DASHBOARD -->
                    <a href="dashboard.php" class="menu-link">📊 Dashboard</a>

                    <!-- INSUMOS -->
                    <div class="dropdown">
                        <a class="menu-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            📦 Insumos
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="./interface/salidas.php">📦 Salidas</a></li>
                            <li><a class="dropdown-item" href="./interface/historial.php">📋 Historial</a></li>
                            <li><a class="dropdown-item" href="./interface/insumos.php">🧰 Ingresar</a></li>
                        </ul>
                    </div>

                    <!-- HERRAMIENTAS -->
                    <div class="dropdown">
                        <a class="menu-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            🛠 Herramientas
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="./interface/herramientas_salida.php">📦 Salidas</a></li>
                            <li><a class="dropdown-item" href="./interface/herramientas_historial.php">📋 Historial</a></li>
                            <li><a class="dropdown-item" href="./interface/herramientas.php">🧰 Ingresar</a></li>
                        </ul>
                    </div>

                    <!-- PERSONAL -->
                    <a href="./interface/personas.php" class="menu-link">👤 Personal</a>

                </div>
            </div>

        </div>
    </nav>

    <!-- CONTENIDO -->
    <div class="container mt-4">

        <h4 class="mb-1">📊 Dashboard Ejecutivo</h4>
        <p class="text-muted">Resumen general del movimiento de insumos en tiempo real</p>

        <!-- KPI -->
        <div class="row g-3">

            <div class="col-md-6">
                <div class="kpi-card kpi-blue">
                    <h6>Total Salidas</h6>
                    <div id="total_salidas" class="metric">0</div>
                    <div class="kpi-icon">📦</div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="kpi-card kpi-green">
                    <h6>Total Insumos</h6>
                    <div id="total_insumos" class="metric">0</div>
                    <div class="kpi-icon">🧰</div>
                </div>
            </div>

        </div>

        <!-- GRÁFICAS -->
        <div class="row mt-4 g-3">

            <div class="col-md-6">
                <div class="card p-3">
                    <h6>📈 Salidas por Día</h6>
                    <canvas id="grafica"></canvas>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card p-3">
                    <h6>🔥 Top Insumos</h6>
                    <canvas id="topChart"></canvas>
                </div>
            </div>

        </div>

        <!-- ÚLTIMAS -->
        <div class="card p-3 mt-4">
            <h6>🕒 Últimas Salidas</h6>
            <ul id="ultimas" class="list-group"></ul>
        </div>

    </div>

    <!-- JS -->
    <script>
        fetch("ajax/dashboard.php")
            .then(res => res.json())
            .then(data => {

                // MÉTRICAS
                document.getElementById("total_salidas").innerText = data.total_salidas;
                document.getElementById("total_insumos").innerText = data.total_insumos;

                // GRÁFICA LÍNEA
                let labels = data.salidas_dia.map(i => i.fecha);
                let valores = data.salidas_dia.map(i => i.total);

                new Chart(document.getElementById("grafica"), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: valores,
                            borderColor: "#17a2b8",
                            backgroundColor: "rgba(23,162,184,0.2)",
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });

                // GRÁFICA BARRAS
                let nombres = data.top_insumos.map(i => i.nombre);
                let cantidades = data.top_insumos.map(i => i.total);

                new Chart(document.getElementById("topChart"), {
                    type: 'bar',
                    data: {
                        labels: nombres,
                        datasets: [{
                            label: 'Consumo',
                            data: cantidades,
                            backgroundColor: "#28a745"
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });

                // ÚLTIMAS
                let html = "";
                data.ultimas.forEach(u => {
                    html += `
                        <li class="list-group-item d-flex justify-content-between">
                            <span>${u.nombre}</span>
                            <small class="text-muted">${u.fecha}</small>
                        </li>`;
                });

                document.getElementById("ultimas").innerHTML = html;

            });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- FOOTER -->
    <footer class="footer">
        Sistema de Almacén © 2026 | Desarrollado por Javier Anel Tapia | Versión 2.0
    </footer>

</body>

</html>