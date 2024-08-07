<html>
    <head>
        <meta charset="UTF-8">
        <title>TrackBase</title>
        <link rel="stylesheet" href="../style/style.css">
    </head>

    <body>

    <section>
        <nav><a href="../index.php" style="text-decoration:none"><img id="imgpetit" src="../image/logopetit.png"></a></nav>
        <?php
        session_start();
        $bdd = new PDO('mysql:host=localhost;dbname=trackbase_alpha2', 'root', 'trackbase');

        if(isset($_SESSION['username'])) {
            // Récupérer les informations de l'utilisateur
            $query = $bdd->prepare("SELECT pseudo, photo_profil FROM users WHERE pseudo = ?");
            $query->execute([$_SESSION['username']]);
            $user = $query->fetch(PDO::FETCH_ASSOC);
        }
        
        // Vérifier si l'utilisateur est connecté
        $user_message = "";
        if(isset($_SESSION['username'])) {
            $user_message = "Connecté en tant que : " . $_SESSION['username'];
        }
        
        // Gérer la déconnexion
        if(isset($_GET['logout'])) {
            session_unset();
            session_destroy();
            header("Location: main.php");
            exit();
        }


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

        <header><a href='main.php'>
            <img id="img" src="../image/trackbase_logo.png">
        </a></header>
        <article><h2>Conditions Générales</h2></article>
    <section>
        <article>
            <h2>I. Introduction : </h2>
            <br>Bienvenue sur TrackBase, une communauté dédiée à la collection et au partage d'informations musicales. En accédant à notre site, vous acceptez de respecter les présentes conditions générales d'utilisation.        
        </article>

        <article>
            <h2>II. Définitions :</h2>
            <br>- Utilisateur : Toute personne utilisant le site.
            <br>- Contributeur : Utilisateur qui soumet du contenu à la base de données.
            <br>- Contenu : Toutes les informations, données, textes, photographies, graphiques, etc., soumises par les utilisateurs.      
        </article>
    </section>
    <section>
        <article>
            <h2>III. Inscription :</h2>
            <br>Pour contribuer, vous devez créer un compte en fournissant des informations exactes et à jour. Vous êtes responsable de la confidentialité de vos identifiants de connexion.        
        </article>

        <article>
            <h2>IV. Utilisation du Site :</h2>
            <br>Le site est destiné à un usage personnel et non commercial. Toute utilisation contraire aux lois en vigueur est strictement interdite.        
        </article>
    </section>
    <section>
        <article>
            <h2>V. Contributions :</h2>
            <br>En soumettant du contenu, vous garantissez que vous détenez les droits nécessaires et que ce contenu ne viole aucun droit de tiers. Vous accordez au site une licence non exclusive, mondiale et gratuite pour utiliser, modifier, et diffuser votre contenu.
        </article>
        <article>
            <h2>VI. Modération :</h2>
            <br>Nous nous réservons le droit de modifier ou supprimer tout contenu inapproprié, sans préavis. Les décisions des modérateurs sont finales.      
        </article>
    </section>
<section>
    <article>
        <h2>VII. Propriété Intellectuelle :</h2>
        <br>Tous les éléments du site, y compris les contributions des utilisateurs, sont protégés par les lois sur la propriété intellectuelle. Vous ne pouvez pas reproduire, distribuer, ou créer des œuvres dérivées sans autorisation.
    </article>
    <article>
        <h2>VIII. Responsabilité :</h2>
        <br> Nous ne pouvons garantir l'exactitude ou la complétude des informations présentes sur le site. Vous utilisez le site à vos propres risques.       
    </article>
</section>
<section>
    <article>
        <h2>IX. Protection des Données:</h2>
        <br>Nous respectons votre vie privée. Les informations personnelles collectées sont traitées conformément à notre Politique de Confidentialité.
    </article>
    <article>
        <h2>X. Comportement de l'Utilisateur:</h2>
        <br>Vous vous engagez à ne pas :

        <br>- Usurper l'identité d'autrui.
        <br>- Publier des contenus diffamatoires, obscènes, ou offensants.
        <br>- Harceler ou intimider d'autres utilisateurs.        
    </article>
</section>
<section>
    <article>
        <h2>XI. Sécurité:</h2>
        <br>Vous êtes responsable de la sécurisation de votre compte. Signalez-nous immédiatement toute utilisation non autorisée de votre compte.
    </article>
    <article>
        <h2>XII. Publicité et Partenariats:</h2>
        <br>Nous pouvons afficher des publicités sur le site. Les relations commerciales avec nos partenaires n'influencent pas le contenu éditorial du site.        
    </article>
</section>
<section>
    <article>
        <h2>XIII. Modifications des Conditions:</h2>
        <br>Nous nous réservons le droit de modifier ces conditions à tout moment. Vous serez informé des modifications par un avis sur le site. La poursuite de l'utilisation du site après modifications vaut acceptation des nouvelles conditions.
    </article>
    <article>
        <h2>XIV. Résiliation:</h2>
        <br>Nous pouvons suspendre ou résilier votre accès au site en cas de violation des présentes conditions. Vous pouvez résilier votre compte à tout moment.        
    </article>
</section>
<section>
    <article>
        <h2>XV. Liens Externes:</h2>
        <br>Le site peut contenir des liens vers des sites tiers. Nous ne sommes pas responsables du contenu de ces sites externes.
    </article>
    <article>
        <h2>XVI.  Indemnisation:</h2>
        <br>Vous acceptez d'indemniser et de dégager de toute responsabilité le site, ses propriétaires, et ses employés pour toute réclamation ou demande, y compris les honoraires d'avocat, découlant de votre utilisation du site ou de votre violation des présentes conditions.        
    </article>
</section>
<section>
    <article>
        <h2>XVII. Loi Applicable:</h2>
        <br>Ces conditions sont régies par les lois du pays où le site est basé. Tout litige sera soumis à la juridiction exclusive des tribunaux compétents de cette juridiction.
    </article>
    <article>
        <h2>XVIII. Contact:</h2>
        <br>Pour toute question concernant ces conditions, vous pouvez nous contacter à l'adresse suivante : Matthieu.57@sfr.fr.        
    </article>
</section>

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
