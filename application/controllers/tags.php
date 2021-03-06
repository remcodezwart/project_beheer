<?php
defined('BASEPATH') OR exit('No direct script access allowed');

  class Tags extends CI_Controller {

  	private static $_validationRules = array(
		array(
			'field' => 'name', 
			'label' => 'Naam',
            'rules' => 'required|max_length[50]|regex_match[/^[\w !?.\/]*$/]',
            'errors' => array(
				'required' => 'Vul een naam in voor de tag',
				'max_length' => 'De naam van de tag mag maximaal 50 karakters lang zijn',
				'regex_match' => 'Alleen de letters a-z .!?/ en spaties zijn toegestaan(niet hoofdlettergevoelig)'
			)
		),
		array(
			'field' => 'description', 
			'label' => 'Beschrijving',
            'rules' => 'required|max_length[500]',
            'errors' => array(
				'required' => 'u moet een beschrijving invullen',
				'max_length' => 'De beschrijving mag maximaal 500 karakters lang zijn'
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
		$this->load->model('tags_model');
	}

	public function index()
	{
		redirect('tags/overview');
	}
	
	public function overview($page = null)
	{
		$this->load->library('pagination');

		$query['tags'] = $this->tags_model->getTags($page);
		
		$config['base_url'] = 'http://project-beheer/tags/overview';
		$config['total_rows'] =  $this->tags_model->AmountOfTags();
		$config['per_page'] = 10;

		$this->pagination->initialize($config);
		
		render('tags/overview', $query);
	}

	public function editTag($slug = null)
	{
		if (empty($slug)) {
			redirect('tags');
		} 

		$this->load->helper('form');
		
		$query['tags'] = $this->tags_model->getTag($slug);
		
		if (empty($query['tags'])) {
			show_404();
		} 

		render('tags/editTag', $query);

	}

	public function editTagAction()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules(self::$_validationRules);
		$this->form_validation->set_rules('slug', '', 'required');

		if ($this->form_validation->run()) {
			$data = Array(
				"name" => $this->input->post('name'),
            	"active" => $this->input->post('active'),
            	"slug" => $this->input->post('slug'),
				"description" => $this->input->post('description')
			);
			$this->tags_model->editTag($data);
		} else if ($this->input->post('slug')) {
			redirect('tags/editTag/'.$this->input->post('slug'));
		} 
		redirect('tags/');
	}

	public function addTag()
	{
		$this->load->helper('form');
		
		$data['data'] = $this->tags_model->getTags();
		render('tags/addTag', $data);
		
	}

	

	public function addTagAction()
	{
		$this->load->library('form_validation');
		$this->load->library('Slug');
		$this->form_validation->set_rules(self::$_validationRules);

		// $this->form_validation->set_rules('git_url','regex_check');

		if ($this->form_validation->run()) {

			$save = Array(
				"name" => $this->input->post('name'),
				'slug' => $this->slug->slug_exists(url_title($this->input->post('name'), 'dash', TRUE), 'tags'),
            	"active" => $this->input->post('active'),
				"description" => $this->input->post('description')
			);
			$this->tags_model->addTag($save);
			redirect('tags/');
		} else {
			redirect('tags/addTag');
		}
	}
}