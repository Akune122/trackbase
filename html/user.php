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

// Traiter le formulaire de changement de pseudo
if (isset($_POST['change_pseudo'])) {
    $new_pseudo = trim($_POST['new_pseudo']);

    // Vérifier si le nouveau pseudo est valide
    if (!empty($new_pseudo) && strlen($new_pseudo) <= 50) {
        // Vérifier si le nouveau pseudo est déjà pris
        $stmt = $bdd->prepare("SELECT COUNT(*) FROM users WHERE pseudo = :new_pseudo");
        $stmt->bindParam(':new_pseudo', $new_pseudo);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count == 0) {
            // Mettre à jour le pseudo dans la base de données
            $stmt = $bdd->prepare("UPDATE users SET pseudo = :new_pseudo WHERE pseudo = :username");
            $stmt->bindParam(':new_pseudo', $new_pseudo);
            $stmt->bindParam(':username', $username);

            if ($stmt->execute()) {
                // Mettre à jour le pseudo dans la session
                $_SESSION['username'] = $new_pseudo;
                $username = $new_pseudo;

                // Rediriger pour éviter la resoumission du formulaire
                header("Location: user.php");
                exit();
            } else {
                echo "Erreur lors du changement de pseudo.";
            }
        } else {
            echo "Le pseudo est déjà pris.";
        }
    } else {
        echo "Le nouveau pseudo est invalide.";
    }
}

// Traiter le formulaire de changement d'e-mail
if (isset($_POST['change_email'])) {
    $new_email = trim($_POST['new_email']);

    // Vérifier si le nouvel e-mail est valide
    if (!empty($new_email) && filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        // Vérifier si le nouvel e-mail est déjà pris
        $stmt = $bdd->prepare("SELECT COUNT(*) FROM users WHERE email = :new_email");
        $stmt->bindParam(':new_email', $new_email);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count == 0) {
            // Mettre à jour l'e-mail dans la base de données
            $stmt = $bdd->prepare("UPDATE users SET email = :new_email WHERE pseudo = :username");
            $stmt->bindParam(':new_email', $new_email);
            $stmt->bindParam(':username', $username);

            if ($stmt->execute()) {
                echo "Adresse e-mail mise à jour avec succès.";
            } else {
                echo "Erreur lors de la mise à jour de l'adresse e-mail.";
            }
        } else {
            echo "L'adresse e-mail est déjà associée à un compte.";
        }
    } else {
        echo "L'adresse e-mail est invalide.";
    }
}

// Traiter le formulaire de changement de mot de passe
if (isset($_POST['change_password'])) {
    $old_password = trim($_POST['old_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Récupérer l'utilisateur depuis la base de données
    $stmt = $bdd->prepare("SELECT * FROM users WHERE pseudo = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si l'utilisateur existe
    if ($user) {
        // Vérifier l'ancien mot de passe
        if (password_verify($old_password, $user['mot_de_passe'])) {
            // Vérifier que le nouveau mot de passe et la confirmation correspondent
            if ($new_password === $confirm_password) {
                // Hacher le nouveau mot de passe
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Mettre à jour le mot de passe dans la base de données
                $stmt = $bdd->prepare("UPDATE users SET mot_de_passe = :new_password WHERE pseudo = :username");
                $stmt->bindParam(':new_password', $hashed_password);
                $stmt->bindParam(':username', $username);

                if ($stmt->execute()) {
                    echo "Mot de passe mis à jour avec succès.";
                } else {
                    echo "Erreur lors de la mise à jour du mot de passe.";
                }
            } else {
                echo "Le nouveau mot de passe et la confirmation ne correspondent pas.";
            }
        } else {
            echo "L'ancien mot de passe est incorrect.";
        }
    } else {
        echo "Utilisateur introuvable.";
    }
}

// Vérifier si le formulaire a été soumis pour ajouter ou mettre à jour la description
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['description'])) {
    // Récupérer la description depuis le formulaire
    $description = $_POST['description'];

    // Mettre à jour la description dans la base de données
    $stmt = $bdd->prepare("UPDATE users SET description = :description WHERE pseudo = :username");
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    // Rediriger pour éviter la soumission multiple du formulaire
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
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

// Définir les deux dates
$date1 = new DateTime('now');
$date2 = new DateTime($user['date_creation_compte']);

// Calculer la différence entre les deux dates
$interval = $date1->diff($date2);

// Obtenir la différence absolue en années
$diffInYears = abs($interval->y);




// Les informations de l'utilisateur sont maintenant stockées dans $user
?>

<!-------------------------------------------------------------------------------------------------------------------------------------------->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil - <?php echo $user['pseudo']; ?></title>
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
<section class="afficher">
<nav><a href="../index.php" style="text-decoration:none"><img id="imgpetit" src="../image/logopetit.png"></a></nav>
    <nav><a href="?logout=true" style="text-decoration:none">Déconnexion</a></nav>
</section>

<header><h1>TrackBase</h1></header> <h3 class="fidelite"><?php if ($diffInYears > 1){ echo "Félicitations, cela fait $diffInYears ans que vous êtes avec nous."; } if ($diffInYears == 1){ echo "Félicitations, cela fait $diffInYears an que vous êtes avec nous."; } ?></h3>

<article>
    <h2><center>Profil de <?php echo $user['pseudo']; ?></center></h2>
    <p><strong>Pseudo:</strong> <?php echo $user['pseudo']; ?></p>
    <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
    <p><strong>date d'inscrition :</strong> <?php echo $user['date_creation_compte']; ?></p>
    <p><strong>Description:</strong> <?php echo $user['description']; ?></p>
    <!-- Afficher d'autres informations de l'utilisateur si nécessaire -->
    
    <!-- Afficher la photo de profil ou la couleur -->
    <?php if (strpos($user['photo_profil'], '#') === 0): ?>
        <div style="width: 100px; height: 100px; background-color: <?php echo $user['photo_profil']; ?>;"></div>
    <?php elseif ($user['photo_profil']): ?>
        <img src="<?php echo $user['photo_profil']; ?>" alt="Photo de profil" style="width: 100px; height: 100px;">
    <?php else: ?>
        <p>Aucune photo de profil définie.</p>
    <?php endif; ?>

</article>

    
    </article>

<article>
    <!-- Bouton pour afficher/masquer le formulaire -->
    <button id="optionButton" onclick="toggleForm()" class="bouton-changer-page">Options</button>
        
        <!-- Formulaire pour changer le pseudo -->
        <div id="changePseudoForm">
            <p><br></br></p>
            <h3>Changer de pseudo</h3>
            <form action="user.php" method="POST">
                <label for="new_pseudo">Nouveau pseudo :</label>
                <input type="text" id="new_pseudo" name="new_pseudo" required>
                <button type="submit" name="change_pseudo" class="bouton-changer-page">Changer de pseudo</button>
            </form>
        </div>

        <!-- Formulaire pour changer l'adresse e-mail -->
        <div id="changeEmailForm">
            <p><br></br></p>
            <h3>Changer d'adresse e-mail</h3>
            <form action="user.php" method="POST">
                <label for="new_email">Nouvelle adresse e-mail :</label>
                <input type="email" id="new_email" name="new_email" required>
                <button type="submit" name="change_email" class="bouton-changer-page">Changer d'adresse e-mail</button>
            </form>
        </div>

        <!-- Formulaire pour changer le mot de passe -->
        <div id="changePasswordForm">
            <p><br></br></p>
            <h3>Changer de mot de passe</h3>
            <form action="user.php" method="POST">
                <label for="old_password">Ancien mot de passe :</label>
                <input type="password" id="old_password" name="old_password" required><br>

                <label for="new_password">Nouveau mot de passe :</label>
                <input type="password" id="new_password" name="new_password" required><br>

                <label for="confirm_password">Confirmer le nouveau mot de passe :</label>
                <input type="password" id="confirm_password" name="confirm_password" required><br>

                <button type="submit" name="change_password" class="bouton-changer-page">Changer de mot de passe</button>
            </form>
        </div>

        <!-- Formulaire pour changer la PP -->
        <div id="changePPForm">
            <p><br></br></p>
            <h3>Télécharger une photo de profil</h3>
            <form action="upload_profile_pic.php" method="post" enctype="multipart/form-data">
                <label for="photo_profil">Télécharger une photo de profil :</label>
                <input type="file" name="photo_profil" id="photo_profil">
                <br></br>
                <button type="submit" class="bouton-changer-page">Télécharger</button>
            </form>
        </div>

        <!-- Formulaire pour ajouter ou mettre à jour la description -->
        <div id="changeDescriptionForm">
            <p><br></br></p>
            <h3>Ajouter/Mettre à jour la description</h3>
            <form method="post" action="">
                    <label for="description">Nouvelle description :</label><br>
                    <textarea name="description" id="description" rows="4" cols="50"></textarea><br>
                    <button type="submit" class="bouton-changer-page">Ajouter/Mettre à jour la description</button>
                </form>
        </div>

</article>

<script>
        function toggleForm() {
            var form1 = document.getElementById("changePseudoForm");
            var form2 = document.getElementById("changeEmailForm");
            var form3 = document.getElementById("changePasswordForm");
            var form4 = document.getElementById("changePPForm");
            var form5 = document.getElementById("changeDescriptionForm");
            var button = document.getElementById("optionButton");

            var forms = [form1, form2, form3, form4, form5];
            var isAnyFormVisible = forms.some(form => form.style.display === "block");

            forms.forEach(form => {
                form.style.display = isAnyFormVisible ? "none" : "block";
            });

            button.classList.toggle("active-button", !isAnyFormVisible);
        }

        document.addEventListener("DOMContentLoaded", function() {
            var form1 = document.getElementById("changePseudoForm");
            var form2 = document.getElementById("changeEmailForm");
            var form3 = document.getElementById("changePasswordForm");
            var form4 = document.getElementById("changePPForm");
            var form5 = document.getElementById("changeDescriptionForm");
            
            // Ensure all forms are hidden initially
            form1.style.display = "none";
            form2.style.display = "none";
            form3.style.display = "none";
            form4.style.display = "none";
            form5.style.display = "none";
        });
    </script>

</body>
</html>