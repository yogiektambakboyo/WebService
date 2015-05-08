<?php

class Umum extends CI_Controller{
    function __construct() {
        parent::__construct();
        if (!$this->session->userdata('OperatorCode') ) {
            redirect(base_url() . "index.php/login");
        }
        $this->load->model('umum_model');
    }
    function index()
    {
        if ($this->input->post('btnCek')) {
            $this->form_validation->set_rules('RackSlotCode', 'Kode Rack', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $this->load->view('umum/cekbrg_view');
            }else{
                $data['brg']=$this->umum_model->get_brg($this->input->post('RackSlotCode'));
                $this->load->view('umum/cekbrg_view',$data);
                
            }
        }
        else{
            $this->load->view('umum/cekbrg_view');
        }
    }
}
?>
