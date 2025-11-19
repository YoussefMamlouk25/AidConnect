<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php';
require_once '../../Model/post.php';
require_once '../../Controller/postcontroller.php';

$postController = new PostController();
$list = $postController->getAllPosts();

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
?>

<div class="container">
    <!-- Bouton pour ajouter un nouveau post -->
    <div style="margin-bottom:12px;">
        <a href="addpost.php" class="btn-add-post">+ Ajouter un post</a>
    </div>
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
                echo '<th>' . htmlspecialchars($c) . '</th>';
            }
            echo '<th>Actions</th></tr></thead><tbody>';

            foreach ($rows as $row) {
                echo '<tr>';
                foreach ($cols as $c) {
                    $val = isset($row[$c]) ? $row[$c] : '';
                    if ($c === 'media' && !empty($val)) {
                        $img = '../../public/uploads/' . htmlspecialchars($val);
                        echo '<td><img src="' . $img . '" style="max-width:120px; max-height:80px; object-fit:cover; border-radius:6px;"></td>';
                    } else {
                        echo '<td>' . nl2br(htmlspecialchars($val)) . '</td>';
                    }                }
                // Actions column (use id_post if present)
                $idPost = isset($row['id_post']) ? $row['id_post'] : '';
                echo '<td class="table-actions">';
                echo '<a href="addpostupdate.php?id_post=' . urlencode($idPost) . '" class="btn-action btn-resolve">✓</a>';
                echo '<a href="updatepost.php?id=' . urlencode($idPost) . '" class="btn-action btn-edit">✎</a>';
                echo '<a href="deletepost.php?id=' . urlencode($idPost) . '" class="btn-action btn-delete">🗑</a>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
        } else {
            echo '<tr><td colspan="100%" class="no-data">Aucun post trouvé</td></tr>';
        }
        ?>
     </table>
 </div>
