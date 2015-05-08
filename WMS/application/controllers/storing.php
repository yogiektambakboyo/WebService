<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Storing extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('storing_model');
        //periksa apakah sudah login atau belum

        if (!$this->session->userdata('OperatorCode')) {
            redirect(base_url() . "index.php/login");
        }
    }

    function index() {
        $this->load->view('storing/main_storing_view');
    }

    //-------------list transaksi bpb yang harus dipilih untuk dimasukkan rack-------------------
    function tambah_bpb_storing() {
        if (!$this->session->userdata("outstanding") && $this->session->userdata("OperatorRole") == '10/WHR/002') {
            //melakukan pemilihan master transaction BPB yang akan operator proses
            if ($this->input->post('btnTambahBPB')) {
                $this->form_validation->set_rules('bpb[]', 'No BPB', 'required');
                $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
                if ($this->form_validation->run() == FALSE) {
                    $data['bpb'] = $this->storing_model->getBPBList();
                    $this->load->view('storing/tambah_bpb_view', $data);
                } else {
                    $bpb = $this->input->post('bpb');
                    $this->storing_model->setDetailTransactionOpr($bpb, $this->session->userdata('OperatorCode'), $this->session->userdata('OperatorRole'));
                    $this->session->set_userdata('ProjectCode', 'BPB');
                    $data['outstanding'] = $this->storing_model->getListDetailTransactionHistory($this->session->userdata('OperatorCode'), $this->session->userdata('OperatorRole'), 'BPB');
                    $this->load->view('storing/daftar_all_outstanding_view', $data);
                }
            } else {
                $data['bpb'] = $this->storing_model->getBPBList($this->session->userdata('OperatorCode'));
                $this->load->view('storing/tambah_bpb_view', $data);
            }
        } else {
            redirect(base_url() . "index.php/login");
        }
    }

    //------------- end list transaksi bpb yang harus dipilih untuk dimasukkan rack-------------------
    //-------------list picklist retur yang harus dipilih untuk dimasukkan rack-------------------
    function tambah_retur_storing() {
        if (!$this->session->userdata("outstanding") && $this->session->userdata("OperatorRole") == '10/WHR/002') {
            //melakukan pemilihan master transaction BPB yang akan operator proses
            if ($this->input->post('btnTambahRetur')) {
                $this->form_validation->set_rules('retur[]', 'No Retur', 'required');
                $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
                if ($this->form_validation->run() == FALSE) {
                    $data['retur'] = $this->storing_model->getReturList();
                    $this->load->view('storing/tambah_list_retur_view', $data);
                } else {
                    $retur = $this->input->post('retur');
                    $returterambil = $this->storing_model->cekDetailTransactionReturOpr($retur, $this->session->userdata('OperatorCode'), $this->session->userdata('OperatorRole'));

                    if (count($returterambil) == 0) {
                        $this->storing_model->setDetailTransactionReturOpr($retur, $this->session->userdata('OperatorCode'), $this->session->userdata('OperatorRole'));
                        $this->session->set_userdata('ProjectCode', 'RJT');
                        $data['outstanding'] = $this->storing_model->getListDetailTransactionHistory($this->session->userdata('OperatorCode'), $this->session->userdata('OperatorRole'), 'RJT');
                    } else {
                        $data['error'] = true;
                        $data['listerror'] = $returterambil;
                        $data['retur'] = $this->storing_model->getReturList();
                        $this->load->view('storing/tambah_list_retur_view', $data);
                        return;
                    }
                    $this->load->view('storing/daftar_all_outstanding_view', $data);
                }
            } else {
                $data['retur'] = $this->storing_model->getReturList($this->session->userdata('OperatorCode'));
                $this->load->view('storing/tambah_list_retur_view', $data);
            }
        } else {
            redirect(base_url() . "index.php/login");
        }
    }

    //-------------end list picklist retur yang harus dipilih untuk dimasukkan rack-------------------
    //----------------kembali ke semua list outstanding----------------------------------------------
    function back_tambah_bpb_storing() {
        if (!$this->session->userdata("outstanding") && $this->session->userdata("OperatorRole") == '10/WHR/002') {
            //menampilkan daftar semua outstanding apabila tombol kembali ditekan
            $ProjectCode = $this->session->userdata('ProjectCode');
            $data['outstanding'] = $this->storing_model->getListDetailTransactionHistory($this->session->userdata('OperatorCode'), $this->session->userdata('OperatorRole'), $ProjectCode);
            $this->load->view('storing/daftar_all_outstanding_view', $data);
        } else {
            redirect(base_url() . "index.php/login");
        }
    }
    function get_ajax_daftar_all_outstanding(){
        $ProjectCode = $this->session->userdata('ProjectCode');
        $Outstanding = $this->storing_model->getListDetailTransactionHistory($this->session->userdata('OperatorCode'), $this->session->userdata('OperatorRole'), $ProjectCode);
        $arr=array();
        foreach ($Outstanding as $row){
            $arr[]= array("ERPCode" => $row['ERPCode'],'BinCode'=>$row['BinCode'],'Keterangan'=>$row['Keterangan'],
                'CurrRackSlot'=>$row['CurrRackSlot'],'Qtykonversi'=>$row['Qtykonversi'],'TransactionCode'=>$row['TransactionCode'],
                'QueueNumber'=>$row['QueueNumber'],'NoUrut'=>$row['NoUrut'] ,'Ratio'=>$row['Ratio'],'RatioName'=>$row['RatioName'],
                'Qty'=>$row['Qty'],'SKUCode'=>$row['SKUCode'],'DestRackSlot'=>$row['DestRackSlot']);
        }
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';                   
    }
    //----------------end kembali ke semua list outstanding----------------------------------------------
    //----------------ambil bin yang akan dibawa----------------------------------------------
    function proses_ambil_bin() {
        if (!$this->session->userdata("outstanding") && $this->session->userdata("OperatorRole") == '10/WHR/002') {
            //melakukan pengecekan apabila operator sekarang akan mengambil suatu bin
            if ($this->input->post('btnProses1')) {
                $TransactionCode = $this->input->post('TransactionCode');
                $QueueNumber = $this->input->post('QueueNumber');
                $NoUrut = $this->input->post('NoUrut');
                $this->storing_model->setUser1stBinCode($TransactionCode, $QueueNumber, $NoUrut, $this->session->userdata('OperatorCode'));
                $this->back_tambah_bpb_storing();
            }
            else if ($this->input->post('btnProses2')) {
                $data['TransactionCode'] = $this->input->post('TransactionCode');
                $data['QueueNumber'] = $this->input->post('QueueNumber');
                $data['NoUrut'] = $this->input->post('NoUrut');
                $data['Keterangan'] = $this->input->post('Keterangan');
                $data['ERPCode'] = $this->input->post('ERPCode');
                $data['BinCode'] = $this->input->post('BinCode');
                $data['Ratio'] = $this->input->post('Ratio');
                $data['Qty'] = $this->input->post('Qty');
                $data['RatioName'] = $this->input->post('RatioName');
                $data['DestRackSlot'] = $this->input->post('DestRackSlot');
                $data['SKUCode'] = $this->input->post('SKUCode');
                $data['Satuan'] = $this->storing_model->getsatuan($this->input->post('SKUCode'));
                $this->storing_model->setUser1stBinCode($data['TransactionCode'], $data['QueueNumber'], $data['NoUrut'], $this->session->userdata('OperatorCode'));
                $this->load->view('storing/taruh_bin_view', $data);
        
            }
            else {
                $data['TransactionCode'] = $this->input->post('TransactionCode');
                $data['QueueNumber'] = $this->input->post('QueueNumber');
                $data['NoUrut'] = $this->input->post('NoUrut');
                $data['Keterangan'] = $this->input->post('Keterangan');
                $data['ERPCode'] = $this->input->post('ERPCode');
                $data['BinCode'] = $this->input->post('BinCode');
                $data['Ratio'] = $this->input->post('Ratio');
                $data['Qty'] = $this->input->post('Qty');
                $data['RatioName'] = $this->input->post('RatioName');
                $data['SKUCode'] = $this->input->post('SKUCode');
                $data['DestRackSlot'] = $this->input->post('DestRackSlot');
                $data['Qtykonversi']=  $this->storing_model->getQtykonversi($data['TransactionCode'], $data['NoUrut'],$data['Qty']);
                $this->load->view('storing/ambil_bin_view', $data);
            }
        } else {
            redirect(base_url() . "index.php/login");
        }
    }

    //----------------end ambil bin yang akan dibawa----------------------------------------------

    function list_my_outstanding() {
        //menampilkan daftar outstanding operator berdasarkan bin yang dia bawa bila tombol kembali ditekan
        $this->session->set_userdata('OperatorRole', '10/WHR/002');
        $data['outstanding'] = $this->storing_model->getMyOutstanding($this->session->userdata('OperatorCode'), $this->session->userdata('OperatorRole'), $this->session->userdata('ProjectCode'));
        $this->load->view('storing/daftar_my_outstanding_view', $data);
    }

    function taruh_bin() {
        //menampilkan form penaruhan bin
        $data['TransactionCode'] = $this->input->post('TransactionCode');
        $data['QueueNumber'] = $this->input->post('QueueNumber');
        $data['NoUrut'] = $this->input->post('NoUrut');
        $data['Keterangan'] = $this->input->post('Keterangan');
        $data['ERPCode'] = $this->input->post('ERPCode');
        $data['BinCode'] = $this->input->post('BinCode');
        $data['Ratio'] = $this->input->post('Ratio');
        $data['RatioName'] = $this->input->post('RatioName');
        $data['DestRackSlot'] = $this->input->post('DestRackSlot');
        $data['SKUCode'] = $this->input->post('SKUCode');
        $data['Qty'] = $this->input->post('Qty');
        $data['Satuan'] = $this->storing_model->getsatuan($this->input->post('SKUCode'));
        $this->load->view('storing/taruh_bin_view', $data);
    }

    function proses_taruh_bin() {
        //melakukan pengecekan apabila operator sekarang akan mearuh bin
        $data['TransactionCode'] = $this->input->post('TransactionCode');
        $data['QueueNumber'] = $this->input->post('QueueNumber');
        $data['NoUrut'] = $this->input->post('NoUrut');
        $data['Keterangan'] = $this->input->post('Keterangan');
        $data['ERPCode'] = $this->input->post('ERPCode');
        $data['BinCode'] = $this->input->post('BinCode');
        $data['Ratio'] = $this->input->post('Ratio');
        $data['RatioName'] = $this->input->post('RatioName');
        $data['DestRackSlot'] = $this->input->post('DestRackSlot');
        $data['SKUCode'] = $this->input->post('SKUCode');
        $data['Qty'] = $this->input->post('Qty');

        if ($this->input->post('btnProses')) {
            $this->form_validation->set_rules('kodebin', 'Kode Bin', 'required|callback_cek_kodebin');
            $this->form_validation->set_rules('koderack', 'Kode Rack', 'required|callback_cek_koderack2');
            $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|numeric|callback_cek_QtyDest');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $data['Satuan'] = $this->storing_model->getsatuan($this->input->post('SKUCode'));
                $this->load->view('storing/taruh_bin_view', $data);
            } else {
                $kodebin = $this->input->post('kodebin');
                $koderack = $this->input->post('koderack');
                $jumlah = $this->input->post('jumlah');
                $satuan = $this->input->post('satuan');
                $isonaisle = $this->input->post('isonaisle');
                //----------------------------cek BinDest
                if($this->input->post('kodebindest')){
                    $kodebindest = $this->input->post('kodebindest');
                }
                else{
                    $kodebindest='';
                }


                if ($kodebindest == '' || $kodebindest == $kodebin) {

                    if ($this->cek_koderack($koderack, $kodebin) == false && $isonaisle == 0) {
                        $data['error'] = 'Bin Tujuan Harus Diisi Dengan Benar';
                        $data['Satuan'] = $this->storing_model->getsatuan($this->input->post('SKUCode'));
                        $this->load->view('storing/taruh_bin_view', $data);
                        return;
                    } else {
                        if ($this->storing_model->getCountSKUBarangInOneBin($kodebin, $data['TransactionCode'], $data['NoUrut'], $data['QueueNumber']) == false && $isonaisle == 0) {//cek apakah bin sekarang ada lebih dari 1 SKU
                            $data['error'] = 'Bin Tujuan Harus Diisi Dengan Benar';
                            $data['Satuan'] = $this->storing_model->getsatuan($this->input->post('SKUCode'));
                            $this->load->view('storing/taruh_bin_view', $data);
                            return;
                        } else {
                            $kodebindest = $kodebin;
                        }
                    }
                }
                else
                {
                    //----------apakah SKU dan ED BInDest sama?
                    if($this->cek_BinSKU_ED($kodebindest, $data['SKUCode'], $kodebin, $data['TransactionCode'], $data['NoUrut'])==false)
                    {
                        $data['error'] = 'Bin Tujuan Tidak Sesuai';
                        $data['Satuan'] = $this->storing_model->getsatuan($this->input->post('SKUCode'));
                        $this->load->view('storing/taruh_bin_view', $data);
                        return false;
                    }
                }
                //--------------------end cek BinDest
                
                //bin boleh masuk apabila isonaisle yg dipilih ya atau bila tidak maka (apabila rak kosong atau multiple) atau (rak tidak kosong dan tidak multiple tetapi SKU dan ED bin tujuan sama dan WHCode sama)
                if ($isonaisle == 1 || ($this->cek_koderack($koderack, $kodebin) == true && $isonaisle == 0) || ($this->cek_koderack($koderack, $kodebin) == false && $isonaisle == 0 && $this->cek_BinSKU_ED($kodebindest, $data['SKUCode'], $kodebin, $data['TransactionCode'], $data['NoUrut']) == true)) {
                    if ($this->storing_model->setTaruhBinSKU($kodebin, $kodebindest, $koderack, $jumlah * $satuan, $isonaisle, $this->session->userdata('OperatorCode'), $data['TransactionCode'], $data['NoUrut'], $data['QueueNumber'])) {
                        $this->list_my_outstanding();
                    } else {
                        $data['error'] = 'Input Gagal';
                        $data['Satuan'] = $this->storing_model->getsatuan($this->input->post('SKUCode'));
                        $this->load->view('storing/taruh_bin_view', $data);
                    }
                } else {
                    $data['error'] = 'Area Rack Tidak Bisa Dipakai';
                    $data['Satuan'] = $this->storing_model->getsatuan($this->input->post('SKUCode'));
                    $this->load->view('storing/taruh_bin_view', $data);
                }
            }
        }
    }

    function cek_QtyDest($jumlah) {
        $NoUrut = $this->input->post('NoUrut');
        $QueueNumber = $this->input->post('QueueNumber');
        $TransactionCode = $this->input->post('TransactionCode');
        $satuan = $this->input->post('satuan');
        if ($this->storing_model->cekQtyDest($TransactionCode, $NoUrut,$QueueNumber, $satuan * $jumlah)) {
            return true;
        }
        $this->form_validation->set_message('cek_QtyDest', 'Jumlah Salah!');
        return false;
    }

    function cek_BinSKU_ED($kodebindest, $SKUcode, $kodebinsrc, $TransactionCode, $NoUrut) {
        //melakukan pengecekan apakah SKU dan ED di Dest bin sama dengan SKU dan ED di bin sekarang dibawa
        if ($this->storing_model->cekSKUBinDest($kodebindest, $SKUcode, $kodebinsrc, $TransactionCode, $NoUrut)) {
            //$this->form_validtion->set_message('cek_koderack', 'Rack Tidak Siap!');
            return true;
        }
        return false;
    }

    function cek_select($str) {
        //melakukan pengecekan apakah tombol select sudah dilakukan pemilihan
        if ($str == 0) {
            $this->form_validation->set_message('cek_select', '%s harus dipilih.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public

    function cek_koderack($koderack, $kodebin) {
        //melakukan pengecekan apakah rak dipakai, apabila dipakai apakah multiple bin,bila tidak false
        if ($this->storing_model->cekvalidasirack($koderack, $kodebin) == false) {
            //$this->form_validation->set_message('cek_koderack', 'Rack Tidak Siap!');
            return false;
        }
        return true;
    }

    public

    function cek_koderack2($koderack) {
        //cek apakah barcode rak terdaftar
        if ($this->storing_model->cekvalidasirack2($koderack) == false) {
            $this->form_validation->set_message('cek_koderack2', 'Kode Rack Salah!');
            return false;
        }
        return true;
    }

    function cek_kodebin($kodebin) {
        //cek apakah bin yang di scan benar-benar ada di transaksi yang sekarang operator jalankan
        $BinCode = $this->input->post('BinCode');
        if (trim($kodebin) != $BinCode) {
            $this->form_validation->set_message('cek_kodebin', 'Kode Bin Salah!');
            return false;
        }
        return true;
    }

    function cek_kodebinexist($kodebin) {
        //cek apakah kodebin benar-benar ada
        if ($this->storing_model->cekvalidasiexistkodebin($kodebin) == false) {
            $this->form_validation->set_message('cek_kodebinexist', 'Kode Bin Tujuan Salah!');
            return false;
        }
        return true;
    }
    function get_ajax_rack() {
        //ajax untuk menampilkan informasi SKU barang yang harus dipilih
        $RackSlotCode = $_POST['RackSlotCode'];
        $result = $this->storing_model->getRackName($RackSlotCode);
        $result2=  $this->storing_model->cekRackSlotNull($RackSlotCode);
        
        $arr = array("RackName" => $result['RackName'],"DestBin"=>$result2);
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }
    
    function get_ajax_suggestion() {
        //ajax untuk menampilkan informasi Rack yang harus dipilih untuk taruh barang
        $SKUCode = $_POST['SKUCode'];
        $TransactionCode = $_POST['TransactionCode'];
        $NoUrut=$_POST['NoUrut'];
        $result = $this->storing_model-> getSuggestion($TransactionCode,$NoUrut,$SKUCode);
        $arr=array();
        foreach ($result as $row)
        {
            $arr[] = array("RackName" => $row['RackName'],"RackLevel" => $row['RackLevel'],"Priority" => $row['Priority']);
        }
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }
}

?>
