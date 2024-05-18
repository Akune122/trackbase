<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>TrackBase</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>

    <section>
        <nav><a href="html/main.php" style="text-decoration:none">TrackBase</a></nav>
        <?php
        if (isset($_SESSION['username'])) {
            echo "<nav>Connecté en tant que : " . htmlspecialchars($_SESSION['username']) . "</nav>";
            echo "<nav><a href='?logout=true' style='text-decoration:none'>Se déconnecter</a></nav>";
        } else {
            echo "<nav><a href='html/login.php' style='text-decoration:none'>Connexion</a></nav>";
        }
        ?>
    </section>

    <?php
    // Gérer la déconnexion
    if (isset($_GET['logout'])) {
        session_unset();
        session_destroy();
        header("Location: html/main.php");
        exit();
    }
    ?>

    <header>
        <a href='html/main.php'>
            <img id="img" src="image/trackbase_logo.png" alt="TrackBase Logo">
        </a>
    </header>
    <article>
        <h2>C'est quoi TrackBase ??</h2>
    </article>

    <section>
        <article>
            <img id="img" src="image/TRACKBASE_ANIM_V2.gif" alt="TrackBase Animation"/>
        </article>
        
        <article>
            TrackBase est une base de données spécialisée exclusivement dans le domaine de la musique. 
            <br> Cette plateforme en ligne offre aux utilisateurs un accès à une vaste collection de données sur des morceaux, des artistes, des albums et bien plus encore. 
            <br>Que vous cherchiez des informations sur vos chansons préférées, les artistes émergents ou les tendances musicales, TrackBase vous permet d'explorer et de découvrir une multitude de ressources musicales.
            <br>Avec son interface conviviale et ses fonctionnalités de recherche avancées, TrackBase est l'outil parfait pour les passionnés de musique désireux d'approfondir leurs connaissances et leur appréciation de cet art universel.
        </article>
    </section>

    <article>
        <h2>Pourquoi TrackBase ?</h2>
    </article>

    <section>
        <article>
            Nous sommes un groupe d'élève en 1ère année d'informatique à l'ESTIAM.
            <br> En tant qu'étudiant, nous écoutons beaucoup de musique et le calvaire de les retrouver
            <br> dans d'énormes playlists ne nous facilitent pas la vie. Pour permettre de retrouver toutes les informations de vos musiques préférées,
            <br> TrackBase a été développé pour vous.
        </article>
        
        <article>
            <img id="img" src="image/estiam.jpg" alt="ESTIAM"/>
        </article>
    </section>

    <p><br><br></p>

    <footer>
        <section>
            <nav>Contact : 
                <br>+33 6 59 32 72 14 
                <br>trackbase@estiam.com
            </nav> 
        </section>
    </footer>

</body>
</html>