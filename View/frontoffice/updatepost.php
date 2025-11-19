<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once '../../config.php';
require_once '../../Model/post.php';
require_once '../../Controller/postcontroller.php';

$postController = new PostController();
$error = '';
$success = false;

// Vérifier si l'ID est présent dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: showPosts.php');
    exit();
}

$id = (int)$_GET['id'];

// Charger les données du post
$postData = $postController->getPostById($id);

if (!$postData) {
    $error = "Post introuvable.";
} else {
    // Traitement du formulaire soumis
    if (
        isset($_POST['titre']) &&
        isset($_POST['nom']) &&
        isset($_POST['numero']) &&
        isset($_POST['email']) &&
        isset($_POST['region']) &&
        isset($_POST['description'])
    ) {
        if (
            !empty($_POST['titre']) &&
            !empty($_POST['nom']) &&
            !empty($_POST['numero']) &&
            !empty($_POST['email']) &&
            !empty($_POST['region']) &&
            !empty($_POST['description'])
        ) {
            // Gestion de l'image
            $media = !empty($postData['media']) ? $postData['media'] : ''; // Garder l'ancienne image par défaut ou chaîne vide
            if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../../public/uploads/';
                // Créer le dossier s'il n'existe pas
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                // Supprimer l'ancienne image si elle existe
                if (!empty($postData['media']) && file_exists($uploadDir . $postData['media'])) {
                    unlink($uploadDir . $postData['media']);
                }
                $mediaName = basename($_FILES['media']['name']);
                $media = $mediaName;
                move_uploaded_file($_FILES['media']['tmp_name'], $uploadDir . $mediaName);
            }

            // Gestion de la date
            $dateCreation = null;
            if (isset($_POST['dateCreation']) && !empty($_POST['dateCreation'])) {
                $dateCreation = new DateTime($_POST['dateCreation']);
            } else {
                // Utiliser la date existante ou la date actuelle
                if (!empty($postData['date_creation'])) {
                    $dateCreation = new DateTime($postData['date_creation']);
                } else {
                    $dateCreation = new DateTime();
                }
            }

            // Création de l'objet Post
            $post = new Post(
                $id,
                $_POST['titre'],
                $_POST['nom'],
                $_POST['numero'],
                $_POST['email'],
                $_POST['region'],
                $_POST['description'],
                $media,
                $dateCreation
            );

            // Mettre à jour le post
            $result = $postController->updatePost($post, $id);
            if ($result) {
                header('Location: showPosts.php');
                exit();
            } else {
                $error = "Erreur lors de la mise à jour du post. Veuillez réessayer.";
            }
        } else {
            $error = "Veuillez remplir tous les champs obligatoires.";
        }
    }
}

// Préparer les valeurs pour le formulaire
$titre = isset($_POST['titre']) ? htmlspecialchars($_POST['titre']) : htmlspecialchars($postData['titre'] ?? '');
$nom = isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : htmlspecialchars($postData['nom'] ?? '');
$numero = isset($_POST['numero']) ? htmlspecialchars($_POST['numero']) : htmlspecialchars($postData['Numéro'] ?? $postData['numero'] ?? '');
$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : htmlspecialchars($postData['email'] ?? '');
$region = isset($_POST['region']) ? htmlspecialchars($_POST['region']) : htmlspecialchars($postData['region'] ?? '');
$description = isset($_POST['description']) ? htmlspecialchars($_POST['description']) : htmlspecialchars($postData['description'] ?? '');
$dateCreation = isset($_POST['dateCreation']) ? htmlspecialchars($_POST['dateCreation']) : (isset($postData['date_creation']) ? htmlspecialchars($postData['date_creation']) : '');
$currentMedia = isset($postData['media']) ? htmlspecialchars($postData['media']) : '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un Post</title>
    <link rel="stylesheet" href="addpost.css">
    <link rel="stylesheet" href="showpost.css">
</head>
<body>
    <h1>Modifier un Post</h1>
    
    <?php if (!empty($error)): ?>
        <div >
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        
        <p>
            <label>Titre :
                <input type="text" name="titre" value="<?php echo $titre; ?>" required>
            </label>
        </p>

        <p>
            <label>Nom :
                <input type="text" name="nom" value="<?php echo $nom; ?>" required>
            </label>
        </p>

        <p>
            <label>Numéro de téléphone :
                <input type="tel" name="numero" pattern="[0-9]{8,15}" placeholder="Ex: 12345678" value="<?php echo $numero; ?>" required>
            </label>
        </p>

        <p>
            <label>Email :
                <input type="email" name="email" placeholder="exemple@domaine.com" value="<?php echo $email; ?>" required>
            </label>
        </p>

        <p>
            <label>Région :
                <select name="region" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="Ariana" <?php echo ($region == 'Ariana') ? 'selected' : ''; ?>>Ariana</option>
                    <option value="Béja" <?php echo ($region == 'Béja') ? 'selected' : ''; ?>>Béja</option>
                    <option value="Ben Arous" <?php echo ($region == 'Ben Arous') ? 'selected' : ''; ?>>Ben Arous</option>
                    <option value="Bizerte" <?php echo ($region == 'Bizerte') ? 'selected' : ''; ?>>Bizerte</option>
                    <option value="Gabes" <?php echo ($region == 'Gabes') ? 'selected' : ''; ?>>Gabes</option>
                    <option value="Gafsa" <?php echo ($region == 'Gafsa') ? 'selected' : ''; ?>>Gafsa</option>
                    <option value="Jendouba" <?php echo ($region == 'Jendouba') ? 'selected' : ''; ?>>Jendouba</option>
                    <option value="Kairouan" <?php echo ($region == 'Kairouan') ? 'selected' : ''; ?>>Kairouan</option>
                    <option value="Kasserine" <?php echo ($region == 'Kasserine') ? 'selected' : ''; ?>>Kasserine</option>
                    <option value="Kébili" <?php echo ($region == 'Kébili') ? 'selected' : ''; ?>>Kébili</option>
                    <option value="Le Kef" <?php echo ($region == 'Le Kef') ? 'selected' : ''; ?>>Le Kef</option>
                    <option value="Mahdia" <?php echo ($region == 'Mahdia') ? 'selected' : ''; ?>>Mahdia</option>
                    <option value="Manouba" <?php echo ($region == 'Manouba') ? 'selected' : ''; ?>>Manouba</option>
                    <option value="Médenine" <?php echo ($region == 'Médenine') ? 'selected' : ''; ?>>Médenine</option>
                    <option value="Monastir" <?php echo ($region == 'Monastir') ? 'selected' : ''; ?>>Monastir</option>
                    <option value="Nabeul" <?php echo ($region == 'Nabeul') ? 'selected' : ''; ?>>Nabeul</option>
                    <option value="Sfax" <?php echo ($region == 'Sfax') ? 'selected' : ''; ?>>Sfax</option>
                    <option value="Sidi Bouzid" <?php echo ($region == 'Sidi Bouzid') ? 'selected' : ''; ?>>Sidi Bouzid</option>
                    <option value="Siliana" <?php echo ($region == 'Siliana') ? 'selected' : ''; ?>>Siliana</option>
                    <option value="Sousse" <?php echo ($region == 'Sousse') ? 'selected' : ''; ?>>Sousse</option>
                    <option value="Tataouine" <?php echo ($region == 'Tataouine') ? 'selected' : ''; ?>>Tataouine</option>
                    <option value="Tozeur" <?php echo ($region == 'Tozeur') ? 'selected' : ''; ?>>Tozeur</option>
                    <option value="Tunis" <?php echo ($region == 'Tunis') ? 'selected' : ''; ?>>Tunis</option>
                    <option value="Zaghouan" <?php echo ($region == 'Zaghouan') ? 'selected' : ''; ?>>Zaghouan</option>
                </select>
            </label>
        </p>

        <p>
            <label>Description :
                <textarea name="description" rows="5" required><?php echo $description; ?></textarea>
            </label>
        </p>

        <p>
            <label>Photo actuelle :
                <?php if (!empty($currentMedia)): ?>
                    <div style="margin: 10px 0;">
                        <img src="../../public/uploads/<?php echo $currentMedia; ?>" alt="Photo actuelle" width="150" style="border: 1px solid #ccc; padding: 5px;">
                        <br><small>Photo actuelle</small>
                    </div>
                <?php else: ?>
                    <small>Aucune photo</small>
                <?php endif; ?>
            </label>
        </p>

        <p>
            <label>Nouvelle photo (laisser vide pour conserver l'actuelle) :
                <input type="file" name="media" accept="image/*">
              
                 
            </label>
        </p>

        <p>
            <label>Date de création :
                <input type="date" name="dateCreation" value="<?php echo $dateCreation; ?>">
            </label>
        </p>

        <p>
            <button type="submit" class="btn-update">Mettre à jour</button>
        </p>

        <p>
            <a href="showPosts.php" class="btn-cancel">Annuler</a>
        </p>
    </form>

    <script src="addpost.js"></script>
</body>
</html>

