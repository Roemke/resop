<?php
/*
 * extend class admin_setting_configtextarea to check DB
 */
require_once("$CFG->dirroot/mod/resop/db/ownDB.php");

class admin_setting_configta_users extends admin_setting_configtextarea {

	public function write_setting($data) {
        // your custom validation logic here
    	//we have no special needs, but we clean empty lines and compare with the db
    	$lines = explode("\n", $data);
		array_walk($lines, create_function('&$val', '$val = trim($val);'));
		$lines = array_filter($lines); //leere weg
		/*
    	echo "<textarea cols=50 rows=20>";
		var_dump($lines);
		echo "</textarea>";
		
		 * 
		 */
		//ok, jetzt abgleich mit db 
		ResopDB::synchDBUsers($lines); //nur einfügen, alte nicht herausnehmen
		$data = implode("\n",$lines);
		return parent::write_setting($data);
		
	}
}

/*
 * extend class admin_setting_configtextarea to check DB
 */
class admin_setting_configta_departements extends admin_setting_configtextarea {

	public function write_setting($data) {
        // your custom validation logic here
    	//we have no special needs, but we clean empty lines and compare with the db
    	$lines = explode("\n", $data);
		array_walk($lines, create_function('&$val', '$val = trim($val);'));
		$lines = array_filter($lines); //leere weg
		/*
    	echo "<textarea cols=50 rows=20>";
		var_dump($lines);
		echo "</textarea>";
		
		 * 
		 */
		//ok, jetzt abgleich mit db 
		ResopDB::synchDBDepartements($lines); //nur einfügen, alte nicht herausnehmen
		$data = implode("\n",$lines);
		return parent::write_setting($data);
		
	}
}
