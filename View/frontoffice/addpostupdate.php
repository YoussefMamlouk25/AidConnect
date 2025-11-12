<?php
include '../../Controller/postupdatecontroller.php';
include_once '../../Model/postupdate.php';

$postUpdateC = new PostUpdateController();

if(isset($_POST['titre']) &&
  isset($_POST['description']) &&
  isset($_POST['ville']) &&
  isset($_POST['intervention']) &&
  isset($_POST['joursReponse']) &&
  isset($_POST['joursResolution'])) {
    if(!empty($_POST['titre']) &&
      !empty($_POST['description']) &&
      !empty($_POST['ville']) &&
      !empty($_POST['intervention']) &&
      !empty($_POST['joursReponse']) &&
      !empty($_POST['joursResolution'])) {
        $postUpdate = new PostUpdate(
            $_POST['titre'],
            $_POST['description'],
            $_POST['photo'] ?? null,
            $_POST['ville'],
            $_POST['intervention'],
            $_POST['joursReponse'],
            $_POST['joursResolution']
        );
        $postUpdateC->addPostUpdate($postUpdate);
        header('Location: showpostupdate.php');
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>AidConnect - Post Résolu</title>
  <link rel="stylesheet" href="addpostupdate.css">
</head>
<body>

  <main>
  <h1>Publier un Problème Résolu</h1>

  <form action="" method="post">
    <p>
      <label>Titre du post résolu :
        <input type="text" name="titre" placeholder="Ex : Réparation école terminée" >
      </label>
    </p>

    <p>
      <label>Description du problème résolu :<br>
        <textarea name="description" rows="5" placeholder="Décris comment le problème a été résolu..." ></textarea>
      </label>
    </p>

    <p>
      <label>Ajouter une photo :
        <div class="photo-upload">
          <input type="file" name="photo" id="photo" accept="image/*" style="display: none;">
          <label for="photo" class="photo-icon">
            📷 Ajouter une photo
          </label>
        </div>
      </label>
    </p>

    <p>
      <label>Emplacement (ville) :
        <select name="ville" >
          <option value="">-- Sélectionner la ville --</option>
          <option value="Ariana">Ariana</option>
          <option value="Béja">Béja</option>
          <option value="Ben Arous">Ben Arous</option>
          <option value="Bizerte">Bizerte</option>
          <option value="Gabès">Gabès</option>
          <option value="Gafsa">Gafsa</option>
          <option value="Jendouba">Jendouba</option>
          <option value="Kairouan">Kairouan</option>
          <option value="Kasserine">Kasserine</option>
          <option value="Kébili">Kébili</option>
          <option value="Le Kef">Le Kef</option>
          <option value="Mahdia">Mahdia</option>
          <option value="Manouba">Manouba</option>
          <option value="Médenine">Médenine</option>
          <option value="Monastir">Monastir</option>
          <option value="Nabeul">Nabeul</option>
          <option value="Sfax">Sfax</option>
          <option value="Sidi Bouzid">Sidi Bouzid</option>
          <option value="Siliana">Siliana</option>
          <option value="Sousse">Sousse</option>
          <option value="Tataouine">Tataouine</option>
          <option value="Tozeur">Tozeur</option>
          <option value="Tunis">Tunis</option>
          <option value="Zaghouan">Zaghouan</option>
        </select>
      </label>
    </p>

    <p>
      <label>Intervention réalisée par :
        <input type="text" name="intervention" placeholder="Nom ou organisation" >
      </label>
    </p>

    <p>
      <label>Temps pour répondre (jours) :
        <input type="number" name="joursReponse" min="0" >
      </label>
    </p>

    <p>
      <label>Temps pour résoudre (jours) :
        <input type="number" name="joursResolution" min="0" >
      </label>
    </p>

    <p>
      <button type="submit">Publier le post résolu</button>
    </p>
  </form>
</main>

<script src="addpostupdate.js"></script>
</body>
</html>
