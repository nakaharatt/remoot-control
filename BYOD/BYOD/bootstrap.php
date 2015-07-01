<?php

$sectionreturn = optional_param('sr', null, PARAM_INT);
$hide = optional_param('hide', 0, PARAM_INT);
$show = optional_param('show', 0, PARAM_INT);
$currentq = optional_param('currentq', 0, PARAM_INT);
$runningq = optional_param('runningq', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$sectionid = optional_param('sectionid', 0, PARAM_INT);
$shift = optional_param('shift', 0, PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);
$maxrep = optional_param('maxrep', 0, PARAM_INT);
$windowopened = optional_param('windowsopened', 0, PARAM_BOOL);

// This page should always redirect
$url = new moodle_url('/BYOD/remote.php');
foreach (compact('hide', 'show', 'confirm', 'sesskey') as $key => $value) {
    if ($value !== 0) {
        $url->param($key, $value);
    }
}
$PAGE->set_url($url);
require_login();

$codeq = $DB->get_record('modules', array('name' => "questionnaire"), '*', MUST_EXIST);

$codequestionnaire = $codeq->id; //32 Ca peut dépendre des plateforme, il faut le lire dans la BDD

//$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$section = $DB->get_record('course_sections', array('id' => $sectionid), '*', MUST_EXIST);

// klermor todo $section->course exist
$course = $DB->get_record('course', array('id' => $section->course), '*', MUST_EXIST);
//echo $section->course;exit;

// On récupère la liste des activités (course_modules) ex : 2592,2593
$sequence = $section->sequence;
// 
$modules = explode(',', $sequence);

