<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of main
 *
 * @author USER
 */
class Main extends CI_Controller{
    //put your code here
    function __construct() {
        parent::__construct();
        if (!$this->session->userdata('OperatorCode') || !$this->session->userdata("OperatorRole") || $this->session->userdata('outstanding')) {
            redirect(base_url() . "index.php/login");
        }
        $this->load->model('main_model');
    }
    
    function index(){
        $data['linktujuan']=  $this->main_model->getlink($this->session->userdata("OperatorRole"));
        $this->load->view('main_view',$data);
    }
}

?>
