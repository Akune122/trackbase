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

// Définir le nombre de résultats par page
$resultats_par_page = 5;

// Récupérer le numéro de page à afficher, par défaut à 1
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $resultats_par_page;

if(isset($_GET['s']) && !empty($_GET['s'])){
    $recherche = htmlspecialchars($_GET['s']);
    $query = "SELECT musique.titre_musique, musique.generation_musique, musique.genre_musique, 
                     chanteur.nom_chanteur, compositeur.nom_compo
              FROM musique
              INNER JOIN chanteur ON musique.id_chanteur = chanteur.id_chanteur
              INNER JOIN compositeur ON musique.id_compo = compositeur.id_compo
              WHERE musique.titre_musique LIKE :recherche OR musique.genre_musique LIKE :recherche 
              OR chanteur.nom_chanteur LIKE :recherche OR compositeur.nom_compo LIKE :recherche
              ORDER BY musique.id_Musique DESC
              LIMIT :offset, :limit";
    $allMusique = $bdd->prepare($query);
    $allMusique->bindValue(':recherche', "%$recherche%");
    $allMusique->bindValue(':offset', $offset, PDO::PARAM_INT);
    $allMusique->bindValue(':limit', $resultats_par_page, PDO::PARAM_INT);
    $allMusique->execute();
} else {
    $allMusique = $bdd->prepare('SELECT musique.titre_musique, musique.generation_musique, 
                                      musique.genre_musique, chanteur.nom_chanteur, compositeur.nom_compo
                               FROM musique
                               INNER JOIN chanteur ON musique.id_chanteur = chanteur.id_chanteur
                               INNER JOIN compositeur ON musique.id_compo = compositeur.id_compo
                               ORDER BY musique.id_Musique DESC
                               LIMIT :offset, :limit');
    $allMusique->bindValue(':offset', $offset, PDO::PARAM_INT);
    $allMusique->bindValue(':limit', $resultats_par_page, PDO::PARAM_INT);
    $allMusique->execute();
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Recherche de musique</title>
        <link rel="stylesheet" href="../style/style.css">
        <style>
            /* Styles de pagination */
            .pagination {
                margin-top: 20px;
            }

            .pagination a {
                color: #fff;
                background-color: #6633CC;
                border: 2px solid #6633CC;
                padding: 8px 16px;
                border-radius: 5px;
                text-decoration: none;
            }

            .pagination a:hover {
                background-color: #000033;
                border-color: #000033;
            }
        </style>
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

                // Ajouter des liens de pagination
                $query_count = isset($_GET['s']) ? "SELECT COUNT(*) FROM musique WHERE musique.titre_musique LIKE :recherche OR musique.genre_musique LIKE :recherche OR chanteur.nom_chanteur LIKE :recherche OR compositeur.nom_compo LIKE :recherche" : "SELECT COUNT(*) FROM musique";
                $count_stmt = $bdd->prepare($query_count);
                if(isset($_GET['s'])) {
                    $count_stmt->bindValue(':recherche', "%$recherche%");
                }
                $count_stmt->execute();
                $total_rows = $count_stmt->fetchColumn();
                $total_pages = ceil($total_rows / $resultats_par_page);

                echo "<div class='pagination'>";
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo "<a href='?page=$i";
                    if(isset($_GET['s'])) {
                        echo "&s=$recherche";
                    }
                    echo "'>$i</a> ";
                }
                echo "</div>";
            } else {
                ?>
                <p>Aucun résultat trouvé</p>
                <?php
            }
            ?>
        </article>
    </body>
</html>
