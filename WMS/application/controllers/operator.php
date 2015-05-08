<?php

class Operator extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('operator_model');
    }

    public function index() {
        $this->load->view("operator/operator_main_view");
    }

    function tambah_tim() {
        if ($this->input->post('btnProsesStep1')) {
            $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
            $this->form_validation->set_rules('shift', 'Shift', 'callback_cek_select');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $this->load->view("operator/tambah_tim_step1_view");
            } else {
                $data['tanggal'] = $this->input->post('tanggal');
                $data['shift'] = $this->input->post('shift');
                $data['tugas'] = $this->operator_model->getTugas();
                $data['zone'] = $this->operator_model->getZone();
                $this->load->view('operator/tambah_tim_step2_view', $data);
            }
        } elseif ($this->input->post('btnProsesStep2')) {
            $this->form_validation->set_rules('tugas', 'Tugas', 'callback_cek_select');
            $this->form_validation->set_rules('zone', 'Zone', 'callback_cek_select');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $data['tanggal'] = $this->input->post('tanggal');
                $data['shift'] = $this->input->post('shift');
                $data['tugas'] = $this->operator_model->getTugas();
                $data['zone'] = $this->operator_model->getZone();
                $this->load->view('operator/tambah_tim_step2_view', $data);
            } else {
                $data['tanggal'] = $this->input->post('tanggal');
                $data['shift'] = $this->input->post('shift');
                $data['tugas'] = $this->input->post('tugas');
                $data['zone'] = $this->input->post('zone');
                $data['operator'] = $this->operator_model->getOperatorWhRole($data['tugas']);
                $this->load->view('operator/tambah_tim_step3_view', $data);
            }
        } elseif ($this->input->post('btnProsesStep3')) {
            $this->form_validation->set_rules('operator[]', 'Operator', 'required');
            $this->form_validation->set_rules('keterangan', 'Keterangan', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $data['tanggal'] = $this->input->post('tanggal');
                $data['shift'] = $this->input->post('shift');
                $data['tugas'] = $this->input->post('tugas');
                $data['zone'] = $this->input->post('zone');
                $data['operator'] = $this->operator_model->getOperatorWhRole($data['tugas']);
                $this->load->view('operator/tambah_tim_step3_view', $data);
            } else {
                $tanggal = $this->input->post('tanggal');
                $shift = $this->input->post('shift');
                $tugas = $this->input->post('tugas');
                $zone = $this->input->post('zone');
                $operator = $this->input->post('operator');
                $keterangan = $this->input->post('keterangan');
                $teamId = $this->operator_model->setTeam("1", $tanggal, $shift, $tugas, $zone, $keterangan);
                echo $teamId;
                foreach ($operator as $row) {
                    //$this->operator_model->setTeamOperator("1",$teamId,$row);
                }
                //$this->session->set_flashdata("pesan","Tim berhasil ditambahkan.");
                //redirect(base_url()."index.php/operator/tambah_tim");
            }
        } else {
            $this->load->view("operator/tambah_tim_step1_view");
        }
    }

    function cek_select($str) {
        if ($str == 0) {
            $this->form_validation->set_message('cek_select', '%s harus dipilih.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

}

?>