<?php

class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("user_model");
        $this->load->library('form_validation');
        $this->load->library('session');
        // $this->output->enable_profiler(TRUE);
    }

    private function validation()
    {
        $this->form_validation->set_rules('email', 'Email', 'required', array('required' => 'Username wajib diisi'));
        $this->form_validation->set_rules('password', 'Password', 'required', array('required' => 'Password wajib diisi'));
        if ($this->form_validation->run() == FALSE) {
            return false;
        }
        else {
            return true;
        }
    }

    public function index()
    {
        if ($this->input->post()) {
            $status = $this->validation();
            if($status == FALSE) {
                $this->session->set_userdata('message', array('type' => 'error', 'message' => [validation_errors()]));
                return redirect(site_url('admin'));
            }
            $user = $this->user_model->doLogin();
            if($user){
                $isPasswordTrue = password_verify($post["password"], $user->password);
                $isAdmin = $user->role == "admin";
                if($isPasswordTrue && $isAdmin){ 
                    $this->session->set_userdata(['user_logged' => $user]);
                    $this->_updateLastLogin($user->user_id);
                    return true;
                }
            }
            else {
                redirect(site_url('admin'));
            }
        }
        $this->load->view("admin/login_page.php");
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect(site_url('admin/login'));
    }

    public function test()
    {
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(array('foo' => 'bar')));
    }
}
