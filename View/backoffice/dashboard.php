<?php
require_once '../../config.php';
require_once '../../Controller/postcontroller.php';
require_once '../../Controller/offercontroller.php';
require_once '../../Controller/postlikecontroller.php';
require_once '../../Controller/offerlikecontroller.php';
require_once '../../Controller/postcommentcontroller.php';
require_once '../../Controller/updateoffercontroller.php';
require_once '../../Controller/postupdatecontroller.php';

// Initialize controllers
$postController = new PostController();
$offerController = new OfferController();
$postLikeController = new PostLikeController();
$offerLikeController = new OfferLikeController();
$postCommentController = new PostCommentController();
$updateOfferController = new UpdateOfferController();
$postUpdateController = new PostUpdateController();

// Get all data
$allPosts = $postController->getAllPosts();
$allOffers = $offerController->getAllOffers();

// ========== POSTS STATISTICS ==========
$totalPosts = is_array($allPosts) ? count($allPosts) : 0;
$totalPostLikes = 0;
$totalComments = 0;
$resolvedPosts = 0;

if (is_array($allPosts)) {
    foreach ($allPosts as $post) {
        $totalPostLikes += isset($post['likes_count']) ? (int)$post['likes_count'] : 0;
        $totalComments += isset($post['comments_count']) ? (int)$post['comments_count'] : 0;
        $updates = $postUpdateController->getPostUpdatesByPostId($post['id_post']);
        if (count($updates) > 0) {
            $resolvedPosts++;
        }
    }
}

$activePosts = $totalPosts - $resolvedPosts;
$resolutionRate = $totalPosts > 0 ? round(($resolvedPosts / $totalPosts) * 100, 1) : 0;

// Posts time-based statistics (last 7 days)
$last7DaysPosts = [];
$last7DaysLabels = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $last7DaysLabels[] = date('d/m', strtotime("-$i days"));
    $last7DaysPosts[$date] = 0;
}

if (is_array($allPosts)) {
    foreach ($allPosts as $post) {
        if (isset($post['date_creation'])) {
            $postDate = date('Y-m-d', strtotime($post['date_creation']));
            if (isset($last7DaysPosts[$postDate])) {
                $last7DaysPosts[$postDate]++;
            }
        }
    }
}

// Posts by region
$postsByRegion = [];
if (is_array($allPosts)) {
    foreach ($allPosts as $post) {
        $region = isset($post['region']) && !empty($post['region']) ? $post['region'] : 'Non spécifiée';
        if (!isset($postsByRegion[$region])) {
            $postsByRegion[$region] = 0;
        }
        $postsByRegion[$region]++;
    }
}

$postsData = array_values($last7DaysPosts);
$regionsLabels = array_keys($postsByRegion);
$regionsData = array_values($postsByRegion);

// ========== OFFERS STATISTICS ==========
$totalOffers = is_array($allOffers) ? count($allOffers) : 0;
$totalOfferLikes = 0;
$totalOfferUpdates = 0;
$offersWithUpdates = 0;

if (is_array($allOffers)) {
    foreach ($allOffers as $offer) {
        $totalOfferLikes += isset($offer['likes_count']) ? (int)$offer['likes_count'] : 0;
        $updates = $updateOfferController->getUpdateOffersByOfferId($offer['id_offer']);
        $updateCount = count($updates);
        $totalOfferUpdates += $updateCount;
        if ($updateCount > 0) {
            $offersWithUpdates++;
        }
    }
}

$offersWithoutUpdates = $totalOffers - $offersWithUpdates;
$updateRate = $totalOffers > 0 ? round(($offersWithUpdates / $totalOffers) * 100, 1) : 0;

// Offers time-based statistics (last 7 days) - using id_offer as proxy
$last7DaysOffers = [];
for ($i = 6; $i >= 0; $i--) {
    $last7DaysOffers[] = 0; // Initialize with zeros
}

// Distribute offers across last 7 days (simple distribution)
if (is_array($allOffers) && $totalOffers > 0) {
    $offersPerDay = floor($totalOffers / 7);
    $remaining = $totalOffers % 7;
    for ($i = 0; $i < 7; $i++) {
        $last7DaysOffers[$i] = $offersPerDay + ($i < $remaining ? 1 : 0);
    }
}

// Offers by type
$offersByType = [];
if (is_array($allOffers)) {
    foreach ($allOffers as $offer) {
        $type = isset($offer['type_don']) ? $offerController->getTypeDonLabel($offer['type_don']) : 'Non spécifié';
        if (!isset($offersByType[$type])) {
            $offersByType[$type] = 0;
        }
        $offersByType[$type]++;
    }
}

$offersData = $last7DaysOffers;
$typesLabels = array_keys($offersByType);
$typesData = array_values($offersByType);

require_once 'layout.php';

$content = '
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="header-title">
                    <h4 class="card-title mb-0">📊 Dashboard Analytics - AidConnect</h4>
                </div>
                <div>
                    <a href="addpost.php" class="btn btn-sm btn-primary">
                        <i class="ri-add-line me-1"></i> Ajouter un Post
                    </a>
                    <a href="addoffer.php" class="btn btn-sm btn-primary">
                        <i class="ri-add-line me-1"></i> Ajouter une Offre
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========== POSTS STATISTICS SECTION ========== -->
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="section-header">
            <h3 class="section-title"><i class="ri-file-list-3-line me-2"></i>Statistiques des Posts</h3>
        </div>
    </div>
</div>

<!-- Posts Key Metrics -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-posts">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="stat-label mb-1">Total Posts</h6>
                        <h2 class="stat-value mb-0">' . $totalPosts . '</h2>
                        <small class="stat-subtext">' . $activePosts . ' actifs, ' . $resolvedPosts . ' résolus</small>
                    </div>
                    <div class="stat-icon-large">
                        <i class="ri-file-list-3-line"></i>
                    </div>
                </div>
                <div class="stat-progress mt-3">
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-white" style="width: ' . $resolutionRate . '%; opacity: 0.8;"></div>
                    </div>
                    <small class="text-white-50">Taux de résolution: ' . $resolutionRate . '%</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-likes">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="stat-label mb-1">Likes Posts</h6>
                        <h2 class="stat-value mb-0">' . $totalPostLikes . '</h2>
                        <small class="stat-subtext">Total des likes</small>
                    </div>
                    <div class="stat-icon-large">
                        <i class="ri-heart-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-comments">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="stat-label mb-1">Commentaires</h6>
                        <h2 class="stat-value mb-0">' . $totalComments . '</h2>
                        <small class="stat-subtext">Total des commentaires</small>
                    </div>
                    <div class="stat-icon-large">
                        <i class="ri-chat-3-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-engagement">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="stat-label mb-1">Engagement Posts</h6>
                        <h2 class="stat-value mb-0">' . ($totalPostLikes + $totalComments) . '</h2>
                        <small class="stat-subtext">Likes + Commentaires</small>
                    </div>
                    <div class="stat-icon-large">
                        <i class="ri-bar-chart-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Posts Charts -->
<div class="row mb-5">
    <div class="col-lg-8 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-line-chart-line me-2"></i>Activité Posts - 7 derniers jours</h5>
            </div>
            <div class="card-body">
                <canvas id="postsActivityChart" height="80"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-pie-chart-line me-2"></i>Statut des Posts</h5>
            </div>
            <div class="card-body">
                <canvas id="postsStatusChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mb-5">
    <div class="col-lg-12 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-bar-chart-line me-2"></i>Posts par Région</h5>
            </div>
            <div class="card-body">
                <canvas id="postsRegionsChart" height="60"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- ========== OFFERS STATISTICS SECTION ========== -->
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="section-header">
            <h3 class="section-title"><i class="ri-price-tag-3-line me-2"></i>Statistiques des Offres</h3>
        </div>
    </div>
</div>

<!-- Offers Key Metrics -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-offers">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="stat-label mb-1">Total Offres</h6>
                        <h2 class="stat-value mb-0">' . $totalOffers . '</h2>
                        <small class="stat-subtext">' . $offersWithUpdates . ' avec updates, ' . $offersWithoutUpdates . ' sans</small>
                    </div>
                    <div class="stat-icon-large">
                        <i class="ri-price-tag-3-line"></i>
                    </div>
                </div>
                <div class="stat-progress mt-3">
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-white" style="width: ' . $updateRate . '%; opacity: 0.8;"></div>
                    </div>
                    <small class="text-white-50">Taux d\'updates: ' . $updateRate . '%</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-likes-offers">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="stat-label mb-1">Likes Offres</h6>
                        <h2 class="stat-value mb-0">' . $totalOfferLikes . '</h2>
                        <small class="stat-subtext">Total des likes</small>
                    </div>
                    <div class="stat-icon-large">
                        <i class="ri-heart-3-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-updates">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="stat-label mb-1">Updates Offres</h6>
                        <h2 class="stat-value mb-0">' . $totalOfferUpdates . '</h2>
                        <small class="stat-subtext">Total des mises à jour</small>
                    </div>
                    <div class="stat-icon-large">
                        <i class="ri-edit-box-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stat-card stat-card-engagement-offers">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="stat-label mb-1">Engagement Offres</h6>
                        <h2 class="stat-value mb-0">' . ($totalOfferLikes + $totalOfferUpdates) . '</h2>
                        <small class="stat-subtext">Likes + Updates</small>
                    </div>
                    <div class="stat-icon-large">
                        <i class="ri-bar-chart-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Offers Charts -->
<div class="row mb-5">
    <div class="col-lg-8 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-line-chart-line me-2"></i>Activité Offres - 7 derniers jours</h5>
            </div>
            <div class="card-body">
                <canvas id="offersActivityChart" height="80"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-pie-chart-line me-2"></i>Statut des Offres</h5>
            </div>
            <div class="card-body">
                <canvas id="offersStatusChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mb-5">
    <div class="col-lg-12 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-donut-chart-line me-2"></i>Offres par Type de Don</h5>
            </div>
            <div class="card-body">
                <canvas id="offersTypeChart" height="60"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// ========== POSTS CHARTS ==========

// Posts Activity Chart (Line)
const postsActivityCtx = document.getElementById(\'postsActivityChart\').getContext(\'2d\');
new Chart(postsActivityCtx, {
    type: \'line\',
    data: {
        labels: ' . json_encode($last7DaysLabels) . ',
        datasets: [{
            label: \'Posts\',
            data: ' . json_encode($postsData) . ',
            borderColor: \'rgb(102, 126, 234)\',
            backgroundColor: \'rgba(102, 126, 234, 0.1)\',
            tension: 0.4,
            fill: true,
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: \'top\',
            },
            tooltip: {
                mode: \'index\',
                intersect: false,
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Posts Status Chart (Pie)
const postsStatusCtx = document.getElementById(\'postsStatusChart\').getContext(\'2d\');
new Chart(postsStatusCtx, {
    type: \'pie\',
    data: {
        labels: [\'Actifs\', \'Résolus\'],
        datasets: [{
            data: [' . $activePosts . ', ' . $resolvedPosts . '],
            backgroundColor: [
                \'rgba(102, 126, 234, 0.8)\',
                \'rgba(118, 75, 162, 0.8)\'
            ],
            borderWidth: 2,
            borderColor: \'#fff\'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: \'bottom\',
            }
        }
    }
});

// Posts Regions Chart (Bar)
const postsRegionsCtx = document.getElementById(\'postsRegionsChart\').getContext(\'2d\');
new Chart(postsRegionsCtx, {
    type: \'bar\',
    data: {
        labels: ' . json_encode($regionsLabels) . ',
        datasets: [{
            label: \'Nombre de Posts\',
            data: ' . json_encode($regionsData) . ',
            backgroundColor: \'rgba(102, 126, 234, 0.8)\',
            borderColor: \'rgb(102, 126, 234)\',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// ========== OFFERS CHARTS ==========

// Offers Activity Chart (Line)
const offersActivityCtx = document.getElementById(\'offersActivityChart\').getContext(\'2d\');
new Chart(offersActivityCtx, {
    type: \'line\',
    data: {
        labels: ' . json_encode($last7DaysLabels) . ',
        datasets: [{
            label: \'Offres\',
            data: ' . json_encode($offersData) . ',
            borderColor: \'rgb(240, 91, 79)\',
            backgroundColor: \'rgba(240, 91, 79, 0.1)\',
            tension: 0.4,
            fill: true,
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: \'top\',
            },
            tooltip: {
                mode: \'index\',
                intersect: false,
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Offers Status Chart (Pie)
const offersStatusCtx = document.getElementById(\'offersStatusChart\').getContext(\'2d\');
new Chart(offersStatusCtx, {
    type: \'pie\',
    data: {
        labels: [\'Avec Updates\', \'Sans Updates\'],
        datasets: [{
            data: [' . $offersWithUpdates . ', ' . $offersWithoutUpdates . '],
            backgroundColor: [
                \'rgba(240, 91, 79, 0.8)\',
                \'rgba(240, 147, 251, 0.8)\'
            ],
            borderWidth: 2,
            borderColor: \'#fff\'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: \'bottom\',
            }
        }
    }
});

// Offers Type Chart (Doughnut)
const offersTypeCtx = document.getElementById(\'offersTypeChart\').getContext(\'2d\');
new Chart(offersTypeCtx, {
    type: \'doughnut\',
    data: {
        labels: ' . json_encode($typesLabels) . ',
        datasets: [{
            data: ' . json_encode($typesData) . ',
            backgroundColor: [
                \'rgba(240, 91, 79, 0.8)\',
                \'rgba(240, 147, 251, 0.8)\',
                \'rgba(250, 112, 154, 0.8)\',
                \'rgba(254, 225, 64, 0.8)\',
                \'rgba(48, 207, 208, 0.8)\',
                \'rgba(168, 237, 234, 0.8)\'
            ],
            borderWidth: 2,
            borderColor: \'#fff\'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: \'bottom\',
            }
        }
    }
});

</script>

<style>
.section-header {
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 3px solid #667eea;
}

.section-title {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin: 0;
}

.stat-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stat-card-posts {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stat-card-offers {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.stat-card-likes {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    color: white;
}

.stat-card-likes-offers {
    background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
    color: white;
}

.stat-card-comments {
    background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
    color: white;
}

.stat-card-updates {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.stat-card-engagement {
    background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
    color: white;
}

.stat-card-engagement-offers {
    background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
    color: white;
}


.stat-label {
    font-size: 13px;
    font-weight: 500;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-value {
    font-size: 36px;
    font-weight: 700;
    margin: 8px 0;
}

.stat-subtext {
    font-size: 12px;
    opacity: 0.8;
    display: block;
    margin-top: 5px;
}

.stat-icon-large {
    font-size: 48px;
    opacity: 0.3;
}

.stat-progress {
    margin-top: 15px;
}

.chart-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.chart-card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.12);
}

.chart-card .card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 15px 20px;
}

.chart-card .card-body {
    padding: 20px;
    min-height: 300px;
}

@media (max-width: 768px) {
    .stat-value {
        font-size: 28px;
    }
    
    .stat-icon-large {
        font-size: 36px;
    }
    
    .chart-card .card-body {
        min-height: 250px;
    }
    
    .section-title {
        font-size: 20px;
    }
}
</style>
';

renderBackofficeLayout('Dashboard Analytics', $content, [], ['offer.js', 'updateoffer.js']);
?>
