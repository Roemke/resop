<?php
require_once("$CFG->libdir/formslib.php");

class InsertForm extends moodleform
{
    //Add elements to form
    public function definition() 
    {
        global $CFG, $DB;
 		$resop = $this->_customdata['resop'];
        $mform = $this->_form; // Don't forget the underscore! 
 		//ressource wählen	
		$resources = $DB->get_records_menu('resop_resource',   array('actid'=>$resop->id), 
			'name', 'id, name'); 
		//var_dump($resources);		
 		$mform->addElement('select', 'res', get_string('insertClass','resop'), $resources);
		$users = $DB->get_records_sql_menu(
			"SELECT  uid,name FROM {resop_resop_user} rru   LEFT JOIN {resop_user} u" . 
			" ON rru.uid = u.id  WHERE rru.actid= ?", array($resop->id)); 	
 		$mform->addElement('select', 'user', get_string('insertUser','resop'), $users);
		$mform->addElement('text', 'kind',get_string('kind','resop'));
		$mform->setType('kind', PARAM_NOTAGS);
		$mform->addHelpButton('kind', 'kind', 'resop'); //_help wird automatisch angehaengt
		$mform->addRule('kind',null,'required');
		
		$mform->addElement('date_time_selector', 'starttime', get_string('termin','resop'));
		$mform->addElement('duration', 'duration', get_string('duration', 'resop'));	
		$mform->setDefault('duration', 90*60);	
		$buttonarray=array();
		$buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savenext','resop'));
		$buttonarray[] = &$mform->createElement('submit', 'submitbutton2', get_string('saveback','resop'));
		$buttonarray[] = &$mform->createElement('cancel');
		$mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
		$mform->closeHeaderBefore('buttonar');
    }
    //Custom validation should be added here
    /*
	 * @param array $data data from the form.
     * @param array $files files uploaded.
     * @return array of errors.
	 * 
	 */
    function validation($data, $files) 
    {
    	$errors = parent::validation($data, $files);
		$date = $data['starttime'];
		if ($date < time())
			$errors['starttime'] = get_string('dateError','resop');
        return $errors;
    }
}
