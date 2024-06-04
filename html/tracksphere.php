<?php
session_start();
$bdd = new PDO('mysql:host=localhost;dbname=trackbase_alpha2', 'root', 'trackbase');

// Vérifier si l'utilisateur est connecté
$user_message = "";
if(isset($_SESSION['username'])) {
    $user_message = "Connecté en tant que : " . $_SESSION['username'];
}

// Gérer la déconnexion
if(isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: tracksphere.php");
    exit();
}

// Récupérer les commentaires de tous les utilisateurs
$query = "SELECT commentaires.*, users.pseudo, musique.titre_musique, chanteur.nom_chanteur 
          FROM commentaires 
          INNER JOIN users ON commentaires.id_utilisateur = users.id 
          LEFT JOIN musique ON commentaires.id_musique = musique.id_Musique
          LEFT JOIN chanteur ON commentaires.id_auteur = chanteur.id_chanteur
          ORDER BY commentaires.date_creation DESC";
$allComments = $bdd->query($query);

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Tracksphere - Commentaires</title>
        <link rel="stylesheet" href="../style/style.css">
    </head>

    <body>
        <section class="afficher">
            <nav><a href="../index.php" style="text-decoration:none">Accueil</a></nav>
            <?php
            // Afficher le nom de l'utilisateur s'il est connecté
            if(isset($_SESSION['username'])) {
                echo "<nav><a href='user.php' style='text-decoration:none'>$user_message</a></nav>";
                echo "<nav><a href='?logout=true' style='text-decoration:none'>Se déconnecter</a></nav>";
            } else {
                echo "<nav><a href='login.php' style='text-decoration:none'>Connexion</a></nav>";
            }
            ?>
        </section>

        <header><h1>Tracksphere - Commentaires</h1></header>
        
        <?php
        if($allComments->rowCount() > 0) {
            while($comment = $allComments->fetch()){
                ?>
                <article class="commentaire">
                    <p class="utilisateur"><?= htmlspecialchars($comment['pseudo']); ?></p>
                    <p class="date"><?= htmlspecialchars($comment['date_creation']); ?></p>
                    <?php if ($comment['titre_musique']): ?>
                        <p class="musique"><strong>Musique :</strong> <?= htmlspecialchars($comment['titre_musique']); ?></p>
                    <?php endif; ?>
                    <?php if ($comment['nom_chanteur']): ?>
                        <p class="chanteur"><strong>Chanteur :</strong> <?= htmlspecialchars($comment['nom_chanteur']); ?></p>
                    <?php endif; ?>
                    <p class="texte"><?= htmlspecialchars($comment['texte']); ?></p>
                </article>
                <?php
            }
        } else {
            ?>
            <p>Aucun commentaire trouvé</p>
            <?php
        }
        ?>
        
        <a href="tracksphere_talk.php" class="bouton">Ajouter un commentaire</a>
        
    </body>
</html>
