<?php
session_start();
try {
    $bdd = new PDO('mysql:host=localhost;dbname=trackbase_alpha2', 'root', 'trackbase');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Récupérer les musiques pour le formulaire
$musiques = $bdd->query("SELECT id_Musique, titre_musique FROM musique")->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les artistes pour le formulaire
$artistes = $bdd->query("SELECT id_chanteur, nom_chanteur FROM chanteur")->fetchAll(PDO::FETCH_ASSOC);

// Traiter le formulaire lorsqu'il est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_utilisateur = $_SESSION['user_id'];
    $id_musique = $_POST['id_musique'] ?? null;
    $id_auteur = $_POST['id_auteur'] ?? null;
    $texte = trim($_POST['texte']);

    if ($texte !== '') {
        $stmt = $bdd->prepare("INSERT INTO commentaires (id_utilisateur, id_musique, id_auteur, texte) VALUES (:id_utilisateur, :id_musique, :id_auteur, :texte)");
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->bindParam(':id_musique', $id_musique);
        $stmt->bindParam(':id_auteur', $id_auteur);
        $stmt->bindParam(':texte', $texte);
        $stmt->execute();

        // Rediriger vers la page tracksphere.php après l'ajout du commentaire
        header("Location: tracksphere.php");
        exit();
    } else {
        $error_message = "Le texte du commentaire ne peut pas être vide.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tracksphere</title>
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>

<section class="afficher">
<nav><a href="../index.php" style="text-decoration:none"><img id="imgpetit" src="../image/logopetit.png"></a></nav>
    <nav><a href="tracksphere.php" style="text-decoration:none">Retour sur la TrackSphere</a></nav>
</section>

<header><h1>TrackTalk</h1></header>

<article>
    <h2>Ajouter un commentaire :</h2>
    <?php
    if (isset($error_message)) {
        echo "<p style='color: red;'>$error_message</p>";
    }
    ?>
    <form action="" method="post">
        <label for="id_musique">Musique :</label>
        <select name="id_musique" id="id_musique">
            <option value="">--Choisir une musique--</option>
            <?php foreach ($musiques as $musique): ?>
                <option value="<?= htmlspecialchars($musique['id_Musique']) ?>"><?= htmlspecialchars($musique['titre_musique']) ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label for="id_auteur">Artiste :</label>
        <select name="id_auteur" id="id_auteur">
            <option value="">--Choisir un artiste--</option>
            <?php foreach ($artistes as $artiste): ?>
                <option value="<?= htmlspecialchars($artiste['id_chanteur']) ?>"><?= htmlspecialchars($artiste['nom_chanteur']) ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label for="texte">Commentaire :</label>
        <textarea name="texte" id="texte" rows="4" cols="50" required></textarea>
        <br><br>

        <input type="submit" value="Valider">
    </form>
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