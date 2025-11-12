<?php

require_once '../../Config.php';
require_once '../../Model/PostUpdate.php';
require_once '../../Controller/PostUpdateController.php';

$postUpdateController = new PostUpdateController();
$list = $postUpdateController->getAllPostUpdates();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Titre</th>
            <th>Description</th>
            <th>Photo</th>
            <th>Ville</th>
            <th>Intervention</th>
            <th>Jours Réponse</th>
            <th>Jours Résolution</th>
            <th>Actions</th>
        </tr>
        <!-- display data from the database here using php -->


     <?php
        if ($list->rowCount() > 0) {
            foreach ($list as $row) {
                echo "
                    <tr>
                        <td>{$row['id']}</td>
                        <td>{$row['titre']}</td>
                        <td>{$row['description']}</td>
                        <td>{$row['photo']}</td>
                        <td>{$row['ville']}</td>
                        <td>{$row['intervention']}</td>
                        <td>{$row['joursReponse']}</td>
                        <td>{$row['joursResolution']}</td>
                        <td> <a href='updatepostupdate.php?id={$row['id']}'> mod </a> </td>

                    </tr>";
            }
        } else {
            echo "<tr><td colspan='9'>Aucun post update trouvé</td></tr>";
        }
        ?>
    </table>

</body>
</html>
