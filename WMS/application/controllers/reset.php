<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Reset2
 *
 * @author USER
 */
class Reset extends CI_Controller {

    //put your code here

    function __construct() {
        parent::__construct();
        $this->load->model('reset_model');
    }

    function bin() {
        if ($this->input->post('btnReset')) {
            $this->form_validation->set_rules('bin', 'Kode Bin', 'required|callback_cek_bin|callback_cek_pakai');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $this->load->view('reset/reset_bin_view');
            } else {
                $bin = $this->input->post('bin');
                $this->reset_model->updateIsUsed($bin);
                $this->session->set_flashdata('pesan', 'Bin telah di reset');
                redirect(base_url()."index.php/reset/bin");
            }
        } else {
            $this->load->view('reset/reset_bin_view');
        }
    }

    function cek_bin($str) {
        if ($this->reset_model->isKodeBin($str)) { //cek kode bin atau bukan
            return TRUE;
        } else {
            $this->form_validation->set_message('cek_bin', '%s tidak valid.');
            return FALSE;
        }
    }
    
    function cek_pakai($str) {
        if ($this->reset_model->isBolehKodeBin($str)) { //cek kode bin sedang dipakai atau tidak
            return TRUE;
        } else {
            $this->form_validation->set_message('cek_pakai', '%s tidak bisa direset.');
            return FALSE;
        }
    }

}

?>
