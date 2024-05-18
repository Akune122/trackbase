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
    header("Location: main.php");
    exit();
}

if(isset($_GET['s']) && !empty($_GET['s'])){
    $recherche = htmlspecialchars($_GET['s']);
    $query = "SELECT musique.titre_musique, musique.generation_musique, musique.genre_musique, 
                     chanteur.nom_chanteur, compositeur.nom_compo
              FROM musique
              INNER JOIN chanteur ON musique.id_chanteur = chanteur.id_chanteur
              INNER JOIN compositeur ON musique.id_compo = compositeur.id_compo
              WHERE musique.titre_musique LIKE :recherche OR musique.genre_musique LIKE :recherche 
              OR chanteur.nom_chanteur LIKE :recherche OR compositeur.nom_compo LIKE :recherche";
    $allMusique = $bdd->prepare($query);
    $allMusique->execute(array(':recherche' => "%$recherche%"));
} else {
    $allMusique = $bdd->query('SELECT musique.titre_musique, musique.generation_musique, 
                                      musique.genre_musique, chanteur.nom_chanteur, compositeur.nom_compo
                               FROM musique
                               INNER JOIN chanteur ON musique.id_chanteur = chanteur.id_chanteur
                               INNER JOIN compositeur ON musique.id_compo = compositeur.id_compo
                               ORDER BY musique.id_Musique DESC');
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Recherche de musique</title>
        <link rel="stylesheet" href="../style/style.css">
    </head>

    <body>
        <section class="afficher">
            <nav><a href="../index.html" style="text-decoration:none">Accueil</a></nav>
            <?php
            // Afficher le nom de l'utilisateur s'il est connecté
            if(isset($_SESSION['username'])) {
                echo "<nav><p>$user_message</p></nav>";
                echo "<nav><a href='?logout=true' style='text-decoration:none'>Se déconnecter</a></nav>";
            } else {
                echo "<nav><a href='login.php' style='text-decoration:none'>Connexion</a></nav>";
            }
            ?>
        </section>

        <!-- Modification ici pour afficher toujours "TrackBase" -->
        <header><h1>TrackBase</h1></header>
        
        <a href="main_add.php" class="redirection bouton-changer-page">Accéder à la page d'ajout de musique</a>

        <article>
            <h2><form method="GET"><center>
                <input type="search" name="s" placeholder="Rechercher une musique" autocomplete="off" class="input">
                <br>
                <input type="submit" name="envoyer" value="Envoyer" class="bouton">
            </center>
            </form></h2>
        </article>

        <article>
            <h3>Résultats :</h3>
            <?php
            if($allMusique->rowCount() > 0) {
                while($musique = $allMusique->fetch()){
                    ?>
                    <br>
                    <p>Titre : <?= $musique['titre_musique']; ?></p>
                    <p>Génération : <?= $musique['generation_musique']; ?></p>
                    <p>Genre : <?= $musique['genre_musique']; ?></p>
                    <p>Chanteur : <?= $musique['nom_chanteur']; ?></p>
                    <p>Compositeur : <?= $musique['nom_compo']; ?></p>
                    <?php
                }
            } else {
                ?>
                <p>Aucun résultat trouvé</p>
                <?php
            }
            ?>
        </article>
    </body>
</html>