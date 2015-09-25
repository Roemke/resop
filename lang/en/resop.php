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
 * English strings for resop
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_resop
 * @copyright  2015 Karsten Römke
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 
 * 
 * 
 * */

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'Resource Planner';
$string['modulenameplural'] = 'Resource Planner';
$string['modulename_help'] = 'Simple Module to plan resources, first version should allow : written examinations';
$string['resopfieldset'] = 'Fine-Tuning, important';
$string['resopname'] = 'Name';
$string['resopname_help'] = 'name of module';
$string['resop'] = 'resop';
$string['pluginadministration'] = 'resop administration';
$string['pluginname'] = 'resop';

// for the settings
$string['listofresources'] = 'List of resources';//both
$string['listofresources_default_help'] = 'Default for the list of ressources, '.
'Each line contains the name of one resource.<br>'.
'If you want to manage writen exams, you should note also class names here.<br>'.
'You can add or delete some entries here, it is only the set of default entries and can later be extended or reduced.'; 
//admin settings
$string['maxnumberexams']='Max numbers of written exams';
$string['maxnumberexams_help'] = 'For type "written exams": Maximum number of written exams each week could be written.';
$string['exportsalt']='Export salt ';
$string['exportsalt_help']='Salt for ical export of resop. Improves security of authtoken. '.
	'If you leave it empty, we generate a 60 character random value, '.
	'if you change this, all tokens get invalid.';
$string['listofresources_help'] = 'Resources for this activity.<br>'. 
   'You surely need to  add and/or remove ressources.'; //module settings

$string['listofdepartements']="Departements";
$string['listofdepartements_help']='Departements, compare resources';

$string['listofusers']="User";
$string['listofusers_help']='User who can book a resource, select multiple entries.';
$string['resoptypestring'] = 'Type';
$string['departement']='Departement';

//--------------------------------
$string['display'] = 'Create Resource Planner';
$string['contentheader'] = 'Content';
$string['typeall'] = 'every resource (does not work)';
$string['typeexam'] = 'only written examina';
$string['abteilungen']="Choose departement.";
$string['abtheader']="Departements";
//--------------- view.php --------------
$string['showall']='Show all reservations';
$string['resExam']='Classes';
$string['resFree']='Resources';
$string['insert'] = 'Insert an entry';
$string['entries'] = 'Entries';
$string['nodata']='no entries found';
$string['class']='Class';
$string['bookedby'] = 'booked for ';
$string['bookedby_header']='booked for (over all instances)';
$string['termin'] = 'Date';
$string['edit'] = 'edit';
$string['iCal'] = 'iCal Export';
//--------------------------- Insert / Update Form
$string['insertClass']='Class:';
$string['choose']='Choose!';
$string['chooseHelp']='You have to choose a value!';
$string['insertUser']='booked for:';
$string['kind']='Course / kind of examina / room:';
$string['kind_help']='Insert name of the subject, eg MA (mathematics) and the kind eg test or ...  .';
$string['termin']="Start-Date/Time:";
$string['duration']="Duration:";
$string['saveback']="Save and back to list";
$string['savenext']="Save, enter next";
$string['update']='Update';
$string['dateError'] = 'Date in the past!';
$string['error3KA'] = 'Maximum number of exams exceeded';	   
$string['errorOverlap'] = 'Overlapping appointment';
$string['confirmdelete'] = 'You want to delete this entry?';
