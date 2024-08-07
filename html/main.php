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

if(isset($_SESSION['username'])) {
    // Récupérer les informations de l'utilisateur
    $query = $bdd->prepare("SELECT pseudo, photo_profil FROM users WHERE pseudo = ?");
    $query->execute([$_SESSION['username']]);
    $user = $query->fetch(PDO::FETCH_ASSOC);
}

// Afficher le message d'erreur s'il existe
$error_message = "";
if(isset($_SESSION['error'])) {
    $error_message = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Définir le nombre de résultats par page
$resultats_par_page = 5;

// Récupérer le numéro de page à afficher, par défaut à 1
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $resultats_par_page;

// Récupérer les années et genres uniques pour les filtres
$years = $bdd->query("SELECT DISTINCT generation_musique FROM musique ORDER BY generation_musique")->fetchAll(PDO::FETCH_COLUMN);
$genres = $bdd->query("SELECT DISTINCT genre_musique FROM musique ORDER BY genre_musique")->fetchAll(PDO::FETCH_COLUMN);

// Préparer la requête de recherche avec les filtres
$recherche = isset($_GET['s']) ? htmlspecialchars($_GET['s']) : '';
$selected_year = isset($_GET['year']) ? htmlspecialchars($_GET['year']) : '';
$selected_genre = isset($_GET['genre']) ? htmlspecialchars($_GET['genre']) : '';

$query = "SELECT musique.titre_musique, musique.generation_musique, musique.genre_musique, 
                 chanteur.nom_chanteur, compositeur.nom_compo
          FROM musique
          INNER JOIN chanteur ON musique.id_chanteur = chanteur.id_chanteur
          INNER JOIN compositeur ON musique.id_compo = compositeur.id_compo
          WHERE (:recherche = '' OR musique.titre_musique LIKE :recherche 
              OR musique.genre_musique LIKE :recherche 
              OR chanteur.nom_chanteur LIKE :recherche 
              OR compositeur.nom_compo LIKE :recherche)
          AND (:selected_year = '' OR musique.generation_musique = :selected_year)
          AND (:selected_genre = '' OR musique.genre_musique = :selected_genre)
          ORDER BY musique.id_Musique DESC
          LIMIT :offset, :limit";

$allMusique = $bdd->prepare($query);
$allMusique->bindValue(':recherche', "%$recherche%");
$allMusique->bindValue(':selected_year', $selected_year);
$allMusique->bindValue(':selected_genre', $selected_genre);
$allMusique->bindValue(':offset', $offset, PDO::PARAM_INT);
$allMusique->bindValue(':limit', $resultats_par_page, PDO::PARAM_INT);
$allMusique->execute();
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
        <nav><a href="../index.php" style="text-decoration:none"><img id="imgpetit" src="../image/logopetit.png"></a></nav>
            <?php
            // Afficher la photo de profil s'il est connecté
            if(isset($_SESSION['username'])) {
                if ($user && $user['photo_profil']) {
                    echo "<a href='user.php'><img src='" . htmlspecialchars($user['photo_profil']) . "' alt='Photo de profil' style='width: 50px; height: 50px; border-radius: 50%;'></a>";
                }
                echo "<nav><a href='?logout=true' style='text-decoration:none'>Se déconnecter</a></nav>";
            } else {
                echo "<nav><a href='login.php' style='text-decoration:none'>Connexion</a></nav>";
            }
            ?>

        </section>

        <!-- Modification ici pour afficher toujours "TrackBase" -->
        <header><h1>TrackBase</h1></header>

        <?php
        // Afficher le message d'erreur s'il existe
        if($error_message) {
            echo "<p style='color:red;'>$error_message</p>";
        }
        ?>

        <a href="main_add.php" class="redirection bouton-changer-page">Accéder à la page d'ajout de musique</a>
        
        <!-- Ajout de l'accès à la page tracksphere.php -->
        <a href="tracksphere.php" class="redirection bouton-changer-page">Accéder à TrackSphere</a>

        <article>
            <h2>
                <form method="GET"><center>
                    <input type="search" name="s" placeholder="Rechercher une musique" autocomplete="off" class="input" value="<?= htmlspecialchars($recherche) ?>">
                    <br>
                    <label for="year">Année:</label>
                    <select name="year" id="year">
                        <option value="">Toutes les années</option>
                        <?php foreach ($years as $year): ?>
                            <option value="<?= htmlspecialchars($year) ?>" <?= $year == $selected_year ? 'selected' : '' ?>><?= htmlspecialchars($year) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="genre">Genre:</label>
                    <select name="genre" id="genre">
                        <option value="">Tous les genres</option>
                        <?php foreach ($genres as $genre): ?>
                            <option value="<?= htmlspecialchars($genre) ?>" <?= $genre == $selected_genre ? 'selected' : '' ?>><?= htmlspecialchars($genre) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <br>
                    <input type="submit" name="envoyer" value="Envoyer" class="bouton">
                </center>
                </form>
            </h2>
        </article>

        <article>
            <h3>Résultats :</h3>
            <?php
            if($allMusique->rowCount() > 0) {
                while($musique = $allMusique->fetch()){
                    ?>
                    <br>
                    <p>Titre : <?= htmlspecialchars($musique['titre_musique']); ?></p>
                    <p>Génération : <?= htmlspecialchars($musique['generation_musique']); ?></p>
                    <p>Genre : <?= htmlspecialchars($musique['genre_musique']); ?></p>
                    <p>Chanteur : <?= htmlspecialchars($musique['nom_chanteur']); ?></p>
                    <p>Compositeur : <?= htmlspecialchars($musique['nom_compo']); ?></p>
                    <?php
                }

                // Ajouter des liens de pagination
                $query_count = "SELECT COUNT(*) FROM musique 
                                INNER JOIN chanteur ON musique.id_chanteur = chanteur.id_chanteur
                                INNER JOIN compositeur ON musique.id_compo = compositeur.id_compo
                                WHERE (:recherche = '' OR musique.titre_musique LIKE :recherche 
                                    OR musique.genre_musique LIKE :recherche 
                                    OR chanteur.nom_chanteur LIKE :recherche 
                                    OR compositeur.nom_compo LIKE :recherche)
                                AND (:selected_year = '' OR musique.generation_musique = :selected_year)
                                AND (:selected_genre = '' OR musique.genre_musique = :selected_genre)";

                $count_stmt = $bdd->prepare($query_count);
                $count_stmt->bindValue(':recherche', "%$recherche%");
                $count_stmt->bindValue(':selected_year', $selected_year);
                $count_stmt->bindValue(':selected_genre', $selected_genre);
                $count_stmt->execute();
                $total_rows = $count_stmt->fetchColumn();
                $total_pages = ceil($total_rows / $resultats_par_page);

                echo "<div class='pagination'>";
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo "<a href='?page=$i";
                    if ($recherche) {
                        echo "&s=" . urlencode($recherche);
                    }
                    if ($selected_year) {
                        echo "&year=" . urlencode($selected_year);
                    }
                    if ($selected_genre) {
                        echo "&genre=" . urlencode($selected_genre);
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

        <p><br></p>
        <p><br></p>
        <p><br></p>
        <p><br></p>
<footer>
        <section>
                <nav>Contact : 
                <br>Téléphone : +33 6 59 32 72 14  
                <br>Adresse mail : trackbase@estiam.com
                </nav> 
        </section>
        <section>
        </section>
        <section>
            <nav>
            <br><a href ="https://trello.com/b/PPdfmOGM/trackbase">Trello</a>
            <br><a href ="https://github.com/Akune122/trackbase">GitHub</a>
            </nav> 
        </section>
      
        <section>
        </section>

        <!-- Lien vers Instagram avec le logo -->
        <a href="https://www.instagram.com/estiamofficiel/" target="_blank">
        <img src="https://psfonttk.com/wp-content/uploads/2020/09/Instagram-Logo-Transparent.png" alt="Logo Instagram" style="width:50px;height:50px;">
        </a>


        <!-- Lien vers Twitter avec le logo -->
        <a href="https://x.com/MetzCampus" target="_blank">
        <img src="https://vectorseek.com/wp-content/uploads/2023/07/Twitter-X-Logo-Vector-01-2.jpg" alt="Logo Twitter" style="width:45px;height:45px;">
        </a>

        <!-- Lien vers LinkedIn avec le logo -->
        <a href="https://fr.linkedin.com/company/polesupjeanxxiii" target="_blank">
        <img src="https://logospng.org/download/linkedin/logo-linkedin-icon-1536.png" alt="Logo LinkedIn" style="width:45px;height:45px;">
        </a>



        <section>
            <nav> 
            <br><a href="conditions.php">Conditions générales d'utilisations </a>
            <br><a href="presentation.php">A propos</a>
            </nav> 
        </section>
    </footer>
    </body>
</html>
