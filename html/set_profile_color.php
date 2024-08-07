<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

try {
    $bdd = new PDO('mysql:host=localhost;dbname=trackbase_alpha2', 'root', 'trackbase');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['color'])) {
    $color = $_POST['color'];

    $stmt = $bdd->prepare("UPDATE users SET photo_profil = :color WHERE pseudo = :username");
    $stmt->bindParam(':color', $color);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    header("Location: user.php");
    exit();
}
