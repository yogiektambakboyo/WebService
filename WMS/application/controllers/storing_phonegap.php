<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Storing_phonegap extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('storing_model');
    }

    function getlistbpbstoring() {
        $bpb = $this->storing_model->getBPBList();
        echo $_GET['jsoncallback'] . '(' . json_encode($bpb) . ');';
    }

    function setDetailTaskRcvbpbstroring($bpb, $OperatorCode, $OperatorRole) {
        $bpb = str_replace('.', '/', $bpb);
        $OperatorCode = str_replace('.', '/', $OperatorCode);
        $OperatorRole = str_replace('.', '/', $OperatorRole);
        $bpb = explode(":", $bpb);
        $this->storing_model->setDetailTransactionOpr($bpb, $OperatorCode, $OperatorRole);
        echo $_GET['jsoncallback'] . '(' . json_encode(true) . ');';
    }

    function getalloutstandingstoring($OperatorCode) {
        $OperatorCode = str_replace('.', '/', $OperatorCode);
        $data = $this->storing_model->getListDetailTransactionHistory($OperatorCode);
        echo $_GET['jsoncallback'] . '(' . json_encode($data) . ');';
    }

    function getinfo_onkodebinchange($kodebin, $OperatorCode) {
        $kodebin = str_replace('.', '/', $kodebin);
        $OperatorCode = str_replace('.', '/', $OperatorCode);
        $result1 = $this->storing_model->getSKUBarang($kodebin, $OperatorCode);
        $jum = 0;
        foreach ($result1 as $row1) {
            if ($jum == 0) {
                $SKUCode = $row1['SKUCode'];
                $jum++;
            }
        }

        $result = $this->storing_model->getInfoSKUBin($kodebin, $SKUCode, $OperatorCode);
        $arr = array();
        foreach ($result as $row) {
            $arr = array("ERPCode" => $row['ERPCode'], "Qty" => $row['Quantity'], "DestRackSlot" => $row['DestRackSlot'],
                "RatioName" => $row['RatioName'], "User_1st" => $row['Name'], 'SKUlist' => $result1);
        }
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }

    function getinfo_onSKUchange($kodebin, $SKUCode, $OperatorCode) {
        $kodebin = str_replace('.', '/', $kodebin);
        $OperatorCode = str_replace('.', '/', $OperatorCode);
        $SKUCode = str_replace('.', '/', $SKUCode);
        $result = $this->storing_model->getInfoSKUBin($kodebin, $SKUCode, $OperatorCode);
        $arr = array();
        foreach ($result as $row) {
            $arr = array("ERPCode" => $row['ERPCode'], "Qty" => $row['Quantity'], "DestRackSlot" => $row['DestRackSlot'],
                "RatioName" => $row['RatioName'], "User_1st" => $row['Name']);
        }
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }

    function getmyoutstandingstoring($OperatorCode) {
        //menampilkan daftar outstanding operator berdasarkan bin yang dia bawa bila tombol kembali ditekan 
        $OperatorCode = str_replace('.', '/', $OperatorCode);
        $data = $this->storing_model->getMyOutstanding($OperatorCode);
        echo $_GET['jsoncallback'] . '(' . json_encode($data) . ');';
    }

    function process_getbinstoring($kodebin, $SKUCode, $OperatorCode) {
        $kodebin = str_replace('.', '/', $kodebin);
        $OperatorCode = str_replace('.', '/', $OperatorCode);
        $SKUCode = str_replace('.', '/', $SKUCode);

        $cekkodebin = $this->cek_kodebin($kodebin, $OperatorCode);
        if ($cekkodebin['error'] == FALSE) {
            $arr = array('error' => FALSE, 'msgerror' => $cekkodebin['msgerror']);
            echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
        } else {
            if ($this->storing_model->getUser1stBinCode($kodebin, $SKUCode, $OperatorCode) == null) {//cek apakah user_1st null
                if ($this->storing_model->getUser2ndBinCode($kodebin, $SKUCode, $OperatorCode) == null) {//cek apakah user_2nd null
                    $this->storing_model->setUser1stBinCode($kodebin, $SKUCode, $OperatorCode);
                    $arr = array('error' => TRUE, 'msgerror' => '', 'paksa' => FALSE);
                    echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
                } else {
                    $arr = array('error' => FALSE, 'msgerror' => 'Pengambil Tidak Diketahui');
                    echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
                }
            } else {

                if ($this->storing_model->getUser2ndBinCode($kodebin, $SKUCode, $OperatorCode) == null) {//cek apakah user_2nd null
                    //taruh bin paksa
                    $result = $this->storing_model->getInfoSKUBin($kodebin, $SKUCode, $OperatorCode);
                    $arr = array('error' => TRUE, 'msgerror' => '', 'paksa' => TRUE,
                        'Keterangan' => $result[0]['Keterangan'], "ERPCode" => $result[0]['ERPCode'], "Qty" => $result[0]['Quantity'], "DestRackSlot" => $result[0]['DestRackSlot'],
                        "RatioName" => $result[0]['RatioName'], "User_1st" => $result[0]['Name']);
                    echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
                } else {
                    $arr = array('error' => FALSE, 'msgerror' => "Bin Seharusnya Telah Selesai Ditaruh");
                    echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
                }
            }
        }
    }

    function cek_kodebin($kodebin, $OperatorCode) {
        //cek apakah bin yang di scan benar-benar ada di transaksi yang sekarang operator jalankan
        if ($this->storing_model->cekvalidasikodebin($kodebin, $OperatorCode)) {
            $result = array('error' => TRUE, 'msgerror' => '');
            return $result;
        } else {
            $result = array('error' => FALSE, 'msgerror' => 'Kode Bin Salah');
            return $result;
        }
    }

    function process_putbinstoring($kodebin, $SKUcode, $OperatorCode, $koderack, $jumlah, $isonaisle, $kodebindest) {
        $kodebin = str_replace('.', '/', $kodebin);
        $OperatorCode = str_replace('.', '/', $OperatorCode);
        $SKUcode = str_replace('.', '/', $SKUcode);
        $koderack = str_replace('.', '/', $koderack);
        $kodebindest = str_replace('.', '/', $kodebindest);

        $cekkodebin = $this->cek_kodebin($kodebin, $OperatorCode);
        $cekkoderack2 = $this->cek_koderack2($koderack);
        $cekqtydest = $this->cek_QtyDest($jumlah, $SKUcode, $kodebin, $OperatorCode);
        $cekqty = $this->cekQty($jumlah);

        if (!$cekkodebin['error'] || !$cekkoderack2['error'] || !$cekqtydest['error'] || !$cekqty['error']) {
            $strerror = $cekkodebin['msgerror'] . ' ' . $cekkoderack2['msgerror'] . ' ' . $cekqtydest['msgerror'] . ' ' . $cekqty['msgerror'];
            $arr = array('error' => FALSE, 'msgerror' => $strerror);
            echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
            return;
        } else {//bila tidak ada error global
            //------------------------menetapkan kodebin dest
            if ($kodebindest == 'isikosong' || $kodebindest == $kodebin) {

                if ($this->cek_koderack($koderack, $kodebin) == false && $isonaisle == 0) {
                    $arr = array('error' => FALSE, 'msgerror' => 'Bin Tujuan Harus Diisi Dengan Benar');
                    echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
                    return;
                } else {
                    if ($this->storing_model->getCountSKUBarangInOneBin($kodebin, $OperatorCode) == false && $isonaisle == 0) {//cek apakah bin sekarang ada lebih dari 1 SKU
                        $arr = array('error' => FALSE, 'msgerror' => 'Bin Tujuan Harus Diisi Dengan Benar');
                        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
                        return;
                    } else {
                        $kodebindest = $kodebin;
                    }
                }
            }
            //------------------------end menetapkan kodebin dest

            if ($this->storing_model->getUser2ndBinCode($kodebin, $SKUcode, $OperatorCode) == null) {//cek apakah user_2nd null
                if ($this->storing_model->getUser1stBinCode($kodebin, $SKUcode, $OperatorCode) == null) {//cek apakah user_1st null
                    $arr = array('error' => FALSE, 'msgerror' => 'Bin Belum Ditetapkan Pengambilnya');
                    echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
                    return;
                } else {

                    //bin boleh masuk apabila isonaisle yg dipilih ya atau bila tidak maka (apabila rak kosong atau multiple) atau (rak tidak kosong dan tidak multiple tetapi SKU dan ED bin tujuan sama dan WHCode sama)
                    if ($isonaisle == 1 || ($this->cek_koderack($koderack, $kodebin) == true && $isonaisle == 0) || ($this->cek_koderack($koderack, $kodebin) == false && $isonaisle == 0 && $this->cek_BinSKU_ED($kodebindest, $SKUcode, $kodebin,$OperatorCode) == true)) {
                        $this->storing_model->setTaruhBinSKU($kodebin, $kodebindest, $koderack, $jumlah, $OperatorCode, $isonaisle, $SKUcode);
                        $arr = array('error' => TRUE, 'msgerror' => '');
                        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
                        return;
                    } else {
                        $arr = array('error' => FALSE, 'msgerror' => 'Area Rack Tidak Bisa Dipakai');
                        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
                        return;
                    }
                }
            } else {

                $arr = array('error' => FALSE, 'msgerror' => 'Bin Seharusnya Telah Selesai Ditaruh');
                echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
                return;
            }
        }//end bila tidak ada error global
    }

    function cekQty($str) {

        if (is_numeric($str)) {
            $result = array('error' => TRUE, 'msgerror' => '');
            return $result;
        } else {
            $result = array('error' => FALSE, 'msgerror' => 'Jumlah Harus Bilangan');
            return $result;
        }
    }

    function cek_QtyDest($jumlah, $SKUcode, $kodebin, $OperatorCode) {
        if ($this->storing_model->cekQtyDest($kodebin, $jumlah, $OperatorCode, $SKUcode)) {
            $result = array('error' => TRUE, 'msgerror' => '');
            return $result;
        } else {
            $result = array('error' => FALSE, 'msgerror' => 'Jumlah Salah!');
            return $result;
        }
    }

    public function cek_koderack2($koderack) {
        //cek apakah barcode rak terdaftar
        if ($this->storing_model->cekvalidasirack2($koderack)) {
            $result = array('error' => TRUE, 'msgerror' => '');
            return $result;
        } else {
            $result = array('error' => FALSE, 'msgerror' => 'Kode Rack Salah!');
            return $result;
        }
    }

    function cek_BinSKU_ED($kodebindest, $SKUcode, $kodebinsrc,$OperatorCode) {
        //melakukan pengecekan apakah SKU dan ED di Dest bin sama dengan SKU dan ED di bin sekarang dibawa
        if ($this->storing_model->cekSKUBinDest($kodebindest, $SKUcode, $kodebinsrc, $OperatorCode)) {
            //$this->form_validation->set_message('cek_koderack', 'Rack Tidak Siap!');
            return true;
        }
        return false;
    }

    function cek_koderack($koderack, $kodebin) {
        //melakukan pengecekan apakah rak dipakai, apabila dipakai apakah multiple bin,bila tidak false
        if ($this->storing_model->cekvalidasirack($koderack, $kodebin) == false) {
            //$this->form_validation->set_message('cek_koderack', 'Rack Tidak Siap!');
            return false;
        }
        return true;
    }

    
    
    
    
    
    
    
    
    
    
    
    
    function get_ajax_info_bin() {
        //ajax untuk menampilkan informasi bin yang di scan
        $kodebin = $_POST['kodebin'];
        //$bpb=$_POST['bpb'];
        $result = $this->storing_model->getInformationBin($kodebin, $this->session->userdata('OperatorCode'));
        $arr = array();
        foreach ($result as $row) {
            $arr = array("ERPCode" => $row['ERPCode'], "User_1st" => $row['Name']);
        }
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }

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
                    //$this->storing_model->setDetailTransactionOpr($bpb, $this->session->userdata('OperatorCode'),$this->session->userdata('OperatorCode'));
                    //$data['outstanding']=$this->storing_model->getListDetailTransactionHistory($this->session->userdata('OperatorCode'));
                    $returterambil = $this->storing_model->cekDetailTransactionReturOpr($retur, $this->session->userdata('OperatorCode'), $this->session->userdata('OperatorRole'));

                    if (count($returterambil) == 0) {
                        $this->storing_model->setDetailTransactionReturOpr($retur, $this->session->userdata('OperatorCode'), $this->session->userdata('OperatorRole'));
                        $data['outstanding'] = $this->storing_model->getListDetailTransactionHistory($this->session->userdata('OperatorCode'));
                    } else {
                        $data['error'] = true;
                        $data['listerror'] = $returterambil;
                        $data['retur'] = $this->storing_model->getReturList();
                        $this->load->view('storing/tambah_list_retur_view', $data);
                        return;
                    }
                    //$data['bpb'] = $this->storing_model->getBPBList();
                    $this->load->view('storing/daftar_all_outstanding_view', $data);
                }
            } else {
                $data['retur'] = $this->storing_model->getReturList();
                $this->load->view('storing/tambah_list_retur_view', $data);
            }
        } else {
            redirect(base_url() . "index.php/login");
        }
    }

}

?>
