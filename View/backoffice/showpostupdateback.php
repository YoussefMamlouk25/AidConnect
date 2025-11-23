<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php';
require_once '../../Model/postupdate.php';
require_once '../../Controller/postupdatecontroller.php';

$postUpdateController = new PostUpdateController();
$list = $postUpdateController->getAllPostUpdates();

// Fonction pour obtenir les initiales d'un nom
function getInitials($name) {
    $words = explode(' ', trim($name));
    $initials = '';
    foreach ($words as $word) {
        if (!empty($word)) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
    }
    return substr($initials, 0, 2);
}

// Fonction pour formater la date
function formatDate($date) {
    if (empty($date)) return '';
    $dateObj = new DateTime($date);
    $now = new DateTime();
    $diff = $now->diff($dateObj);

    if ($diff->days == 0) {
        return "Aujourd'hui";
    } elseif ($diff->days == 1) {
        return "Hier";
    } elseif ($diff->days < 7) {
        return "Il y a " . $diff->days . " jours";
    } else {
        return $dateObj->format('d/m/Y');
    }
}

// Détecter si c'est une requête AJAX (pour dashboard.php) ou une page directe
$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
          || (isset($_GET['ajax']) && $_GET['ajax'] == '1');

// Si ce n'est pas AJAX, afficher la page complète avec sidebar
if (!$isAjax) {
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AidConnect - Liste des Posts Résolus</title>
    <link rel="stylesheet" href="showpostback.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
<div class="layout">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
<?php
}
?>

<div class="container">
    <!-- Bouton pour ajouter un nouveau post update -->

    <table class="post-table">
        <?php
        // Convertir $list en tableau de lignes si nécessaire
        $rows = [];
        if ($list) {
            if (is_object($list) && method_exists($list, 'fetchAll')) {
                $rows = $list->fetchAll(PDO::FETCH_ASSOC);
            } elseif (is_array($list)) {
                $rows = $list;
            }
        }
        if (count($rows) > 0) {
            // En-têtes dynamiques à partir des clés de la première ligne
            $cols = array_keys($rows[0]);
            echo "<thead><tr>";
            foreach ($cols as $c) {
                // Traduire les noms de colonnes pour un affichage plus clair
                $displayName = $c;
                if ($c === 'realiser') {
                    $displayName = 'Intervention réalisée par';
                } elseif ($c === 'temp_reponse') {
                    $displayName = 'Temps de réponse (jours)';
                } elseif ($c === 'temp_resolue') {
                    $displayName = 'Temps de résolution (jours)';
                } elseif ($c === 'id_post') {
                    $displayName = 'ID Post';
                } elseif ($c === 'id_update_post') {
                    $displayName = 'ID';
                } elseif ($c === 'id_user') {
                    $displayName = 'ID Utilisateur';
                }
                echo '<th>' . htmlspecialchars($displayName) . '</th>';
            }
            echo '<th>Actions</th></tr></thead><tbody>';

            foreach ($rows as $row) {
                echo '<tr>';
                foreach ($cols as $c) {
                    $val = isset($row[$c]) ? $row[$c] : '';
                    if ($c === 'media' && !empty($val)) {
                        $img = '../../public/uploads/' . htmlspecialchars($val);
                        echo '<td><img src="' . $img . '" style="max-width:120px; max-height:80px; object-fit:cover; border-radius:6px;"></td>';
                    } elseif ($c === 'realiser') {
                        // Pour la colonne realiser, afficher le texte saisi tel quel (VARCHAR)
                        if (!empty($val)) {
                            echo '<td>' . nl2br(htmlspecialchars($val)) . '</td>';
                        } else {
                            echo '<td><em>Non spécifié</em></td>';
                        }
                    } else {
                        echo '<td>' . nl2br(htmlspecialchars($val)) . '</td>';
                    }
                }
                // Actions column (use id_update_post for update/delete)
                $idUpdate = isset($row['id_update_post']) ? $row['id_update_post'] : '';
                echo '<td class="table-actions">';
                echo '<a href="updatepostupdate.php?id=' . urlencode($idUpdate) . '" class="btn-action btn-edit">✎</a>';
                echo '<a href="deletepostupdate.php?id=' . urlencode($idUpdate) . '" class="btn-action btn-delete">🗑</a>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
        } else {
            echo '<tr><td colspan="100%" class="no-data">Aucun post résolu trouvé</td></tr>';
        }
        ?>
     </table>
 </div>

<?php
// Si ce n'est pas AJAX, fermer la structure HTML
if (!$isAjax) {
?>
    </div>
</div>
    <script src="dashboard.js"></script>
</body>
</html>
<?php
}
?>
