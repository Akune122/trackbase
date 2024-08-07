<?php
session_start();

// Connexion à la base de données
try {
    $bdd = new PDO('mysql:host=localhost;dbname=trackbase_alpha2', 'root', 'trackbase');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Vérifier si l'utilisateur est connecté
$user_message = "";
if (isset($_SESSION['user_id'])) {
    // Récupérer l'ID de l'utilisateur connecté
    $user_id = $_SESSION['user_id'];

    // Vérifier si l'utilisateur est administrateur
    $stmt = $bdd->prepare("SELECT administrateur FROM users WHERE id = :id");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['administrateur'] == 1) {
        $user_message = "Connecté en tant que : " . $_SESSION['username'];
    } else {
        $error_message = "Accès refusé. Vous n'avez pas les droits d'administrateur.";
    }
} else {
    $error_message = "Vous devez être connecté pour ajouter une musique.";
}

// Gérer la déconnexion
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: main_add.php");
    exit();
}

if(isset($_SESSION['username'])) {
    // Récupérer les informations de l'utilisateur
    $query = $bdd->prepare("SELECT pseudo, photo_profil FROM users WHERE pseudo = ?");
    $query->execute([$_SESSION['username']]);
    $user = $query->fetch(PDO::FETCH_ASSOC);
}

// Traitement du formulaire d'ajout de musique
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    if (isset($user) && $user['administrateur'] == 1) {
        $titre = htmlspecialchars($_POST['titre']);
        $generation = htmlspecialchars($_POST['generation']);
        $genre = htmlspecialchars($_POST['genre']);
        $chanteur = htmlspecialchars($_POST['chanteur']);
        $compositeur = htmlspecialchars($_POST['compositeur']);
        $num_paroles = $_POST['num_paroles'];

        try {
            // Requête pour créer un nouveau chanteur
            $queryChanteur = "INSERT INTO chanteur (nom_chanteur) VALUES (:chanteur)";
            $stmtChanteur = $bdd->prepare($queryChanteur);
            $stmtChanteur->bindParam(':chanteur', $chanteur);
            $stmtChanteur->execute();
            // Récupération de l'ID du chanteur nouvellement créé
            $id_chanteur = $bdd->lastInsertId();

            // Requête pour créer un nouveau compositeur
            $queryCompositeur = "INSERT INTO compositeur (nom_compo) VALUES (:compositeur)";
            $stmtCompositeur = $bdd->prepare($queryCompositeur);
            $stmtCompositeur->bindParam(':compositeur', $compositeur);
            $stmtCompositeur->execute();
            // Récupération de l'ID du compositeur nouvellement créé
            $id_compositeur = $bdd->lastInsertId();

            // Requête pour ajouter une nouvelle musique
            $queryMusique = "INSERT INTO musique (titre_musique, generation_musique, genre_musique, id_chanteur, id_compo, num_paroles) 
            VALUES (:titre, :generation, :genre, :id_chanteur, :id_compo, :num_paroles)";
            $stmtMusique = $bdd->prepare($queryMusique);
            $stmtMusique->bindParam(':titre', $titre);
            $stmtMusique->bindParam(':generation', $generation);
            $stmtMusique->bindParam(':genre', $genre);
            $stmtMusique->bindParam(':id_chanteur', $id_chanteur);
            $stmtMusique->bindParam(':id_compo', $id_compositeur);
            $stmtMusique->bindParam(':num_paroles', $num_paroles);
            $stmtMusique->execute();

            $success_message = "Musique ajoutée avec succès !";
        } catch (PDOException $e) {
            $error_message = "Erreur lors de l'ajout de la musique : " . $e->getMessage();
        }
    } else {
        $error_message = "Accès refusé. Vous n'avez pas les droits d'administrateur.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une musique - TrackBase</title>
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

<header><h1>TrackBase</h1></header>
<a href="main.php" class="redirection bouton-changer-page">Accéder à la page de recherche de la musique</a>

<article>
    <h2><center>Ajouter une musique</center></h2>
    <form method="POST">
        <center>
            <label for="titre">Titre :</label><br>
            <input type="text" name="titre" required><br>
            <label for="generation">Génération :</label><br>
            <input type="text" name="generation" required><br>
            <label for="genre">Genre :</label><br>
            <input type="text" name="genre" required><br>
            <label for="chanteur">Chanteur :</label><br>
            <input type="text" name="chanteur" required><br>
            <label for="compositeur">Compositeur :</label><br>
            <input type="text" name="compositeur" required><br>
            <label for="num_paroles">Nombre de paroles :</label><br>
            <input type="number" name="num_paroles" required><br><br>
            <input type="submit" name="submit" value="Ajouter">
        </center>
    </form>
    <?php
    if (isset($success_message)) {
        echo "<p style='color:green;'>$success_message</p>";
    }
    if (isset($error_message)) {
        echo "<p style='color:red;'>$error_message</p>";
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