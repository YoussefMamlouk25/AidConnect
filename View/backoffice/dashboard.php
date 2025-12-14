<?php
require_once 'layout.php';
require_once '../../config.php';
require_once '../../Model/Event.php';
require_once '../../Model/user.php';
require_once '../../Controller/postcontroller.php';
require_once '../../Controller/offercontroller.php';

// Récupérer les statistiques
$db = Config::getConnexion();

// Compter les posts
$postController = new PostController();
$allPosts = $postController->getAllPosts();
$totalPosts = count($allPosts);

// Compter les offres
$offerController = new OfferController();
$allOffers = $offerController->getAllOffers();
$totalOffers = count($allOffers);

// Compter les utilisateurs
$userModel = new UserModel();
$allUsers = $userModel->getAllUsers();
$totalUsers = count($allUsers);

// Compter les événements
$eventModel = new Event($db);
$stmt = $eventModel->readAll();
$allEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalEvents = count($allEvents);

// Statistiques détaillées - Posts par région
$postsByRegion = [];
foreach ($allPosts as $post) {
    $region = $post['region'] ?? 'Non spécifiée';
    $postsByRegion[$region] = ($postsByRegion[$region] ?? 0) + 1;
}

// Statistiques détaillées - Offres par type de don
$offersByType = [];
foreach ($allOffers as $offer) {
    $type = $offer['type_don'] ?? 'Non spécifié';
    $offersByType[$type] = ($offersByType[$type] ?? 0) + 1;
}

// Statistiques détaillées - Utilisateurs par rôle
$usersByRole = [];
foreach ($allUsers as $user) {
    $role = $user['role'] ?? 'volunteer';
    $usersByRole[$role] = ($usersByRole[$role] ?? 0) + 1;
}

// Statistiques détaillées - Utilisateurs par région
$usersByRegion = [];
foreach ($allUsers as $user) {
    $region = $user['region'] ?? 'Non spécifiée';
    $usersByRegion[$region] = ($usersByRegion[$region] ?? 0) + 1;
}

// Statistiques temporelles - Posts par mois (6 derniers mois)
$postsByMonth = [];
$months = [];
for ($i = 5; $i >= 0; $i--) {
    $date = date('Y-m', strtotime("-$i months"));
    $months[] = date('M Y', strtotime("-$i months"));
    $postsByMonth[$date] = 0;
}
foreach ($allPosts as $post) {
    if (isset($post['date_creation'])) {
        $month = date('Y-m', strtotime($post['date_creation']));
        if (isset($postsByMonth[$month])) {
            $postsByMonth[$month]++;
        }
    }
}

// Statistiques temporelles - Offres par mois
$offersByMonth = [];
for ($i = 5; $i >= 0; $i--) {
    $date = date('Y-m', strtotime("-$i months"));
    $offersByMonth[$date] = 0;
}
// Note: Les offres n'ont pas de date_creation dans la structure actuelle, on utilisera id_offer comme approximation
// ou on peut laisser à 0 si pas de date disponible

// Statistiques temporelles - Utilisateurs par mois
$usersByMonth = [];
for ($i = 5; $i >= 0; $i--) {
    $date = date('Y-m', strtotime("-$i months"));
    $usersByMonth[$date] = 0;
}
foreach ($allUsers as $user) {
    if (isset($user['date_creation'])) {
        $month = date('Y-m', strtotime($user['date_creation']));
        if (isset($usersByMonth[$month])) {
            $usersByMonth[$month]++;
        }
    }
}

// Préparer les données JSON pour les graphiques
$postsByRegionData = json_encode(array_values($postsByRegion));
$postsByRegionLabels = json_encode(array_keys($postsByRegion));

$offersByTypeData = json_encode(array_values($offersByType));
$offersByTypeLabels = json_encode(array_keys($offersByType));

$usersByRoleData = json_encode(array_values($usersByRole));
$usersByRoleLabels = json_encode(array_keys($usersByRole));

$usersByRegionData = json_encode(array_values($usersByRegion));
$usersByRegionLabels = json_encode(array_keys($usersByRegion));

$postsMonthlyData = json_encode(array_values($postsByMonth));
$offersMonthlyData = json_encode(array_values($offersByMonth));
$usersMonthlyData = json_encode(array_values($usersByMonth));
$monthsLabels = json_encode($months);

// Statistiques supplémentaires
$totalLikesPosts = 0;
$totalCommentsPosts = 0;
foreach ($allPosts as $post) {
    $totalLikesPosts += (int)($post['likes_count'] ?? 0);
    $totalCommentsPosts += (int)($post['comments_count'] ?? 0);
}

$totalLikesOffers = 0;
foreach ($allOffers as $offer) {
    $totalLikesOffers += (int)($offer['likes_count'] ?? 0);
}

$postsWithMedia = 0;
foreach ($allPosts as $post) {
    if (!empty($post['media'])) {
        $postsWithMedia++;
    }
}

$offersWithMedia = 0;
foreach ($allOffers as $offer) {
    if (!empty($offer['media'])) {
        $offersWithMedia++;
    }
}

$content = '
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Bienvenue dans le Dashboard AidConnect 👋</h4>
                </div>
            </div>
            <div class="card-body">
                <p class="mb-4">Vue d\'ensemble complète des statistiques de la plateforme avec graphiques et analyses détaillées.</p>
            </div>
        </div>
    </div>
</div>

<!-- Cartes de statistiques principales -->
<div class="row mt-4">
    <!-- Statistique Posts -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="ri-file-list-3-line" style="font-size: 28px;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0 text-muted">Posts</h6>
                        <h3 class="mb-0">' . $totalPosts . '</h3>
                        <small class="text-muted">
                            <i class="ri-heart-line"></i> ' . $totalLikesPosts . ' likes | 
                            <i class="ri-chat-3-line"></i> ' . $totalCommentsPosts . ' commentaires
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistique Offres -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar avatar-md bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="ri-price-tag-3-line" style="font-size: 28px;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0 text-muted">Offres</h6>
                        <h3 class="mb-0">' . $totalOffers . '</h3>
                        <small class="text-muted">
                            <i class="ri-heart-line"></i> ' . $totalLikesOffers . ' likes
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistique Utilisateurs -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar avatar-md bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="ri-user-line" style="font-size: 28px;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0 text-muted">Utilisateurs</h6>
                        <h3 class="mb-0">' . $totalUsers . '</h3>
                        <small class="text-muted">
                            ' . ($usersByRole['admin'] ?? 0) . ' admins | ' . ($usersByRole['volunteer'] ?? 0) . ' volunteers
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistique Événements -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar avatar-md bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="ri-calendar-line" style="font-size: 28px;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0 text-muted">Événements</h6>
                        <h3 class="mb-0">' . $totalEvents . '</h3>
                        <small class="text-muted">Événements actifs</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques en courbes - Tendances temporelles -->
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ri-line-chart-line me-2"></i>Tendances temporelles (6 derniers mois)
                </h5>
            </div>
            <div class="card-body">
                <canvas id="trendsChart" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques circulaires et statistiques détaillées -->
<div class="row mt-4">
    <!-- Posts par région -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ri-pie-chart-line me-2"></i>Répartition des Posts par Région
                </h5>
            </div>
            <div class="card-body">
                <canvas id="postsRegionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Offres par type -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ri-pie-chart-2-line me-2"></i>Répartition des Offres par Type de Don
                </h5>
            </div>
            <div class="card-body">
                <canvas id="offersTypeChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Utilisateurs par rôle -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ri-pie-chart-fill me-2"></i>Répartition des Utilisateurs par Rôle
                </h5>
            </div>
            <div class="card-body">
                <canvas id="usersRoleChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Utilisateurs par région -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ri-pie-chart-box-line me-2"></i>Répartition des Utilisateurs par Région
                </h5>
            </div>
            <div class="card-body">
                <canvas id="usersRegionChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques supplémentaires -->
<div class="row mt-4">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ri-bar-chart-line me-2"></i>Statistiques des Posts
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="p-3 bg-light rounded">
                            <h4 class="mb-0 text-primary">' . $postsWithMedia . '</h4>
                            <small class="text-muted">Posts avec média</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="p-3 bg-light rounded">
                            <h4 class="mb-0 text-primary">' . ($totalPosts - $postsWithMedia) . '</h4>
                            <small class="text-muted">Posts sans média</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded">
                            <h4 class="mb-0 text-success">' . ($totalPosts > 0 ? round(($totalLikesPosts / $totalPosts), 1) : 0) . '</h4>
                            <small class="text-muted">Likes moyen/post</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded">
                            <h4 class="mb-0 text-info">' . ($totalPosts > 0 ? round(($totalCommentsPosts / $totalPosts), 1) : 0) . '</h4>
                            <small class="text-muted">Commentaires moyen/post</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ri-bar-chart-box-line me-2"></i>Statistiques des Offres
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="p-3 bg-light rounded">
                            <h4 class="mb-0 text-success">' . $offersWithMedia . '</h4>
                            <small class="text-muted">Offres avec média</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="p-3 bg-light rounded">
                            <h4 class="mb-0 text-success">' . ($totalOffers - $offersWithMedia) . '</h4>
                            <small class="text-muted">Offres sans média</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded">
                            <h4 class="mb-0 text-warning">' . ($totalOffers > 0 ? round(($totalLikesOffers / $totalOffers), 1) : 0) . '</h4>
                            <small class="text-muted">Likes moyen/offre</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded">
                            <h4 class="mb-0 text-info">' . count($offersByType) . '</h4>
                            <small class="text-muted">Types de dons différents</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
';

renderBackofficeLayout('Dashboard', $content, [], []);
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Configuration des couleurs
    const colors = {
        primary: 'rgba(13, 110, 253, 0.8)',
        success: 'rgba(25, 135, 84, 0.8)',
        info: 'rgba(13, 202, 240, 0.8)',
        warning: 'rgba(255, 193, 7, 0.8)',
        danger: 'rgba(220, 53, 69, 0.8)',
        secondary: 'rgba(108, 117, 125, 0.8)'
    };

    // Plugin personnalisé pour afficher les pourcentages dans les graphiques circulaires
    const percentagePlugin = {
        id: 'percentagePlugin',
        afterDatasetsDraw: function(chart) {
            if (chart.config.type === 'pie' || chart.config.type === 'doughnut') {
                const ctx = chart.ctx;
                const data = chart.data.datasets[0].data;
                const total = data.reduce((a, b) => a + b, 0);
                
                chart.data.datasets.forEach((dataset, i) => {
                    const meta = chart.getDatasetMeta(i);
                    meta.data.forEach((element, index) => {
                        const value = dataset.data[index];
                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                        
                        // Calculer la position au centre de chaque segment
                        const arc = element;
                        const angle = (arc.startAngle + arc.endAngle) / 2;
                        const radius = (arc.innerRadius + arc.outerRadius) / 2;
                        const x = arc.x + Math.cos(angle) * radius;
                        const y = arc.y + Math.sin(angle) * radius;
                        
                        // Afficher le pourcentage seulement si le segment est assez grand
                        if (percentage > 3) {
                            ctx.save();
                            ctx.fillStyle = '#fff';
                            ctx.font = 'bold 12px Arial';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            ctx.shadowColor = 'rgba(0, 0, 0, 0.5)';
                            ctx.shadowBlur = 3;
                            ctx.fillText(percentage + '%', x, y);
                            ctx.restore();
                        }
                    });
                });
            }
        }
    };

    // Fonction pour calculer le pourcentage de croissance
    function calculateGrowthRate(data) {
        if (data.length < 2) return [];
        const rates = [];
        for (let i = 1; i < data.length; i++) {
            const prev = data[i - 1];
            const curr = data[i];
            if (prev === 0) {
                rates.push(curr > 0 ? 100 : 0);
            } else {
                rates.push(((curr - prev) / prev) * 100);
            }
        }
        return [0, ...rates]; // Premier élément = 0 (pas de croissance)
    }

    // Graphique des tendances temporelles
    const trendsCtx = document.getElementById("trendsChart");
    if (trendsCtx) {
        const postsData = <?php echo $postsMonthlyData; ?>;
        const offersData = <?php echo $offersMonthlyData; ?>;
        const usersData = <?php echo $usersMonthlyData; ?>;
        
        new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: <?php echo $monthsLabels; ?>,
                datasets: [
                    {
                        label: 'Posts',
                        data: postsData,
                        borderColor: colors.primary,
                        backgroundColor: colors.primary.replace('0.8', '0.1'),
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Offres',
                        data: offersData,
                        borderColor: colors.success,
                        backgroundColor: colors.success.replace('0.8', '0.1'),
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Utilisateurs',
                        data: usersData,
                        borderColor: colors.info,
                        backgroundColor: colors.info.replace('0.8', '0.1'),
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.parsed.y;
                                
                                // Ajouter le pourcentage de croissance
                                const index = context.dataIndex;
                                const data = context.dataset.data;
                                if (index > 0 && data[index - 1] > 0) {
                                    const growth = ((data[index] - data[index - 1]) / data[index - 1]) * 100;
                                    label += ' (' + (growth >= 0 ? '+' : '') + growth.toFixed(1) + '%)';
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Graphique circulaire - Posts par région
    const postsRegionCtx = document.getElementById("postsRegionChart");
    if (postsRegionCtx) {
        const postsRegionData = <?php echo $postsByRegionData; ?>;
        const postsRegionLabels = <?php echo $postsByRegionLabels; ?>;
        const postsTotal = postsRegionData.reduce((a, b) => a + b, 0);
        
        new Chart(postsRegionCtx, {
            type: 'doughnut',
            plugins: [percentagePlugin],
            data: {
                labels: postsRegionLabels,
                datasets: [{
                    data: postsRegionData,
                    backgroundColor: [
                        colors.primary,
                        colors.success,
                        colors.info,
                        colors.warning,
                        colors.danger,
                        colors.secondary,
                        'rgba(102, 16, 242, 0.8)',
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        const percentage = postsTotal > 0 ? ((value / postsTotal) * 100).toFixed(1) : 0;
                                        return {
                                            text: label + ' (' + value + ' - ' + percentage + '%)',
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const percentage = postsTotal > 0 ? ((value / postsTotal) * 100).toFixed(1) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    // Graphique circulaire - Offres par type
    const offersTypeCtx = document.getElementById("offersTypeChart");
    if (offersTypeCtx) {
        const offersTypeData = <?php echo $offersByTypeData; ?>;
        const offersTypeLabels = <?php echo $offersByTypeLabels; ?>;
        const offersTotal = offersTypeData.reduce((a, b) => a + b, 0);
        
        new Chart(offersTypeCtx, {
            type: 'pie',
            plugins: [percentagePlugin],
            data: {
                labels: offersTypeLabels,
                datasets: [{
                    data: offersTypeData,
                    backgroundColor: [
                        colors.success,
                        colors.primary,
                        colors.info,
                        colors.warning,
                        colors.danger,
                        colors.secondary,
                        'rgba(102, 16, 242, 0.8)',
                        'rgba(255, 99, 132, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        const percentage = offersTotal > 0 ? ((value / offersTotal) * 100).toFixed(1) : 0;
                                        return {
                                            text: label + ' (' + value + ' - ' + percentage + '%)',
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const percentage = offersTotal > 0 ? ((value / offersTotal) * 100).toFixed(1) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    // Graphique circulaire - Utilisateurs par rôle
    const usersRoleCtx = document.getElementById("usersRoleChart");
    if (usersRoleCtx) {
        const usersRoleData = <?php echo $usersByRoleData; ?>;
        const usersRoleLabels = <?php echo $usersByRoleLabels; ?>;
        const usersTotal = usersRoleData.reduce((a, b) => a + b, 0);
        
        new Chart(usersRoleCtx, {
            type: 'doughnut',
            plugins: [percentagePlugin],
            data: {
                labels: usersRoleLabels,
                datasets: [{
                    data: usersRoleData,
                    backgroundColor: [
                        colors.info,
                        colors.warning,
                        colors.secondary
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        const percentage = usersTotal > 0 ? ((value / usersTotal) * 100).toFixed(1) : 0;
                                        return {
                                            text: label + ' (' + value + ' - ' + percentage + '%)',
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const percentage = usersTotal > 0 ? ((value / usersTotal) * 100).toFixed(1) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    // Graphique circulaire - Utilisateurs par région
    const usersRegionCtx = document.getElementById("usersRegionChart");
    if (usersRegionCtx) {
        const usersRegionData = <?php echo $usersByRegionData; ?>;
        const usersRegionLabels = <?php echo $usersByRegionLabels; ?>;
        const usersRegionTotal = usersRegionData.reduce((a, b) => a + b, 0);
        
        new Chart(usersRegionCtx, {
            type: 'pie',
            plugins: [percentagePlugin],
            data: {
                labels: usersRegionLabels,
                datasets: [{
                    data: usersRegionData,
                    backgroundColor: [
                        colors.primary,
                        colors.success,
                        colors.info,
                        colors.warning,
                        colors.danger,
                        colors.secondary,
                        'rgba(102, 16, 242, 0.8)',
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        const percentage = usersRegionTotal > 0 ? ((value / usersRegionTotal) * 100).toFixed(1) : 0;
                                        return {
                                            text: label + ' (' + value + ' - ' + percentage + '%)',
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const percentage = usersRegionTotal > 0 ? ((value / usersRegionTotal) * 100).toFixed(1) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>

