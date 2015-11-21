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
			      " ORDER BY rru.termin";
			      //" ORDER BY rr.name, rru.termin";
		    
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
		public static function getUser($id = null)
		{
			global $DB;
			$res = null;
			if ($id===null)
				$res = $DB->get_records_menu('resop_user');
			else 
			{ //get users which are already choosen for this instance
				$sql = "SELECT DISTINCT ru.id, ru.name FROM {resop_resop_user} rru JOIN {resop_user} ru ON rru.uid=ru.id " .
					   " WHERE rru.actid=? ";
				$res = $DB->get_records_sql_menu($sql,array($id));	
			}
			return $res;
		}
		//liefert array mit index aus der DB und name aus der DB
		public static function getResources($id = null)
		{
			global $DB;
			$res = null;
			if ($id === null)
				$sql = "SELECT DISTINCT id, name FROM {resop_resource}  ";
			else
				$sql = "SELECT DISTINCT id, name FROM {resop_resource}   " .
					   " WHERE actid=? ";
				
			$res = $DB->get_records_sql_menu($sql,array($id));	
			
			return $res;
		}
		
		/*
		 * @param array $resources array with resources as key=> value pairs
		 * @param object $resop actual resop instance
		 * inserts resources from array		 
		 * */
		private static function insertResourcesFromArr($resources,$resop)
		{
			global $DB;
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
			if (count($records) > 0)									
				$DB->insert_records('resop_resource',$records);
		}

		/*
		 * @param array $users array with users as key=> value pairs, value is user id
		 * @param object $resop actual resop instance
		 * inserts user from array		 
		 * */
		private static function insertUserFromArr($users,$resop)
		{
			global $DB;
			$records  = array();
			foreach ($users as $key => $value) {
				$record = new stdClass();
				$record->actid = (int) $resop->id;
				$record->uid = (int) $value;
				$records[]=$record;
			}
			if (count($records) > 0)									
				$DB->insert_records('resop_resop_user',$records);
			//schnellste Methode und behebt problem, das resop_resop_users keine id hat. 
			//ressourcen eintragen
			
		}
		public static function insertResop(stdClass &$resop, $formContent)
		{
			global $DB;	
			$abt = $formContent->resop_departement;
			$resop->id_abt = $abt ;
			$resop->type =  $formContent->resop_type;  	
    		$resop->id = $DB->insert_record('resop', $resop);
		
			$resources = explode("\n",$formContent->resop_resources); 
			array_walk($resources, create_function('&$val', '$val = trim($val);'));
			$resources = array_filter($resources); //leere weg			
			ResopDB::insertResourcesFromArr($resources,$resop);			
			$users = $formContent->resop_users;
			ResopDB::insertUserFromArr($users,$resop);
		}

		public static function updateResop(stdClass &$resop, $formContent)
		{
			global $DB;	
			//$DB->set_debug(true);
			//add resources which are used in this instance, need to keep the keys
			$resourcesUsed=$DB->get_records_sql('SELECT DISTINCT rr.id, rr.name, rr.actid FROM {resop_resource_user} rru JOIN {resop_resource} rr ' .
												 'ON rru.resid=rr.id WHERE rru.actid=? ',array($resop->id)); 
												 //array of objects			
			$resourcesKnown=$DB->get_records_sql_menu('SELECT DISTINCT id,name FROM {resop_resource} ' .
												 ' WHERE actid=? ',array($resop->id)); 
												 //array with id as key and name as value
			
			$resneu = explode("\n",$formContent->resop_resources); 
			array_walk($resneu, function(&$val)  {$val = trim($val);});
			$resneu = array_filter($resneu); //leere weg			
			
			//construct list of resources which should be not deleted if existent
			$noDelete = implode("','",$resneu);
			$noDelete = "'" . $noDelete . "'"; //list of elements in ' '
			$sql = array();
			foreach ($resourcesUsed as $key => $value)
			{
				$noDelete .= ",'{$value->name}'"; //maybe double name - doesn't matter				
			}
			$sql = "DELETE FROM {resop_resource} WHERE name NOT IN ($noDelete) AND actid={$resop->id} ";
			$DB->execute($sql);
			//construct array of new resources
			//need all entries of $resneu which are not in $resourcesKnown
			$resneu = array_diff($resneu,$resourcesKnown);
			
			ResopDB::insertResourcesFromArr($resneu,$resop);
			
									 		
			//handle edit of users
			//get the already used user-ids, we don't delete them
			$usedUids=array_keys($DB->get_records_sql_menu('SELECT DISTINCT uid, actid FROM {resop_resource_user}  ' .
												 ' WHERE actid=? ',array($resop->id)));
			 												 
			$newUids = array_map( function($v) {return (int)$v ; }, $formContent->resop_users);
			$noDelete = array_merge($usedUids,$newUids);
			$noDelete = implode(',',$noDelete);
			//resop_resop_user: which users can be used in this instance (beziehungstabelle / relation table)
			$sql = "DELETE FROM {resop_resop_user} WHERE uid NOT IN ($noDelete) AND actid={$resop->id} ";
			$DB->execute($sql);
			$knownUids = array_keys(ResopDB::getUser($resop->id));
			//var_dump($knownUids);echo "<br>";			
			$newUids = array_diff($newUids,$knownUids);
			ResopDB::insertUserFromArr($newUids,$resop);
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