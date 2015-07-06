<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/// studentRemote01.php 
$remoteversion="15070502";

require_once("../config.php");
require_once("../course/lib.php");
require_once("studentBootstrap.php");
?>
<!doctype html>
<html >
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title><?php echo "Zappette étudiant"; ?></title>
<!--        <meta http-equiv="refresh" content="5">
        <link rel="stylesheet" href="css/pure-min.css" />
        <link rel="stylesheet" href="css/font-awesome-min.css" />
        <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
        <link rel="stylesheet" href="css/remote.css" />  -->
        <link rel="stylesheet" href="css/studentRemoteStyles01.css" />
	<script>
		function zapInit() {
				document.getElementById("voter").style.color = "magenta";
				
		}
		
		function compterReponses() {
			boutons = document.getElementsByClassName("ChxBtn");
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
				document.getElementById("valid").style.backgroundImage = "url('../img/valid72.png')";
				document.getElementById("valid").style.background = "green";
			}
			else {
				document.getElementById("valid").style.backgroundImage = "url('../img/invalid72.png')";
				document.getElementById("valid").style.backgroundImage = "orange";
			}

			document.getElementById("valid").innerHTML=nb.toString();
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
            echo "version $remoteversion.<br>";
           if(false /*$continue */) {
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
                    <br><br><br><br><br><br>
                    <input name="continue" id="continue" type="submit" value="continue"  >
                    <label class="val" id="valid" for="continue" >&nbsp;</label>

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
                    <br><br><br><br><br><br>
                    <input name="continue" type="submit" value="continuer"  >
                    <label class="val" id="valid" for="continue" >&nbsp;</label>

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
                    <br><br><br><br><br><br>
                    <input name="continue" type="submit" value="continuer"  >
                    <label class="val" id="valid" for="continue" >&nbsp;</label>

                </form>
                                </nav>
        <?php
                exit();
                
            }
        
 
        
        
            if($voter) { 
                echo " qid=$questid";
                // s'il y a déjà une tentative c'est une erreur
                $attempts = $DB->get_records_select('questionnaire_attempts', " qid=$questid AND userid=$userid ");
                if ($attempts){
                ?>
                    </section></div>
                    <nav><article><div>Vous avez déjà participé<br>Merci<div></article>
                    <form action="" method="GET" >
                        <br><br><br><br><br><br>
                        <input name="continue" id="continue" type="submit" value="continue"  >
                        <label class="val" id="valid" for="continue" >&nbsp;</label>

                        <input type="hidden" name="sectionid" value="<?php echo $sectionid; ?>" >
                    </form>
                                </nav>
            <?php
                    exit();
                } 
            $subtime = time();
            
            

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
            <br><br><br><br><br><br>
            <input name="continue" id="continue" type="submit" value="continue"  >
                <label class="val" id="valid" for="continue" >&nbsp;</label>

                <input type="hidden" name="sectionid" value="<?php echo $sectionid; ?>" >
                
            </form>
            
  <?php }
        else {
    

            // Détermination du code du module questionnaire
            $codeq = $DB->get_record('modules', array('name' => "questionnaire"), '*', MUST_EXIST);
            $codequestionnaire = $codeq->id; 

        
        
            if (empty($runningq)) {
				$questid = 0;
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
                    <br><br><br><br><br><br>
                    <input id="continue" type="submit" value="continue"  >
                    <label class="val" id="valid" for="continue" >&nbsp;</label>

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
                    <br><br><br><br><br><br>
                    <input name="continue" id="continue" type="submit" value="continue"  >
                    <label class="val" id="valid" for="continue" >&nbsp;</label>

                    <input type="hidden" name="sectionid" value="<?php echo $sectionid; ?>" >
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

                // determiner le type de question
                $qtype = $question->type_id;

                if ($qtype == 99) {
                    continue;  // saut de page
                } else if ($qtype == 5) {
                    $tabrep = 'questionnaire_resp_multiple';
                    $min = $question->length;
                    $max = $question->precise;
                } else if ($qtype == 4) {
                    $tabrep = 'questionnaire_resp_single';
                    $min = 1;
                    $max = 1;
                } else {
                    echo "<article><div>Type de question incorrect<div></article>";
                    exit();
                }

                echo "<article><div>" . $question->name . "</div></article>";

                // chercher les choix proposées
                if (!($choices = $DB->get_records_select('questionnaire_quest_choice', "question_id=$qid"))) {
                    exit("Aucun choix proposé dans cette question");
                }
                $nbchoices=count($choices);
                if ($nbchoices < 7){
                    $nbsauts = intval((8 - $nbchoices) / 2);
                    // echo "nb sauts : $nbsauts.";
                    for ($i = 1 ; $i <= $nbsauts ; $i++) {
                        echo "<br/>";
                    }
                }
				?>
                <form action="studentRemote01.php" method="get"  
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
                        class="ChxBtn" 
                        name="chx[]" 
                        value="<?php echo $choiceid ?>" 
                        onclick="validerSubmit(<?php echo $min.','.$max ?>)"
                        >

                    <label id="<?php echo $cbName ?>" for="<?php echo $cbId ?>"  >&nbsp;</label>
                    
<!--            </td> -->



                    <?php 

//                    echo "<td>" . $choiceid . "</td>";
//                    echo "<td>" . $choicetext . "</td>";
                    $lettre++;
                }
/*                    echo "</tr></table>"; */

                if ($nbchoices < 8){
                    $reste = 8 - $nbchoices - $nbsauts;
                    for ($i=1; $i <= $reste;$i++) {
                        echo "<br/>";
                    }
                }
                    ?>
                    <input id="annuler" type="reset" name="currentq"  value="reset">
                    <label class="val" id="cancel" for="annuler">&nbsp;</label>

                    <input id="voter" name="voter" type="submit" class="VoteBtn" value="1" >
                    <label class="val" id="valid" for="voter">&nbsp;</label>


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
