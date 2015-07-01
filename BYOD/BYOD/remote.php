<?php
require_once("../config.php");
require_once("../course/lib.php");
require_once("bootstrap.php");
?>
<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta http-equiv="refresh" content="15">
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title><?php echo $section->name; ?></title>
          
        <link rel="stylesheet" href="css/pure-min.css" />
        <link rel="stylesheet" href="css/font-awesome-min.css" />
        <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
        <link rel="stylesheet" href="css/remote_light.css" />
    </head>
    <body>
        <div class="pure-g-r">

            <div class="pure-u-5-5">
                <h1><?php echo $section->name; ?></h1>
                
                <h2></h2>
                <?php
                 //if (!empty($maxrep)) {
                 //   echo '<small>' . $maxrep .' participants</small>';
                 //}
                 ?>
            </div>

            <?php
            if (empty($runningq)) {
                if (!empty($hide)) {
                    $cm = get_coursemodule_from_id('', $hide, 0, true, MUST_EXIST);
                    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

                    require_login($course, false, $cm);
                    $coursecontext = context_course::instance($course->id);
                    $modcontext = context_module::instance($cm->id);
                    require_capability('moodle/course:activityvisibility', $modcontext);

                    set_coursemodule_visible($cm->id, 0);
                } else if (!empty($show)) {
                    $cm = get_coursemodule_from_id('', $show, 0, true, MUST_EXIST);
                    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

                    require_login($course, false, $cm);
                    $coursecontext = context_course::instance($course->id);
                    $modcontext = context_module::instance($cm->id);
                    require_capability('moodle/course:activityvisibility', $modcontext);

                    $section = $DB->get_record('course_sections', array('id' => $cm->section), '*', MUST_EXIST);

                    $module = $DB->get_record('modules', array('id' => $cm->module), '*', MUST_EXIST);

                    if ($module->visible and ( $section->visible or ( SITEID == $cm->course))) {
                        set_coursemodule_visible($cm->id, 1);
                    }
                }



                foreach ($modules as $module) {
                    $cm = $DB->get_record('course_modules', array('id' => $module), '*', MUST_EXIST);
                    if ($cm->module == $codequestionnaire) {
                        // ajouter ce cm à la liste
                        $questid = $cm->instance;
                        $quest = $DB->get_record('questionnaire', array('id' => $questid), '*', MUST_EXIST);
                        $questname = $quest->name;
                        // calculer le nb de réponses
                        $anscount = $DB->count_records_select('questionnaire_attempts', "qid = " . $questid);

                        if ($cm->visible) {
                            ?>
                            
                            <form action="" >
                                <div class="pure-u-5-5">
                                    <h3><?php echo $questname . " (" . (int) $anscount . ")"; ?></h3> 

                                    <button type="submit" name="hide" class="button-warning pure-button" value="<?php echo $module; ?>">
                                        <i class="fa fa-eye-slash"></i>
                                        Fermer le vote</button>
                                    <button type="submit" name="runningq" class="button-success pure-button" value="<?php echo $questid; ?>">
                                        <i class="fa fa-stethoscope"></i>
                                        Réponses</button>

                                </div>

                                <input type="hidden" name="courseid" value="<?php echo $courseid; ?>" >
                                <input type="hidden" name="sectionid" value="<?php echo $sectionid; ?>" >
                                <input type="hidden" name="maxrep" value="<?php echo $maxrep; ?>" >
                            </form>
                            <?php
                        } else {
                            ?>
                           
                            <form action="">
                                <div class="pure-u-5-5">
                                    <h3><?php echo $questname; ?></h3>
                                    <button type="submit" name="show" class="pure-button-primary pure-button"  value="<?php echo $module; ?>">
                                        <i class="fa fa-eye"></i>
                                        Ouvrir le vote</button>
                                    <button type="submit" name="runningq" class="button-success pure-button" value="<?php echo $questid; ?>">
                                        <i class="fa fa-stethoscope"></i>
                                        Réponses</button>
                                </div>

                                <input type="hidden" name="courseid" value="<?php echo $courseid; ?>" >
                                <input type="hidden" name="sectionid" value="<?php echo $sectionid; ?>" >
                                <input type="hidden" name="maxrep" value="<?php echo $maxrep; ?>" >
                            </form>
                            <?php
                        }
                    }
                }
                ?>


                <?php
            } else {

                $questid = $runningq;
                $quest = $DB->get_record('questionnaire', array('id' => $questid), '*', MUST_EXIST);
                $questname = $quest->name;
				$survid = $quest->sid;
         
                $anscount = $DB->count_records_select('questionnaire_attempts', "qid = " . $questid);
                if ($anscount > $maxrep) {
                    $maxrep = $anscount;
                }
                echo '<div class="pure-u-5-5">';
                echo "<h2>" . $questname . "</h2>";
                

//                if (empty($showresult)) {
                    // montrer le compteur

                    echo '<p align="center" ><span style="font-size : 18pt; vertical-align:middle;">';
					echo 'Participants : ' . $anscount ;
                    
//                    if (!empty($maxrep)) {
//                        echo '<br/>' . $maxrep .' participants';
//                    }
                    echo '</span></p>';

//                } else {
//                    // montrer le graphique
//                    
//                }
                    $sql = "SELECT id, userid, max(rid) FROM " . $CFG->prefix . "questionnaire_attempts WHERE qid=$questid GROUP BY userid";
                    
                    if (!($attempts = $DB->get_records_sql($sql))) {
                        $attempts[] = '';
                        //exit("Erreur 0");
                    }
                    $nbAttempts = 0;
                    $listRep = array();
                    foreach ($attempts as $attempt) {
                        $nbAttempts++;
                        $listRep[] = $attempt->userid;
                    }
                    //echo "nbAttempts : ".$nbAttempts;
                    // extraire les questions du questionnaire
                    if (!($questions = $DB->get_records_select('questionnaire_question', "survey_id=$survid AND deleted='n'"))) {
                        exit("Erreur 1");
                    }

                    foreach ($questions as $question) {
                        $qid = $question->id;
                        
                        // normalement il n'y a en a qu'une
                        echo '<table  class="pure-table pure-table-bordered" width="95%"><tr><td colspan="3">';
                        echo "<b>" . $question->content."</b>"; // $question->name . "Question " .
                        echo "</td></tr>";
                        echo "<tr><td> </td><td>Réponses</td><td style=\"width:13em;\">% des participants</td></tr>";

                        // determiner le type de question
                        $qtype = $question->type_id;
                        if ($qtype == 5) {
                            $tabrep = 'questionnaire_resp_multiple';
                        } else {
                            $tabrep = 'questionnaire_resp_single';
                        }

                        // chercher les choix proposées
                        if (!($choices = $DB->get_records_select('questionnaire_quest_choice', "question_id=$qid"))) {
                            exit("Erreur 2");
                        }
                        foreach ($choices as $choice) {
                            $choicetext = $choice->content;
                            $choiceid = $choice->id;
                            // compter le nb réponses où ce choix à été sélectionné
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
                            echo "<tr>";
                            echo "<td>" . $choicetext . "</td>";
                            echo "<td>", $choicenb . "</td>";
                            echo "<td>" . $graph . "</td>";
                        }
                        echo "</table><br>";
                        ?>
                        <form action="">     <input type="hidden" name="courseid" value="<?php echo $courseid; ?>" >
                            <input type="hidden" name="sectionid" value="<?php echo $sectionid; ?>" >
                            <input type="hidden" name="maxrep" value="<?php echo $maxrep; ?>" >
                            <div class="pure-u-5-5">

                                <button type="submit" name="currentq" class="pure-button-primary pure-button" value="0">
                                    <i class="fa fa-arrow-circle-left"></i>
                                    retour</button>
                            </div>


                        </form>
					<?php
						break;  // on ne veut qu'une question par questionnaire
					}
//			}
					?>
            </div>
	<?php
        }
    ?>
        
        </div>

    </body>
</html>
