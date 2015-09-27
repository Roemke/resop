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
 * ICal export for Resop Module a particular instance of resop
 * Taken many hints from the iCal export of the calendar of Moodle (export_execute.php)
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_resop
 * @copyright  2015 Karsten Römke
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once (dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once ('db/ownDB.php');
require_once ($CFG -> libdir . '/bennu/bennu.inc.php');
//Bennu implements object-oriented iCalender functionalit

/*
 * @param int $resopInCourseId id of course
 * @param int $userId userid
 * @return bool (true if user has booking cap)
 * check if user has capability to book -> higher rights to get calendar
 * */
function checkIfBooker($resopInCourseId, $user) {
	$cm = get_coursemodule_from_id('resop', $resopInCourseId, 0, false, MUST_EXIST);
	$context = context_module::instance($cm -> id);
	return has_capability('mod/resop:book', $context, $user);
}

//---------------------parameter
$authtoken = required_param('authtoken', PARAM_ALPHANUM);
$userId = required_param('userid', PARAM_INT);
$resopInCourseId = required_param('id', PARAM_INT);
$type = required_param('type', PARAM_ALPHA);
$resopId = required_param('resopId', PARAM_INT);
$requestedName = optional_param('name', '', PARAM_RAW);

$checkUserId = !empty($userId) && $user = $DB -> get_record('user', array('id' => $userId), 'id,password');
if (!$checkUserId) {
	//No such user
	die('Invalid User');
}
//Check authentication token
$authUserId = !empty($userId) && $authtoken == sha1($userId . $user -> password . $CFG -> resop_exportsalt);

if (!$authUserId)
	die('Invalid authentication');

$isBooker = checkIfBooker($resopInCourseId, $user);
if (!$isBooker && ($type == 'booker' || $type == 'class' && $requestedName == 'all'))
	die('Access rights are not sufficient');

//now we have should done authentication stuff
if ($requestedName == 'all')
	$requestedName = '%';

//get the data
if ($type == 'class')
	$entries = ResopDB::getClassEntries($requestedName, $resopId);
else if ($type == 'booker')
	$entries = ResopDB::getBookerEntries($requestedName);
else
	die('Ivalid request');
/*
echo "<textarea cols=50 rows=20>";
print_r($entries);
echo "</textarea>";
*/
//build the ical stuff
$ical = new iCalendar;
$ical -> add_property('method', 'PUBLISH');
$hostaddress = str_replace('http://', '', $CFG -> wwwroot);
$hostaddress = str_replace('https://', '', $hostaddress);

/* example for one entry, key in array is always id
 [1] => stdClass Object
 (
 [id] => 1
 [termin] => 1442697000
 [time] => 5400
 [creation] => 1442610703
 [moun] => Admin Nutzer
 [note] => Ma / KA / G
 [rname] => fh115
 [uname] => leh
 )
 [uname] => leh
 */
foreach ($entries as $key => $event) {
	$ev = new iCalendar_event;
	$ev -> add_property('uid', $event -> id . '@' . $hostaddress);
	$ev -> add_property('summary', $event -> rname . '/' . $event -> uname);
	$ev -> add_property('description', clean_param($event -> note, PARAM_NOTAGS));
	$ev -> add_property('class', 'PUBLIC');
	// PUBLIC / PRIVATE / CONFIDENTIAL
	$ev -> add_property('created', Bennu::timestamp_to_datetime($event -> creation));
	//$ev->add_property('last-modified', Bennu::timestamp_to_datetime($event->creation));
	$ev -> add_property('dtstamp', Bennu::timestamp_to_datetime());
	// now
	$ev -> add_property('dtstart', Bennu::timestamp_to_datetime($event -> termin));
	// when event starts
	if ($event -> time > 0) {
		//dtend is better than duration, because it works in Microsoft Outlook and works better in Korganizer
		$ev -> add_property('dtend', Bennu::timestamp_to_datetime($event -> termin + $event -> time));
	}
	//following part in  ical export (export_execute.php) is done if courseid is stored in event
	//I can always get a course Id
	$cm = get_coursemodule_from_id('resop', $resopInCourseId, 0, false, MUST_EXIST);
	$course = get_course($cm->course);
	$coursecontext = context_course::instance($course->id);
	$ev -> add_property('categories', format_string($course->shortname, true, array('context' => $coursecontext)));
	
	$ical -> add_component($ev);
}
/*
var_dump($cm);
echo "<br><br>";
var_dump($course);
echo "<br><br>";
var_dump($coursecontext);
*/
//write the ical stuf in the right format
$serialized = $ical->serialize();
if(empty($serialized)) {
    // TODO
    die('bad serialization to iCal-Format of resop-Dates');
}


if ($type=='class')
{
	if ($requestedName == '%')
		$ext='Class_All';
	else 
		$ext = 'Class_'.$requestedName;	
}
else 
	$ext='_'.$requestedName;

$filename = 'ical' . $ext . '.ics';

header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
header('Expires: '. gmdate('D, d M Y H:i:s', 0) .'GMT');
header('Pragma: no-cache');
header('Accept-Ranges: none'); // Comment out if PDFs do not work...
header('Content-disposition: attachment; filename='.$filename);
header('Content-length: '.strlen($serialized));
header('Content-type: text/calendar; charset=utf-8');

echo $serialized;
