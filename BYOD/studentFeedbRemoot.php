<?php
require_once("../config.php");
require_once("../course/lib.php");
require_once("studentFeedbBootstrap.php");
?>
<!doctype html>
<html >
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title><?php echo "Feedback Remoot"; ?></title>
<!--        <meta http-equiv="refresh" content="5">
        <link rel="stylesheet" href="css/pure-min.css" />
        <link rel="stylesheet" href="css/font-awesome-min.css" />
        <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
        <link rel="stylesheet" href="css/remote.css" />  -->
        <link rel="stylesheet" href="studentRemoteStyles.css" />
	<script>
		function zapInit() {
				document.getElementById("voter").style.color = "magenta";
				
		}
		
		function compterReponses() {
			boutons = document.getElementsByClassName("VoteBtn");
			var nbchx=0;
			for (i = 0; i < boutons.length; i++) {
				if (boutons[i].checked) {
					nbchx++;
				}
			}
			return nbchx;
		}
		function validerSubmit(min,max) {
			var nb = compterReponses();
			if ((nb >= min) && (nb <= max)) {
				document.getElementById("btnvoter").style.background = "green";
			}
			else {
				document.getElementById("btnvoter").style.background = "orange";
			}

			document.getElementById("compteur").innerHTML=nb.toString();
		}
		
		function validerForm(min,max) {
			var nbchx = compterReponses();
			if ((nbchx >= min) && (nbchx <= max)) {
				return true;
			}
			else {
				if (nbchx < min) {		
					alert("Validez au moins "+min.toString()+" choix");
					return false;
				}
				else if (nbchx > max) {		
					alert("Validez au plus "+max.toString()+" choix");
					return false;
				}
			}
		}
	</script>
    </head>
    <body>
        <div><section id="traces">
        <?php
           if($cfini) {
            ?>
            </section></div>
            <nav><article><div>Au revoir<div></article></nav>
        <?php
                exit();
            } 

        if(false /* $dejavote*/) {
            ?>
            </section></div>
                <nav><article><div>Vous avez déjà participé<br>Merci<div></article>
                <form action="" method="GET" >
                    <input name="cfini" id="cfini" type="submit" value="cfini"  >
                    <label id="btnfini" for="cfini" >continuer</label>

                </form>
                </nav>
        <?php
                exit();
            } 
           echo "Userid : ".$userid."<br>";

            if (empty($USER->id)){
            ?>
            </section></div>
                <nav><article><div>Vous n'êtes pas connecté<br>Merci<div></article>
                <form action="https://moodle-admin-qualif.parisdescartes.fr/login/index.html" method="GET" >
                    <input name="continue" type="submit" value="continuer"  >
                    <label id="btnfini" for="continue" >continuer</label>

                </form>
                </nav>
        <?php
                exit();
            } 
        
            if ($USER->id == 1){
            ?>
            </section></div>
                <nav><article><div>Vous ne pouvez pas voter avec une connexion anonyme<br>Merci<div></article>
                <form action="/login/index.php" method="GET" >
                    <input name="continue" type="submit" value="continuer"  >
                    <label id="btnfini" for="continue" >continuer</label>

                </form>
                                </nav>
        <?php
                exit();
                
            }
        
 
        
        
            if($voter) { 
                echo " feedbackid=$feedbackid";
                // s'il y a déjà une tentative c'est une erreur
                //$attempts = $DB->get_records_select('questionnaire_attempts', " qid=$questid AND userid=$userid ");
/*
                // chercher les questions du feedback
                $fbitems = $DB->get_records_select('feedback_item', " feedback=$feedbackid AND typ LIKE 'multichoice' AND required=1 ");
                if (!$fbitems) {
					exit("Pas de questions active de type multichoice dans ce feedback");
				}
				// find  first active item
				$itemid=-1;
				foreach ($fbitems as $fbitem) {
					if ($itemid=$fbitem.id) {
						break;
					}
					
				}
*/
                //$attempts = $DB->get_records_select('feedback_attempts', " qid=$questid AND userid=$userid ");
                $attempts = $DB->get_records_select('feedback_complete', " feedback=$feedbackid AND userid=$userid ");
                if ($attempts){
                ?>
                    </section></div>
                    <nav><article><div>Vous avez déjà participé<br>Merci<div></article>
                    <form action="" method="GET" >
                        <input name="cfini" id="cfini" type="submit" value="cfini"  >
                        <label id="btnfini" for="cfini" >continuer</label>

                    </form>
                                </nav>
            <?php
                    exit();
                } 
            $subtime = time();
            
			// create fb_complete record

            $resp['survey_id'] = $survid;
            $resp['submitted'] = $subtime;
            $resp['complete']  = "y";
            $resp['grade']     = 0;
            $resp['username'] = $userid;
            //echo print_r($resp,true);
            $respid = $DB->insert_record("questionnaire_response", (object)$resp);
                
            $attempt['qid'] = $questid;
            $attempt['userid'] = $userid;
            $attempt['rid'] = $respid;
            $attempt['timemodified'] = time();
            //echo print_r($attempt,true);
            $attemptid = $DB->insert_record("questionnaire_attempts", (object)$attempt);

            foreach($_GET['chx'] as $valeur)
                {
                    echo "<br><br>Le choix $valeur a été selectionné<br><br>";
                    $repmul['response_id'] = $respid;
                    $repmul['question_id'] = $questionid;
                    $repmul['choice_id'] = $valeur;
                    //echo print_r($repmul,true);
                    $repmulid = $DB->insert_record("questionnaire_resp_multiple", (object)$repmul);

                }

            ?>
            </section></div>
            <nav><article><div>Merci d'avoir voté<div></article>
            <form action="" method="GET" >
                <input name="cfini" id="cfini" type="submit" value="cfini"  >
                <label id="btnfini" for="cfini" >continuer</label>
                
            </form>
            
  <?php }
        else {
    

            // Détermination du code du module feedback
            $codefb = $DB->get_record('modules', array('name' => "feedback"), '*', MUST_EXIST);
            $codefeedback = $codefb->id; 

        
        
            if (empty($runningq)) {
				$feedbackid = 0;
                foreach ($modules as $module) {
                    $cm = $DB->get_record('course_modules', array('id' => $module), '*', MUST_EXIST);
                    if ($cm->module == $codequestionnaire) {
                        // ajouter ce cm à la liste
                        $qid = $cm->instance;
                        echo "Questionnaire_id calculé : ".$qid."<br>";
                        if ($cm->visible) {
                            // lets simulate form and moodle traitment
                            $questid = $qid;
                            break;  // on ne peut activer qu'un questionnaire à la fois
                            
                        }
                    }
                }
				if ($questid === 0) {
            ?>
            </section></div>
                <nav><article><div>Aucun vote n'est ouvert<div></article>
                <form action="" method="GET" >
                    <input id="cfini" type="submit" value="cfini"  >
                    <label id="btnfini" for="cfini" >continuer</label>

                    <input type="hidden" name="sectionid" value="<?php echo $sectionid; ?>" >
                </form>
                                </nav>
        <?php
					exit();
				}
                ?>


                <?php
            } else {

                $questid = $runningq;
                echo "Questionnaire_id transmis : ".$questid."<br>";
            }
            $courseid = $course->id;
            echo "Course_id : ".$courseid."<br>";
                
            $sectionid = $section->id;
            echo "Section_id : ".$sectionid."<br>";
                
            $quest = $DB->get_record('questionnaire', array('id' => $questid), '*', MUST_EXIST);
            $questname = $quest->name;
            echo "Questname : ".$questname."<br>";

			$survid = $quest->sid;
            echo "Surveyid : ".$survid."<br>";



            // s'il y a déjà une tentative on ne peut pas voter
            $attempts = $DB->get_records_select('questionnaire_attempts', " qid=$questid AND userid=$userid ");
            if ($attempts){
            ?>
            </section></div>
                <nav><article><div>Vous avez déjà participé<br>Merci<div></article>
                <form action="" method="GET" >
                    <input name="cfini" id="cfini" type="submit" value="cfini"  >
                    <label id="btnfini" for="cfini" >continuer</label>

                </form>
                                </nav>
        <?php
                exit();
            } 

            
            
            
            echo "Questionnaire " . $questname . " (".$questid.")";


			if (empty($survid)) {
				exit("Survid non défini");
			}

            // extraire les questions du questionnaire
            if (!($questions = $DB->get_records_select('questionnaire_question', "survey_id=$survid AND deleted='n'"))) {
                exit("Aucune question disponible");
            }
            echo "</section></div>";

            echo "<nav>\n";
 
            foreach ($questions as $question) {
                $qid = $question->id;
				$min = $question->length;
				$max = $question->precise;
                echo "<article><div>" . $question->name . "<div></article>";

                // determiner le type de question
                $qtype = $question->type_id;
                if ($qtype == 5) {
                    $tabrep = 'questionnaire_resp_multiple';
                } else if ($qtype == 4) {
                    $tabrep = 'questionnaire_resp_single';
                } else {
                    exit("Type de question incorrect");
                }

                // chercher les choix proposées
                if (!($choices = $DB->get_records_select('questionnaire_quest_choice', "question_id=$qid"))) {
                    exit("Aucun choix proposé dans cette question");
                }
				?>
                <form action="studentRemote.php" method="get"  
					onsubmit="return validerForm(<?php echo $min.','.$max ?>)" >
				<?php

/*                echo '<table border="1">'; */
                $lettre=0;
                foreach ($choices as $choice) {
                    $choicetext = $choice->content;
                    $choiceid = $choice->id;
/*
*                         // compter le nb réponses où ce choix à été sélectionné

                    // TODO : si on autorise la modification des réponses, il faut filtrer sur response_id in ($listRep)
                    $choicenb = $DB->count_records_select($tabrep, "question_id = $qid AND choice_id = $choiceid ");
                    if ($choicenb > 0) {
                        $n = ($choicenb * 100 );
                        $m = $n / $nbAttempts;
                        $choicepc = sprintf("%1.2f", $m);
                        $graph = '<progress width="100%" value="' . $choicepc .'" max="100" position="0"></progress> ' .round($choicepc, 1) .'%';

                    } else {
                        $choicepc = 0;
                        $graph = "0%";
                    }
*/
                    $cbId="cb".chr(ord('1')+$lettre);
                    $cbName="chx".chr(ord('A')+$lettre);
                    $cbValue=chr(ord('A')+$lettre);
                    ?>
            
<!--        <tr><td>  -->
                    <input 
                        id="<?php echo $cbId ?>" 
                        type="checkbox" 
                        class="VoteBtn" 
                        name="chx[]" 
                        value="<?php echo $choiceid ?>" 
                        onclick="validerSubmit(<?php echo $min.','.$max ?>)"
                        >

                    <label for="<?php echo $cbId ?>"  ><?php echo $cbValue ?></label>
                    
<!--            </td> -->



                    <?php 

//                    echo "<td>" . $choiceid . "</td>";
//                    echo "<td>" . $choicetext . "</td>";
                    $lettre++;
                }
/*                    echo "</tr></table>"; */
                    ?>


                    <br>
                    <input id="voter" name="voter" type="submit" class="VoteBtn" value="1" >
                    <label id="btnvoter" for="voter">voter</label>


                    <br><br>
                    <input id="annuler" type="reset" name="currentq"  value="reset">
                    <label id="btnannul" for="annuler"><small><small>annuler</small></small></label>


                    <input type="hidden" name="courseid" value="<?php echo $courseid; ?>" >
                    <input type="hidden" name="sectionid" value="<?php echo $sectionid; ?>" >
                    <input type="hidden" name="questid" value="<?php echo $questid; ?>" >
                    <input type="hidden" name="survid" value="<?php echo $survid; ?>" >
                    <input type="hidden" name="questionid" value="<?php echo $qid; ?>" >
<!--                <input type="hidden" name="maxrep" value="<?php //echo $maxrep; ?>" > -->
                    <div>

                    </div>


                    </form>
        </div>
        <?php
    }
           echo "</nav>\n";

?>

        

        </div>
        <?php } ?>
    </body>
</html>
