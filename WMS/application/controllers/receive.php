<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of receiving
 *
 * @author USER
 */
class Receive extends CI_Controller {

    //put your code here
    function __construct() {
        parent::__construct();
        if (!$this->session->userdata('OperatorCode') || !$this->session->userdata("OperatorRole") || $this->session->userdata('outstanding')) {
            redirect(base_url() . "index.php/login");
        }
        $this->session->set_userdata('OperatorRole', '10/WHR/001'); //ubah jadi receiver
        $this->load->model('receive_model');
        $this->load->model('storing_model');
        $this->load->model('penolakan_model');
        //$this->load->library('My_Cart');
    }

    function index() {

        $this->tambah_retur_storing();
    }

    function tambah_retur_storing() {
        if (!$this->session->userdata("outstanding")) {
            //melakukan pemilihan master transaction retur yang akan operator proses
            if ($this->input->post('btnProses')) {
                $this->form_validation->set_rules('transactionCode', 'Nota Retur', 'required');
                $this->form_validation->set_rules('koderack', 'koderack', 'required|callback_cekexistkoderack');
                $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
                if ($this->form_validation->run() == FALSE) {
                    $data['retur'] = $this->receive_model->getReturList();
                    $this->load->view('receive/list_task_retur_view', $data);
                } else {
                    $retur = $this->input->post('transactionCode');

                    $koderack = $this->input->post('koderack'); //bukan bin tapi staging area

                    $returterambil = $this->receive_model->cekDetailTransactionReturOpr($retur, $this->session->userdata('OperatorCode'), $this->session->userdata('OperatorRole'));

                    if ($returterambil == TRUE) {
                        $this->receive_model->setDetailTransactionReturOpr($retur, $this->session->userdata('OperatorCode'), $this->session->userdata('OperatorRole'));
                        $this->session->set_userdata('koderack', $koderack);
                        $this->session->set_userdata('transactionCode', $retur);
                        $this->cart->destroy();
                        $this->load->view("receive/home_view");
                    } else {
                        $data['error'] = "Task Sudah Diambil";
                        $data['retur'] = $this->receive_model->getReturList($this->session->userdata('OperatorCode'));
                        $this->load->view('receive/list_task_retur_view', $data);
                        return;
                    }
                }
            } else {
                $data['retur'] = $this->receive_model->getReturList($this->session->userdata('OperatorCode'));
                $this->load->view('receive/list_task_retur_view', $data);
            }
        } else {
            redirect(base_url() . "index.php/login");
        }
    }

    function penerimaan_tolakan() {
        if (!$this->session->userdata("outstanding")) {
            if ($this->input->post('btnProses')) {
                $this->form_validation->set_rules('transactionCode', 'Kode Nota', 'required');
                $this->form_validation->set_rules('kodeRack', 'Kode Rack', 'required|callback_cekexistkoderack');
                $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
                if ($this->form_validation->run() == FALSE) {
                    $data['tolakan'] = $this->penolakan_model->getTolakanList();
                    $this->load->view('receive/list_task_tolakan_view', $data);
                } else {
                    $tolakan = $this->input->post('transactionCode');
                    $kodeRack = $this->input->post('kodeRack');
                    $cekOpExist = $this->receive_model->cekDetailTransactionReturOpr($tolakan, $this->session->userdata('OperatorCode'), $this->session->userdata('OperatorRole'));

                    if ($cekOpExist == TRUE) {
                        $this->receive_model->cekDetailTransactionReturOpr($tolakan, $this->session->userdata('OperatorCode'), $this->session->userdata('OperatorRole'));
                        $this->session->set_userdata('kodeRack', $kodeRack);
                        $this->session->set_userdata('transactionCode', $tolakan);
                        $this->cart->destroy();
                        $this->load->view("receive/home_view");
                    } else {
                        $data['error'] = "Task Sudah Diambil";
                        $data["penolakan"] = $this->penolakan_model->getTolakanList();
                        $this->load->view('receive/list_task_tolakan_view', $data);
                        return;
                    }
                }
            } else {
                $data['tolakan'] = $this->penolakan_model->getTolakanList();
                //var_dump($data);
                $this->load->view('receive/list_task_tolakan_view', $data);
            }
        } else {
            redirect(base_url() . "index.php/login");
        }
    }

    function cekexistkoderack($koderack) {
        //melakukan pengecekan apakah SKU dan ED di Dest bin sama dengan SKU dan ED di bin sekarang dibawa
        if (!$this->storing_model->cekvalidasirack2($koderack)) {
            $this->form_validation->set_message('cekexistkoderack', 'Kode Rack Salah');
            return FALSE;
        }
        return TRUE;
    }

    function cari_barang($keyword='') {
        if ($this->input->post('btnCari')) {
            $cari = $this->input->post('cari');
            $kodeNota = $this->session->userdata('transactionCode');
            $data['barang'] = $this->receive_model->cari_barang($kodeNota, $cari);
            $this->load->view('receive/cari_barang_view', $data);
        } elseif ($this->input->post('btnPilih')) {
            $this->form_validation->set_rules('barang', 'Kode Barang', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $this->load->view('receive/cari_barang_view');
            } else {
                $kodeBarang = $this->input->post('barang');
                $kodeBarang = explode("~", $kodeBarang);
                $data['barang'] = array("kode" => $kodeBarang[0], "keterangan" => $kodeBarang[1]);
                $this->load->view("receive/home_view", $data);
            }
        } else {
            $data['keyword']=$keyword;
            $this->load->view('receive/cari_barang_view',$data);
        }
    }

    function add_retur() {
        $this->form_validation->set_rules('bin', 'Bin', 'required|callback_cekBin');
        $this->form_validation->set_rules('barang', 'Barang', 'required|callback_cekBarang');
        $this->form_validation->set_rules('ed', 'ED', 'required');
        $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|integer|callback_cekQty');
        $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
        if ($this->form_validation->run() == TRUE) { //masukkan ke dalam cart
            $bin = $this->input->post('bin');
            $barang = $this->input->post('barang');
            $ed = $this->input->post('ed');
            $jumlah = $this->input->post('jumlah');
            $namabarang = $this->input->post('namabarang');
            $rasio = $this->input->post('rasio');
            $retur = array(
                'id' => $bin,
                'qty' => $jumlah,
                'price' => 1,
                'name' => $barang,
                'options' => array('ed' => $ed, 'namabarang' => $namabarang, 'rasio' => $rasio)
            );
            $this->cart->insert($retur);
            $data = array();
            $data['status'] = TRUE; //mengirimkan status TRUE ke view
            header('Content-Type: application/json', true);
            echo json_encode($data);
        } else { //ada kesalahan, sehingga mengembalikan pesan error ke view
            $data = array();
            $data['status'] = FALSE;
            $data['error'] = validation_errors();
            header('Content-Type: application/json', true);
            echo json_encode($data);
        }
    }

    function edit_retur() {
        $this->form_validation->set_rules('bin', 'Bin', 'required');
        $this->form_validation->set_rules('barang', 'Barang', 'required|callback_cekBarang');
        $this->form_validation->set_rules('ed', 'ED', 'required');
        $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|integer|callback_cekQty');
        $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
        if ($this->form_validation->run() == TRUE) {
            $rowid = $this->input->post('rowid');
            $bin = $this->input->post('bin');
            $barang = $this->input->post('barang');
            $ed = $this->input->post('ed');
            $jumlah = $this->input->post('jumlah');
            $namabarang = $this->input->post('namabarang');
            $rasio = $this->input->post('rasio');
            $retur = array(
                'rowid' => $rowid,
                'qty' => 0
            );
            $this->cart->update($retur);


            $retur = array(
                'rowid' => $rowid,
                'id' => $bin,
                'qty' => $jumlah,
                'price' => 1,
                'name' => $barang,
                'options' => array('ed' => $ed, 'namabarang' => $namabarang, 'rasio' => $rasio)
            );
            $this->cart->insert($retur);
            $data = array();
            $data['status'] = TRUE;
            header('Content-Type: application/json', true);
            echo json_encode($data);
        } else {
            $data = array();
            $data['status'] = FALSE;
            $data['error'] = validation_errors();
            header('Content-Type: application/json', true);
            echo json_encode($data);
        }
    }

    function delete_retur() {
        $rowid = $this->input->post('rowid');
        $retur = array(
            'rowid' => $rowid,
            'qty' => 0
        );
        $this->cart->update($retur);
    }

    function list_retur() {
        $this->load->view('receive/list_retur_view');
    }

    function save_retur() {
        if (count($this->cart->contents()) > 0) { //ada retur
            $jumlah = count($this->cart->contents());
            $i = 0;
            $daftarSKU = '';
            foreach ($this->cart->contents() as $item) {
                $daftarSKU.="'".$item['name']."'";
                if ($i < $jumlah - 1) {
                    $daftarSKU.=",";
                }
                $i++;
            }
            if ($this->receive_model->cekSKUDERP($daftarSKU,$this->session->userdata('transactionCode'))) {
                foreach ($this->cart->contents() as $item) {
                    // echo $item['id'] . " - "; //set database retur
                    //insert database
                    $this->receive_model->setDetailTransaction($this->session->userdata('transactionCode'), $item['id'], $item['name'], $item['options']['ed'], $item['qty'] * $item['options']['rasio'], $this->session->userdata('koderack'), $this->session->userdata('koderack'), $this->session->userdata('OperatorCode'),$this->session->userdata('ReceiveSource'));
                }
                $this->cart->destroy();
                $this->session->set_flashdata('pesan', 'Data berhasil ditambahkan.');
                $this->session->set_userdata('OperatorRole', '10/WHR/002'); //ubah jadi mover
                $this->session->set_userdata("outstanding", TRUE);
                $this->receive_model->setDetailTransactionReturOpr($this->session->userdata('transactionCode'), $this->session->userdata('OperatorCode'), $this->session->userdata('OperatorRole'));
                redirect(base_url() . "/index.php/receive");
            } else {
                $data['error']='Barang Kurang.';
            }
        } else { //retur tidak ada
            $data['error']='Data tidak ada.';
        }
        $this->load->view("receive/home_view",$data);
    }

    function cekBin($str) {
        if ($this->receive_model->isUsedBin($str)) {
            return TRUE;
        }
        $this->form_validation->set_message('cekBin', '%s tidak valid atau %s sedang digunakan.');
        return FALSE;
    }
    
    function cekQty($Qty) {
        if ($Qty<=0) {
            $this->form_validation->set_message('cekQty', 'Jumlah Tidak Boleh <=0');
            return FALSE;
        }
        return TRUE;
    }

    function cekBarang($str) {
        if ($this->receive_model->isBarang($str,$this->session->userdata('transactionCode'))) {
            return TRUE;
        }
        $this->form_validation->set_message('cekBarang', '%s tidak valid.');
        return FALSE;
    }

    function get_ajax_barang() {
        //ajax untuk menampilkan informasi SKU barang yang harus dipilih
        $barcodeSKU = $_POST['barcodeSKU'];
        $result = $this->receive_model->cari_barang_ajax($barcodeSKU, $this->session->userdata('transactionCode'));
        $arr = array();
        if(count($result)>1){
            $arr=array("Status"=>false);
        }
        else if(count($result)==0)
        {
            $arr = array("Status"=>true,"Kode" => '', "Keterangan" => '');
        }
        else{
            foreach ($result as $row) {
                $arr = array("Status"=>true,"Kode" => $row['Kode'], "Keterangan" => $row['Keterangan']);
            }
        }
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }

    function get_ajax_satuan() {
        //ajax untuk menampilkan informasi SKU barang yang harus dipilih
        $SKUCode = $_POST['SKUCode'];
        $result = $this->receive_model->getsatuan($SKUCode);
        if(!$this->session->userdata('transactionCode'))
        {
            $TransactionCode=$this->session->userdata('TransactionCode');
        }
        else {
            $TransactionCode=$this->session->userdata('transactionCode');
        }
        $result2 = $this->receive_model->getsatuanDERP($TransactionCode, $SKUCode);

        $arr = array();
        foreach ($result as $row) {
            $arr[] = array("Rasio" => $row['Rasio'], "Satuan" => $row['Satuan'], "DERP" => $result2['RatioName']);
        }
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }
    function get_ajax_rack() {
        //ajax untuk menampilkan informasi SKU barang yang harus dipilih
        $RackSlotCode = $_POST['RackSlotCode'];
        $result = $this->receive_model->getRackName($RackSlotCode);
        
        $arr = array("RackName" => $result['RackName']);
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }
    function get_ajax_EDlama() {
        //ajax untuk menampilkan informasi SKU barang yang harus dipilih
        $SKUCode = $_POST['SKUCode'];
        $EDinput = $_POST['EDinput'];
        
        if(!$this->session->userdata('transactionCode'))
        {
            $TransactionCode=$this->session->userdata('TransactionCode');
        }
        else {
            $TransactionCode=$this->session->userdata('transactionCode');
        }
        $edSKULama = $this->receive_model->getEDLama($TransactionCode, $SKUCode);
        if ($edSKULama == NULL) {
                $edSKULama=$this->receive_model->getEDdefault($SKUCode,$EDinput);
            }
            else{
                if($EDinput!=''){
                    $edSKUBaru=$this->receive_model->getEDdefault($SKUCode,$EDinput);
                    if($edSKULama!=$edSKUBaru){
                        $edSKULama=$this->receive_model->getEDdefault($SKUCode,$EDinput);
                    }
                }
            }
        $arr= array("EDlama" => $edSKULama);
      
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }
}

?>
