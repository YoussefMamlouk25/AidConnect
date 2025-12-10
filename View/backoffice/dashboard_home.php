<?php
require_once '../../config.php';

// Get total users
$usersCount = $conn->query("SELECT COUNT(*) FROM user")->fetchColumn();

// Get total posts
$postsCount = $conn->query("SELECT COUNT(*) FROM posts")->fetchColumn();

// Get resolved posts
$resolvedCount = $conn->query("SELECT COUNT(*) FROM posts WHERE status = 'resolved'")->fetchColumn();

// Simple engagement = resolved + pending
$engagement = $postsCount > 0 ? round(($resolvedCount / $postsCount) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Home</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        body { background: transparent; }

        /* STAT CARDS */
        .stat-card {
            border-radius: 18px;
            padding: 22px;
            color: white;
            height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 6px 18px rgba(0,0,0,0.12);
        }

        .purple { background: linear-gradient(135deg,#6a5af9,#8367f7); }
        .yellow { background: linear-gradient(135deg,#ffc56f,#ff9d3c); }
        .green  { background: linear-gradient(135deg,#36d399,#22b37a); }
        .blue   { background: linear-gradient(135deg,#3c8bfd,#1e63e9); }

        .chart-box {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
    </style>
</head>

<body>

<h3 class="mb-4 text-primary">Statistiques générales</h3>

<!-- STAT CARDS -->
<div class="row g-3 mb-4">

    <div class="col-md-3">
        <div class="stat-card purple">
            <div>
                <h5>Total Utilisateurs</h5>
                <h2><?= $usersCount ?></h2>
            </div>
            <i class="bi bi-people fs-2"></i>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card yellow">
            <div>
                <h5>Total Posts</h5>
                <h2><?= $postsCount ?></h2>
            </div>
            <i class="bi bi-file-text fs-2"></i>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card green">
            <div>
                <h5>Posts Résolus</h5>
                <h2><?= $resolvedCount ?></h2>
            </div>
            <i class="bi bi-check-circle fs-2"></i>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card blue">
            <div>
                <h5>Engagement</h5>
                <h2><?= $engagement ?>%</h2>
            </div>
            <i class="bi bi-bar-chart-line fs-2"></i>
        </div>
    </div>

</div>

<!-- CHARTS -->
<div class="row g-4">

    <div class="col-md-8">
        <div class="chart-box">
            <h5>Activité des Posts – 7 derniers jours</h5>
            <div id="postsChart" style="height: 300px;"></div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="chart-box">
            <h5>Statut des Posts</h5>
            <div id="statusChart" style="height: 300px;"></div>
        </div>
    </div>

</div>

<script>
    // Dummy activity data (replace later with real stats)
    var options1 = {
        chart: { type: 'line', height: 280 },
        series: [{ name: 'Posts', data: [3,4,2,5,3,6,4] }],
        xaxis: { categories: ['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'] },
        colors: ['#3c8bfd']
    };
    new ApexCharts(document.querySelector("#postsChart"), options1).render();


    // Donut chart with real DB values
    var options2 = {
        chart: { type: 'donut', height: 280 },
        labels: ['Résolus', 'Actifs'],
        series: [<?= $resolvedCount ?>, <?= $postsCount - $resolvedCount ?>],
        colors: ['#36d399', '#3c8bfd']
    };
    new ApexCharts(document.querySelector("#statusChart"), options2).render();
</script>

</body>
</html>
