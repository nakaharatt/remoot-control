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
/// fbstudentRemote.php version 15/07/03

require_once("../config.php");
require_once("../course/lib.php");
require_once("fbstudentBootstrap.php");
?>


<!--
Vote
>    merci d'avoir voté
Thanks for your vote
>    vous avez déjà participé
You have already voted
>    aucun vote en cours
No current vote
>    sélectionnez au moins n réponses
Select at least an answer
>   sélectionnez au plus n réponses
Select one or more answers
-->

<!doctype html>
<html >
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title><?php echo "Zappette étudiant"; ?></title>
        <link rel="stylesheet" href="css/fbstudentRemoteStyles.css" />
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
           echo "Userid : ".$userid."<br>";

            if (empty($USER->id)){
            ?>
            </section></div>
                <nav><article><div>Vous n'êtes pas connecté<br>Merci</div></article>
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
                <nav><article><div>Vous ne pouvez pas voter avec une connexion anonyme<br>Merci</div></article>
                <form action="/login/index.php" method="GET" >
                    <input name="continue" type="submit" value="continuer"  >
                    <label id="btnfini" for="continue" >continuer</label>

                </form>
                                </nav>
        <?php
                exit();
                
            }
        
 
        
        
            if($voter) { 
                echo " Feedid : $feedid.<br>";
                // s'il y a déjà une tentative c'est une erreur
                $attempts = $DB->get_records_select('feedback_completed', " feedback=$feedid AND userid=$userid ");
                if ($attempts){
                ?>
                    </section></div>
                    <nav><article><div>Vous avez déjà participé<br>Merci</div></article>
                    <form action="" method="GET" >
                        <input name="continue" id="continue" type="submit" value="continue"  >
                        <label id="btnfini" for="continue" >continuer</label>

                        <input type="hidden" name="sectionid" value="<?php echo $sectionid; ?>" >
                    </form>
                                </nav>
            <?php
                    exit();
                } 
            $subtime = time();
            
			// préparation d'un record feedback_completed
			$resp=array();

            $resp['feedback'] = $feedid;
            $resp['userid'] = $userid;
            $resp['timemodified']  = $subtime;
            $resp['random_response']     = 0;
            $resp['anonymous_response'] = 1;
            echo "resp : ".print_r($resp,true);
            $compid = $DB->insert_record("feedback_completed", (object)$resp);
                
			// préparation d'un record feedback_value
			// course_id 	item 	completed 	tmp_completed 	value
			$value=array();
            $value['course_id'] = $courseid;
            $value['item'] = $itemid;
            $value['completed'] = $compid;
            $value['tmp_completed'] = 0;
            $value['value'] = $choix;


            echo "".print_r($value,true);
            $valueid = $DB->insert_record("feedback_value", (object)$value);


            ?>
            </section></div>
            <nav><article><div>Merci d'avoir voté</div></article>
            <form action="" method="GET" >
                <input name="continue" id="continue" type="submit" value="continue"  >
                <label id="btnfini" for="continue" >continuer</label>

                <input type="hidden" name="sectionid" value="<?php echo $sectionid; ?>" >
                
            </form>
            
  <?php }
        else {
    

            // Détermination du code du module feedback
            $codefb = $DB->get_record('modules', array('name' => "feedback"), '*', MUST_EXIST);
            $codefeedback = $codefb->id; 
			echo "codefeedback : ".$codefeedback.".<br/>";

			echo "séquence : ".$sequence.".<br/>";
			$modules = explode(',', $sequence);
        
            if (empty($runningq)) {
				$feedid = 0;
                foreach ($modules as $module) {
                    $cm = $DB->get_record('course_modules', array('id' => $module), '*', MUST_EXIST);
                    if ($cm->module == $codefeedback) {
                        // ajouter ce cm à la liste
                        $fbid = $cm->instance;
                        echo "Questionnaire_id calculé : ".$fbid;
                        if ($cm->visible) {
                            // lets simulate form and moodle traitment
                            echo " ouvert.<br/>";
                            $feedid = $fbid;
                            break;  // on ne peut activer qu'un questionnaire à la fois
                            
                        }
						else {
                            echo " fermé.<br/>";
						}
                    }
                }
				if ($feedid === 0) {
            ?>
            </section></div>
                <nav><article><div>Aucun vote n'est ouvert</div></article>
                <form action="" method="GET" >
                    <input id="continue" type="submit" value="continue"  >
                    <label id="btnfini" for="continue" >continuer</label>

                    <input type="hidden" name="sectionid" value="<?php echo $sectionid; ?>" >
                </form>
                                </nav>
        <?php
					exit();
				}
			}
			else {

				$feedid = $runningq;
				echo "Questionnaire_id transmis : ".$feedid."<br>";
			}
            $courseid = $course->id;
            echo "Course_id : ".$courseid."<br>";
                
            $sectionid = $section->id;
            echo "Section_id : ".$sectionid."<br>";
                
            $feed = $DB->get_record('feedback', array('id' => $feedid), '*', MUST_EXIST);
            $feedname = $feed->name;
            echo "feedname : ".$feedname."<br>";


            // s'il y a déjà une tentative on ne peut pas voter
            $attempts = $DB->get_records_select('feedback_completed', " feedback=$feedid AND userid=$userid ");
            if ($attempts){
            ?>
            </section></div>
			<nav><article><div>Vous avez déjà participé<br>Merci</div></article>
				<form action="" method="GET" >
					<input name="continue" id="continue" type="submit" value="continue"  >
					<label id="btnfini" for="continue" >continuer</label>

					<input type="hidden" name="sectionid" value="<?php echo $sectionid; ?>" >
				</form>
			</nav>
        <?php
                exit();
            } 

            
            
            
            echo "Feedback " . $feedname . " (".$feedid.")";



            // extraire les questions du questionnaire
            if (!($questions = $DB->get_records_select('feedback_item', "feedback=$feedid AND typ LIKE 'multichoice'"))) {
                exit("Aucune question disponible");
            }
			?>
            </section></div>

			<nav><div>
		<?php
            foreach ($questions as $question) {
                $itemid = $question->id;

                echo "<article><div>" . $question->name . "</div></article>";

                // chercher les choix proposées
				$choices = explode('|',$question->presentation);
				?>
                <form action="fbstudentRemote.php" method="get"  
					onsubmit="return validerForm(1,1)" >
				<?php

/*                echo '<table border="1">'; */
                $choice_count=0;
                foreach ($choices as $choicetext) {
					$choice_count++;
                    $choiceid = $choice_count;
                    $cbId="cb".chr(ord('0')+$choice_count);
                    $cbName="chx".chr(ord('A')+$choice_count-1);
                    $cbValue=chr(ord('A')+$choice_count-1);
                    ?>
            
<!--        <tr><td>  -->
                    <input 
                        id="<?php echo $cbId ?>" 
                        type="radio" 
                        class="VoteBtn" 
                        name="choix" 
                        value="<?php echo $choiceid ?>" 
                        onclick="validerSubmit(1,1)"
                        >

                    <label for="<?php echo $cbId ?>"  ><?php echo $cbValue ?></label>
                    
<!--            </td> -->



                    <?php 

                }
                    ?>


                    <br>
                    <input id="voter" name="voter" type="submit" class="VoteBtn" value="1" >
                    <label id="btnvoter" for="voter">voter</label>


                    <br><br>
                    <input id="annuler" type="reset" name="currentq"  value="reset">
                    <label id="btnannul" for="annuler"><small><small>annuler</small></small></label>


                    <input type="hidden" name="courseid" value="<?php echo $courseid; ?>" >
                    <input type="hidden" name="sectionid" value="<?php echo $sectionid; ?>" >
                    <input type="hidden" name="feedid" value="<?php echo $feedid; ?>" >
					<input type="hidden" name="itemid" value="<?php echo $itemid; ?>" >
<!--                <input type="hidden" name="maxrep" value="<?php //echo $maxrep; ?>" > -->
                    <div>

                    </div>


                    </form>
        </div>
				<?php
			}
    
           

?>
		</nav>
        

        <?php
		} 
		?>
    </body>
</html>
