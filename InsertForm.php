<?php
require_once("$CFG->libdir/formslib.php");

class ResopInsEditMoodleForm extends moodleform
{
	protected $selectedRes;
	protected $selectedUser;
	protected function userSelect($mform,$resop)
	{
		global $DB;
		$usersDB = $DB->get_records_sql_menu(
			"SELECT  uid,name FROM {resop_resop_user} rru   LEFT JOIN {resop_user} u" . 
			" ON rru.uid = u.id  WHERE rru.actid= ?", array($resop->id)); 
		//array-merge verändert die keys - das geht nicht		
		$users = array('disabled'=>get_string('choose','resop'));
		foreach ($usersDB as $key => $value) 
		{
			$users[$key] = $value;	
		}
 		$this->selectedUser = $mform->addElement('select', 'user', get_string('insertUser','resop'), $users);
		$mform->addRule('user',get_string('chooseHelp','resop'),'numeric');
		$mform->addRule('user',null,'required');	
	}
	
	protected function classSelect($mform,$resop)
	{
		global $DB;
 		//ressource wählen	
		$resourcesDB = $DB->get_records_menu('resop_resource',   array('actid'=>$resop->id), 
			'name', 'id, name'); 
		$resources = array('disabled'=>get_string('choose','resop'));
		foreach ($resourcesDB as $key => $value) 
		{
			$resources[$key] = $value;	
		}
 		$this->selectedRes = $mform->addElement('select', 'res', get_string('insertClass','resop'), $resources);
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
				$mform->setDefault('duration', 90*60);	
		
		$mform->addElement('date_time_selector', 'starttime', get_string('termin','resop'));
		$mform->addElement('duration', 'duration', get_string('duration', 'resop'));	
		$mform->setDefault('duration', 90*60);	
    }
    //Custom validation should be added here
    /*
	 * @param array $data data from the form.
     * @param array $files files uploaded.
     * @return array of errors.
	 * 
	 */
    public function validation($data, $files) 
    {
    	$errors = parent::validation($data, $files);
		$date = $data['starttime'];
		if ($date < time())
			$errors['starttime'] = get_string('dateError','resop');
        return $errors;
    }
	
} 

class InsertForm extends ResopInsEditMoodleForm
{
    //Add elements to form
    public function definition() 
    {
		parent::definition(); 	
 		$mform = $this->_form;
		$buttonarray=array();
		$buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savenext','resop'));
		$buttonarray[] = &$mform->createElement('submit', 'submitbutton2', get_string('saveback','resop'));
		$buttonarray[] = &$mform->createElement('cancel');
		$mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
		$mform->closeHeaderBefore('buttonar');
    }
}
class EditForm extends ResopInsEditMoodleForm
{
	private function setValues($editId,$resop)
	{
		global $DB;
		$mform = $this->_form;
		//get resource
		$resource = $DB->get_record_sql(
			"SELECT  rr.id,name FROM {resop_resource_user} rru  JOIN {resop_resource} rr" . 
			" ON rru.resid = rr.id  WHERE rru.id = ? AND rru.actid = ?", array($editId,$resop->id));
			//I think I don't need actid 
		//select name only for debugging purpose, select seems to work
		$this->selectedRes->setSelected($resource->id);
		
		//get user
		$user = $DB->get_record_sql(
			"SELECT ru.id, ru.name FROM {resop_resource_user} rru JOIN {resop_user} ru " .
			"ON rru.uid = ru.id WHERE rru.id = ?",array($editId));
		$this->selectedUser->setSelected($user->id);		
		
		//get note / kind, duration (time) and starttime (termin)
		$termin = $DB->get_record_sql(
			"SELECT termin, note, time FROM {resop_resource_user} WHERE id = ?",array($editId));
		$mform->setDefault('duration', $termin->time);
		$mform->setDefault('kind', $termin->note);	
		$mform->setDefault('starttime',$termin->termin);
		//ok, setzen der alten Werte scheint zu klappen 
	}
	public function definition()
	{
		parent::definition();
		
		$mform = $this->_form;
		$resop = $this->_customdata['resop'];
		$editId = $this->_customdata['editId'];
		$this->setValues($editId, $resop);
		
		$buttonarray[] = &$mform->createElement('submit', 'submitbutton2', get_string('update','resop'));
		//submitbutton2 leads after updating back to the list
		$buttonarray[] = &$mform->createElement('cancel');
		$mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
		$mform->closeHeaderBefore('buttonar');		
	}
}
