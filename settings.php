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

if ($ADMIN->fulltree) { //scheint ueblich

    $name =        get_string('listofressources', 'mod_resop');
    $description = get_string('listofressources_help', 'mod_resop');
    $default = "we take it from db";
	/* unwichtig geworden settings pro aktivität
    $settings->add(new admin_setting_configtextarea('resop_resources',
                                                    $name,
                                                    $description,""));
                                                    //"abc"));//var_export($result,true)));
    
	 * 
	 */
	 //speichern passiert automatisch wenn der button gedrueckt wird
    //kann das auch spaeter abfragen
}
	
	//$output = $PAGE->get_renderer('tool_demo');
	//$output->print("nothing to do here, it's historical :-)");
