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
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Replace resop with the name of your module and remove this line.

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once($CFG->dirroot . '/mod/resop/locallib.php');

//$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
//$n  = optional_param('n', 0, PARAM_INT);  // ... resop instance ID - it should be named as the first character of the module.

$id = required_param('id', PARAM_INT);

$urlparams = array('id' => $id,
                  'action' => optional_param('action', '', PARAM_TEXT),
                  'abt' => optional_param('abt', 0, PARAM_INT),
                  'res' => optional_param('res', 0, PARAM_INT), //ressource we want to see eg name of class
                  'user' => optional_param('user',0,PARAM_INT)
				  );

				   
//ok, dann geht so was wie http://localhost/moodle/mod/resop/view.php?id=36&abt=FOS
/* should be not neccessary is from template
if ($id) {
    $cm         = get_coursemodule_from_id('resop', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $resop  = $DB->get_record('resop', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $resop  = $DB->get_record('resop', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $resop->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('resop', $resop->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}
 */
$url = new moodle_url('/mod/resop/view.php', $urlparams);
 
 
$cm         = get_coursemodule_from_id('resop', $id, 0, false, MUST_EXIST);
$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

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
if (empty($urlparams['action']))
{
	echo $OUTPUT->heading(get_string('abtheader','resop'),4);
	echo $OUTPUT->action_link(new moodle_url('view.php', array('id' => $id, 'action' => 'showall')),
	 		get_string('showall','resop')); // Required
	$text = ($resop->type == 'typeexam') ? get_string('resExam','resop') : get_string('resFree','resop');	 		
	echo $OUTPUT->heading($text,4);
	//linkliste
	$resources = $DB->get_records_sql('SELECT name FROM {resop_resource_user} ru LEFT JOIN {resop_resource} rr '
		. " ON ru.resid=rr.id  WHERE ru.actid={$resop->id} ORDER BY name");
	
	var_dump($resources); //ok geht
}
echo $OUTPUT->box_end();

// Finish the page.
echo $OUTPUT->footer();
