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
require_once($CFG->dirroot.'/mod/resop/db/ownDB.php');


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
		global $CFG, $DB;
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
        if (empty($this->current->id)) //nicht editierbare elemente, nur beim hinzufügen
        {
        	$restype = $mform->addElement('select', 'resop_type', get_string('resoptypestring', 'resop'), $RES_TYPES);
        	$restype->setSelected('typeexam');        
	  	    //restype fuehrt zu name restype und id id_restype
			//-------------------------------------
			$departements =  ResopDB::getDepartements();//
			$resdep = $mform->addElement('select', 'resop_departement', get_string('departement', 'resop'), $departements);
			//klappt, baut aus dem schluessel in $departements den value des select-feldes
			$depKeys = array_keys($departements);
			$resdep->setSelected($depKeys[0]);
		}
		else 
		{
			$abt = $DB->get_record_select('resop_abt',"id={$this->current->id_abt}",$params=null, $fields='name');
			$mform->addElement('html', '<p style="font-weight: bold;">' . get_string('departement', 'resop') . 
								': ' . $abt->name . get_string('noteditable','resop') . '</p>'); 	
			$type = get_string($this->current->type,'resop');	
			$mform->addElement('html', '<p style="font-weight: bold;">' . get_string('resoptypestring', 'resop') . 
								': ' . $type . get_string('noteditable','resop') . '</p>'); 	
		}
		//Wer kann buchen
		$user =  ResopDB::getUser();//
		//echo "Current is {$this->current->id} <br>";
		$userSelected = empty($this->current->id) ? '' : ResopDB::getUser($this->current->id);
		$usdep = $mform->addElement('select', 'resop_users', get_string('listofusers', 'resop'), $user, 
		  							array('size'=>20));
		$usdep->setMultiple(true);
		if (!empty($userSelected))
			$usdep->setSelected(array_keys($userSelected));
		
		$mform->addHelpButton('resop_users', 'listofusers', 'resop'); //_help wird automatisch angehaengt
        $mform->addRule('resop_users',null,'required');
		
        //Ressourcen selbst
		$mform->addElement('textarea', 'resop_resources', 
		                    get_string("listofresources", "resop"), 'wrap="virtual" rows="15" cols="50"'); 
		$mform->addHelpButton('resop_resources', 'listofresources', 'resop'); //_help wird automatisch angehaengt
        $mform->addRule('resop_resources',null,'required');
		if (empty($this->current->id))
			$mform->setDefault('resop_resources',$CFG->resop_resources);
		else 		
			$mform->setDefault('resop_resources',implode("\n",ResopDB::getResources($this->current->id)));			
			                  		        
        // Add standard grading elements.
        //$this->standard_grading_coursemodule_elements();

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
    }

    //Custom validation should be added here
    /*
	 * @param array $data data from the form.
     * @param array $files files uploaded.
     * @return array of errors.
	 * 
	 */
	public function validation($data,$files)
	{
		$errors = parent::validation($data, $files);
		if (empty($data['resop_users']))
			$errors['resop_users'] = get_string('noentryError','resop');			
		$res = trim($data['resop_resources']);
		if (empty($res))
			$errors['resop_resources'] = get_string('noentryError','resop');			
		return $errors;	
	} 
}
