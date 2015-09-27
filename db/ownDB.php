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

		/*
		 * @param string $name name of class
		 * @param int $resopId id of resop-instance
		 * @return records of entries which belong to class and resop-instance 
		 * */
		public static function getClassEntries($name,$resopId)
		{
			global $DB;		
			$operator=' LIKE ';
			if ($name!='%')
				$operator = ' = '; 
			$sql= 'SELECT rru.id, rru.termin, rru.time, rru.creation, rru.moun, rru.note, rr.name as rname, ru.name  as uname '.
				  'FROM {resop_resource_user} rru ' .
			      'JOIN {resop_resource} rr ON rru.resid=rr.id  '. 
			      'JOIN {resop_user} ru ON rru.uid=ru.id ' . 
			      'WHERE rr.name' . " $operator '$name' " . 
			      " AND rr.actid=$resopId " .
			      " ORDER BY rr.name, rru.termin";
		    
		    $classEntries = $DB->get_records_sql($sql);
			return $classEntries;
		}
			
		/*
		 * @param string $name name of booker
		 * @return records of entries which belong to booker (done over all resop instances) 
		 * */
		public static function getBookerEntries($name)
		{
			global $DB; 
			$sql= 'SELECT rru.id, rru.termin, rru.time, rru.creation, rru.moun, rru.note, rr.name as rname, ru.name  as uname '.
				  'FROM {resop_resource_user} rru ' .
			      'JOIN {resop_resource} rr ON rru.resid=rr.id  '. 
			      'JOIN {resop_user} ru ON rru.uid=ru.id ' . 
			      "WHERE ru.name ='$name' ORDER BY rr.name, rru.termin";
		    $entries = $DB->get_records_sql($sql);
			return $entries;
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
		
		public static function insertResop(stdClass &$resop, $formContent)
		{
			global $DB;	
			$abt = $formContent->resop_departement;
			$resop->abt_id = $abt ;
			$resop->type =  $formContent->resop_type;  	
    		$resop->id = $DB->insert_record('resop', $resop);
		
			$resources = explode("\n",$formContent->resop_resources); 
			array_walk($resources, create_function('&$val', '$val = trim($val);'));
			$resources = array_filter($resources); //leere weg			
			$users = $formContent->resop_users;
			$records  = array();
			foreach ($users as $key => $value) {
				$record = new stdClass();
				$record->actid = $resop->id;
				$record->uid = $value;
				$records[]=$record;
			}
			$DB->insert_records('resop_resop_user',$records);
			//schnellste Methode und behebt problem, das resop_resop_users keine id hat. 
			//ressourcen eintragen
			
			$records = array();
			foreach ($resources as $key => $value)
			{
				$record = new stdClass();
				$name = trim($value); //strange trim noetig
				$record->name = $name;
				$record->actid = $resop->id;
				$record->anzahl = 1; //erstmal  auf Verdacht eingebaut
				$records[]=$record;
			}										
			$DB->insert_records('resop_resource',$records);
		}

		/*
		 * @param int $id id of resop modul
		 * @param object $fromform Formdata submitted by user
		 * @throws exception with error text
		 * checks if there is a collision of dates, and if not insert
		 * the new entry 
		 * */
		public static function tryInsertExamResource($id,$fromform) 
		{
			ResopDB::checkInsertUpdateExamResource($fromform);		
			ResopDB::insertExamResource($id, $fromform);
		} 			
		
		/*
		 * @param int $id id of resop modul
		 * @param object $fromform Formdata submitted by user
		 * @throws exception with error text
		 * checks if there is a collision of dates, and if not insert
		 * the new entry 
		 * */
		public static function tryUpdateExamResource($id,$editId,$fromform) 
		{
			ResopDB::checkInsertUpdateExamResource($fromform);
			ResopDB::updateExamResource($id, $editId, $fromform);
		} 			


		/*
		 * @param int $id id of resop modul
		 * @param object $fromform Formdata submitted by user
		 * @throws exception with error text
		 * checks if there is a collision of dates
		 * */
		public static function checkInsertUpdateExamResource($fromform) 
		{
			global $DB;
			$error = null;
			//get entries in this week starting with monday
			$start = $fromform->starttime;
			$duration = $fromform->duration;
			$lastMonday = strtotime("last Monday",$start);
			$nextSunday = strtotime("Sunday",$lastMonday);
			$result = $DB->get_records_sql("SELECT * FROM {resop_resource_user} WHERE termin BETWEEN ? AND ? ".
				"AND resid=?", array($lastMonday,$nextSunday,(int) $fromform->res));
			/* todo: check exceptions   
			if (count($result) >= 3) //todo sollte man in den Einstellungen konfigurieren
			{
				throw new Exception(get_string("error3KA","resop"));
			}
			else 
			{ 
			    foreach ($result as $key => $value) 
				{
					if ( ($start >= $value->termin && $start <= $value->termin + $value->time )
					  || ($value->termin >= $start && $value->termin <= $start + $duration) )
						throw new Exception(get_string("errorOverlap","resop"));
				}		
			} */
		} 			
		
		/*
		 * @param int $id id of resop modul
		 * @param object $fromform Formdata submitted by user
		 * insert the new entry
		 */
		private static function insertExamResource($id,$fromform)
		{
			global $DB, $USER;
			$record = new stdClass();
			$record->uid =   (int) $fromform->user;     //user
			$record->resid = (int) $fromform->res;     //resource
			$record->actid = $id;	  //this resop module
			$record->creation = time(); //creation time
			$record->termin = (int) $fromform->starttime;
			$record->time = (int) $fromform->duration;
			$record->moun =  $USER->firstname . ' ' . $USER->lastname;//moodle user name who creates the entry  
			$record->note = $fromform->kind; //kind is stored under note
			$DB->insert_record('resop_resource_user', $record);//true returns id
			//insert record geht nicht, wenn keine id 						
		}
		/*
		 * @param int $id id of resop modul
		 * @param int $editId id of entry which should be updated
		 * @param object $fromform Formdata submitted by user
		 * insert the new entry 
		 */
		private static function updateExamResource($id,$editId,$fromform)
		{
			global $DB, $USER;
			$record = new stdClass();
			$record->id = $editId;
			$record->uid =   (int) $fromform->user;     //user
			$record->resid = (int) $fromform->res;     //resource
			$record->actid = $id;	  //this resop module
			$record->creation = time(); //creation time
			$record->termin = (int) $fromform->starttime;
			$record->time = (int) $fromform->duration;
			$record->moun =  $USER->firstname . ' ' . $USER->lastname;//moodle user name who creates the entry  
			$record->note = $fromform->kind; //kind is stored under note
			$DB->update_record('resop_resource_user', $record);//true returns id
						
		}
		
		public static function deleteResop($actid)
		{
			global $DB;
			$DB->delete_records('resop_resource_user', array('actid' => $actid));	//alle von Trainern eingetragenen ressourcen
			$DB->delete_records('resop_resop_user', array('actid' => $actid));	 //bei der erstellung eingetragen
			$DB->delete_records('resop_resource', array('actid' => $actid));	//ebenso
		}	
 }		