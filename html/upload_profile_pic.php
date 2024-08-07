<?php
session_start();

// Vérifier si un fichier a été téléchargé
if ($_FILES['photo_profil']['name']) {
    // Répertoire de destination pour enregistrer les photos de profil
    $upload_dir = "profile_pics/";
    // Nom du fichier temporaire
    $tmp_file = $_FILES['photo_profil']['tmp_name'];
    // Nom du fichier sur le serveur
    $target_file = $upload_dir . basename($_FILES['photo_profil']['name']);

    // Déplacer le fichier téléchargé vers le répertoire de destination
    if (move_uploaded_file($tmp_file, $target_file)) {
        // Connexion à la base de données
        try {
            $bdd = new PDO('mysql:host=localhost;dbname=trackbase_alpha2', 'root', 'trackbase');
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }

        // Mettre à jour le chemin de la photo de profil dans la base de données
        $username = $_SESSION['username'];
        $stmt = $bdd->prepare("UPDATE users SET photo_profil = :photo_profil WHERE pseudo = :username");
        $stmt->bindParam(':photo_profil', $target_file);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        // Rediriger vers la page de profil avec un message de succès
        header("Location: user.php?upload_success=true");
        exit();
    } else {
        echo "Erreur lors du téléchargement du fichier.";
    }
}
