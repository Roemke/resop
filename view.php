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

// Replace resop with the name of your module and remove this line.

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once($CFG->dirroot . '/mod/resop/locallib.php');
require_once($CFG->dirroot . '/mod/resop/InsertForm.php');
require_once($CFG->dirroot.'/mod/resop/db/ownDB.php');

//some functions used only here
//maybe we should seperate this from the actual file 
function showInsEditForm( $url,$id,$resop,$editId = null)
{
	global $OUTPUT;
	if ($resop->type == 'typeexam')
	{
		if ($editId) 
			$form = new EditForm($url,array('resop'=>$resop,'editId'=>$editId));
		else
			$form = new InsertForm($url,array('resop' => $resop));	
		if ($form->is_cancelled())
		{
			showInsertLink($id); 
			showListOfLinks($id,$resop);					
		}
		else if ($fromform = $form->get_data()) 
		{
  			//In this case you process validated data. $mform->get_data() returns data posted in form.
			if ($editId)			
				ResopDB::tryUpdateExamResource($resop->id, $editId, $fromform);
			else
				ResopDB::tryInsertExamResource($resop->id,$fromform) ; 			
			if (isset($fromform->submitbutton2))
			{
				showInsertLink($id);
				showListOfLinks($id,$resop);
			}
			else 
			{
				echo $OUTPUT->container('ok', 'important', 'notice');			
				$form->display();
			}				
		} 
		else 
		{
  			// this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
  			// or on the first display of the form. 
  			//errors are set from base class of form and from inherited InsertForm
  			$form->display();
		}		
	}
	else 
	{
		echo "sorry, not supported";
	}
}

function showListOfLinks($id,$resop)
{
	global $OUTPUT, $DB;
	//need it to check capability
	$cm = get_coursemodule_from_id('resop', $id, 0, false, MUST_EXIST);
	$context = context_module::instance($cm->id);
	
	
	echo $OUTPUT->heading(get_string('entries','resop'),4);
	echo $OUTPUT->action_link(new moodle_url('view.php', array('id' => $id, 'action' => 'showAll')),
	 		get_string('showall','resop')); // Required
	//linkliste Klassen/Resources - specific for this resop instance
	$text = ($resop->type == 'typeexam') ? get_string('resExam','resop') : get_string('resFree','resop');	 		
	echo $OUTPUT->heading($text,4);
	$resources = $DB->get_records_sql('SELECT DISTINCT name FROM {resop_resource_user} ru JOIN {resop_resource} rr '
		. " ON ru.resid=rr.id  WHERE ru.actid={$resop->id} ORDER BY name");
		//ok, should only be the resources which are handled in this resop instance
	foreach ($resources as $key => $value) 
	{
		echo $OUTPUT->action_link(new moodle_url('view.php', 
			array('id' => $id, 'action' => 'showClass','class'=>$key)),$key); // Required
		echo "&nbsp;&nbsp;&nbsp;";			
	}
	//link list owner over all instances of resop modul
	if (has_capability('mod/resop:book', $context) )
	{
		echo $OUTPUT->heading(get_string('bookedby_header','resop'),4);
		$resources = $DB->get_records_sql('SELECT DISTINCT name FROM {resop_resource_user} ru JOIN {resop_resop_user} rru '.
										  'on ru.uid = rru.uid JOIN {resop_user} u on ru.uid=u.id ORDER BY name' );
		foreach ($resources as $key => $value) 
		{
			echo $OUTPUT->action_link(new moodle_url('view.php',
				array('id'=>$id,'action'=>'showBooker','name'=>$key)),$key);						
			echo "&nbsp;&nbsp;&nbsp;";			
		}
	}
}
/*
 * @param int $id id of modul
 * @param string $select which class should be shown
 * */
function showClasses($id,$select = '%')
{
	global $DB,$OUTPUT,$USER, $CFG, $resop; //resop->id : see note in showBookers
	$authtoken =  sha1($USER->id . $DB->get_field('user', 'password', array('id' => $USER->id)) . $CFG->resop_exportsalt);
    $className = $select == '%' ? 'all' : $select;
	//% in url does not work - strange, it should its encoded as %25 which is right
	$urlExport = array('id' => $id,'resopId'=>$resop->id,'userid'=>$USER->id,'authtoken'=>$authtoken,'type'=>'class','name'=>$className);
	$linkExport = $OUTPUT->action_link(new moodle_url('exportIcal.php',$urlExport),
							'iCal',null,array('title'=>get_string('iCal','resop'),'class'=>'ical-link'));
	//$OUTPUT->action_link you find under lib/outputrenderers.php - but I don't understand action (set to null is default)					
						//delete get's the old action and class to go back to this page	
	echo $linkExport;    
    echo $OUTPUT->help_icon('iCal','resop');
	$classEntries = ResopDB::getClassEntries($select, $resop->id);
	showEntriesTable($id, $classEntries, array('class'=>$select));	
}
/*
 * @param int $id id of modul
 * @param string $select which class should be shown
 * */
function showBookers($id,$name)
{
	global $DB, $USER, $CFG, $OUTPUT, $resop; //thought id and resop id are the same but they are not. 
	//I don't understand why, so what resop->id is used in my internal db-tables and id is the id of an instance
	//inside of a course
	$authtoken =  sha1($USER->id . $DB->get_field('user', 'password', array('id' => $USER->id)) . $CFG->resop_exportsalt);   
	$urlExport = array('id' => $id,'resopId'=>$resop->id,'userid'=>$USER->id,'authtoken'=>$authtoken,'type'=>'booker','name'=>$name);
	$linkExport = $OUTPUT->action_link(new moodle_url('exportIcal.php',$urlExport),
							'iCal',null,array('title'=>get_string('iCal','resop'),'class'=>'ical-link'));
	//$OUTPUT->action_link you find under lib/outputrenderers.php - but I don't understand action (set to null is default)					
						//delete get's the old action and class to go back to this page	
	echo $linkExport; 
	echo $OUTPUT->help_icon('iCal','resop');
	   
	$bookerEntries = ResopDB::getBookerEntries($name);
	showEntriesTable($id, $bookerEntries, array('name'=>$name));
}

function showEntriesTable($id,$classes, $additionalUrlParams)
{
	global $OUTPUT, $urlparams;
    //print_r($classes);
    $iconDel  =  '<img class="smallicon" alt="' . get_string('delete') . '" src="' . $OUTPUT->pix_url('t/delete') . '" >'; 
    $iconEdit =  '<img class="smallicon" alt="' . get_string('edit')   . '" src="' . $OUTPUT->pix_url('t/edit')   . '" >'; 
	 
	if (count($classes)>0)
	{
		$table = new html_table();
		$table->head = array(get_string('class','resop') ,
							 get_string('termin','resop') ,
							 get_string('duration','resop'),
							 get_string('kind','resop'),
							 get_string('bookedby','resop'),
							 get_string('edit','resop'));
							 
    	$table->data = array();
		
		foreach ($classes as $key => $value) 
		{
			$urlDel = array_merge(array('id' => $id, 'action' => 'delete','delId'=>$value->id,
						'fromAction' => $urlparams['action'] ), $additionalUrlParams);
			$linkDel =  $OUTPUT->action_link(new moodle_url('delete.php',$urlDel),$iconDel);					
						//delete get's the old action and class to go back to this page	
			$urlEdit = array_merge(array('id'=>$id,'action'=>'edit','editId'=>$value->id,
						'fromAction' => $urlparams['action']), $additionalUrlParams);
			$linkEdit = $OUTPUT->action_link(new moodle_url('view.php',$urlEdit),$iconEdit);
			$table->data[] = array($value->rname,
								   strftime('%a, %d.%m.%g %R',$value->termin),
								   $value->time/60 . " Min.",
								   $value->note,
								   $value->uname,
								   $linkDel ." ".  $linkEdit) ;				
		}
		echo html_writer::table($table);		 
	}
	else 
	{
		$OUTPUT->box(get_string('nodata','resop'));
	}
}
function showInsertLink($id)
{
	echo '<div style=" display: inline-block; position: relative; top: -30px; float: right; margin-bottom:5px;"><a href="view.php?id='.$id . '&action=insert">' 
    	. get_string('insert','resop') .  '</a></div>';	
}
//-----------------------------------------------------

//$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
//$n  = optional_param('n', 0, PARAM_INT);  // ... resop instance ID - it should be named as the first character of the module.

$id = required_param('id', PARAM_INT);

$urlparams = array('id' => $id,
                  'action' => optional_param('action', '', PARAM_TEXT), 
                  'class' => optional_param('class','',PARAM_TEXT),
                  'delId' => optional_param('delId','',PARAM_INT),
                  'editId' => optional_param('editId','',PARAM_INT),
                  'name' => optional_param('name','',PARAM_TEXT),
                  'fromAction' => optional_param('fromAction','',PARAM_TEXT), //woher kam der Aufruf
				  );//second argument of optional_param is the value if the demanded value is not set

				   
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
$context = context_module::instance($cm->id);

if (has_capability('mod/resop:book', $context) 
	&& ($urlparams['action'] != 'insert')
	&& ($urlparams['action'] != 'edit')
	)
{
    showInsertLink($id);
} 
if (empty($urlparams['action']))
{
	showListOfLinks($id,$resop);						
}


if ($urlparams['action']=='showAll') 
{
	showClasses($id,'%');
}
else if ($urlparams['action']=='showClass') //if a class is clicked
{
	showClasses($id,$urlparams['class']);	
}
else if (has_capability('mod/resop:book', $context))
{
	if ($urlparams['action'] == 'insert')
	{
		showInsEditForm($url,$id,$resop);
	}
	else if ($urlparams['action']== 'edit')
	{
		$editId = $urlparams['editId'];
		showInsEditForm($url,$id,$resop,$editId);
	}
	else if($urlparams['action'] == 'showBooker')
	{
		showBookers($id,$urlparams['name']);
	}
}
echo $OUTPUT->box_end();

// Finish the page.
echo $OUTPUT->footer();


