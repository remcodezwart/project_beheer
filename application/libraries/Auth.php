<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Auth {

	public function __construct()
    {
		$CI =& get_instance();
		$CI->load->library('session');
		$this->_key = $CI->config->item('encryption_key');
		$_SESSION["error"] = array();
    }
   	
   	private $maxAttempts = 10;
	private $_key = ""; 
	private $_loginuri = LOGIN_URI;
	private $f_checksum = "";

	public function getPasswordHash($f_password, $f_user)
	{
		$f_hash = "";
		if (is_object($f_user)) {
			$f_hash = sha1($this->_key . $f_password . $f_user->date_created . strrev($this->_key));
		}
		return $f_hash;
	}

	public function getChecksum()
	{
		$CI =& get_instance();
		$CI->load->helper('url');
		$CI->load->database();
		$CI->load->library('user_agent');
		$CI->load->library('session');
		$this->f_checksum = sha1($this->_key . $CI->agent->agent_string() . $CI->input->ip_address()  . session_id()  .  strrev($this->_key)) ;
	}

	public function check($permision = '0')
	{
		$permision = (string)$permision;

		if (!is_string($permision)) redirect($this->_loginuri);

		$CI =& get_instance();
		$CI->load->helper('url');
		$CI->load->library('user_agent');
		$this->getChecksum();
		if (!empty($CI->session->user_id) && $CI->session->user_id > 0) {

			$query = $CI->db->get_where('user_activities', array("user_id" => $_SESSION["user_id"], "checksum" => $this->f_checksum));

			if ($query->num_rows() != 0 && $permision !== '0') {

				array_push($_SESSION["error"], "Er is te lang geen gebruik gemaakt van de software");
				redirect($this->_loginuri . "?sessionend=1");

			} else {

				if ($permision !== $CI->session->permision && $permision !== '0') {
					header('Location: ' . $this->_loginuri);
					exit;
				} else if ($CI->session->permision === '1') {
					$CI->db->update('user_activities', array( "date_modify" => date('Y-m-d H:i:s')), array("user_id" => $CI->session->user_id));
				}
			
			}
		} else if ($permision !== '0') {
			redirect($this->_loginuri);
		}
	}

	public function doLogin($f_username, $f_password)
	{
		$CI =& get_instance();
		$CI->load->helper('url');
		$CI->load->database();
		$CI->load->library('user_agent');
		$CI->load->library('session');
		$CI->load->model('ConfigModel', 'config_model');
		$user = $CI->config_model->getUserByUsername($f_username);
		if (is_object($user)) {

			if ($user->failed_logins >= $this->maxAttempts) {

				if (time() + (60*60)  <= $user->last_failed_login) {
					$user->failed_logins = 0;

					$CI->db->where('id', $user->id);
					$CI->db->update('users', array(
				        'failed_logins' => 0,
					));
				} else {

					array_push($_SESSION["error"], "U kunt maximaal " . $this->maxAttempts . " keer per uur proberen in te loggen.", 					"Restende tijd tot u weer kan proberen in te loggen over " . 	date('i', $user->last_failed_login - time()) . " minuten" );
					redirect('/login');
					exit;
				}
			}

			if ((time() + (30))  <= $user->last_failed_login) {
				$this->failedLogin($user, $CI);
			}

			$hash = $this->getPasswordHash($f_password, $user);
			if ($hash == $user->password) {
				$this->getChecksum();
				$query = $CI->db->get_where('user_activities', array("user_id" => $user->id));

				if ($query->num_rows() == 0) {
					$CI->db->insert('user_activities', array("user_id" => $user->id, "checksum" => $this->f_checksum, "date_modify" => date('Y-m-d H:i:s')));
				} else {
					$CI->db->update('user_activities', array("checksum" => $this->f_checksum, "date_modify" => date('Y-m-d H:i:s')), array("user_id" => $user->id));
				}
			
				$sessiondata = array(
					"name" => $user->name,
					"user_id" => $user->id,
					"profile_id" => $user->profile_id,
					"profile_image" =>  !empty($user->profile_image) ? $user->profile_image : '/custom/images/users/default.png',
					"menu_state" => $user->menu_state,
					"start_page" => !empty($user->start_page) ? $user->start_page : '/dashboard' ,
					"email" => $user->email,
					"permision" => !empty($user->admin) ? $user->admin : '0'
				);

				$CI->session->set_userdata($sessiondata);

				if ($user->start_page) {
					redirect($user->start_page);
				} else {
					redirect("/dashboard");
				}
			} else {
				array_push($_SESSION["error"], "Gebruikersnaam en/of wachtwoord onjuist");
				$this->failedLogin($user, $CI);
			}
		} else {
			array_push($_SESSION["error"], "Gebruikersnaam en/of wachtwoord onjuist");
			redirect('/login');
		}		
	}

	public function doLogout()
	{
		$CI =& get_instance();
		$CI->load->library('session');
		$CI->session->sess_destroy();
	}

	protected function failedLogin($user, $CI)
	{
		$failedLoginData = array(
	        'last_failed_login' => time(),
	        'failed_logins' => ($user->failed_logins+1 > $this->maxAttempts) ? $this->maxAttempts : $user->failed_logins+1,
		);

		$CI->db->where('id', $user->id);
		$CI->db->update('users', $failedLoginData);

		redirect("/login");
		exit;
	}


	/*public function installSystem(){
		
		$CI =& get_instance();
		$CI->load->database();

		if(empty($CI->config->item('encryption_key')))
		{
			die("Genereer een encryption key via /Key_creator en plaats deze in het /application/config/config.php bestand");
		}

		if (!$CI->db->table_exists('users') ){
			$CI->db->query(file_get_contents( __DIR__ . DIRECTORY_SEPARATOR ."MXFNT_install.sql"));
		}

		$query = $CI->db->get('users');

		if($query->num_rows() == 0){

			$data = array(
				'name' => 'John Do',
				'username' => 'admin',
				'email' => 'johndoe@example.com',
				'profile_id' => 1,
				'active' => 1,
				'date_created'=> date('Y-m-d H:i:s')
			);

			$CI->db->insert("users", $data );
			$CI->load->model('ConfigModel', 'config_model');
			$f_id =  $CI->db->insert_id();
			$query = $CI->db->get_where('users', array("id <>" => $f_id, "username" => $data['username']));
			if($query->num_rows() == 0){
				$CI->db->update('users', array("username" => $data['username']), array("id" => $f_id));
			} else {
				array_push($_SESSION["error"], "Fout tijdens opslaan van gebruiker");
			}
			$user = $CI->config_model->getUser($f_id);
			$hash = $this->getPasswordHash("admin", $user);
			$CI->db->update('users', array("password" => $hash), array("id" => $f_id));
			array_push($_SESSION["error"], "Installatie gelukt. Standaard gebruiker: admin/admin");
		}
	}*/
}