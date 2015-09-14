<?php
require_once("$CFG->libdir/formslib.php");

class InsertForm extends moodleform
{

	private function userSelect($mform,$resop)
	{
		global $DB;
		$users = $DB->get_records_sql_menu(
			"SELECT  uid,name FROM {resop_resop_user} rru   LEFT JOIN {resop_user} u" . 
			" ON rru.uid = u.id  WHERE rru.actid= ?", array($resop->id)); 	
		$users = array_merge(array('disabled'=>get_string('choose','resop')),$users);
 		$mform->addElement('select', 'user', get_string('insertUser','resop'), $users);
		$mform->addRule('user',get_string('chooseHelp','resop'),'numeric');
		$mform->addRule('user',null,'required');	
	}
	private function classSelect($mform,$resop)
	{
		global $DB;
 		//ressource wählen	
		$resources = $DB->get_records_menu('resop_resource',   array('actid'=>$resop->id), 
			'name', 'id, name'); 
		$resources = array_merge(array('disabled'=>get_string('choose','resop')),$resources);
 		$mform->addElement('select', 'res', get_string('insertClass','resop'), $resources);
		$mform->addRule('res',get_string('chooseHelp','resop'),'numeric');
		$mform->addRule('res',null,'required');
	}
	
    //Add elements to form
    public function definition() 
    {
        global $CFG, $DB;
 	
 		$mform = $this->_form;
 		$resop = $this->_customdata['resop'];
 		$this->classSelect($mform, $resop);
		$this->userSelect($mform,$resop);
			
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
