<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>TrackBase - Connexion</title>
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>

<?php
// Démarrer la session
session_start();

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connexion à la base de données
    try {
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=trackbase_alpha2', 'root', 'trackbase');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupérer les données du formulaire
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Préparer et exécuter la requête SQL pour récupérer l'utilisateur
        $stmt = $pdo->prepare("SELECT * FROM users WHERE pseudo = :pseudo");
        $stmt->bindParam(':pseudo', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérifier si l'utilisateur existe et si le mot de passe est correct
        if ($user && password_verify($password, $user['mot_de_passe'])) {
            // Stocker les informations de l'utilisateur dans la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['pseudo'];

            // Rediriger l'utilisateur vers la page principale ou une page de succès
            header("Location: main.php");
            exit();
        } else {
            $error_message = "Nom d'utilisateur ou mot de passe incorrect.";
        }
    } catch (PDOException $e) {
        $error_message = "Erreur de connexion à la base de données : " . $e->getMessage();
    }
}
?>

<section>
    <nav><a href="../index.html" style="text-decoration:none">Accueil</a></nav>
    <nav><a href="main.php" style="text-decoration:none">TrackBase</a></nav>
</section>

<div class="login-container">
<div class="login-box">
    <h2>Connexion</h2>
    <?php
    if (isset($error_message)) {
        echo "<p style='color: red;'>$error_message</p>";
    }
    ?>
    <form action="" method="post">
        <input type="text" name="username" placeholder="Nom d'utilisateur" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <input type="submit" value="Se Connecter">
    </form>
    <p>Vous n'avez pas de compte ? <a href="register.php">Inscrivez-vous ici</a>.</p>
    </div>
</div>

</body>
</html>