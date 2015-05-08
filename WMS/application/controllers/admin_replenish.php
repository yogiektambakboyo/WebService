<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of admin_replenish
 *
 * @author USER
 */
class Admin_replenish extends CI_Controller {

    //put your code here
    function __construct() {
        parent::__construct();
        if (!$this->session->userdata('OperatorCode') || $this->session->userdata("OperatorRole") != '10/WHR/000' && $this->session->userdata("OperatorRole") != '10/WHR/999') {
            redirect(base_url() . "index.php/login");
        }
        $this->load->model('admin_replenish_model');
        $this->load->model('barcode_model');
    }

    function index() {
        $this->load->view('admin_replenish/main_view');
    }

    function tambah_replenish() {
        $data['outstanding'] = $this->admin_replenish_model->getoutstandingbatas();
        $this->load->view('admin_replenish/list_tambah_replenish_view', $data);
    }

    function pilih_barang_ambil() {
        $RackType = $this->input->post('RackType');
        $SKUCode = $this->input->post('SKUCode');
        $RackSlotCode = $this->input->post('RackSlotCode');

        $data['src'] = $this->admin_replenish_model->getSrcRack($RackType, $RackSlotCode, $SKUCode);
        $data['destrackslot'] = $RackSlotCode;
        $data['NamaRackDest'] = $this->input->post('NamaRackDest');
        $this->load->view('admin_replenish/src_ambil_rack_view', $data);
    }

    function input_qty() {

        if ($this->input->post('btnProses')) {
            $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|numeric|callback_cek_qty|trim');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $data['DestRackSlot'] = $this->input->post('DestRackSlot');
                $data['RackType'] = $this->input->post('RackType');
                $data['SKUCode'] = $this->input->post('SKUCode');
                $data['RackSlotCode'] = $this->input->post('RackSlotCode');
                $data['BinCode'] = $this->input->post('BinCode');
                $data['WHCode'] = $this->input->post('WHCode');
                $data['ExpDate'] = $this->input->post('ExpDate');
                $data['jumlah'] = $this->input->post('jumlah');
                $data['NamaRackDest'] = $this->input->post('NamaRackDest');
                $data['NamaRackSrc'] = $this->input->post('NamaRackSrc');
                $data['jumlahawal'] = $this->input->post('jumlahawal');
                $data['error'] = "Jumlah Salah Input!";
                $this->load->view('admin_replenish/input_qty_view', $data);
            } else {
                $DestRackSlot = $this->input->post('DestRackSlot');
                $RackType = $this->input->post('RackType');
                $SKUCode = $this->input->post('SKUCode');
                $CurrRackSlot = $this->input->post('RackSlotCode');
                $BinCode = $this->input->post('BinCode');
                $WHCode = $this->input->post('WHCode');
                $ExpDate = $this->input->post('ExpDate');
                $jumlah = $this->input->post('jumlah');
                if ($this->admin_replenish_model->setDetailTaskRpl($BinCode, $SKUCode, $ExpDate, $CurrRackSlot, $DestRackSlot, $jumlah)) {
                    $this->tambah_replenish();
                } else {
                    $data['DestRackSlot'] = $this->input->post('DestRackSlot');
                    $data['RackType'] = $this->input->post('RackType');
                    $data['SKUCode'] = $this->input->post('SKUCode');
                    $data['RackSlotCode'] = $this->input->post('RackSlotCode');
                    $data['BinCode'] = $this->input->post('BinCode');
                    $data['WHCode'] = $this->input->post('WHCode');
                    $data['ExpDate'] = $this->input->post('ExpDate');
                    $data['jumlah'] = $this->input->post('jumlah');
                    $data['NamaRackDest'] = $this->input->post('NamaRackDest');
                    $data['NamaRackSrc'] = $this->input->post('NamaRackSrc');
                    $data['jumlahawal'] = $this->input->post('jumlahawal');
                    $data['error'] = "Gagal Input";
                    $this->load->view('admin_replenish/input_qty_view', $data);
                }
            }
        } else {
            $data['DestRackSlot'] = $this->input->post('DestRackSlot');
            $data['RackType'] = $this->input->post('RackType');
            $data['SKUCode'] = $this->input->post('SKUCode');
            $data['RackSlotCode'] = $this->input->post('RackSlotCode');
            $data['BinCode'] = $this->input->post('BinCode');
            $data['WHCode'] = $this->input->post('WHCode');
            $data['ExpDate'] = $this->input->post('ExpDate');
            $data['jumlah'] = $this->input->post('Jml');
            $data['jumlahawal'] = $this->input->post('Jml');
            $data['NamaRackDest'] = $this->input->post('NamaRackDest');
            $data['NamaRackSrc'] = $this->input->post('NamaRackSrc');
            $this->load->view('admin_replenish/input_qty_view', $data);
        }
    }

    function cek_qty($jumlah) {
        //cek jumlah yang dimasukkan
        $ExpDate = $this->input->post('ExpDate');
        $BinCode = $this->input->post('BinCode');
        $SKUCode = $this->input->post('SKUCode');
        if ($this->admin_replenish_model->cekJumlahBarang($BinCode, $SKUCode, $ExpDate, $jumlah) == false) {
            $this->form_validation->set_message('cek_qty', 'Jumlah Salah!');
            return false;
        }
        return true;
    }

    function tambah_task_replenish() {
        $data['RackName'] = $this->admin_replenish_model->getRackSlot();
        $this->load->view('admin_replenish/tambah_task_rpl_view', $data);
    }

    function get_ajax_cekrackname() {
        $name = $_POST['RackName'];
        $result = $this->admin_replenish_model->cekRackSlotName($name);

        $arr = array("status" => $result['status'], 'RackSlotCode' => $result['RackSlotCode']);
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }

    function get_ajax_binrack() {
        $RackSlotCode = $_POST['RackSlotCode'];
        $result = $this->admin_replenish_model->getBinRackSlot($RackSlotCode);
        $arr = array();
        foreach ($result as $row) {
            $arr[] = array("BinCode" => $row['BinCode']);
        }
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }

    function get_ajax_binsku() {
        $BinCode = $_POST['BinCode'];
        $result = $this->admin_replenish_model->getBinSKU($BinCode);
        $arr = array();
        $arr = array("SKUCode" => $result['Kode'], 'Keterangan' => $result['Keterangan'], 'Qty' => $result['Qty'], 'Qtykonversi' => $result['Qtykonversi'], 'WHCode' => $result['WHCode']);

        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }

    function get_ajax_satuan() {
        //ajax untuk menampilkan informasi SKU barang yang harus dipilih
        $SKUCode = $_POST['SKUCode'];
        $result = $this->admin_replenish_model->getsatuan($SKUCode);
        $arr = array();
        foreach ($result as $row) {
            $arr[] = array("Rasio" => $row['Rasio'], "Satuan" => $row['Satuan']);
        }
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }

    function cari_rack() {
        if ($this->input->post('btnPilih')) {
            $this->form_validation->set_rules('barangrack', 'Barang', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $data['barang'] = $this->admin_replenish_model->getBarang();
                $this->load->view('admin_replenish/cari_rack_view', $data);
            } else {
                $Barang = $this->input->post('barangrack');
                $Barang = explode("~", $Barang);
                $this->session->set_flashdata('BinCode', $Barang[0]);
                $this->session->set_flashdata('RackSlotCode', $Barang[1]);
                $this->session->set_flashdata('RackNameCari', $Barang[2]);
                redirect(base_url() . "/index.php/admin_replenish/tambah_task_replenish");
            }
        } else {
            $data['barang'] = $this->admin_replenish_model->getBarang();
            $this->load->view('admin_replenish/cari_rack_view', $data);
        }
    }

    function add_replenish() {
        $this->form_validation->set_rules('SrcRack', 'Rack Asal', 'required');
        $this->form_validation->set_rules('BinCode', 'Bin Asal', 'required');
        $this->form_validation->set_rules('SKUCode', 'Barang', 'required');
        $this->form_validation->set_rules('SrcQty', 'Jumlah Asal', 'required|integer|callback_cek_jumlah_asal');
        $this->form_validation->set_rules('DestRack', 'Rack Tujuan', 'required|callback_cek_WHSKURack_tujuan');
        $this->form_validation->set_rules('Qty', 'Jumlah Ambil', 'required|integer|callback_cek_jumlah_tujuan');
        $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
        if ($this->form_validation->run() == TRUE) { //masukkan ke dalam cart
            $SrcRack = $this->input->post('SrcRack');
            $SrcRackName = $this->input->post('SrcRackName');
            $BinCode = $this->input->post('BinCode');
            $SKUCode = $this->input->post('SKUCode');
            $Keterangan = $this->input->post('Keterangan');
            $SrcQty = $this->input->post('SrcQty');
            $DestRack = $this->input->post('DestRack');
            $DestRackName = $this->input->post('DestRackName');
            $Qty = $this->input->post('Qty');
            $Rasio = $this->input->post('Rasio');
            $SrcQtykonversi = $this->input->post('SrcQtykonversi');
            $Qtykonversi = $this->admin_replenish_model->getQtykonversi($SKUCode, $Qty * $Rasio);
            $tempdata = array();
            if ($this->session->userdata('ListRpl')) {
                $tempdata = $this->session->userdata('ListRpl');
                $WHSrc = $this->admin_replenish_model->getWH($tempdata[0]['SrcRack']);
                $WHDest = $this->admin_replenish_model->getWH($tempdata[0]['DestRack']);
                if ($WHDest == 'kosong') {
                    $WHDest = $WHSrc;
                }
            }
            $status = true;
            $i = 0;
            while ($status == true && $i < count($tempdata)) {//cek apakah ada yang sama yang pernah diinputkan 
                if ($tempdata[$i]['SrcRack'] == $SrcRack && $tempdata[$i]['BinCode'] == $BinCode && $tempdata[$i]['SKUCode'] == $SKUCode && $tempdata[$i]['DestRack'] == $DestRack) {
                    $status = false;
                }
                $i++;
            }
            if ($this->session->userdata('ListRpl')) {
                if ($this->admin_replenish_model->getWH($SrcRack) != $WHSrc && $this->admin_replenish_model->getWH($DestRack) != $WHDest) {
                    $data = array();
                    //$this->cart->destroy();
                    $data['status'] = FALSE;

                    $data['error'] = '<div class="alert alert-error"><h5>Gudang Beda</h5></div>';
                    header('Content-Type: application/json', true);
                    echo json_encode($data);
                    return;
                }
            }
            if ($status == true) {
                $list = array('SrcRack' => $SrcRack, 'SrcRackName' => $SrcRackName,
                    'BinCode' => $BinCode, 'SKUCode' => $SKUCode, 'Keterangan' => $Keterangan, 'SrcQty' => $SrcQty,
                    'DestRack' => $DestRack, 'DestRackName' => $DestRackName, 'Qty' => $Qty, 'Rasio' => $Rasio,
                    'SrcQtykonversi' => $SrcQtykonversi, 'Qtykonversi' => $Qtykonversi);

                array_push($tempdata, $list);

                $this->session->set_userdata('ListRpl', $tempdata);
                $data = array();
                $data['status'] = TRUE; //mengirimkan status TRUE ke view
                header('Content-Type: application/json', true);
                echo json_encode($data);
            } else {
                $data = array();
                //$this->cart->destroy();
                $data['status'] = FALSE;

                $data['error'] = '<div class="alert alert-error"><h5>Data Yang Sama Pernah Diinput</h5></div>';
                header('Content-Type: application/json', true);
                echo json_encode($data);
            }
        } else { //ada kesalahan, sehingga mengembalikan pesan error ke view
            $data = array();
            //$this->cart->destroy();
            $data['status'] = FALSE;

            $data['error'] = validation_errors();
            header('Content-Type: application/json', true);
            echo json_encode($data);
        }
    }

    function list_replenish() {
        $this->load->view('admin_replenish/list_rpl_view');
    }

    function cek_jumlah_asal($jumlah) {
        if ($jumlah <= 0) {
            $this->form_validation->set_message('cek_jumlah_asal', 'Jumlah Kosong');
            return false;
        }
        return true;
    }

    function cek_jumlah_tujuan($jumlah) {
        $rasio = $this->input->post('Rasio');
        $SrcQty = $this->input->post('SrcQty');

        if ($jumlah * $rasio > $SrcQty) {
            $this->form_validation->set_message('cek_jumlah_tujuan', 'Jumlah Berlebih');
            return false;
        } else if ($jumlah <= 0) {
            $this->form_validation->set_message('cek_jumlah_tujuan', 'Jumlah Kurang');
            return false;
        }
        return true;
    }

    function cek_WHSKURack_tujuan($DestRack) {
        $SrcRack = $this->input->post('SrcRack');
        $SKUCode = $this->input->post('SKUCode');
        $BinCode = $this->input->post('BinCode');
        $result = $this->admin_replenish_model->cekWHSKURack($SrcRack, $DestRack, $BinCode, $SKUCode);
        if ($result['status'] == FALSE) {
            $this->form_validation->set_message('cek_WHSKURack_tujuan', $result['msg']);
            return false;
        }
        return true;
    }

    function delete_replenish() {
        $rowid = $this->input->post('rowid');
        $ListRpl = $this->session->userdata('ListRpl');
        unset($ListRpl[$rowid]);
        $this->session->set_userdata('ListRpl', $ListRpl);
    }

    function edit_replenish() {
        $this->form_validation->set_rules('SrcRack', 'Rack Asal', 'required');
        $this->form_validation->set_rules('BinCode', 'Bin Asal', 'required');
        $this->form_validation->set_rules('SKUCode', 'Barang', 'required');
        $this->form_validation->set_rules('SrcQty', 'Jumlah Asal', 'required|integer|callback_cek_jumlah_asal');
        $this->form_validation->set_rules('DestRack', 'Rack Tujuan', 'required|callback_cek_WHSKURack_tujuan');
        $this->form_validation->set_rules('Qty', 'Jumlah Ambil', 'required|integer|callback_cek_jumlah_tujuan');
        $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
        if ($this->form_validation->run() == TRUE) { //masukkan ke dalam cart
            $RowId = $this->input->post('RowId');
            $SrcRack = $this->input->post('SrcRack');
            $SrcRackName = $this->input->post('SrcRackName');
            $BinCode = $this->input->post('BinCode');
            $SKUCode = $this->input->post('SKUCode');
            $Keterangan = $this->input->post('Keterangan');
            $SrcQty = $this->input->post('SrcQty');
            $DestRack = $this->input->post('DestRack');
            $DestRackName = $this->input->post('DestRackName');
            $Qty = $this->input->post('Qty');
            $Rasio = $this->input->post('Rasio');
            $SrcQtykonversi = $this->input->post('SrcQtykonversi');
            $Qtykonversi = $this->admin_replenish_model->getQtykonversi($SKUCode, $Qty * $Rasio);
            $tempdata = array();
            if ($this->session->userdata('ListRpl')) {
                $tempdata = $this->session->userdata('ListRpl');
                $WHSrc = $this->admin_replenish_model->getWH($tempdata[0]['SrcRack']);
                $WHDest = $this->admin_replenish_model->getWH($tempdata[0]['DestRack']);
                if ($WHDest == 'kosong') {
                    $WHDest = $WHSrc;
                }
            }
            $status = true;
            $i = 0;
            while ($status == true && $i < count($tempdata)) {//cek apakah ada yang sama yang pernah diinputkan 
                if ($tempdata[$i]['SrcRack'] == $SrcRack && $tempdata[$i]['BinCode'] == $BinCode && $tempdata[$i]['SKUCode'] == $SKUCode && $tempdata[$i]['DestRack'] == $DestRack && $i != $RowId) {
                    $status = false;
                }
                $i++;
            }
            if ($this->session->userdata('ListRpl')) {
                if ($this->admin_replenish_model->getWH($SrcRack) != $WHSrc && $this->admin_replenish_model->getWH($DestRack) != $WHDest) {
                    $data = array();
                    //$this->cart->destroy();
                    $data['status'] = FALSE;

                    $data['error'] = '<div class="alert alert-error"><h5>Gudang Beda</h5></div>';
                    header('Content-Type: application/json', true);
                    echo json_encode($data);
                    return;
                }
            }
            if ($status == true) {
                $list = array('SrcRack' => $SrcRack, 'SrcRackName' => $SrcRackName,
                    'BinCode' => $BinCode, 'SKUCode' => $SKUCode, 'Keterangan' => $Keterangan, 'SrcQty' => $SrcQty,
                    'DestRack' => $DestRack, 'DestRackName' => $DestRackName, 'Qty' => $Qty, 'Rasio' => $Rasio,
                    'SrcQtykonversi' => $SrcQtykonversi, 'Qtykonversi' => $Qtykonversi);

                $tempdata = array_replace($tempdata, array($RowId => $list));

                $this->session->set_userdata('ListRpl', $tempdata);
                $data = array();
                $data['status'] = TRUE; //mengirimkan status TRUE ke view
                header('Content-Type: application/json', true);
                echo json_encode($data);
            } else {
                $data = array();
                //$this->cart->destroy();
                $data['status'] = FALSE;

                $data['error'] = '<div class="alert alert-error"><h5>Data Yang Sama Pernah Diinput</h5></div>';
                header('Content-Type: application/json', true);
                echo json_encode($data);
            }
        } else { //ada kesalahan, sehingga mengembalikan pesan error ke view
            $data = array();
            //$this->cart->destroy();
            $data['status'] = FALSE;

            $data['error'] = validation_errors();
            header('Content-Type: application/json', true);
            echo json_encode($data);
        }
    }

    function save_replenish() {
        if (!$this->session->userdata('ListRpl')) {
            $data['error'] = 'Tambahkan Data Dahulu';
            $data['RackName'] = $this->admin_replenish_model->getRackSlot();
            $this->load->view('admin_replenish/tambah_task_rpl_view', $data);
        } else {
            $tempdata = $this->session->userdata('ListRpl');
            $WHSrc = $this->admin_replenish_model->getWH($tempdata[0]['SrcRack']);
            $WHDest = $this->admin_replenish_model->getWH($tempdata[0]['DestRack']);
            if ($WHDest == 'kosong') {
                $WHDest = $WHSrc;
            }
            $TransactionCode = $this->admin_replenish_model->getTransactionCode();
            if ($this->admin_replenish_model->setMasterTaskRpl($TransactionCode, $WHSrc, $WHDest, $this->session->userdata('OperatorCode'))) {
                $status = true;
                $i = 0;
                $temp = $this->session->userdata('ListRpl');
                while ($i < count($temp) && $status == true) {
                    if (!$this->admin_replenish_model->setDetailTaskRplmanual($TransactionCode, $i + 1, $temp[$i]['BinCode'], $temp[$i]['SKUCode'], $temp[$i]['Qty']*$temp[$i]['Rasio'], $temp[$i]['SrcRack'], $temp[$i]['DestRack'])) {
                        $status = false;
                    }
                    $i++;
                }
                if ($status) {
                    $DERP = array();
                    for ($i = 0; $i < count($temp); $i++) {
                        $sDERP = true;
                        $j = 0;
                        while ($j < count($DERP) && $sDERP == true) {
                            if ($DERP[$j]['SKUCode'] == $temp[$i]['SKUCode']) {
                                $sDERP = false;
                                $index = $j;
                            }
                            $j++;
                        }
                        if ($sDERP == true) {
                            $list = array('SKUCode' => $temp[$i]['SKUCode'], 'Qty' => ($temp[$i]['Qty'] * $temp[$i]['Rasio']));
                            array_push($DERP, $list);
                        } else {
                            $list = array('SKUCode' => $temp[$i]['SKUCode'], 'Qty' => (($temp[$i]['Qty'] * $temp[$i]['Rasio']) + $DERP[$index]['Qty']));
                            $DERP[$index] = $list;
                        }
                    }
                    $i = 0;
                    while ($i < count($DERP) && $status == true) {
                        if (!$this->admin_replenish_model->setDetailTaskDERP($TransactionCode, $DERP[$i]['SKUCode'], $DERP[$i]['Qty'])) {
                            $status = false;
                        }
                        $i++;
                    }
                    if ($status) {
                        $this->session->unset_userdata('ListRpl');
                        $this->session->set_flashdata('pesan', 'Input Berhasil');
                        redirect(base_url() . "/index.php/admin_replenish/tambah_task_replenish");
                    } else {
                        $data['error'] = 'Input Gagal';
                        $data['RackName'] = $this->admin_replenish_model->getRackSlot();
                        $this->load->view('admin_replenish/tambah_task_rpl_view', $data);
                    }
                } else {
                    $data['error'] = 'Input Gagal';
                    $data['RackName'] = $this->admin_replenish_model->getRackSlot();
                    $this->load->view('admin_replenish/tambah_task_rpl_view', $data);
                }
            } else {
                $data['error'] = 'Input Gagal';
                $data['RackName'] = $this->admin_replenish_model->getRackSlot();
                $this->load->view('admin_replenish/tambah_task_rpl_view', $data);
            }
        }
    }

    function daftar_transaksi_rpl() {
        $data['rpl'] = $this->admin_replenish_model->getTransaksiRPLList();
        $this->load->view('admin_replenish/daftar_transaksi_rpl_view', $data);
    }

    function detail_transaksi_rpl($transactionCode) {
        $transactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $transactionCode)));
        $data['transaksi'] = $this->admin_replenish_model->getDetailTransaction($transactionCode);
        $data['detail_transaksi'] = $this->admin_replenish_model->getDetailTransaction2($transactionCode);
        $this->load->view('admin_replenish/daftar_detail_transaksi_rpl_view', $data);
    }

    function history_detail_transaksi($transactionCode, $NoUrut) {
        $transactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $transactionCode)));
        $NoUrut = base64_decode(str_replace('-', '=', str_replace('_', '/', $NoUrut)));
        $data['detail_transaksi'] = $this->admin_replenish_model->getDetailTransaction4($transactionCode, $NoUrut);
        $data['history_detail_transaksi'] = $this->admin_replenish_model->getDetailTransaction3($transactionCode, $NoUrut);
        $this->load->view('admin_replenish/daftar_history_detail_transaksi_rpl_view', $data);
    }

    function edit_destrack($TransactionCode, $NoUrut, $DestRackSlot) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $NoUrut = base64_decode(str_replace('-', '=', str_replace('_', '/', $NoUrut)));
        $DestRackSlot = base64_decode(str_replace('-', '=', str_replace('_', '/', $DestRackSlot)));
        $data['TransactionCode'] = $TransactionCode;
        $data['NoUrut'] = $NoUrut;
        $data['DestRackSlot'] = $DestRackSlot;
        $data['rack'] = $this->barcode_model->getAllBarcodeRack();
        $this->load->view('admin_replenish/select_rackslot_view', $data);
    }

    function simpan_destrack() {

        if ($this->input->post('btnPilih')) {
            $TransactionCode = $this->input->post('TransactionCode');
            $NoUrut = $this->input->post('NoUrut');
            $RackSlotCode = $this->input->post('rackslotcode');
            $this->admin_replenish_model->setDestRackSlot($TransactionCode, $NoUrut, $RackSlotCode);
            $data['transaksi'] = $this->admin_replenish_model->getDetailTransaction($TransactionCode);
            $data['detail_transaksi'] = $this->admin_replenish_model->getDetailTransaction2($TransactionCode);
            $this->load->view('admin_replenish/daftar_detail_transaksi_rpl_view', $data);
        }
    }

    function edit_note($TransactionCode, $NoUrut, $Status2) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $NoUrut = base64_decode(str_replace('-', '=', str_replace('_', '/', $NoUrut)));
        $Status2 = base64_decode(str_replace('-', '=', str_replace('_', '/', $Status2)));
        $data['note'] = $this->admin_replenish_model->getNote($TransactionCode, $NoUrut);
        $data['Status2'] = $Status2;
        $this->load->view('admin_replenish/edit_note_view', $data);
    }

    function simpan_note() {
        if ($this->input->post('btnSimpan')) {
            $TransactionCode = $this->input->post('TransactionCode');
            $NoUrut = $this->input->post('NoUrut');
            $Note = $this->input->post('note');

            if ($this->admin_replenish_model->setNote($TransactionCode, $NoUrut, $Note)) {
                $data['transaksi'] = $this->admin_replenish_model->getDetailTransaction($TransactionCode);
                $data['detail_transaksi'] = $this->admin_replenish_model->getDetailTransaction2($TransactionCode);
                $this->load->view('admin_replenish/daftar_detail_transaksi_rpl_view', $data);
            } else {
                $data['note'] = $this->admin_replenish_model->getNote($TransactionCode, $NoUrut);
                $data['error'] = "Gagal Menyimpan";
                $this->load->view('admin_replenish/edit_note_view', $data);
            }
        }
    }

    function edit_mastertaskrpl($TransactionCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        if ($TransactionCode == '') {
            $TransactionCode = $this->session->userdata('TransactionCode');
        }
        $result = $this->admin_replenish_model->getstatustask($TransactionCode);
        if ($result['isCancel'] == 0) {
            // $status = $this->transaksi_model->getstatustask($TransactionCode);
            $this->session->set_userdata('TransactionCode', $TransactionCode);
            $data['isFinishMove'] = $result['isFinishMove'];
            $data['note'] = $this->admin_replenish_model->getNoteMaster($TransactionCode);
            $this->load->view('admin_replenish/edit_note_master_view', $data);
        }
    }

    function simpan_mastertaskrpl() {
        if ($this->input->post('btnSimpan')) {
            $TransactionCode = $this->input->post('TransactionCode');
            $Note = $this->input->post('note');
            $this->admin_replenish_model->setNoteMaster($TransactionCode, $Note);

            $this->session->set_flashdata('pesan', 'Catatan berhasil ditambahkan.');
            redirect(base_url() . 'index.php/admin_replenish/daftar_transaksi_rpl');
        }
    }

    function cek_finishmove($TransactionCode, $ERPCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $ERPCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $ERPCode)));
        $OutstandingMove = $this->admin_replenish_model->cekOutstandingFinishMove($TransactionCode);
        if (count($OutstandingMove) > 0) {
            $data['outstanding'] = $OutstandingMove;
            $data['TransactionCode'] = $TransactionCode;
            $this->session->set_userdata('TransactionCode', $TransactionCode);
            $this->session->set_userdata('ERPCode', $ERPCode);
            $data['ERPCode'] = $ERPCode;
            $this->load->view('admin_replenish/outstanding_finishmove_view', $data);
        } else {
            $OperatorCode = $this->session->userdata('OperatorCode');
            if ($this->admin_replenish_model->setFinishMove($TransactionCode, $OperatorCode)) {
                if ($this->admin_replenish_model->setFinishAdmin($TransactionCode, $OperatorCode)) {
                    $this->session->set_flashdata('pesan','Transaksi Replenish Berhasil Menyelesaikan Perpindahan Barang.');
                    redirect(base_url().'index.php/admin_replenish/daftar_transaksi_rpl');
                } else {
                    $data['error'] = "Transaksi Replenish Gagal Menyelesaikan Perpindahan Barang.";
                    $data['isFinishMove'] = 0;
                    $data['note'] = $this->admin_replenish_model->getNoteMaster($TransactionCode);
                    $this->load->view('admin_replenish/edit_note_master_view', $data);
                }
            } else {
                $data['error'] = "Transaksi Replenish Gagal Menyelesaikan Perpindahan Barang.";
                $data['isFinishMove'] = 0;
                $data['note'] = $this->admin_replenish_model->getNoteMaster($TransactionCode);
                $this->load->view('admin_replenish/edit_note_master_view', $data);
            }
        }
    }

    function simpan_pembatalanrpl() {
        if ($this->input->post('btnSimpanCancel')) {
            $this->form_validation->set_rules('notecancel', 'Catatan Pembatalan', 'required');
            $this->form_validation->set_rules('TransactionCode', 'TransactionCode', 'required|callback_cek_detailpembatalan');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $TransactionCode = $this->input->post('TransactionCode');
                $result = $this->admin_replenish_model->getstatustask($TransactionCode);
                $data['isFinishMove'] = $result['isFinishMove'];
                $data['note'] = $this->admin_replenish_model->getNoteMaster($TransactionCode);
                $this->load->view('admin_replenish/edit_note_master_view', $data);
            } else {
                $TransactionCode = $this->input->post('TransactionCode');
                $operatorCode = $this->session->userdata('OperatorCode');
                $Note = $this->admin_replenish_model->getNoteMaster($TransactionCode);
                $Notelama = $Note['Note'];
                $Notebaru = $this->input->post('notecancel');
                $Notelama.='#' . $Notebaru;


                //$this->transaksi_model->setBPB($kode_proyek, $ERPCode, $tanggal, $siteId, $operatorCode);
                $this->admin_replenish_model->setNoteMasterPembatalan($TransactionCode, $Notelama, $operatorCode);
                redirect(base_url() . 'index.php/admin_replenish/daftar_transaksi_rpl');
            }
        }
    }

    function simpan_clear_outstanding_finishmove() {
        if ($this->input->post('btnSimpanClear')) {
            $this->form_validation->set_rules('noteclear', 'Catatan Penyelesaian Outstanding', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $data['outstanding'] = $this->admin_replenish_model->cekOutstandingFinishMove($this->session->userdata('TransactionCode'));
                $data['TransactionCode'] = $this->session->userdata('TransactionCode');
                $data['ERPCode'] = $this->session->userdata('ERPCode');
                $this->load->view('admin_replenish/outstanding_finishmove_view', $data);
            } else {
                $TransactionCode = $this->input->post('TransactionCode');
                $NoUrut = $this->input->post('NoUrut');
                $QueueNumber = $this->input->post('QueueNumber');
                $OperatorCode = $this->session->userdata('OperatorCode');
                $Note = $this->admin_replenish_model->getNote($TransactionCode, $NoUrut);
                $Notelama = $Note['Note'];
                $Notebaru = $this->input->post('noteclear');
                $Notelama.='#' . $Notebaru;
                $this->admin_replenish_model->setNote($TransactionCode, $NoUrut, $Notelama);
                $this->admin_replenish_model->setNotePembatalan($TransactionCode, $NoUrut, $QueueNumber, $OperatorCode);

                $data['pesan'] = 'Outstanding Berhasil DiClear.';
                $data['outstanding'] = $this->admin_replenish_model->cekOutstandingFinishMove($this->session->userdata('TransactionCode'));
                if (count($data['outstanding']) > 0) {
                    $data['TransactionCode'] = $this->session->userdata('TransactionCode');
                    $data['ERPCode'] = $this->session->userdata('ERPCode');
                    $this->load->view('admin_replenish/outstanding_finishmove_view', $data);
                } else {
                    $this->edit_mastertaskrpl(str_replace('=', '-', str_replace('/', '_', base64_encode($this->session->userdata('TransactionCode')))));
                    //$this->cek_finishmove(str_replace('=', '-', str_replace('/', '_', base64_encode($this->session->userdata('TransactionCode'))),str_replace('=', '-', str_replace('/', '_', base64_encode($this->session->userdata('ERPCode'))))));
                }
            }
        }
    }
    public function show_assigned($TransactionCode, $ERPCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $ERPCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $ERPCode)));
        $this->session->set_userdata('ERPCode', $ERPCode);
        $this->session->set_userdata('TransactionCode', $TransactionCode);
        $data['Assigned'] = $this->admin_replenish_model->getAssigned($TransactionCode);
        $data['Role'] = $this->admin_replenish_model->getRole($TransactionCode);
        $this->load->view('admin_replenish/Assigned_view', $data);
    }

    public function delete_assigned($OperatorCode, $OprRole) {
        $TransactionCode = $this->session->userdata('TransactionCode');
        $OperatorCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $OperatorCode)));
        $OprRole = base64_decode(str_replace('-', '=', str_replace('_', '/', $OprRole)));
        if ($this->admin_replenish_model->deleteAssigned($TransactionCode, $OperatorCode, $OprRole)) {
            $data['Assigned'] = $this->admin_replenish_model->getAssigned($TransactionCode);
            $data['Role'] = $this->admin_replenish_model->getRole($TransactionCode);
            $data['pesan'] = "Penugasan Berhasil Dihapus";
            $this->load->view('admin_replenish/Assigned_view', $data);
        } else {
            $data['Assigned'] = $this->admin_replenish_model->getAssigned($TransactionCode);
            $data['Role'] = $this->admin_replenish_model->getRole($TransactionCode);
            $data['error'] = "Penugasan Gagal Dihapus";
            $this->load->view('admin_replenish/Assigned_view', $data);
        }
    }

    function get_ajax_Operator() {
        //ajax untuk menampilkan informasi SKU barang yang harus dipilih
        $WHRoleCode = $_POST['WHRoleCode'];
        $TransactionCode = $this->session->userdata('TransactionCode');
        $result = $this->admin_replenish_model->getOperator($WHRoleCode, $TransactionCode);

        $arr = array();
        foreach ($result as $row) {
            $arr[] = array('OperatorCode' => $row['OperatorCode'], 'Name' => $row['Name']);
        }
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }

    function tambah_assigned() {
        if ($this->input->post('btnTambah')) {
            $this->form_validation->set_rules('Operator', 'Operator', 'required');
            $this->form_validation->set_rules('Role', 'Role', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $TransactionCode = $this->session->userdata('TransactionCode');
                $data['Assigned'] = $this->admin_replenish_model->getAssigned($TransactionCode);
                $data['Role'] = $this->admin_replenish_model->getRole($TransactionCode);
                $this->load->view('admin_replenish/Assigned_view', $data);
            } else {
                $TransactionCode = $this->session->userdata('TransactionCode');
                $OperatorCode = $this->input->post('Operator');
                $OprRole = $this->input->post('Role');
                if ($this->admin_replenish_model->setAssigned($TransactionCode, $OperatorCode, $OprRole)) {
                    $data['Assigned'] = $this->admin_replenish_model->getAssigned($TransactionCode);
                    $data['Role'] = $this->admin_replenish_model->getRole($TransactionCode);
                    $data['pesan'] = "Penugasan Berhasil Ditambah";
                    $this->load->view('admin_replenish/Assigned_view', $data);
                } else {
                    $data['Assigned'] = $this->admin_replenish_model->getAssigned($TransactionCode);
                    $data['Role'] = $this->admin_replenish_model->getRole($TransactionCode);
                    $data['error'] = "Penugasan Gagal Ditambah";
                    $this->load->view('admin_replenish/Assigned_view', $data);
                }
            }
        }
    }

}

?>
