<?php

//$sectionreturn = optional_param('sr', null, PARAM_INT);
// $hide = optional_param('hide', 0, PARAM_INT);
// $show = optional_param('show', 0, PARAM_INT);
//$currentq = optional_param('currentq', 0, PARAM_INT);
//$runningq = optional_param('runningq', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$sectionid = optional_param('sectionid', 0, PARAM_INT);
$questid = optional_param('questid', 0, PARAM_INT);
$survid = optional_param('survid', 0, PARAM_INT);
$questionid = optional_param('questionid', 0, PARAM_INT);
// $shift = optional_param('shift', 0, PARAM_INT);
//$confirm = optional_param('confirm', 0, PARAM_BOOL);
$voter = optional_param('voter', 0, PARAM_BOOL);
$cfini = optional_param('cfini', 0, PARAM_BOOL);
//$dejavote = optional_param('dejavote', 0, PARAM_BOOL);
// $maxrep = optional_param('maxrep', 0, PARAM_INT);
$windowopened = optional_param('windowsopened', 0, PARAM_BOOL);


// This page should always redirect
$url = new moodle_url('/BYOD/studentRemote.php');
foreach (compact('sectionid', 'questid', 'survid', 'questionid', 'voter', 'cfini', 'confirm', 'sesskey') as $key => $value) {
    if ($value !== 0) {
        $url->param($key, $value);
    }
}
$PAGE->set_url($url);
// require_login($courseorid = NULL, $autologinguest = true, $cm = NULL, $setwantsurltome = true, $preventredirect = false) 
require_login(NULL,false,NULL,true,false);

$userid = $USER->id;

if (!$cfini) {
    //$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
    if ($sectionid){
        $section = $DB->get_record('course_sections', array('id' => $sectionid), '*');
        if (empty($section)) {
            exit("Section de cours non trouvée.");
        }
    }
    else { exit("paramètre sectionid absent.");}

    // klermor todo $section->course exist
    $course = $DB->get_record('course', array('id' => $section->course), '*', MUST_EXIST);
    //echo $section->course;exit;

    // On récupère la liste des activités (course_modules) ex : 2592,2593
    $sequence = $section->sequence;
    // 
    $modules = explode(',', $sequence);
}
?>