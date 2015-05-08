<?php

class login extends CI_Controller {

    //put your code here
    function __construct() {
        parent::__construct();
        $this->load->model("login_model");
        
    }

    function index() {
        if ($this->session->userdata('OperatorCode')) {
            //redirect(base_url() . "index.php/main");
            if ($this->login_model->cekOutstanding($this->session->userdata("OperatorCode"))) {//cek apakah login operator masih mempunyai outstanding
                if ($this->session->userdata("outstanding")) {
                    $this->session->unset_userdata("outstanding");
                }
				// var_dump($this->session->userdata("OperatorRole"));
                if ($this->session->userdata("OperatorRole") == '10/WHR/000' || $this->session->userdata("OperatorRole") == '10/WHR/999') {	
				   redirect(base_url() . "index.php/admin/List_Summary_Outstanding");
               
				} else {
					$data['Role'] = $this->login_model->getRole($this->session->userdata("OperatorCode"));
                    $this->load->view('login/choose_role_view', $data);
                }
            } else {
                $this->session->set_userdata("outstanding", TRUE);
                $data['menuoutstanding'] = $this->login_model->getOutstanding($this->session->userdata("OperatorCode"));
                //menampilkan menu apa saja yang outstanding
                $this->load->view('login/menu_outstanding_view', $data);
            }
        } else {
            if ($this->input->post('btnLogin')) {
                $this->form_validation->set_rules('username', 'Username', 'required');
                $this->form_validation->set_rules('password', 'Password', 'required');
                $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
                if ($this->form_validation->run() == FALSE) {
                    $this->load->view('login/login_view');
                } else {
                    $username = $this->input->post('username');
                    $password = $this->input->post('password');
                    //echo $username . " - " . $password;
                    if ($this->login_model->getLogin($username, $password)) {
                        $login = $this->login_model->getDataLogin($username, $password);
                        $this->session->set_userdata("OperatorCode", $login['OperatorCode']);
                        $this->session->set_userdata("login_name", $login['name']);
                        $this->session->set_userdata("login_siteId", $login['siteId']);
                        $this->session->set_userdata("siteId", $login['siteId']);
                        $binimaginer=$this->login_model->getbinimaginer($login['siteId']);
                        $this->session->set_userdata('ReceiveSource',$binimaginer['ReceiveSource']);
                        $this->session->set_userdata('ReceiveProblem',$binimaginer['ReceiveProblem']);
                        $this->session->set_userdata('ShippingProblem',$binimaginer['ShippingProblem']);
                        $this->session->set_userdata('ReplenishProblem',$binimaginer['ReplenishProblem']);
                        $this->session->set_userdata('HariCabang',200);
                        if ($this->login_model->cekOutstanding($this->session->userdata("OperatorCode"))) {//cek apakah login operator masih mempunyai outstanding
                            if ($this->session->userdata("outstanding")) {
                                $this->session->unset_userdata("outstanding");
                            }
                            $data['Role'] = $this->login_model->getRole($this->session->userdata("OperatorCode"));
							// var_dump($data['Role'][0]['WHRoleCode']);
                            if ($data['Role'][0]['WHRoleCode'] == '10/WHR/000' || $data['Role'][0]['WHRoleCode'] == '10/WHR/999') {
							
                                $this->session->set_userdata("OperatorRole", $data['Role'][0]['WHRoleCode']);
                                $this->session->set_userdata("RoleName", $data['Role'][0]['Name']);
                                redirect(base_url() . "index.php/admin/List_Summary_Outstanding");
                           
							} else {
							   $this->load->view('login/choose_role_view', $data);
                            }
                        } else {
                            $this->session->set_userdata("outstanding", TRUE);
                            $data['menuoutstanding'] = $this->login_model->getOutstanding($this->session->userdata("OperatorCode"));
                            //menampilkan menu apa saja yang outstanding
                            $this->load->view('login/menu_outstanding_view', $data);
                        }
                        //redirect(base_url() . "index.php/main");
                    } else {
                        $data['error'] = "Username / Password Anda salah.";
                        $this->load->view('login/login_view', $data);
                    }
                }
            } else {
                $this->load->view('login/login_view');
            }
        }
    }

    function logout() {
        $this->session->sess_destroy();
        redirect(base_url() . "index.php/login");
    }

    function getstatusoutstanding() {
        echo $this->login_model->cekOutstanding($this->session->userdata("OperatorCode"));
    }

    function selectRole() {
        if ($this->input->post('btnSubmit')) {
            //$rolecode = $this->encrypt->decode($rolecode);
            $this->session->set_userdata("OperatorRole", $this->input->post('rolecode'));
            $this->session->set_userdata("RoleName", $this->input->post('rolename'));
            redirect(base_url() . "index.php/main");
        }
    }

}

?>
