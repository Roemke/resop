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
 * Resource module admin settings and defaults
 *
 * @package    mod_resop
 * @copyright  2015 Karsten Römke
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 

defined('MOODLE_INTERNAL') || die;
require_once ("classes/admin_settings.php");

if ($ADMIN->fulltree) { //scheint ueblich
  //resourcen, nehme die normale Text-Area, kein Eintrag in db erforderlich
    $name =        get_string('listofresources', 'mod_resop');
    $description = get_string('listofresources_default_help', 'mod_resop');	
    $settings->add(new admin_setting_configtextarea('resop_resources',
                                                    $name,
                                                    $description,""));
                                                    //"abc"));//var_export($result,true)));
 //user                                                       
    $name =        get_string('listofusers', 'mod_resop');
    $description = get_string('listofusers_help', 'mod_resop');	
    $settings->add(new admin_setting_configta_users('resop_users',
                                                    $name,
                                                    $description,""));

  //departements
    $name =        get_string('listofdepartements', 'mod_resop');
    $description = get_string('listofdepartements_help', 'mod_resop');	
    $settings->add(new admin_setting_configta_departements('resop_departements',
                                                    $name,
                                                    $description,""));
	 
	 //speichern passiert automatisch wenn der button gedrueckt wird
    //kann das auch spaeter abfragen
}
	
	//$output = $PAGE->get_renderer('tool_demo');
	//$output->print("nothing to do here, it's historical :-)");
