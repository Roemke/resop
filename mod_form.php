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
 * The main resop configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_resop
 * @copyright  2015 Karsten Roemke
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');


/**
 * Module instance settings form
 *
 * @package    mod_resop
 * @copyright  2015 Karsten Roemke
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_resop_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
		
        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('resopname', 'resop'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'resopname', 'resop');

        // Adding the standard "intro" and "introformat" fields.
        //$this->add_intro_editor();//deprecated
        $this->standard_intro_elements();

        // Adding the rest of resop settings, spreading all them into this fieldset
        // ... or adding more fieldsets ('header' elements) if needed for better logic.
        //$mform->addElement('static', 'label1', 'resopsetting1', 'Your resop fields go here. Replace me!');

        $mform->addElement('header', 'resopfieldset', get_string('resopfieldset', 'resop'));
        //kr later take from db?
        //ressource-Type, nur klassenarbeit und andere bisher moeglich        
        $RES_TYPES = array('typeall' => get_string('typeall','resop'),
        					 'typeexam' => get_string('typeexam','resop'));
        $restype = $mform->addElement('select', 'resop_type', get_string('resoptypestring', 'resop'), $RES_TYPES);
        $restype->setSelected('typeexam');        
  	    //restype fuehrt zu name restype und id id_restype
		//-------------------------------------
		//Abteilungen
		$mform->addElement('textarea', 'resop_abteilungen', 
		                    get_string("abteilungen", "resop"), 'wrap="virtual" rows="5" cols="50"');
		$mform->setDefault('resop_abteilungen','FOS');
        $mform->addRule('resop_abteilungen',null,'required');
        
        //Ressourcen selbst
		$mform->addElement('textarea', 'resop_resources', 
		                    get_string("listofresources", "resop"), 'wrap="virtual" rows="15" cols="50"'); 
		$mform->addHelpButton('resop_resources', 'listofresources', 'resop'); //_help wird automatisch angehaengt
        $mform->addRule('resop_resources',null,'required');
		$mform->setDefault('resop_resources',"fo115,FOS\nfo215,FOS\nfo114,FOS\nfo214,FOS\nfh115,FOS");
				                  		
        
        //aktualisiere die Tabelle mit den ressourcen aus der Klassenliste in den settings 
        //ResopDB::actualizeClasses();
        // Add standard grading elements.
        //$this->standard_grading_coursemodule_elements();

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
    }
}
