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
class Assigned extends CI_Controller{
    //put your code here
    //put your code here
    function __construct() {
        parent::__construct();
        if (!$this->session->userdata('OperatorCode') || $this->session->userdata('outstanding') || $this->session->userdata("OperatorRole") == '10/WHR/000') {
            redirect(base_url() . "index.php/login");
        }
        $this->load->model("assigned_model");
    }
    function getAllAssigment(){
        $data['Assignment']=  $this->assigned_model->getAllAssignment($this->session->userdata('OperatorCode'));
        $this->load->view('assigned/menu_assignment_view', $data);
    }
    function selectAssignment()
    {
        $this->session->set_userdata("OperatorRole", $this->input->post('OprRole'));
        $this->session->set_userdata("RoleName", $this->input->post('RoleName'));
        redirect(base_url() . $this->input->post('LinkAddress'));
    }
}

