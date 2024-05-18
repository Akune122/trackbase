<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Vérifier si l'utilisateur a cliqué sur le lien de déconnexion
if (isset($_GET['logout'])) {
    // Terminer la session
    session_unset();
    session_destroy();
    // Rediriger vers la page de connexion
    header("Location: login.php");
    exit();
}

// Récupérer le pseudo de l'utilisateur connecté
$username = $_SESSION['username'];

// Connexion à la base de données
try {
    $bdd = new PDO('mysql:host=localhost;dbname=trackbase_alpha2', 'root', 'trackbase');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Récupérer les informations de l'utilisateur depuis la base de données
$stmt = $bdd->prepare("SELECT * FROM users WHERE pseudo = :username");
$stmt->bindParam(':username', $username);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifier si l'utilisateur existe dans la base de données
if (!$user) {
    // Rediriger vers une page d'erreur ou afficher un message d'erreur
    echo "Utilisateur introuvable.";
    exit();
}

// Les informations de l'utilisateur sont maintenant stockées dans $user
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil - <?php echo $user['pseudo']; ?></title>
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
<section class="afficher">
    <nav><a href="../index.php" style="text-decoration:none">Accueil</a></nav>
    <nav><a href="?logout=true" style="text-decoration:none">Déconnexion</a></nav>
</section>

<header><h1>TrackBase</h1></header>

<article>
    <h2><center>Profil de <?php echo $user['pseudo']; ?></center></h2>
    <p><strong>Pseudo:</strong> <?php echo $user['pseudo']; ?></p>
    <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
    <!-- Afficher d'autres informations de l'utilisateur si nécessaire -->
</article>

</body>
</html>