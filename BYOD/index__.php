<?php

if (isset($_GET["sectionid"])) {
    $sectionid = filter_input(INPUT_GET, "sectionid", FILTER_SANITIZE_NUMBER_INT, FILTER_VALIDATE_INT);
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>BYOD</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/pure-min.css" />
        <link rel="stylesheet" href="css/font-awesome-min.css" />
        <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">

        
        <!--[if lte IE 8]>
  
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/grids-responsive-old-ie-min.css">
  
<![endif]-->
<!--[if gt IE 8]><!-->
  
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/grids-responsive-min.css">
  
<!--<![endif]-->


        <link rel="stylesheet" href="css/controle.css" />

        <script>
            function pop(fichier, fenetre) {
                ff = window.open(fichier, fenetre, "width=500,height=800,left=130,top=520,\n\
        directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=no");
            }

        </script>
    </head>
    <body>

        <div class="splash-container">
            <div class="splash">
                <h1 class="splash-head">Vos activités en BYOD</h1>
                <p class="splash-subhead">
                    Avant d'activer la zapette, vérifier que vous êtes connecté à votre plateforme Moodle.<br /><br />

                    
                </p>
                <p>
                    <a href="remote.php?sectionid=<?php echo $sectionid; ?>" target="popup" class="pure-button pure-button-primary" 
                       onClick="pop(this.href, this.target);
                               return false">Activation de la zapette</a>
                </p>
            </div>
        </div>

        <div class="content-wrapper">
            <div class="content">
                <h2 class="content-head is-center">Lorem ipsum dolor...</h2>

                <div class="pure-g">
                    <div class="l-box pure-u-1 pure-u-md-1-2 pure-u-lg-1-4">

                        <h3 class="content-subhead">
                            <i class="fa fa-rocket"></i>
                            Bien démarrer
                        </h3>
                        <p>
                            Phasellus eget enim eu lectus faucibus vestibulum. Suspendisse sodales pellentesque elementum.
                        </p>
                    </div>
                    <div class="l-box pure-u-1 pure-u-md-1-2 pure-u-lg-1-4">
                        <h3 class="content-subhead">
                            <i class="fa fa-th-large"></i>
                            Visibilité des questions
                        </h3>
                        <p>
                            Phasellus eget enim eu lectus faucibus vestibulum. Suspendisse sodales pellentesque elementum.
                        </p>
                    </div>
                    <div class="l-box pure-u-1 pure-u-md-1-2 pure-u-lg-1-4">
                        <h3 class="content-subhead">
                            <i class="fa fa-stethoscope"></i>
                            Visualisation des résultats
                        </h3>
                        <p>
                            Phasellus eget enim eu lectus faucibus vestibulum. Suspendisse sodales pellentesque elementum.
                        </p>
                    </div>
                    <div class="l-box pure-u-1 pure-u-md-1-2 pure-u-lg-1-4">
                        <h3 class="content-subhead">
                            <i class="fa fa-check-square-o"></i>
                            Bla bla bla
                        </h3>
                        <p>
                            Phasellus eget enim eu lectus faucibus vestibulum. Suspendisse sodales pellentesque elementum.
                        </p>
                    </div>
                </div>
            </div>

            <div class="ribbon l-box-lrg pure-g">
                <div class="l-box-lrg is-center pure-u-1 pure-u-md-1-2 pure-u-lg-2-5">
                    <img class="pure-img-responsive" alt="File Icons" width="300" src="img/common/file-icons.png">
                </div>
                <div class="pure-u-1 pure-u-md-1-2 pure-u-lg-3-5">

                    <h2 class="content-head content-head-ribbon">Laboris nisi ut aliquip.</h2>

                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                        tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                        quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                        consequat. Duis aute irure dolor.
                    </p>
                </div>
            </div>


            <div class="footer l-box is-center">
               2014 Université Paris Descartes / TICE
            </div>

        </div>


    </body>
</html>