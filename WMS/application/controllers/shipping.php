<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of shipping
 *
 * @author USER
 */
class shipping extends CI_Controller {

    //put your code here
    function __construct() {
        parent::__construct();
        if (!$this->session->userdata('OperatorCode')) {
            redirect(base_url() . "index.php/login");
        }
        $this->load->model('shipping_model');
        //$this->load->library('My_Cart');
    }

    function index() {
        $this->tambah_shipping_task();
    }

    function tambah_shipping_task() {//pilih task shipping
        if (!$this->session->userdata("outstanding") && $this->session->userdata("OperatorRole") == '10/WHR/004') {
            //melakukan pemilihan master transaction retur yang akan operator proses

            if ($this->input->post('btnProses')) {
                $this->form_validation->set_rules('transactioncode', 'PickList', 'required|callback_cek_ambil_task');
                $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
                if ($this->form_validation->run() == FALSE) {
                    $data['shipping'] = $this->shipping_model->getShipping($this->session->userdata('OperatorCode'));
                    $this->load->view('shipping/tambah_shipping_view', $data);
                } else {
                    $transactioncode = $this->input->post('transactioncode');
                    $ERPCode = $this->input->post('ERPCode');
                    //set user_1st di DetailTaskPckS supaya menjadi outstanding pemilih task
                    $this->shipping_model->setDetailTransactionOpr($transactioncode, $this->session->userdata('OperatorCode'), $this->session->userdata('OperatorRole'));
                    //$this->session->set_userdata("outstanding", TRUE);
                    $this->session->set_userdata("ERPCode", $ERPCode);
                    $this->session->set_userdata("TransactionCode", $transactioncode);
                    $this->load->view('shipping/shipping_main_view');
                    //$data['bin'] = $this->shipping_model->getListBinBelumShipping($this->session->userdata('OperatorCode'));
                    //$this->load->view('shipping/scan_bin_view', $data);
                }
            } else {
                $data['shipping'] = $this->shipping_model->getShipping($this->session->userdata('OperatorCode'));
                $this->load->view('shipping/tambah_shipping_view', $data);
            }
        } else {
            redirect(base_url() . "index.php/login");
        }
    }

    function gotomainmenu() {
        $this->load->view('shipping/shipping_main_view');
    }

    function scan_bin_kendaraan() {//scan bin yang akan dimasukkan ke van
        /* if (!$this->session->userdata('TransactionCode') || !$this->session->userdata('ERPCode')) {//menetapkan TransactionCode yg pernah diambil
          $task = $this->shipping_model->getTransactionERPCode($this->session->userdata('OperatorCode'));
          foreach ($task as $row) {
          $transactioncode = $row['TransactionCode'];
          $ERPCode = $row['ERPCode'];
          }
          $this->session->set_userdata("ERPCode", $ERPCode);
          $this->session->set_userdata("TransactionCode", $transactioncode);
          } */
        //if ($this->session->userdata("outstanding")) {
        if ($this->input->post('btnProses')) {
            $this->form_validation->set_rules('kodebin', 'KodeBin', 'required|callback_cek_bin_outstanding|callback_cek_bin_to_dest');
            $this->form_validation->set_rules('kodebindest', 'KodeBinDest', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $data['bin'] = $this->shipping_model->getListBinBelumShipping($this->session->userdata('TransactionCode'));
                $this->load->view('shipping/scan_bin_view', $data);
            } else {
                $kodebin = $this->input->post('kodebin');
                $kodebindest = $this->input->post('kodebindest');
                $this->session->set_userdata('kodebin', $kodebin);
                $this->session->set_userdata('kodebindest', $kodebindest);
                $this->list_sku_bin();
            }
        } else {
            $data['bin'] = $this->shipping_model->getListBinBelumShipping($this->session->userdata('TransactionCode'));
            $this->load->view('shipping/scan_bin_view', $data);
        }
        //} else {
        //   redirect(base_url() . "index.php/login");
        //}
    }

    function masuk_barang() {
        if ($this->input->post('SKUCode')) {
            $data['SKUCode'] = $this->input->post('SKUCode');
            $data['Keterangan'] = $this->input->post('Keterangan');
            $data['TransactionCode'] = $this->input->post('TransactionCode');
            $data['BinCode'] = $this->input->post('BinCode');
            $data['QtyNeedNow'] = $this->input->post('QtyNeedNow');
            $data['listsku'] = $this->shipping_model->getListDetailSKUPicking($data['TransactionCode'], $data['BinCode'], $data['SKUCode']);

            $this->load->view('shipping/kirim_qty_view', $data);
            //$data['Qty'] = $this->input->post('Qty');
            //$data['NoUrut'] = $this->input->post('NoUrut');
            //$data['satuan'] = $this->shipping_model->getsatuan($data['SKUCode']);
        }
    }

    /* function proses_masuk_barang() {
      if ($this->input->post('btnProses')) {
      $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|callback_cek_qty_max');
      $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
      if ($this->form_validation->run() == FALSE) {
      $data['SKUCode'] = $this->input->post('SKUCode');
      $data['Keterangan'] = $this->input->post('Keterangan');
      $data['TransactionCode'] = $this->input->post('TransactionCode');
      $data['Qty'] = $this->input->post('Qty');
      $data['NoUrut'] = $this->input->post('NoUrut');
      $data['satuan'] = $this->shipping_model->getsatuan($data['SKUCode']);
      $this->load->view('shipping/masuk_qty_view', $data);
      } else {

      $TransactionCode = $this->input->post('TransactionCode');
      $satuan = $this->input->post('satuan');
      $BinDest = $this->session->userdata('kodebindest');
      $jumlah = $this->input->post('jumlah');
      $NoUrut = $this->input->post('NoUrut');
      if ($this->shipping_model->setQtySKU($TransactionCode, $NoUrut, $BinDest, $jumlah * $satuan)) {
      $this->list_sku_bin();
      } else {
      $data['SKUCode'] = $this->input->post('SKUCode');
      $data['Keterangan'] = $this->input->post('Keterangan');
      $data['TransactionCode'] = $this->input->post('TransactionCode');
      $data['Qty'] = $this->input->post('Qty');
      $data['NoUrut'] = $this->input->post('NoUrut');
      $data['satuan'] = $this->shipping_model->getsatuan($data['SKUCode']);
      $data['error'] = "Input Gagal";
      $this->load->view('shipping/masuk_qty_view', $data);
      }
      }
      } else {
      $this->list_sku_bin();
      }
      } */

    function proses_masuk_barang() {
        if ($this->input->post('btnProses')) {
            $this->form_validation->set_rules('Qty[]', 'Jumlah', 'required|numeric');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $data['SKUCode'] = $this->input->post('SKUCode');
                $data['Keterangan'] = $this->input->post('Keterangan');
                $data['TransactionCode'] = $this->input->post('TransactionCode');
                $data['BinCode'] = $this->input->post('BinCode');
                $data['listsku'] = $this->shipping_model->getListDetailSKUPicking($data['TransactionCode'], $data['BinCode'], $data['SKUCode']);

                $this->load->view('shipping/kirim_qty_view', $data);
            } else {
                $Qty = $this->input->post('Qty');
                $QtyNeedNow = $this->input->post('QtyNeedNow');
                if ($this->cek_qty_max($Qty, $QtyNeedNow)) {
                    $NoUrut = $this->input->post('NoUrut');
                    $TransactionCode = $this->input->post('TransactionCode');
                    $BinDest = $this->session->userdata('kodebindest');
                    $jumlahrecord = count($Qty);
                    $status = true;
                    for ($i = 0; $i < $jumlahrecord && $status == true; $i++) {
                        $status = $this->shipping_model->setQtySKU($TransactionCode, $NoUrut[$i], $BinDest, $Qty[$i], $this->session->userdata('OperatorCode'));
                    }
                    if ($status) {
                        $this->list_sku_bin();
                    } else {
                        $data['SKUCode'] = $this->input->post('SKUCode');
                        $data['Keterangan'] = $this->input->post('Keterangan');
                        $data['TransactionCode'] = $this->input->post('TransactionCode');
                        $data['BinCode'] = $this->input->post('BinCode');
                        $data['listsku'] = $this->shipping_model->getListDetailSKUPicking($data['TransactionCode'], $data['BinCode'], $data['SKUCode']);
                        $data['error'] = 'Input Gagal';
                        $this->load->view('shipping/kirim_qty_view', $data);
                    }
                } else {
                    $data['SKUCode'] = $this->input->post('SKUCode');
                    $data['Keterangan'] = $this->input->post('Keterangan');
                    $data['TransactionCode'] = $this->input->post('TransactionCode');
                    $data['BinCode'] = $this->input->post('BinCode');
                    $data['listsku'] = $this->shipping_model->getListDetailSKUPicking($data['TransactionCode'], $data['BinCode'], $data['SKUCode']);
                    $data['error'] = 'Jumlah Ada yang Salah!';
                    $this->load->view('shipping/kirim_qty_view', $data);
                }
            }
        }
    }

    function list_bermasalah() {
        if ($this->input->post('btnProses')) {
            $this->form_validation->set_rules('SKUCode[]', 'Barang', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $data['listsku'] = $this->shipping_model->getListDetailSKUPicking2($this->session->userdata('TransactionCode'));
                $this->load->view('shipping/list_bermasalah_view', $data);
            } else {
                $temp = $this->input->post('SKUCode');

                $status = true;
                //echo count($temp);
                for ($i = 0; $i < count($temp) && $status == true; $i++) {
                    $temp2 = explode("~", $temp[$i]);
                    $SKUCode = $temp2[1];
                    $BinCode = $temp2[0];
                    if (!$this->shipping_model->setShippingBermasalah($this->session->userdata('TransactionCode'), $SKUCode, $BinCode, $this->session->userdata('OperatorCode'),$this->session->userdata('ShippingProblem'))) {
                        $status = false;
                    }
                }
                if ($status == false) {
                    $data['error'] = 'Input Gagal';
                    $data['listsku'] = $this->shipping_model->getListDetailSKUPicking2($this->session->userdata('TransactionCode'));
                    $this->load->view('shipping/list_bermasalah_view', $data);
                } else {
                    $this->load->view('shipping/shipping_main_view');
                }
            }
        } else {
            $data['listsku'] = $this->shipping_model->getListDetailSKUPicking2($this->session->userdata('TransactionCode'));
            $this->load->view('shipping/list_bermasalah_view', $data);
        }
    }

    function cek_qty_max($jumlah, $QtyNeedNow) {
        $status = TRUE;
        $i = 0;
        $total = count($jumlah);
        //$QtyNeedNow = $this->input->post('QtyNeedNow');

        while ($i < $total && $status == true) {

            if ($QtyNeedNow[$i] < $jumlah[$i]) {
                $status = false;
            }
            $i++;
        }
        return $status;
    }

    /* function cek_qty_max($jumlah) {
      $Qty = $this->input->post('Qty');
      $satuan = $this->input->post('satuan');
      if ($satuan * $jumlah > $Qty) {
      $this->form_validation->set_message('cek_qty_max', 'Jumlah Berlebih');
      return FALSE;
      }
      return TRUE;
      } */

    function list_sku_bin() {
        if ($this->session->userdata('kodebin') && $this->session->userdata('kodebindest')) {
            $data['skubin'] = $this->shipping_model->getListSKUBin($this->session->userdata('kodebin'), $this->session->userdata('TransactionCode'));
            $this->load->view('shipping/sku_shipping_view', $data);
        } else {
            $this->scan_bin_kendaraan();
        }
    }

    function list_kembali() {
        $data['listsku'] = $this->shipping_model->getListDetailSKUPicking3($this->session->userdata('TransactionCode'));
        $this->load->view('shipping/list_kembali_view', $data);
    }

    function kembali_barang() {
        if ($this->input->post('btnProses')) {
            $this->form_validation->set_rules('NoUrut', 'Barang', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $this->list_kembali();
            } else {

                $temp = $this->input->post('NoUrut');

                $temp = explode("~", $temp);
                $data['NoUrut'] = $temp[0];
                $data['ExpDate'] = $temp[1];
                $data['BinCode'] = $temp[2];
                $data['SKUCode'] = $temp[3];
                $data['QtyNeedNow'] = $temp[4];
                $data['Keterangan'] = $temp[5];
                $data['WHCode'] = $temp[6];
                $data['Status2'] = $temp[7];
                $data['Rasio'] = $this->shipping_model->getsatuan($data['SKUCode']);
                $this->load->view('shipping/proses_kembali_view', $data);
            }
        }
    }

    function proses_kembali_barang() {
        if ($this->input->post('btnProses')) {
            $this->form_validation->set_rules('DestBin', 'Bin Tujuan', 'required|callback_cek_bin_validation|callback_cek_bin_rack_kembali');
            $this->form_validation->set_rules('DestRackSlot', 'Rack Tujuan', 'required|callback_cek_rack_validation');
            $this->form_validation->set_rules('Qty', 'Jumlah', 'required|numeric|callback_cek_qty_max_kembali');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $data['NoUrut'] = $this->input->post('NoUrut');
                $data['ExpDate'] = $this->input->post('ExpDate');
                $data['BinCode'] = $this->input->post('BinCode');
                $data['SKUCode'] = $this->input->post('SKUCode');
                $data['QtyNeedNow'] = $this->input->post('QtyNeedNow');
                $data['Keterangan'] = $this->input->post('Keterangan');
                $data['WHCode'] = $this->input->post('WHCode');
                $data['Status2'] = $this->input->post('Status2');
                $data['Rasio'] = $this->shipping_model->getsatuan($data['SKUCode']);
                $this->load->view('shipping/proses_kembali_view', $data);
            } else {
                $NoUrut = $this->input->post('NoUrut');
                $DestBin = $this->input->post('DestBin');
                $Qty = $this->input->post('Qty');
                $Rasio = $this->input->post('Rasio');
                if ($this->shipping_model->setShippingKembali($this->session->userdata('TransactionCode'), $NoUrut, $DestBin, (int)$Qty * (int)$Rasio, $this->session->userdata('OperatorCode'))) {
                    $this->list_kembali();
                } else {
                    $data['NoUrut'] = $this->input->post('NoUrut');
                    $data['ExpDate'] = $this->input->post('ExpDate');
                    $data['BinCode'] = $this->input->post('BinCode');
                    $data['SKUCode'] = $this->input->post('SKUCode');
                    $data['QtyNeedNow'] = $this->input->post('QtyNeedNow');
                    $data['Keterangan'] = $this->input->post('Keterangan');
                    $data['WHCode'] = $this->input->post('WHCode');
                    $data['Status2'] = $this->input->post('Status2');
                    $data['Rasio'] = $this->shipping_model->getsatuan($data['SKUCode']);
                    $data['error']='Input Gagal';
                    $this->load->view('shipping/proses_kembali_view', $data);
                }
            }
        }
    }

    function get_ajax_suggestionkembali() {
        //ajax untuk menampilkan suggestion kembali barang
        $BinCode = $_POST['BinCode'];
        $SKUCode = $_POST['SKUCode'];
        $TransactionCode = $_POST['TransactionCode'];
        $ExpDate = $_POST['ExpDate'];


        $result = $this->shipping_model->getSuggestionKembali($TransactionCode, $SKUCode, $BinCode, $ExpDate);
        $arr = array();
        foreach ($result as $row) {
            $arr[] = array("SrcBin" => $row['SrcBin'], "Keterangan" => $row['Keterangan'], "SrcRackSlot" => $row['SrcRackSlot']);
        }
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }

    function cek_qty_max_kembali($jumlah) {
        $ratio = $this->input->post('Rasio');
        $jumlah = (int)$jumlah * (int)$ratio;
        $QtyNeedNow = $this->input->post('QtyNeedNow');
        
        if ((int)$jumlah > (int)$QtyNeedNow) {
            $this->form_validation->set_message('cek_qty_max_kembali', 'Jumlah Berlebihan');
            return FALSE;
        }
        return TRUE;
    }

    function cek_bin_outstanding($kodebin) {
        if ($this->shipping_model->cekBinOutstanding($kodebin, $this->session->userdata('TransactionCode'))) {
            return TRUE;
        }
        $this->form_validation->set_message('cek_bin_outstanding', 'Bin Salah atau Selesai Dipindah');
        return FALSE;
    }

    function cek_bin_rack_kembali($kodebin) {
        $RackSlotCode = $this->input->post('DestRackSlot');
        $SKUCode = $this->input->post('SKUCode');
        $WHCode = $this->input->post('WHCode');
        $ExpDate = $this->input->post('ExpDate');
        $status=$this->shipping_model->cekBinRackSlot($kodebin, $RackSlotCode, $SKUCode, $WHCode, $ExpDate);
        if ($status['status']==TRUE) {
            return TRUE;
        }
        $this->form_validation->set_message('cek_bin_rack_kembali', $status['msg']);
        return FALSE;
    }

    function cek_bin_to_dest($kodebin) {
        if ($this->shipping_model->cekBinSrctoBinDest($kodebin, $this->input->post('kodebindest'), $this->session->userdata("TransactionCode"))) {
            return TRUE;
        }
        $this->form_validation->set_message('cek_bin_to_dest', 'Kode Bin Kendaraan Salah');
        return FALSE;
    }

    function cek_bin_validation($kodebin) {
        if ($this->shipping_model->cekBinvalidation($kodebin)) {
            return TRUE;
        }
        $this->form_validation->set_message('cek_bin_validation', 'Kode Bin Salah');
        return FALSE;
    }

    function cek_rack_validation($koderack) {
        if ($this->shipping_model->cekRackvalidation($koderack)) {
            return TRUE;
        }
        $this->form_validation->set_message('cek_rack_validation', 'Kode Rack Salah');
        return FALSE;
    }

    function cek_ambil_task($transactioncode) {
        if ($this->shipping_model->cekDetailTransactionOpr($transactioncode, $this->session->userdata('OperatorCode'), $this->session->userdata('OperatorRole'))) {
            return TRUE;
        }
        $this->form_validation->set_message('cek_ambil_task', 'Task Sudah Diambil');
        return FALSE;
    }
    function tambahbrgkembali()
    {
        if($this->input->post('btnTambah'))
        {
            $this->form_validation->set_rules('kodeSKU', 'Kode SKU', 'required');
            $this->form_validation->set_rules('namaSKU', 'Nama SKU', 'required');
            $this->form_validation->set_rules('jumlahSKU', 'Jumlah SKU', 'required|integer');
            $this->form_validation->set_rules('edSKU', 'ED SKU', 'required');
            $this->form_validation->set_rules('BinCode', 'Kode BIn', 'required|callback_cek_bin_validation');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $kodeBarang = $this->input->post('kodeSKU');
                $data['BinCode'] = $this->input->post('BinCode');
                $data['barang'] = $this->shipping_model->getDetailBarang($kodeBarang);
                $this->load->view('shipping/tambah_brg_kembali_view', $data);
            }
            else
            {
                $jumlah=  $this->input->post('jumlahSKU');
                $ExpDate=  $this->input->post('edSKU');
                $SKUCode = $this->input->post('kodeSKU');
                $BinCode = $this->input->post('BinCode');
                $ratio=$this->input->post('ratio');
                $TransactionCode=  $this->session->userdata('TransactionCode');
                if($this->shipping_model->setTambahBarang($TransactionCode,$BinCode,$SKUCode,$ExpDate,$jumlah*$ratio))
                {
                   $this->session->set_flashdata('pesan', 'Barang Berhasil Ditambahkan'); 
                }
                else
                {
                    $this->session->set_flashdata('error', 'Barang Gagal DItambahkan');
                }
                redirect(base_url() . "index.php/shipping/list_kembali");
            }
        }
        else
        {
            $kodeBarang = $this->session->flashdata('kodeBarang');
            $BinCode = $this->session->flashdata('BinCode');
            $ExpDate = $this->session->flashdata('ExpDate');
            $data['BinCode']=$BinCode;
            $data['ExpDate']=$ExpDate;
            $data['barang'] = $this->shipping_model->getDetailBarang($kodeBarang);
            $this->load->view('shipping/tambah_brg_kembali_view', $data);
        }
    }
    function cari_barang($BinCode='') {
        if ($this->input->post('btnCari')) {
            $cari = $this->input->post('cari');
            
            $data['barang'] = $this->shipping_model->cari_barang($this->session->userdata('TransactionCode'),$cari);
            $data['BinCode']=$cari;
            $this->load->view('shipping/cari_barang_view', $data);
        } elseif ($this->input->post('btnPilih')) {
            $this->form_validation->set_rules('barang', 'Kode Barang', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $this->load->view('shipping/cari_barang_view');
            } else {
                $kodeBarang = $this->input->post('barang');
                $temp=  explode("~", $kodeBarang);
                $BinCode = $this->input->post('BinCode');
                $this->session->set_flashdata('BinCode', $BinCode);
                $this->session->set_flashdata('kodeBarang', $temp[0]);
                $this->session->set_flashdata('ExpDate', $temp[1]);
                redirect(base_url() . "index.php/shipping/tambahbrgkembali");
            }
        } else {
            $data['BinCode']=$BinCode;
            $this->load->view('shipping/cari_barang_view',$data);
        }
    }

    
    function get_ajax_konversiqty() {
        //ajax untuk menampilkan suggestion kembali barang
        $Total = $_POST['Total'];
        $SKUCode = $_POST['SKUCode'];

        $result = $this->shipping_model->konversiQty($SKUCode, $Total);
        $arr = array("Qtykonversi" => $result);
        
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }
    function get_ajax_barang() {
        //ajax untuk menampilkan informasi SKU barang yang harus dipilih
        $barcodeSKU = $_POST['barcodeSKU'];
        $result = $this->shipping_model->cari_barang_ajax($barcodeSKU);
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
}

?>
