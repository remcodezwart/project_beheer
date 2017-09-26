<?php
defined('BASEPATH') OR exit('No direct script access allowed');

  class Projects extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		//Load projectmodel
		$this->load->model('projects_model');
		//load auth en check, session
		$this->load->library('Auth');
		$this->auth->check('1');
		$this->load->library('session');
		//Load url_helper
		$this->load->helper('url_helper');
		$this->load->model('projects_model');
	}
	
	public function index()
	{
		$query['projects'] = $this->projects_model->getProjects();
		//  //render view projects
		render('projects/overview', $query);
	}

	public function addMembers($slug = null)
	{
		$query['project'] = $this->projects_model->getProject($slug);
		
		if (empty($query['project'])) {
			show_404();
		} 

		render('projects/addMembers', $query);
	}


	public function editProject($slug = null)
	{
		$this->load->helper('form');
    	$this->load->library('form_validation');
		
		$query['project'] = $this->projects_model->getProject($slug);
		
		if (empty($query['project'])) {
			show_404();
		} 

		render('projects/editProject', $query);
	}

	public function editProjectAction()
	{
		$data = Array(
			"name" => $this->input->post('name'),
			"slug" => $this->input->post('slug'),
			"client" => $this->input->post('client'),
			"teacher" => $this->input->post('teacher'),
			"description" => $this->input->post('description')
		);
		$this->projects_model->editProject($data);
		redirect('projects/editProject/'.$data['slug']);
	}

	public function add_project()
	{
		$this->load->helper('form');
    	$this->load->library('form_validation');
		$data['data'] = $this->projects_model->getProjects();
		// get data from form

		$post = Array(
			"posted" => $this->input->post('posted')
		);
		//	Check if form is posted before inserting data 
		if ($post['posted'] == 1) {
			$save = Array(
				"name" => $this->input->post('name'),
				"client" => $this->input->post('client'),
				"teacher" => $this->input->post('teacher'),
				"description" => $this->input->post('description'),
				"posted" => $this->input->post('posted'),
				"members" => $this->input->post('members')
			);
			$this->projects_model->addProject($save);
		}
		//	render view projects
		render('projects/add_project', $data);
		
	}

	
}