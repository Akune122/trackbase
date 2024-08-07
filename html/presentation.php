<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../style/style.css">
    <title>TRACKBASE</title>
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

    <article id="txt">
        <p class="grande-police">
            <h3><center>Bien le bonjour cher internaute,<br>
            A travers cette page de présentation, vous en apprendrez davantage sur qui nous sommes<br>
            et surtout vous comprendrez à quel point notre site internet est tout à fait innovant et peut être très utile au quotidien.</center>
            </h3></p>
    </article>
    <p><br></p>
    <h2 class="grande-police"><b><center>I. Qui sommes-nous?</center></b></h2>
    <section>
        <article id="txt">
            <p class="grande-police">
                <center><i> Vous vous demandez bien qui a fait ce magnifique site ? Eh bien, c’est dans vos droits !</i><br>
                Détrompez-vous, nous ne sommes pas des professionnels mais juste un groupe d’amis passionnés par la musique et l’informatique,<br>
                qui en ont assez de patauger dans la recherche de musique.<br>
                <br>
                C’est pourquoi nous avons décidé de réaliser ce projet dans le cadre de notre première année scolaire en informatique, à l’ESTIAM, sur le campus de Metz.</center>
            </p>
        </article>
    </section>
    <p><br></p>
    <p><br></p>
    <h2 class="grande-police"><b><center>II. Présentation de Trackbase</center></b></h2>
    <center>
        <section>
            <article id="txt">
                <p class="grande-police">
                    <b>Si vous êtes fan de musique mais que vous ne vous rappelez jamais des titres ou des chanteurs, Trackbase est fait pour vous, je vous le garantis !</b><br>
                    <br>
                    A l’heure actuelle, pour trouver une musique/chanson, il vous faut soit le titre de la chanson soit le chanteur en question. <br>
                    Ou alors, bien entendu, vous allez certainement penser à Shazam.<br>
                    Mais Shazam est une application pour reconnaître les chansons de façon orale et donc on ne peut pas faire de recherches écrites de chansons.<br>
                    <br>
                    Tout cela pour que vous réalisiez qu’aujourd’hui, la recherche de musique est encore beaucoup trop limitée.<br>
                    <br>
                    Pour y remédier, mes amis et moi, avons décidé de créer un site internet qui regrouperait un très grand nombre de musique classées selon différents critères<br>
                    comme l’origine de la musique, le style de la musique, certains mots clés… <br>
                    pour ainsi avoir accès à une recherche de musique beaucoup plus compétente que ce qui existe déjà aujourd’hui.<br>
                    <br>
                    <i>Si vous n’avez pas bien compris à quoi sert Trackbase, je vais vous le résumer plus simplement.</i><br>
                    <br>
                    Vous voulez trouver une musique mais vous ne connaissez ni le nom de la chanson ni l’interprète. C’est là qu’intervient notre site.<br>
                    En effet, vous allez pouvoir rechercher votre musique simplement en vous rappelant du genre de la musique ou d’un mot clé… et ainsi,<br>
                    si vous le désirez, ajouter votre musique à votre compte musical directement via notre site.<br>
                    <br>
                    De plus, notre site comporte des comptes que vous pouvez créer,  <a href="register.php"  style="color:#6633CC;text-decoration:none"><u>créer mon compte,</u></a><br>
                    pour ainsi avoir accès à un plus grand nombre de fonctionnalités telles que pouvoir enregistrer vos morceaux préférés, échanger avec les autres utilisateurs,<br>
                    relier votre compte de musique (quel qu’il soit : Deezer, Spotify, Apple Music…) à celui de Trackbase pour ainsi directement ajouter votre musique à votre compte musical….
                </p>
            </article>
        </section>
    </center>
    <p><br></p>
    <p><br></p>
    <h2 class="grande-police"><b><center>III. Pourquoi utiliser Trackbase?</center></b></h2>
    <center>
        <section>
            <article id="txt">
                <p class="grande-police">
                    <b>Si vous êtes fan de musique, Trackbase vous changera la vie !</b><br>
                    <br>
                    <i>Vous vous demandez toujours pourquoi utiliser Trackbase ? Cela est une évidence.<br></i>
                    <br>
                    Trouvez vos musiques grâce à des recherches plus performantes juste en inscrivant un mot-clé, le style, l’origine de la musique...

                    Échangez vos avis avec les autres utilisateurs.<br>
                    <br>
                    Reliez vos comptes Trackbase et musical, pour un maximum de confort.<br>
                </p>
            </article>
        </section>
    </center>
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