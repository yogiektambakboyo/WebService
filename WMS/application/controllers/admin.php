<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Admin extends CI_Controller{
    
    function __construct() {
        parent::__construct();
        if ($this->session->userdata('OperatorCode') && ($this->session->userdata("OperatorRole")=='10/WHR/000' || $this->session->userdata("OperatorRole")=='10/WHR/999') ) {
			$this->load->model('admin_model');
        }
		else{
			redirect(base_url() . "index.php/login");
		}
        
    }
    
    function List_Summary_Outstanding()
    {
        $data['summary']=  $this->admin_model->getSummaryOutstanding();
        $this->load->view('admin/summary_outstanding_view',$data);
    }
}
?>
