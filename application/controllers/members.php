<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Members extends CI_Controller {

	private static $_validationRules = array(
		array(
			'field' => 'name', 
			'label' => 'Voornaam',
            'rules' => 'required|max_length[100]|regex_match[/^[\w öóáäéýúíÄËÿüïöÖÜǧğ]*$/]',
            'errors' => array(
				'required' => 'U moet een voornaam invullen',
				'max_length' => 'De naam mag maximaal 100 karakters lang zijn',
				'regex_match' => 'Alleen de letters a-z, en spaties zijn toegestaan(niet hoofdlettergevoelig)'
			)
		),
		array(
			'field' => 'insertion', 
			'label' => 'Tussenvoegsel',
            'rules' => 'max_length[100]|regex_match[/^[\w öóáäéýúíÄËÿüïöÖÜǧğ]*$/]',
            'errors' => array(
				'max_length' => 'Het tussenvoegsel mag maximaal 100 karakters lang zijn',
				'regex_match' => 'Alleen de letters a-z, en spaties zijn toegestaan(niet hoofdlettergevoelig)'
			)
		),
		array(
			'field' => 'lastname', 
			'label' => 'Achternaam',
            'rules' => 'max_length[100]|regex_match[/^[\w öóáäéýúíÄËÿüïöÖÜǧğ]*$/]',
            'errors' => array(
				'max_length' => 'De achternaam mag maximaal 100 karakters lang zijn',
				'regex_match' => 'Alleen de letters a-z, en spaties zijn toegestaan(niet hoofdlettergevoelig)'
			)
		),
		array(
			'field' => 'ovnumber', 
			'label' => 'Ovnummer',
            'rules' => 'required|greater_than[1]|exact_length[8]|is_numeric',
            'errors' => array(
				'required' => 'U moet een ovnummer invullen',
				'exact_length' => 'Vul een geldig ov-nummer in',
				'is_numeric' => 'Vul een geldig ov-nummer in',
				'greater_than' => 'Vul een geldig ov-nummer in'
			)
		)
	);


	public function __construct()
	{
		parent::__construct();
		//load auth en check, session
		$this->load->library('Auth');
		$this->auth->check('1');
		$this->load->library('session');
		//Load url_helper
		$this->load->helper('url_helper');
		$this->load->model('members_model');
	}
	
	public function index()
	{
		redirect('members/overview');
	}

	public function overview($page = null, $json = false)
	{
		$data['members'] = $this->members_model->getMembers($page, $this->input->post('search'));
		$data['amount'] = $this->members_model->AmountOfMembers();

		if ($json == 'true') {
			header('Content-type:application/json');
			echo json_encode($data);
			exit;
		}

		$this->load->helper('form');

		render('members/overview', $data);
	}

	public function addMember()
	{
		$this->load->helper('form');

		$data['data'] = $this->members_model->getMembers();
		render('members/addMembers', $data);
		
	}
	public function addMemberAction()
	{
		$this->load->library('form_validation');
		$this->load->library('Slug');

		$this->form_validation->set_rules(self::$_validationRules);

		if ($this->form_validation->run()) {

			$saveData = Array(
				"ovnumber" => $this->input->post('ovnumber'),
				"name" => $this->input->post('name'),
				"slug" => $this->slug->slug_exists(url_title($this->input->post('name'), 'dash', TRUE)),
				"insertion" => $this->input->post('insertion'),
				"lastname" => $this->input->post('lastname')
			);
			$this->members_model->addMember($saveData);
			redirect('members/');
		} else {
			redirect('members/addMember');
		}
		
	}
	public function editMember($slug = null)
	{
		if (empty($slug)) {
			redirect('members');
		} 

		$this->load->helper('form');
		
		$query['member'] = $this->members_model->getMember($slug);
		
		if (empty($query['member'])) {
			show_404();
		} 

		render('members/editMember', $query);

	}

	public function editMemberAction()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules(self::$_validationRules);
		$this->form_validation->set_rules('slug', '', 'required',
		array('required' => 'Er is een onbekende fout opgetreden'));

		if ($this->form_validation->run()) {

			$data = Array(
				"ovnumber" => $this->input->post('ovnumber'),
				"name" => $this->input->post('name'),
				"insertion" => $this->input->post('insertion'),
				"slug" => $this->input->post('slug'),
				"active" => $this->input->post('active'),
				"lastname" => $this->input->post('lastname')
			);		
			$this->members_model->editMember($data);
		} else if ($this->input->post('slug')) {
			redirect('members/editMember/'.$this->input->post('slug'));
		}
		redirect('members/');
		
	}

	public function import($fileData)
	{
		$this->load->helper('url_helper');

        $config['upload_path']          = realpath(APPPATH . '../uploads/');
        $config['file_name']            = 'import.csv';
        $config['allowed_types']        = 'csv';
        $config['max_size']             = 100;

        $this->load->library('upload', $config);
        
        $data = array('file' => $this->upload->data());

        if ($this->upload->do_upload('userfile')) {
            $data = array('upload_data' => $this->upload->data());

            $fileData = str_replace(array("\r\n", "\n", "\r"), '', file($data['upload_data']['full_path']));
            $fileData = preg_replace('/[\x00-\x1F\x7F]/u', '', explode(';', implode($fileData, '')));

            unlink($data['upload_data']['full_path']);
      
            $errors = [];
            $counter = count($fileData)-1;
            for ($i = 4; $i < $counter; $i+=4) {
                if (!isset($fileData[$i]) || 1 > $fileData[$i] || strlen($fileData[$i]) !== 8 || !is_numeric($fileData[$i])) {
                    $errors[] = "Ongeldig Ov nummer regelnummer ".((string)$i/4+1);
                } 
                if (!isset($fileData[$i+1])|| strlen($fileData[$i+1]) >= 100 || !preg_match("/^[\w öóáäéýúíÄËÿüïöÖÜǧğ]*$/",     $fileData[$i+1])) {
                    $errors[] = "Ongeldig naam regelnummer ".((string)$i/4+1) ;
                } 
                if (!isset($fileData[$i+2]) || strlen($fileData[$i+2]) >= 100 || !preg_match("/^[\w öóáäéýúíÄËÿüïöÖÜǧğ]*$/",     $fileData[$i+2])) {
                    $errors[] = "Ongeldig tussenvoegsel regelnummer ".((string)$i/4+1) ;
                } 
                if (!isset($fileData[$i+3]) || strlen($fileData[$i+3]) >= 100 || !preg_match("/^[\w öóáäéýúíÄËÿüïöÖÜǧğ]*$/",     $fileData[$i+3])) {
                    $errors[] = "Ongeldig achternaam regelnummer ".((string)$i/4+1) ;
                }
            } 
           
            if (!empty($errors)) {
                addFeeback($errors, 'negative');
                return false;
            }
            $this->members_model->import($fileData);
        } else {
        	addFeeback(array($this->upload->display_errors()), 'negative');
        }
        addFeeback(array('import gelukt'));

		redirect('members/');
	}
}
