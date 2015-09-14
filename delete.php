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

/**
 * Prints a particular instance of resop
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_resop
 * @copyright  2015 Karsten Römke
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once($CFG->dirroot . '/mod/resop/locallib.php');

//delete an entry
function deleteEntry(&$urlparams)
{
	global $DB;
	$delId = $urlparams['delId'];
	if (isset($delId))
	{
		$DB->delete_records('resop_resource_user', array( "id" => $delId));
	}
	else 
	{
		echo "strange - you like to delete an entry without giving the id";	
	}
}

//-----------------------------------------------------

//$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
//$n  = optional_param('n', 0, PARAM_INT);  // ... resop instance ID - it should be named as the first character of the module.

$id = required_param('id', PARAM_INT);

$urlparams = array('id' => $id,
                  'class' => optional_param('class','',PARAM_TEXT),
                  'delId' => optional_param('delId','',PARAM_INT),
                  'fromAction' => optional_param('fromAction','',PARAM_TEXT), //woher kam der Aufruf
				  
                  );//second argument of optional_param is the value if the demanded value is not set

$confirm = optional_param('confirm', 0, PARAM_INT); 
				   
$url = new moodle_url('/mod/resop/delete.php', $urlparams);
 
 
$cm         = get_coursemodule_from_id('resop', $id, 0, false, MUST_EXIST);

$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

//echo $id . " and " .$cm->id;
//exit(); gleich
$resop  = $DB->get_record('resop', array('id' => $cm->instance), '*', MUST_EXIST);
 
require_login($course, true, $cm);

/*don't understand event - why should i trigger an event
 * nobody could have registered so far 
 * maybe trigger leads to some logging - could be, there are controverse discussions
 * about Event2 */
$event = \mod_resop\event\course_module_viewed::create(array(
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $resop);
$event->trigger();
//----------------------------------------------------
// Print the page header.
$PAGE->set_url($url);
/*assign module does set_url here and the rest in own class ?
 * we try it first without own class and traditional output
 * */

$PAGE->set_title(format_string($resop->name));
$PAGE->set_heading(format_string($course->fullname));

/*
 * Other things you may want to set - remove if not needed.
 * $PAGE->set_cacheable(false);
 * $PAGE->set_focuscontrol('some-html-id');
 * $PAGE->add_body_class('resop-'.$somevar);
 */

// Output starts here.
echo $OUTPUT->header();
// Conditions to show the intro can change to look for own settings or whatever.
if ($resop->intro) {
    echo $OUTPUT->box(format_module_intro('resop', $resop, $cm->id), 'generalbox mod_introbox', 'resopintro');
}

// Replace the following lines with you own code.
//wahrscheinlich sollte ich hier einen renderer einsetzen, aber die Thematik ist mir im moment 
//zu komplex 

//echo $OUTPUT->heading(get_string('modulename','resop'));
echo $OUTPUT->box_start();
//show some links if no action is set
//var_dump($urlparams);

$context = context_module::instance($cm->id);
//glossary uses a session key - should I add one too?
if (has_capability('mod/resop:book', $context))
{	 
	$urlparams['action']  = $urlparams['fromAction'];
	if ($confirm==1)
	{
		//deleteEntry($urlparams);
		unset($urlparams['delId']);
		unset($urlparams['fromAction']);
		$url = new moodle_url('view.php',$urlparams);
		redirect($url);//todo
	}	
	else {
		//todo url bauen
		$linkyes = 'delete.php';
		$optionsyes = $urlparams; //should be a copy
		$optionsyes['confirm'] = 1;
		$linkno = 'view.php';
		echo $OUTPUT->confirm(get_string('confirmdelete', 'resop'),
		        new moodle_url($linkyes,$optionsyes),
		        new moodle_url($linkno,$urlparams));	
	}
}
echo $OUTPUT->box_end();

// Finish the page.
echo $OUTPUT->footer();


