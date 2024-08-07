<?php
session_start();
$bdd = new PDO('mysql:host=localhost;dbname=trackbase_alpha2', 'root', 'trackbase');

if(!isset($_SESSION['username'])) {
    $_SESSION['error'] = "Veuillez vous connecter pour accéder à la TrackSphere.";
    header("Location: main.php");
    exit();
}

if(isset($_SESSION['username'])) {
    // Récupérer les informations de l'utilisateur
    $query = $bdd->prepare("SELECT pseudo, photo_profil FROM users WHERE pseudo = ?");
    $query->execute([$_SESSION['username']]);
    $user = $query->fetch(PDO::FETCH_ASSOC);
}

// Vérifier si l'utilisateur est connecté
$user_message = "";
$is_admin = false;
if(isset($_SESSION['username'])) {
    $user_message = "Connecté en tant que : " . $_SESSION['username'];
    $user_id = $_SESSION['user_id'];

    // Vérifier si l'utilisateur est un administrateur
    $query_check_admin = "SELECT administrateur FROM users WHERE id = ?";
    $stmt_check_admin = $bdd->prepare($query_check_admin);
    $stmt_check_admin->execute([$user_id]);
    $user_info = $stmt_check_admin->fetch();
    if($user_info['administrateur'] == 1) {
        $is_admin = true;
    }
}

// Gérer la déconnexion
if(isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: tracksphere.php");
    exit();
}

// Récupérer les commentaires de tous les utilisateurs
$comments_per_page = 5;
$page_comment = isset($_GET['page_comment']) ? $_GET['page_comment'] : 1;
$offset_comment = ($page_comment - 1) * $comments_per_page;
$query_comment = "SELECT commentaires.*, users.pseudo, musique.titre_musique, chanteur.nom_chanteur 
          FROM commentaires 
          INNER JOIN users ON commentaires.id_utilisateur = users.id 
          LEFT JOIN musique ON commentaires.id_musique = musique.id_Musique
          LEFT JOIN chanteur ON commentaires.id_auteur = chanteur.id_chanteur
          ORDER BY commentaires.date_creation DESC
          LIMIT $comments_per_page OFFSET $offset_comment";
$allComments = $bdd->query($query_comment);

// Calculer le nombre total de pages pour les commentaires
$total_comments_query = "SELECT COUNT(*) AS total_comments FROM commentaires";
$total_comments_result = $bdd->query($total_comments_query);
$total_comments = $total_comments_result->fetch()['total_comments'];
$total_pages_comment = ceil($total_comments / $comments_per_page);
// Insérer la proposition de musique dans la table "propositions" si le formulaire est soumis
if(isset($_POST['submit_proposition'])) {
    $titre = $_POST['titre'];
    $generation = $_POST['generation'];
    $chanteur = $_POST['chanteur'];
    $compositeur = $_POST['compositeur'];
    $genre = $_POST['genre'];
    $texte_proposition = $_POST['texte_proposition'];

    // Vérifier si la proposition n'existe pas déjà dans la base de données
    $existing_proposition_query = "SELECT * FROM propositions WHERE id_utilisateur = ? AND titre = ?";
    $stmt_existing_proposition = $bdd->prepare($existing_proposition_query);
    $stmt_existing_proposition->execute([$user_id, $titre]);
    $existing_proposition = $stmt_existing_proposition->fetch();

    if(!$existing_proposition) {
        // Insérer la proposition dans la table "propositions" seulement si elle n'existe pas déjà
        $insert_query = "INSERT INTO propositions (id_utilisateur, titre, generation, chanteur, compositeur, genre, texte_proposition) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $bdd->prepare($insert_query);
        $stmt->execute([$user_id, $titre, $generation, $chanteur, $compositeur, $genre, $texte_proposition]);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}



// Gérer le like d'une proposition
if(isset($_POST['like_proposition'])) {
    $id_proposition = $_POST['id_proposition'];

    // Vérifier si l'utilisateur n'a pas déjà liké cette proposition
    $check_like_query = "SELECT * FROM propositions WHERE id_proposition = ?";
    $stmt_check_like = $bdd->prepare($check_like_query);
    $stmt_check_like->execute([$id_proposition]);
    $proposition = $stmt_check_like->fetch();

    if($proposition) {
        // Mettre à jour le nombre de likes de la proposition
        $update_likes_query = "UPDATE propositions SET likes = likes + 1 WHERE id_proposition = ?";
        $stmt_update_likes = $bdd->prepare($update_likes_query);
        $stmt_update_likes->execute([$id_proposition]);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Gérer la suppression d'un commentaire
if(isset($_POST['delete_comment'])) {
    $comment_id = $_POST['comment_id'];
    
    // Supprimer le commentaire de la base de données
    $delete_query = "DELETE FROM commentaires WHERE id = ?";
    $stmt_delete = $bdd->prepare($delete_query);
    if($stmt_delete->execute([$comment_id])) {
        echo "Commentaire supprimé avec succès de la base de données."; // Débogage
    } else {
        echo "Erreur lors de la suppression du commentaire de la base de données."; // Débogage
        print_r($stmt_delete->errorInfo()); // Afficher les informations sur l'erreur
    }
    
    // Rediriger pour rafraîchir la page et éviter la resoumission du formulaire
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Gérer l'ajout d'une proposition comme une nouvelle musique
if(isset($_POST['add_music'])) {
    $titre_musique = $_POST['titre_musique'];
    $generation_musique = $_POST['generation_musique'];
    $genre_musique = $_POST['genre_musique'];
    $chanteur = $_POST['chanteur'];
    $compositeur = $_POST['compositeur'];

    // Vérifier si le chanteur existe déjà dans la base de données
    $query_check_chanteur = "SELECT id_chanteur FROM chanteur WHERE nom_chanteur = ?";
    $stmt_check_chanteur = $bdd->prepare($query_check_chanteur);
    $stmt_check_chanteur->execute([$chanteur]);
    $chanteur_row = $stmt_check_chanteur->fetch();

    // Si le chanteur n'existe pas, l'ajouter à la base de données
    if(!$chanteur_row) {
        $insert_chanteur_query = "INSERT INTO chanteur (nom_chanteur) VALUES (?)";
        $stmt_insert_chanteur = $bdd->prepare($insert_chanteur_query);
        $stmt_insert_chanteur->execute([$chanteur]);
        $chanteur_id = $bdd->lastInsertId(); // Récupérer l'ID du nouveau chanteur
    } else {
        $chanteur_id = $chanteur_row['id_chanteur'];
    }

    // Vérifier si le compositeur existe déjà dans la base de données
    $query_check_compositeur = "SELECT id_compo FROM compositeur WHERE nom_compo = ?";
    $stmt_check_compositeur = $bdd->prepare($query_check_compositeur);
    $stmt_check_compositeur->execute([$compositeur]);
    $compositeur_row = $stmt_check_compositeur->fetch();

    // Si le compositeur n'existe pas, l'ajouter à la base de données
    if(!$compositeur_row) {
        $insert_compositeur_query = "INSERT INTO compositeur (nom_compo) VALUES (?)";
        $stmt_insert_compositeur = $bdd->prepare($insert_compositeur_query);
        $stmt_insert_compositeur->execute([$compositeur]);
        $compositeur_id = $bdd->lastInsertId(); // Récupérer l'ID du nouveau compositeur
    } else {
        $compositeur_id = $compositeur_row['id_compo'];
    }

    // Insérer la nouvelle musique dans la table "musique"
    $insert_music_query = "INSERT INTO musique (titre_musique, generation_musique, genre_musique, id_chanteur, id_compo) 
                            VALUES (?, ?, ?, ?, ?)";
    $stmt_insert_music = $bdd->prepare($insert_music_query);
    $stmt_insert_music->execute([$titre_musique, $generation_musique, $genre_musique, $chanteur_id, $compositeur_id]);

    // Supprimer la proposition de la table "propositions"
    $delete_proposition_query = "DELETE FROM propositions WHERE titre = ?";
    $stmt_delete_proposition = $bdd->prepare($delete_proposition_query);
    $stmt_delete_proposition->execute([$titre_musique]);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if(isset($_POST['delete_proposition'])) {
    $proposition_id = $_POST['proposition_id'];
    
    // Supprimer la proposition de la base de données
    $delete_proposition_query = "DELETE FROM propositions WHERE id_proposition = ?";
    $stmt_delete_proposition = $bdd->prepare($delete_proposition_query);
    if($stmt_delete_proposition->execute([$proposition_id])) {
        echo "Proposition supprimée avec succès de la base de données."; // Débogage
    } else {
        echo "Erreur lors de la suppression de la proposition de la base de données."; // Débogage
        print_r($stmt_delete_proposition->errorInfo()); // Afficher les informations sur l'erreur
    }
    
    // Rediriger pour rafraîchir la page et éviter la resoumission du formulaire
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>TrackBase</title>
    <link rel="stylesheet" href="../style/style.css">
    <style>
         .hidden {
            display: none;
        }
        .active-button {
            background-color: #6633CC;
            color: #fff;
        }
        .center-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
    </style>
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

    <header><h1>Tracksphere</h1></header>

    <div class="center-buttons">
        <button id="tracktalkBtn" class="bouton-changer-page active-button">TrackTalk</button>
        <button id="trackpulseBtn" class="bouton-changer-page">TrackPulse</button>
        <button id="trackboardBtn" class="bouton-changer-page">TrackBoard</button>
    </div>

    <article id="commentsSection">
        <h2>TrackTalk</h2>
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
            <?php
            // Afficher le bouton "Supprimer" si l'utilisateur est un administrateur
            if($is_admin) {
                echo "<p><form method='post' class='delete-comment-form'>
                        <input type='hidden' name='comment_id' value='".htmlspecialchars($comment['id'])."'>
                        <button type='submit' name='delete_comment' class='supprimer-commentaire'>Supprimer</button>
                      </form></p>";
            }
            ?>
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
    </article>

    <article id="trackpulseSection" class="hidden">
    <h2>TrackPulse</h2>
    <form method="post" action="">
        <label for="titre">Titre :</label>
        <input type="text" name="titre" id="titre">
        <label for="generation">Génération :</label>
        <input type="text" name="generation" id="generation">
        <label for="chanteur">Chanteur :</label>
        <input type="text" name="chanteur" id="chanteur">
        <label for="compositeur">Compositeur :</label>
        <input type="text" name="compositeur" id="compositeur">
        <label for="genre">Genre :</label>
        <input type="text" name="genre" id="genre">
        <label for="texte_proposition">Texte de la proposition :</label>
        <textarea name="texte_proposition" id="texte_proposition"></textarea>
        <input type="submit" name="submit_proposition" value="Proposer">
    </form>
</article>

<article id="trackboardSection" class="hidden">
    <h2>TrackBoard</h2>
    <?php
// Récupérer les propositions
$propositions_query = "SELECT propositions.*, users.pseudo 
                       FROM propositions 
                       INNER JOIN users ON propositions.id_utilisateur = users.id 
                       ORDER BY propositions.likes DESC";
$propositions_result = $bdd->query($propositions_query);

if($propositions_result->rowCount() > 0) {
    while($proposition = $propositions_result->fetch()){
        ?>
        <article class="proposition">
            <p class="utilisateur"><b>Proposition de :</b> <?= htmlspecialchars($proposition['pseudo']); ?></p>
            <p class="titre"><strong>Titre :</strong> <?= htmlspecialchars($proposition['titre']); ?></p>
            <p class="generation"><strong>Génération :</strong> <?= htmlspecialchars($proposition['generation']); ?></p>
            <p class="chanteur"><strong>Chanteur :</strong> <?= htmlspecialchars($proposition['chanteur']); ?></p>
            <p class="compositeur"><strong>Compositeur :</strong> <?= htmlspecialchars($proposition['compositeur']); ?></p>
            <p class="genre"><strong>Genre :</strong> <?= htmlspecialchars($proposition['genre']); ?></p>
            <p class="texte_proposition"><strong>Texte de la proposition :</strong> <?= htmlspecialchars($proposition['texte_proposition']); ?></p>
            <?php
            // Afficher les boutons "Ajouter" et "Supprimer" uniquement pour les administrateurs
            if($is_admin) {
                echo "<form method='post' style='display: inline-block;'>
                        <input type='hidden' name='titre_musique' value='".htmlspecialchars($proposition['titre'])."'>
                        <input type='hidden' name='generation_musique' value='".htmlspecialchars($proposition['generation'])."'>
                        <input type='hidden' name='genre_musique' value='".htmlspecialchars($proposition['genre'])."'>
                        <input type='hidden' name='chanteur' value='".htmlspecialchars($proposition['chanteur'])."'>
                        <input type='hidden' name='compositeur' value='".htmlspecialchars($proposition['compositeur'])."'>
                        <button type='submit' name='add_music' class='add-music-button'>Ajouter comme musique</button>
                      </form>";

                echo "<form method='post' style='display: inline-block;'>
                        <input type='hidden' name='proposition_id' value='".htmlspecialchars($proposition['id_proposition'])."'>
                        <button type='submit' name='delete_proposition' class='delete-proposition-button'>Supprimer proposition</button>
                      </form>";
            }
            ?>
            <form method="post" action="">
                <input type="hidden" name="id_proposition" value="<?= $proposition['id_proposition'] ?>">
                <button type="submit" name="like_proposition" class="like-button">Like</button>
            </form>
            <p class="likes"><strong>Likes :</strong> <?= htmlspecialchars($proposition['likes']); ?></p>
        </article>
        <?php
    }
} else {
    ?>
    <p>Aucune proposition trouvée</p>
    <?php
}
?>

</article>



    <script>
        document.getElementById('tracktalkBtn').addEventListener('click', function() {
            document.getElementById('commentsSection').classList.remove('hidden');
            document.getElementById('trackpulseSection').classList.add('hidden');
            document.getElementById('trackboardSection').classList.add('hidden');
            document.getElementById('tracktalkBtn').classList.add('active-button');
            document.getElementById('trackpulseBtn').classList.remove('active-button');
            document.getElementById('trackboardBtn').classList.remove('active-button');
        });

        document.getElementById('trackpulseBtn').addEventListener('click', function() {
            document.getElementById('commentsSection').classList.add('hidden');
            document.getElementById('trackpulseSection').classList.remove('hidden');
            document.getElementById('trackboardSection').classList.add('hidden');
            document.getElementById('tracktalkBtn').classList.remove('active-button');
            document.getElementById('trackpulseBtn').classList.add('active-button');
            document.getElementById('trackboardBtn').classList.remove('active-button');
        });

        document.getElementById('trackboardBtn').addEventListener('click', function() {
            document.getElementById('commentsSection').classList.add('hidden');
            document.getElementById('trackpulseSection').classList.add('hidden');
            document.getElementById('trackboardSection').classList.remove('hidden');
            document.getElementById('tracktalkBtn').classList.remove('active-button');
            document.getElementById('trackpulseBtn').classList.remove('active-button');
            document.getElementById('trackboardBtn').classList.add('active-button');
        });
    </script>
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
