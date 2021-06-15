<link rel="stylesheet" href="http://localhost/PHP-Car_Rental/index.php/../css/bootstrap.min.css">
<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MyApp extends CI_Controller
{
	public function index()
	{
		if ($this->session->userdata('email') == null) {
			$this->load->view('template/header2');
		} else {
			$this->load->view('template/header');
		}
		$this->load->view('template/index'); //your web page goes here going to use index.php as homepage
	}

	public function login()
	{
		$this->load->view('template/header2');
		$this->load->view('template/login');
	}
	public function dashboard()
	{
		$this->load->model('Cars');
		$data['cars'] = $this->Cars->getAll_cars();
		$this->load->view('template/header');
		$this->load->view('template/dashboard', $data);
	}

	public function getLoginInfo()
	{
		$this->form_validation->set_rules('email', 'Email', 'required');
		$this->form_validation->set_rules('pswd', 'Password', 'required');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		if ($this->form_validation->run()) {
			$email = $this->input->post('email');
			$password = hash('sha512', $this->input->post('pswd'));
			$this->load->model('Users');
			if ($this->Users->canLogin($email, $password)) {
				//getting logged in user;
				$this->load->model('Users');
				$roleId = $this->Users->getLoggedInUser($email);
				$session_data = array(
					'roleId' => $roleId,
					'email' => $email
				);
				$this->session->set_userdata($session_data);
				redirect(base_url('/MyApp/dashboard'));
			}
			$this->load->view('template/header');
			$error = "invalid email or password";
			$this->load->view('template/login', compact('error'));
		} else {
			$this->load->view('template/header');
			$this->load->view('template/login');
		}
	}
	public function logout()
	{
		$this->session->unset_userdata('email');
		redirect(base_url('MyApp/login'));
	}

	public function get_pdf()
	{
		$this->load->model('Users');
		$data = $this->Users->getAll_users();
		$this->load->library('fpdf183/fpdf');
		ob_start();
		$this->fpdf = new fpdf('P', 'mm', 'A4');
		$this->fpdf->SetTitle('List Of All Users');
		$this->fpdf->SetMargins(22, 10, 1);
		$this->fpdf->AddPage();
		$this->fpdf->SetFont('Arial', 'B', 15);
		$this->fpdf->Cell(70, 10, "Names", 1);
		$this->fpdf->Cell(60, 10, "Email", 1);
		$this->fpdf->Cell(40, 10, "Phone", 1);
		$this->fpdf->Ln();
		foreach ($data as $user) {
			$this->fpdf->SetFont('Arial', '', 12);

			$this->fpdf->Cell(70, 10, $user->name, 1);
			$this->fpdf->Cell(60, 10, $user->email, 1);
			$this->fpdf->Cell(40, 10, $user->phone, 1);
			$this->fpdf->Ln();
			ob_clean();
		}
		$this->fpdf->Output();
	}
}
