<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>TrackBase - Créer un compte</title>
    <link rel="stylesheet" href="../style/style.css">
    <style>
        .password-requirements {
            list-style-type: none;
            padding: 0;
            display: none;
        }
        .password-requirements li {
            color: red;
        }
        .password-requirements li.valid {
            color: green;
        }
        .error-message {
            color: red;
        }
    </style>
    <script>
        function validatePassword() {
            const password = document.getElementById('password').value;
            const lengthRequirement = document.getElementById('lengthRequirement');
            const uppercaseRequirement = document.getElementById('uppercaseRequirement');
            const symbolRequirement = document.getElementById('symbolRequirement');

            // Check the length of the password
            if (password.length >= 8) {
                lengthRequirement.classList.add('valid');
            } else {
                lengthRequirement.classList.remove('valid');
            }

            // Check for at least one uppercase letter
            if (/[A-Z]/.test(password)) {
                uppercaseRequirement.classList.add('valid');
            } else {
                uppercaseRequirement.classList.remove('valid');
            }

            // Check for at least one symbol
            if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                symbolRequirement.classList.add('valid');
            } else {
                symbolRequirement.classList.remove('valid');
            }
        }

        function showPasswordRequirements() {
            document.querySelector('.password-requirements').style.display = 'block';
        }

        function hidePasswordRequirements() {
            document.querySelector('.password-requirements').style.display = 'none';
        }
    </script>
</head>
<body>

<?php
// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connexion à la base de données
    try {
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=trackbase_alpha2', 'root', 'trackbase');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupérer les données du formulaire
        $pseudo = $_POST['pseudo'];
        $email = $_POST['email'];
        $password = $_POST['mot_de_passe'];
        $date_naissance = $_POST['date_naissance'];

        // Vérifier que les champs ne sont pas vides
        if (!empty($pseudo) && !empty($email) && !empty($password) && !empty($date_naissance)) {
            // Calculer l'âge de l'utilisateur
            $birthDate = new DateTime($date_naissance);
            $today = new DateTime('today');
            $age = $today->diff($birthDate)->y;

            // Vérifier si l'utilisateur a au moins 15 ans
            if ($age < 15) {
                $error_message = "Vous devez avoir au moins 15 ans pour créer un compte.";
            } else {
                // Vérifier si le pseudo ou l'email est déjà utilisé
                $stmt_check = $pdo->prepare("SELECT * FROM users WHERE pseudo = :pseudo OR email = :email");
                $stmt_check->bindParam(':pseudo', $pseudo);
                $stmt_check->bindParam(':email', $email);
                $stmt_check->execute();

                if ($stmt_check->rowCount() > 0) {
                    $error_message = "Le pseudo ou l'email est déjà utilisé. Veuillez choisir un autre.";
                } else {
                    // Vérifier la longueur et le contenu du mot de passe
                    if (strlen($password) < 8) {
                        $error_message = "Le mot de passe doit contenir au moins 8 caractères.";
                    } elseif (!preg_match('/[A-Z]/', $password)) {
                        $error_message = "Le mot de passe doit contenir au moins une majuscule.";
                    } elseif (!preg_match('/[\W]/', $password)) {
                        $error_message = "Le mot de passe doit contenir au moins un symbole.";
                    } else {
                        // Hachage du mot de passe
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                        // Préparer et exécuter la requête SQL pour insérer le nouvel utilisateur dans la base de données
                        $stmt_insert = $pdo->prepare("INSERT INTO users (pseudo, email, mot_de_passe, Date_naissance) VALUES (:pseudo, :email, :mot_de_passe, :date_naissance)");
                        $stmt_insert->bindParam(':pseudo', $pseudo);
                        $stmt_insert->bindParam(':email', $email);
                        $stmt_insert->bindParam(':mot_de_passe', $hashed_password);
                        $stmt_insert->bindParam(':date_naissance', $date_naissance);

                        if ($stmt_insert->execute()) {
                            // Rediriger l'utilisateur vers une page de succès ou une autre page de ton choix
                            header("Location: login.php");
                            exit();
                        } else {
                            $error_message = "Une erreur est survenue lors de la création de votre compte. Veuillez réessayer.";
                        }
                    }
                }
            }
        } else {
            $error_message = "Veuillez remplir tous les champs.";
        }
    } catch (PDOException $e) {
        $error_message = "Erreur de connexion à la base de données : " . $e->getMessage();
    }
}
?>

<section>
    <nav><a href="../index.php" style="text-decoration:none"><img id="imgpetit" src="../image/logopetit.png"></a></nav>
    <nav><a href="main.php" style="text-decoration:none">TrackBase</a></nav>
</section>

<div class="login-container">
    <div class="login-box">
        <h2>Créer un compte</h2>
        <?php
        if (isset($error_message)) {
            echo "<p class='error-message'>$error_message</p>";
        }
        ?>
        <form action="" method="post">
            <input type="text" name="pseudo" placeholder="Pseudo" required>
            <?php
            if (isset($error_message) && strpos($error_message, 'pseudo') !== false) {
                echo "<p class='error-message'>$error_message</p>";
            }
            ?>
            <input type="email" name="email" placeholder="Adresse email" required>
            <?php
            if (isset($error_message) && strpos($error_message, 'email') !== false) {
                echo "<p class='error-message'>$error_message</p>";
            }
            ?>
            <input type="password" id="password" name="mot_de_passe" placeholder="Mot de passe" required onfocus="showPasswordRequirements()" onblur="hidePasswordRequirements()" oninput="validatePassword()">
            <ul class="password-requirements">
                <li id="lengthRequirement">Le mot de passe doit contenir au moins 8 caractères.</li>
                <li id="uppercaseRequirement">Le mot de passe doit contenir au moins une majuscule.</li>
                <li id="symbolRequirement">Le mot de passe doit contenir au moins un symbole.</li>
            </ul>
            <input type="date" name="date_naissance" placeholder="Date de naissance" required>
            <input type="submit" value="Créer un compte">
        </form>
        <p>Vous avez déjà un compte ? <a href="login.php">Connectez-vous ici</a>.</p>
    </div>
</div>
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
