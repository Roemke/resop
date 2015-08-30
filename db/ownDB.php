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
 * own resop db-class, if this is the right way - I don't know
 *
 * @package    mod_resop
 * @copyright  2015 Karsten Roemke
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 require_once($CFG->dirroot.'/config.php');
 
 class ResopDB
 {
 		//synch users and 	
 		/*
		 * data: array with usernames
		 */
 		public static function synchDBUsers($data)
		{
			global $DB;
			foreach ($data as $key => $value) {
				if (!$DB->record_exists('resop_user',array('name' => $value)))
				{
					$record = new stdClass();
					$record->name = $value;
					$DB->insert_record('resop_user', $record, false);//true returns id
				}	
			}
		}
 		/*
		 * data: array with departements
		 */
 		public static function synchDBDepartements($data)
		{
			global $DB;
			foreach ($data as $key => $value) {
				if (!$DB->record_exists('resop_abt',array('name' => $value)))
				{
					$record = new stdClass();
					$record->name = $value;
					$DB->insert_record('resop_abt', $record, false);//true returns id
				}	
			}
		}
		
		//liefert array mit index aus der DB und name aus der DB
		public static function getDepartements()
		{
			global $DB;
			return $DB->get_records_menu('resop_abt');
		}
		//liefert array mit index aus der DB und name aus der DB
		public static function getUser()
		{
			global $DB;
			return $DB->get_records_menu('resop_user');
		}
		
		public static function insertResop($id, $formContent)
		{
			global $DB;	
			$abt = $formContent->resop_departement;
			$type = $formContent->resop_type;
			$resources = explode("\n",$formContent->resop_resources); 
			$users = explode("\n",$formContent->resop_users);
			$abtIds = array();
			
			//hole die Abteilung aus der DB wenn sie vorhanden ist
			$abtInDB=$DB->get_record('resop_abt', array('name'=>$abt));//, $fields='*', $strictness=IGNORE_MISSING) 
			$record = new stdClass();
			$record->name = $abt;
			if (!$abtInDb)
			{
			}
			//bearbeite die Usertabelle, wenn User vorhanden ordne ihn dem  
			//aktuellen Planer zu 
			foreach ($abt as $key => $value) {
				$record = new stdClass();
				$record->name         = trim($value);
				$record->actid = $id;
				$lastinsertid = $DB->insert_record('resop_abt', $record, true);//true returns id
				$abtIds[$value]=$lastinsertid;			
			} 
			
			foreach ($resources as $key => $value)
			{
				$record = new stdClass();
				$name = trim($value); //strange trim noetig
				$record->name = $name;
				$record->abt_id = $abtIds[$abt];
				$record->type = $type;
				$record->actid = $id;
				$record->anzahl = 1; //erstmal  auf Verdacht eingebaut
				$DB->insert_record('resop_resource',$record,false);
			}
			//var_dump($abtIds);exit();
		}
		
		public static function deleteResop($actid)
		{
			global $DB;
			$DB->delete_records('resop_resource_user', array('actid' => $actid));	
			$DB->delete_records('resop_user', array('actid' => $actid));	
			$DB->delete_records('resop_', array('actid' => $actid));	
			$DB->delete_records('resop_resource', array('actid' => $actid));	
			$DB->delete_records('resop_abt', array('actid' => $actid));	
		}	
 }		