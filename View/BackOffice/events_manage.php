<?php
/**
 * View/BackOffice/events_manage.php
 * 🎯 Gestion complète des événements (CRUD)
 * ✅ CORRECTION: Lien de modification corrigé (ligne 175)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../Model/Event.php';

$database = new Database();
$db = $database->getConnection();
$eventModel = new Event($db);

// Gestion des actions
$action = $_GET['action'] ?? 'list';
$event_id = $_GET['id'] ?? null;

// Suppression
if ($action === 'delete' && $event_id) {
    $eventModel->setIdEvent($event_id);
    if ($eventModel->delete()) {
        $_SESSION['success'] = "Événement supprimé avec succès";
    } else {
        $_SESSION['error'] = "Erreur lors de la suppression";
    }
    header('Location: ?action=list');
    exit;
}

// Récupérer la liste des événements
$stmt = $eventModel->readAll();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Événements - BackOffice</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            color: #333;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5568d3;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-small {
            padding: 8px 15px;
            font-size: 0.9rem;
        }
        
        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: #667eea;
            color: white;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        tbody tr:hover {
            background: #f8f9fa;
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .status-actif {
            background: #d4edda;
            color: #155724;
        }
        
        .status-complet {
            background: #fff3cd;
            color: #856404;
        }
        
        .actions {
            display: flex;
            gap: 8px;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
            margin: 10px 0;
        }
        
        .stat-label {
            color: #666;
            font-size: 1rem;
        }
        
        .nav-links {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .nav-link {
            padding: 10px 20px;
            background: white;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
        }
        
        .nav-link:hover {
            background: #667eea;
            color: white;
        }
        
        .nav-link.active {
            background: #667eea;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Navigation -->
        <div class="nav-links">
            <a href="index.php?controller=event&action=manage" class="nav-link active">📅 Événements</a>
            <a href="index.php?controller=inscription&action=liste" class="nav-link">📋 Inscriptions</a>
        </div>
        
        <!-- En-tête -->
        <div class="header">
            <div>
                <h1>📅 Gestion des Événements</h1>
                <p>Créez et gérez vos événements</p>
            </div>
            <a href="index.php?controller=event&action=create" class="btn btn-primary">
                ➕ Nouvel Événement
            </a>
        </div>
        
        <!-- Messages -->
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                ✅ <?php echo htmlspecialchars($_SESSION['success']); ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                ❌ <?php echo htmlspecialchars($_SESSION['error']); ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">📊 Total Événements</div>
                <div class="stat-value"><?php echo count($events); ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">✅ Événements Actifs</div>
                <div class="stat-value">
                    <?php echo count(array_filter($events, fn($e) => $e['statut'] === 'actif')); ?>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">📅 À venir ce mois</div>
                <div class="stat-value">
                    <?php 
                    $ce_mois = count(array_filter($events, function($e) {
                        return date('Y-m', strtotime($e['date_event'])) === date('Y-m');
                    }));
                    echo $ce_mois;
                    ?>
                </div>
            </div>
        </div>
        
        <!-- Tableau des événements -->
        <div class="table-container">
            <?php if (count($events) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Date</th>
                            <th>Lieu</th>
                            <th>Type</th>
                            <th>Places</th>
                            <th>Inscrits</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($events as $event): 
                            $eventModel->setIdEvent($event['id_event']);
                            $inscrits = $eventModel->countInscriptions();
                            $places_restantes = $event['nombre_places'] - $inscrits;
                        ?>
                        <tr>
                            <td>#<?php echo $event['id_event']; ?></td>
                            <td><strong><?php echo htmlspecialchars($event['titre']); ?></strong></td>
                            <td>📅 <?php echo date('d/m/Y', strtotime($event['date_event'])); ?></td>
                            <td>📍 <?php echo htmlspecialchars($event['lieu']); ?></td>
                            <td><?php echo htmlspecialchars($event['type_event']); ?></td>
                            <td><?php echo $event['nombre_places']; ?></td>
                            <td>
                                <strong style="color: <?php echo $places_restantes > 0 ? 'green' : 'red'; ?>">
                                    <?php echo $inscrits; ?>
                                </strong>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $event['statut']; ?>">
                                    <?php echo ucfirst($event['statut']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="actions">
                                    <!-- ✅ CORRECTION: Suppression du "123" en trop -->
                                    <a href="index.php?controller=event&action=edit&id=<?php echo $event['id_event']; ?>" 
                                       class="btn btn-warning btn-small">
                                        ✏️ Modifier
                                    </a>
                                    <a href="?action=delete&id=<?php echo $event['id_event']; ?>" 
                                       class="btn btn-danger btn-small"
                                       onclick="return confirm('Confirmer la suppression ?')">
                                        🗑️ Supprimer
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="padding: 50px; text-align: center;">
                    <h3>😢 Aucun événement</h3>
                    <p>Commencez par créer votre premier événement</p>
                    <a href="event_form.php" class="btn btn-primary" style="margin-top: 15px;">
                        ➕ Créer un événement
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>