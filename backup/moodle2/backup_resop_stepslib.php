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
 * Define all the backup steps that will be used by the backup_resop_activity_task
 *
 * @package   mod_resop
 * @category  backup
 * @copyright 2015 Your Name <your@email.adress>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Define the complete resop structure for backup, with file and id annotations
 *
 * @package   mod_resop
 * @category  backup
 * @copyright 2015 Your Name <your@email.adress>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_resop_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines the backup structure of the module
     *
     * @return backup_nested_element
     */
    protected function define_structure() {

        // Get know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define the root element describing the resop instance.
        $resop = new backup_nested_element('resop', array('id'), array(
            'name', 'intro', 'introformat', 'grade','id_abt','type'));

        // If we had more elements, we would build the tree here.
		//(kr) yes, I forgot this, so the backup contains nothing :-)
		//leider nicht verstanden - backup bleibt erstmal unvollständig
		//https://docs.moodle.org/dev/Backup_2.0_for_developers#Defining_each_element
		 
		/* 
		 * Problem: habe elemente, die die gesamte moodle-Instanz betreffen und elemente,
		 * die nur das Modul hier betreffen - mir unklar, wie ich hier ein Backup
		 * mit der moodlefunktionalität stricken kann.
		 * vielleicht muesen die in der task class aufgenommen werden ?
		 * 
		 * */
		$usedResources = new backup_nested_element('usedResources');//mdl_resop_resource_user
		$usedResource = new backup_nested_element('usedResource', array('id'), array('uid','resid','actid',
												  'creation','termin','moun','note','time')); 	

		$usedUsers = new backup_nested_element('usedUsers');//mdl_resop_resop_user, alle s. unten bei source
		$usedUser = new backup_nested_element('usedUser', array('uid'),array('actid'));//die uid reicht an sich
        //bei fester actid muesste uid eindeutig sein! lasse ich actid jedoch weg, speichert er nichts
		$resources = new backup_nested_element('resources'); //mdl_resop_resource
		$resource = new backup_nested_element('resource', array('id'), array('actid','name','anzahl'));
		 		  
		$departements = new backup_nested_element('departements');//mdl_resop_abt, alle s. unten bei source
		$departement = new backup_nested_element('departement', array('id'), array('name'));
		$users = new backup_nested_element('users');//mdl_resop_user, alle s. unten bei source
		$user = new backup_nested_element('user', array('id'), array('name'));
		//
		 		 /*
		//$users = new backup_nested_element('users');//mdl_resop_user
		//$user = new backup_nested_element('user', array('id'), array('name'));
		$usedUsers = new backup_nested_element('usedUsers');//mdl_resop_resop_user
		$usedUser = new backup_nested_element('usedUser', array('id'),array('actid','uid'));
		//Problem: have no id in this the resop_resop_user table, so maybe i have to generate one
        */         
        //build the tree
		$resop->add_child($usedResources);
		$usedResources->add_child($usedResource);
		$resop->add_child($resources);
		$resources->add_child($resource);
		
		$resop->add_child($usedUsers);
		$usedUsers->add_child($usedUser);
		
        $resop->add_child($departements);
		$departements->add_child($departement);
        $resop->add_child($users);
		$users->add_child($user);
		
		
		/*
		//$resop->add_child($users);
		//$users->add_child($user);
		$resop->add_child($usedUsers);
		$usedUsers->add_child($usedUser);
		*/
        // Define data sources.
        $resop->set_source_table('resop', array('id' => backup::VAR_ACTIVITYID));
		$usedResource->set_source_table('resop_resource_user',array('actid' => backup::VAR_ACTIVITYID));
		$resource->set_source_table('resop_resource',array('actid' => backup::VAR_ACTIVITYID));

		$usedUser->set_source_table('resop_resop_user',array('actid' => backup::VAR_ACTIVITYID));
		//muesste einfacher gehen, da uid eindeutig wenn actid fest
		//$usedUser->set_source_sql('SELECT @i:=@i+1 AS id, t.* FROM mdl_resop_resop_user AS t, (SELECT @i:=0) AS foo where t.actid=?',
		//							array('actid' => backup::VAR_ACTIVITYID));
		
		//hier nehme ich alle 
		$departement->set_source_sql('select * from {resop_abt} where id like ?',array(backup_helper::is_sqlparam('%')));
		$user->set_source_sql('select * from {resop_user} where id like ?',array(backup_helper::is_sqlparam('%')));
		//damit alle departements

		//$usedUser->set_source_table('resop_resop_user',array('actid'=>backup::VAR_ACTIVITYID));		
		/* nicht verstanden 

		
        */		 
         // If we were referring to other tables, we would annotate the relation
        // with the element's annotate_ids() method.
		//$usedUser->annotate_ids('resop_user', 'uid');
        //$resop->annotate_ids('resop_abt','id_abt'); //hmm, kein unterschied?, ist glaube ich auch uninteressant
		 
		
        // Define file annotations (we do not use itemid in this example).
        $resop->annotate_files('mod_resop', 'intro', null);
			
		
        // Return the root element (resop), wrapped into standard activity structure.
        return $this->prepare_activity_structure($resop);
    }
}
	