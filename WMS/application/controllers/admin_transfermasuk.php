<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of admin_transfermasuk
 *
 * @author USER
 */
class admin_transfermasuk extends CI_Controller {

    //put your code here
    function __construct() {
        parent::__construct();

        //$this->set_session();
        if (!$this->session->userdata('OperatorCode') || $this->session->userdata("OperatorRole") != '10/WHR/000') {
            redirect(base_url() . "index.php/login");
        }
        $this->load->model('admin_transfermasuk_model');
        $this->load->model('barcode_model');
    }


    function tambah_admin_transfermasuk() {
        if ($this->input->post('btnTambah')) {
            $this->form_validation->set_rules('kodenota[]', 'Nota Transfer Stok', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $data['cabang'] = $this->admin_transfermasuk_model->getCabang();
                $today = date("Y/m/d");
                if (count($data['cabang']) == 0) {
                    $cabang = '';
                } else {
                    $cabang = $data['cabang'][0]['Cabang'];
                }
                $data['kodenota'] = $this->admin_transfermasuk_model->getTransferMasuk($today, $cabang);
                $this->load->view('admin_transfermasuk/tambah_admin_transfermasuk_view', $data);
            } else {
                $siteId = $this->session->userdata('siteId');
                $kodenota = $this->input->post('kodenota');
                $operatorCode = $this->session->userdata('OperatorCode');
                $tanggal = $this->session->userdata('ERPDate');
                $kode_proyek = 'RTS';
                foreach ($kodenota as $row) {
                    $this->admin_transfermasuk_model->setTransferMasuk($kode_proyek, $row, $tanggal, $siteId, $operatorCode);
                }
                $this->session->set_flashdata('pesan', 'Kode Nota Transfer Masuk berhasil ditambahkan.');
                
                $data['cabang'] = $this->admin_transfermasuk_model->getCabang();
                $today = date("Y/m/d");
                if (count($data['cabang']) == 0) {
                    $cabang = '';
                } else {
                    $cabang = $data['cabang'][0]['Cabang'];
                }
                $data['kodenota'] = $this->admin_transfermasuk_model->getTransferMasuk($today, $cabang);
                $this->load->view('admin_transfermasuk/tambah_admin_transfermasuk_view', $data);
            }
        } else {
            $data['cabang'] = $this->admin_transfermasuk_model->getCabang();
            $today = date("Y/m/d");
            if (count($data['cabang']) == 0) {
                $cabang = '';
            } else {
                $cabang = $data['cabang'][0]['Cabang'];
            }
            $data['kodenota'] = $this->admin_transfermasuk_model->getTransferMasuk($today, $cabang);
            $this->load->view('admin_transfermasuk/tambah_admin_transfermasuk_view', $data);
        }
    }

    function get_ajax_tambah_admin_transfermasuk() {
        //ajax untuk menampilkan informasi SKU barang yang harus dipilih
        $tgl = $_POST['tgl'];
        $this->session->set_userdata('ERPDate',$tgl);
        $cabang = $_POST['cabang'];
        $result = $this->admin_transfermasuk_model->getTransferMasuk($tgl, $cabang);
        $arr = array();
        foreach ($result as $row) {
            $arr[] = array("KodeNota" => $row['KodeNota'], "Perusahaan" => $row['Perusahaan'], "Keterangan" => $row['Keterangan']);
        }
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }

    function detail_admin_transfermasuk($transactionCode) {
        $transactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $transactionCode)));
        $data['admin_transfermasuk'] = $this->admin_transfermasuk_model->getDetailTransaction($transactionCode);
        $data['detail_admin_transfermasuk'] = $this->admin_transfermasuk_model->getDetailTransaction2($transactionCode,  $this->session->userdata('ReceiveSource'));
        $this->load->view('admin_transfermasuk/daftar_detail_admin_transfermasuk_view', $data);
    }

    function history_detail_admin_transfermasuk($transactionCode, $NoUrut) {
        $transactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $transactionCode)));
        $NoUrut = base64_decode(str_replace('-', '=', str_replace('_', '/', $NoUrut)));
        $data['detail_admin_transfermasuk'] = $this->admin_transfermasuk_model->getDetailTransaction4($transactionCode, $NoUrut);
        $data['history_detail_admin_transfermasuk'] = $this->admin_transfermasuk_model->getDetailTransaction3($transactionCode, $NoUrut);
        $this->load->view('admin_transfermasuk/daftar_history_detail_admin_transfermasuk_view', $data);
    }

    function daftar_admin_transfermasuk() {
        $data['transfermasuk'] = $this->admin_transfermasuk_model->getadmin_transfermasukList();
        $this->load->view('admin_transfermasuk/daftar_admin_transfermasuk_view', $data);
    }

    function edit_note($TransactionCode, $NoUrut, $Status2) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $NoUrut = base64_decode(str_replace('-', '=', str_replace('_', '/', $NoUrut)));
        $Status2 = base64_decode(str_replace('-', '=', str_replace('_', '/', $Status2)));
        $data['note'] = $this->admin_transfermasuk_model->getNote($TransactionCode, $NoUrut);
        $data['Status2'] = $Status2;
        $this->load->view('admin_transfermasuk/edit_note_view', $data);
    }

    function simpan_note() {
        if ($this->input->post('btnSimpan')) {
            $TransactionCode = $this->input->post('TransactionCode');
            $NoUrut = $this->input->post('NoUrut');
            $Note = $this->input->post('note');

            if ($this->admin_transfermasuk_model->setNote($TransactionCode, $NoUrut, $Note)) {
                $data['admin_transfermasuk'] = $this->admin_transfermasuk_model->getDetailTransaction($TransactionCode);
                $data['detail_admin_transfermasuk'] = $this->admin_transfermasuk_model->getDetailTransaction2($TransactionCode,  $this->session->userdata('ReceiveSource'));
                $this->load->view('admin_transfermasuk/daftar_detail_admin_transfermasuk_view', $data);
            } else {
                $data['note'] = $this->admin_transfermasuk_model->getNote($TransactionCode, $NoUrut);
                $data['error'] = "Gagal Menyimpan";
                $this->load->view('admin_transfermasuk/edit_note_view', $data);
            }
        }
    }

    function cek_detailpembatalan($kode) {
        if ($this->admin_transfermasuk_model->cekPembatalan($kode)) {
            return TRUE;
        }
        $this->form_validation->set_message('cek_detailpembatalan', 'Task Sudah Berjalan Tidak Dapat Dibatalkan');
        return FALSE;
    }

    function edit_destrack($TransactionCode, $NoUrut, $DestRackSlot='') {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $NoUrut = base64_decode(str_replace('-', '=', str_replace('_', '/', $NoUrut)));
        if($DestRackSlot!=''){
            $DestRackSlot = base64_decode(str_replace('-', '=', str_replace('_', '/', $DestRackSlot)));
        }
        else{
            $DestRackSlot='kosong';
        }
        $data['TransactionCode'] = $TransactionCode;
        $data['NoUrut'] = $NoUrut;
        $data['DestRackSlot'] = $DestRackSlot;
        $data['rack'] = $this->barcode_model->getAllBarcodeRack();
        $this->load->view('admin_transfermasuk/select_rackslot_view', $data);
    }

    function simpan_destrack() {

        if ($this->input->post('btnPilih')) {
            $TransactionCode = $this->input->post('TransactionCode');
            $NoUrut = $this->input->post('NoUrut');
            $RackSlotCode = $this->input->post('rackslotcode');
            $this->admin_transfermasuk_model->setDestRackSlot($TransactionCode, $NoUrut, $RackSlotCode);
            $data['admin_transfermasuk'] = $this->admin_transfermasuk_model->getDetailTransaction($TransactionCode);
            $data['detail_admin_transfermasuk'] = $this->admin_transfermasuk_model->getDetailTransaction2($TransactionCode,  $this->session->userdata('ReceiveSource'));
            $this->load->view('admin_transfermasuk/daftar_detail_admin_transfermasuk_view', $data);
        }
    }

    function edit_mastertaskrcv($TransactionCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        if ($TransactionCode == '') {
            $TransactionCode = $this->session->userdata('TransactionCode');
        }
        $result = $this->admin_transfermasuk_model->getstatustask($TransactionCode);
        if ($result['isCancel'] == 0) {
            // $status = $this->admin_transfermasuk_model->getstatustask($TransactionCode);
            $this->session->set_userdata('TransactionCode', $TransactionCode);
            $data['isFinishMove'] = $result['isFinishMove'];
            $data['note'] = $this->admin_transfermasuk_model->getNoteMaster($TransactionCode);
            $this->load->view('admin_transfermasuk/edit_note_master_view', $data);
        }
    }

    function simpan_mastertaskrcv() {
        if ($this->input->post('btnSimpan')) {
            $TransactionCode = $this->input->post('TransactionCode');
            $ERPCode = $this->input->post('ERPCode');
            $Note = $this->input->post('note');
            $siteId = $this->session->userdata('siteId');
            $operatorCode = $this->session->userdata('OperatorCode');
            $tanggal = date("Y/m/d");
            $kode_proyek = 'RTS'; //kode BPB Principle    

            $this->admin_transfermasuk_model->setTransferMasuk($kode_proyek, $ERPCode, $tanggal, $siteId, $operatorCode);
            $this->admin_transfermasuk_model->setNoteMaster($TransactionCode, $Note);

            $this->session->set_flashdata('pesan', 'Transfer Stok Masuk berhasil ditambahkan.');
            $data['pesan'] = 'Transfer Stok Masuk berhasil ditambahkan.';
            $this->daftar_admin_transfermasuk();
        }
    }

    function cek_finishmove($TransactionCode, $ERPCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $ERPCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $ERPCode)));
        $OutstandingMove = $this->admin_transfermasuk_model->cekOutstandingFinishMove($TransactionCode);
        if (count($OutstandingMove) > 0) {
            $data['outstanding'] = $OutstandingMove;
            $data['TransactionCode'] = $TransactionCode;
            $this->session->set_userdata('TransactionCode', $TransactionCode);
            $this->session->set_userdata('ERPCode', $ERPCode);
            $data['ERPCode'] = $ERPCode;
            $this->load->view('admin_transfermasuk/outstanding_finishmove_view', $data);
        } else {
            $OperatorCode = $this->session->userdata('OperatorCode');
            if ($this->admin_transfermasuk_model->setFinishMove($TransactionCode, $OperatorCode)) {
                $data['pesan'] = 'Transfer Stok Masuk Berhasil Menyelesaikan Perpindahan Barang.';
                $data['note'] = $this->admin_transfermasuk_model->getNoteMaster($TransactionCode);
                $data['isFinishMove'] = 1;
                $this->load->view('admin_transfermasuk/edit_note_master_view', $data);
            } else {
                $data['error'] = "Transfer Stok Masuk Gagal Menyelesaikan Perpindahan Barang.";
                $data['isFinishMove'] = 0;
                $data['note'] = $this->admin_transfermasuk_model->getNoteMaster($TransactionCode);
                $this->load->view('admin_transfermasuk/edit_note_master_view', $data);
            }
        }
    }

    function simpan_clear_outstanding_finishmove() {
        if ($this->input->post('btnSimpanClear')) {
            $this->form_validation->set_rules('noteclear', 'Catatan Penyelesaian Outstanding', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $data['outstanding'] = $this->admin_transfermasuk_model->cekOutstandingFinishMove($this->session->userdata('TransactionCode'));
                $data['TransactionCode'] = $this->session->userdata('TransactionCode');
                $data['ERPCode'] = $this->session->userdata('ERPCode');
                $this->load->view('admin_transfermasuk/outstanding_finishmove_view', $data);
            } else {
                $TransactionCode = $this->input->post('TransactionCode');
                $NoUrut = $this->input->post('NoUrut');
                $QueueNumber = $this->input->post('QueueNumber');
                $OperatorCode = $this->session->userdata('OperatorCode');
                $Note = $this->admin_transfermasuk_model->getNote($TransactionCode, $NoUrut);
                $Notelama = $Note['Note'];
                $Notebaru = $this->input->post('noteclear');
                $Notelama.='#' . $Notebaru;
                $this->admin_transfermasuk_model->setNote($TransactionCode, $NoUrut, $Notelama);
                $this->admin_transfermasuk_model->setNotePembatalan($TransactionCode, $NoUrut, $QueueNumber, $OperatorCode,$this->session->userdata('ReceiveProblem'));

                $data['pesan'] = 'Outstanding Berhasil DiClear.';
                $data['outstanding'] = $this->admin_transfermasuk_model->cekOutstandingFinishMove($this->session->userdata('TransactionCode'));
                if (count($data['outstanding']) > 0) {
                    $data['TransactionCode'] = $this->session->userdata('TransactionCode');
                    $data['ERPCode'] = $this->session->userdata('ERPCode');
                    $this->load->view('admin_transfermasuk/outstanding_finishmove_view', $data);
                } else {
                    $this->edit_mastertaskrcv(str_replace('=', '-', str_replace('/', '_', base64_encode($this->session->userdata('TransactionCode')))));
                    //$this->cek_finishmove(str_replace('=', '-', str_replace('/', '_', base64_encode($this->session->userdata('TransactionCode'))),str_replace('=', '-', str_replace('/', '_', base64_encode($this->session->userdata('ERPCode'))))));
                }
            }
        }
    }

    function cek_finishadmin($TransactionCode, $ERPCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $ERPCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $ERPCode)));
        
        $siteId = $this->session->userdata('siteId');
        $operatorCode = $this->session->userdata('OperatorCode');
        $tanggal = $this->admin_transfermasuk_model->getTransactionDate($TransactionCode);
        $kode_proyek = 'RTS';   
        $this->admin_transfermasuk_model->setTransferMasuk($kode_proyek, $ERPCode, $tanggal, $siteId, $operatorCode);
        
        $OutstandingDERP = $this->admin_transfermasuk_model->cekOutstandingFinishAdmin($TransactionCode);
        if (count($OutstandingDERP) > 0) {
            $data['outstanding'] = $OutstandingDERP;
            $data['TransactionCode'] = $TransactionCode;
            $data['penyelesaian'] = $this->admin_transfermasuk_model->getNotaPenyelesaian($TransactionCode);
            $this->session->set_userdata('TransactionCode', $TransactionCode);
            $this->session->set_userdata('ERPCode', $ERPCode);
            $data['ERPCode'] = $ERPCode;
            
            $this->load->view('admin_transfermasuk/outstanding_finishadmin_view', $data);
        } else {
            $OperatorCode = $this->session->userdata('OperatorCode');
            if ($this->admin_transfermasuk_model->setFinishAdmin($TransactionCode, $OperatorCode)) {
                $this->session->set_flashdata('pesan', 'Transfer Stok Masuk Berhasil Selesai.');
                $this->daftar_admin_transfermasuk();
            } else {
                $data['error'] = "Transfer Stok Masuk Gagal Selesai.";
                $data['isFinishMove'] = 0;
                $data['note'] = $this->admin_transfermasuk_model->getNoteMaster($TransactionCode);
                $this->load->view('admin_transfermasuk/edit_note_master_view', $data);
            }
        }
    }

    function simpan_pembatalanrcv() {
        if ($this->input->post('btnSimpanCancel')) {
            $this->form_validation->set_rules('notecancel', 'Catatan Pembatalan', 'required');
            $this->form_validation->set_rules('TransactionCode', 'TransactionCode', 'required|callback_cek_detailpembatalan');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $TransactionCode = $this->input->post('TransactionCode');
                $result = $this->admin_transfermasuk_model->getstatustask($TransactionCode);
                $data['isFinishMove'] = $result['isFinishMove'];
                $data['note'] = $this->admin_transfermasuk_model->getNoteMaster($TransactionCode);
                $this->load->view('admin_transfermasuk/edit_note_master_view', $data);
            } else {
                $TransactionCode = $this->input->post('TransactionCode');
                $ERPCode = $this->input->post('ERPCode');
                $siteId = $this->session->userdata('siteId');
                $operatorCode = $this->session->userdata('OperatorCode');
                $tanggal = date("Y/m/d");
                $kode_proyek = 'RTS'; 
                $Note = $this->admin_transfermasuk_model->getNoteMaster($TransactionCode);
                $Notelama = $Note['Note'];
                $Notebaru = $this->input->post('notecancel');
                $Notelama.='#' . $Notebaru;


                //$this->admin_transfermasuk_model->setBPB($kode_proyek, $ERPCode, $tanggal, $siteId, $operatorCode);
                $this->admin_transfermasuk_model->setNoteMasterPembatalan($TransactionCode, $Notelama, $operatorCode);
                $this->daftar_admin_transfermasuk();
            }
        }
    }

    function tambah_notapenyelesaian() {
        if ($this->input->post('btnSimpan')) {
            $this->form_validation->set_rules('kodenota', 'Kode Nota', 'required|callback_cek_kodenota');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $data['TransactionCode'] = $this->session->userdata('TransactionCode');
                $OutstandingDERP = $this->admin_transfermasuk_model->cekOutstandingFinishAdmin($data['TransactionCode']);
                $data['outstanding'] = $OutstandingDERP;

                $data['penyelesaian'] = $this->admin_transfermasuk_model->getNotaPenyelesaian($data['TransactionCode']);
                $data['ERPCode'] = $this->session->userdata('ERPCode');
                $this->load->view('admin_transfermasuk/outstanding_finishadmin_view', $data);
            } else {
                $TransactionCode = $this->session->userdata('TransactionCode');
                $KodeNota = $this->input->post('kodenota');
                $Tipe = $this->input->post('tipe');
                if ($this->admin_transfermasuk_model->setPenyelesaian($TransactionCode, $KodeNota, $Tipe)) {
                    $data['pesan'] = 'Kode Nota Berhasil Ditambahkan.';
                    $data['TransactionCode'] = $this->session->userdata('TransactionCode');
                    $OutstandingDERP = $this->admin_transfermasuk_model->cekOutstandingFinishAdmin($data['TransactionCode']);
                    if (count($OutstandingDERP) > 0) {
                        $data['outstanding'] = $OutstandingDERP;
                        $data['penyelesaian'] = $this->admin_transfermasuk_model->getNotaPenyelesaian($data['TransactionCode']);
                        $data['ERPCode'] = $this->session->userdata('ERPCode');
                        $this->load->view('admin_transfermasuk/outstanding_finishadmin_view', $data);
                    } else {
                        $this->edit_mastertaskrcv(str_replace('=', '-', str_replace('/', '_', base64_encode($this->session->userdata('TransactionCode')))));
                        //$this->cek_finishmove(str_replace('=', '-', str_replace('/', '_', base64_encode($this->session->userdata('TransactionCode'))),str_replace('=', '-', str_replace('/', '_', base64_encode($this->session->userdata('ERPCode'))))));
                    }
                } else {
                    $data['TransactionCode'] = $this->session->userdata('TransactionCode');
                    $data['error'] = 'Kode Nota Gagal Ditambahkan.';
                    $OutstandingDERP = $this->admin_transfermasuk_model->cekOutstandingFinishAdmin($data['TransactionCode']);
                    $data['outstanding'] = $OutstandingDERP;
                    $data['penyelesaian'] = $this->admin_transfermasuk_model->getNotaPenyelesaian($data['TransactionCode']);
                    $data['ERPCode'] = $this->session->userdata('ERPCode');
                    $this->load->view('admin_transfermasuk/outstanding_finishadmin_view', $data);
                }
            }
        }
    }

    function hapus_kodenota($TransactionCode, $KodeNota) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $KodeNota = base64_decode(str_replace('-', '=', str_replace('_', '/', $KodeNota)));
        if ($this->admin_transfermasuk_model->removePenyelesaian($TransactionCode, $KodeNota)) {
            $data['pesan'] = 'Kode Nota Berhasil Dihapus.';
        } else {
            $data['error'] = 'Kode Nota Gagal Dihapus.';
        }
        $data['TransactionCode'] = $this->session->userdata('TransactionCode');
        $OutstandingDERP = $this->admin_transfermasuk_model->cekOutstandingFinishAdmin($data['TransactionCode']);
        $data['outstanding'] = $OutstandingDERP;
        $data['penyelesaian'] = $this->admin_transfermasuk_model->getNotaPenyelesaian($data['TransactionCode']);
        $data['ERPCode'] = $this->session->userdata('ERPCode');
        $this->load->view('admin_transfermasuk/outstanding_finishadmin_view', $data);
    }

    function cek_kodenota($KodeNota) {
        $Tipe = $this->input->post('tipe');
        if ($this->admin_transfermasuk_model->cekKodeNota($KodeNota, $Tipe, $this->session->userdata('ERPCode'))) {
            return true;
        }
        $this->form_validation->set_message('cek_kodenota', '%s tidak valid.');
        return false;
    }

    function export_excel($TransactionCode, $ERPCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $ERPCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $ERPCode)));
        $OutstandingDERP = $this->admin_transfermasuk_model->cekOutstandingFinishAdmin($TransactionCode);
        if (count($OutstandingDERP) > 0) {
            /** Include PHPExcel */
            require_once APPPATH . "/third_party/phpexcel/PHPExcel.php";
            // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();

            // Set document properties
            $objPHPExcel->getProperties()->setCreator($this->session->userdata('OperatorCode'))
                    ->setLastModifiedBy($this->session->userdata('OperatorCode'))
                    ->setTitle("Masalah Transfer Stok Masuk " . $ERPCode)
                    ->setSubject("Masalah Transfer Stok Masuk  " . $ERPCode)
                    ->setDescription("Masalah Transfer Stok Masuk  " . $ERPCode)
                    ->setKeywords("Masalah Transfer Stok Masuk  " . $ERPCode)
                    ->setCategory("Masalah Transfer Stok Masuk  " . $ERPCode);



            // Add some data
            $PHPExcel = $objPHPExcel->setActiveSheetIndex(0);
            $PHPExcel->setCellValue('A1', 'SKU');
            $PHPExcel->setCellValue('B1', 'Barang');
            $PHPExcel->setCellValue('C1', 'Jumlah');
            $PHPExcel->setCellValue('D1', 'Status');

            $rowexcel = 2;
            foreach ($OutstandingDERP as $row) {
                $PHPExcel->setCellValue('A' . $rowexcel, $row['Kode']);
                $PHPExcel->setCellValue('B' . $rowexcel, $row['Keterangan']);
                $PHPExcel->setCellValue('C' . $rowexcel, $row['Jml']);
                $PHPExcel->setCellValue('D' . $rowexcel, $row['Status']);
                $rowexcel++;
            }

            // Rename worksheet
            //$objPHPExcel->getActiveSheet()->setTitle("Masalah Receiving BPB " . $ERPCode);
            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            $judul = "\"Masalah Receiving " . date('d-m-Y') . ".xls\"";

            // Redirect output to a client’s web browser (Excel5)
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename=' . $judul);
            header('Cache-Control: max-age=0');


            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
        } else {
            $data['TransactionCode'] = $TransactionCode;
            $data['error'] = 'Tidak Ada Data yang Dapat Dieskport';
            $OutstandingDERP = $this->admin_transfermasuk_model->cekOutstandingFinishAdmin($data['TransactionCode']);
            $data['outstanding'] = $OutstandingDERP;
            $data['penyelesaian'] = $this->admin_transfermasuk_model->getNotaPenyelesaian($TransactionCode);
            $data['ERPCode'] = $ERPCode;
            $this->load->view('admin_transfermasuk/outstanding_finishadmin_view', $data);
        }
    }

    function batal_finish($TransactionCode, $ERPCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $ERPCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $ERPCode)));
        if ($this->admin_transfermasuk_model->buka_finishmove($TransactionCode)) {
            $this->daftar_admin_transfermasuk();
        } else {
            $siteId = $this->session->userdata('siteId');
            $operatorCode = $this->session->userdata('OperatorCode');
            $tanggal = $this->admin_transfermasuk_model->getTransactionDate($TransactionCode);
            $kode_proyek = 'RTS'; //kode BPB Principle    
            $this->admin_transfermasuk_model->setTransferMasuk($kode_proyek, $ERPCode, $tanggal, $siteId, $operatorCode);
            $data['TransactionCode'] = $TransactionCode;
            $data['error'] = 'Penambahan Receiving Gagal';
            $OutstandingDERP = $this->admin_transfermasuk_model->cekOutstandingFinishAdmin($data['TransactionCode']);
            $data['outstanding'] = $OutstandingDERP;
            $data['penyelesaian'] = $this->admin_transfermasuk_model->getNotaPenyelesaian($TransactionCode);
            $data['ERPCode'] = $ERPCode;
            $this->load->view('admin_transfermasuk/outstanding_finishadmin_view', $data);
        }
    }
    function tambah_OverRcv()
    {
        if($this->input->post('btnAmbil')){
            $TransactionCode=  $this->input->post('TransactionCode');
            $SKUCode=$this->input->post('SKUCode');
            $Qty=$this->input->post('Qty');
            $ERPCode=$this->input->post('ERPCode');
            if($this->admin_transfermasuk_model->setOverRcv($TransactionCode,$SKUCode,$Qty))
            {
                $data['pesan'] = 'Penambahan Tugas Pengambilan Berlebih Berhasil';
            }
            else{
                $data['error'] = 'Penambahan Tugas Pengambilan Berlebih Gagal';
            }
            $siteId = $this->session->userdata('siteId');
            $operatorCode = $this->session->userdata('OperatorCode');
            $tanggal = $this->admin_transfermasuk_model->getTransactionDate($TransactionCode);
            $kode_proyek = 'RTS';  
            $this->admin_transfermasuk_model->setTransferMasuk($kode_proyek, $ERPCode, $tanggal, $siteId, $operatorCode);
            
            $OutstandingDERP = $this->admin_transfermasuk_model->cekOutstandingFinishAdmin($TransactionCode);

            if (count($OutstandingDERP) > 0) {
                $data['outstanding'] = $OutstandingDERP;
                $data['TransactionCode'] = $TransactionCode;
                $data['penyelesaian'] = $this->admin_transfermasuk_model->getNotaPenyelesaian($TransactionCode);
                $this->session->set_userdata('TransactionCode', $TransactionCode);
                $this->session->set_userdata('ERPCode', $ERPCode);
                $data['ERPCode'] = $ERPCode;

                $this->load->view('admin_transfermasuk/outstanding_finishadmin_view', $data);
            } else {
                $data['isFinishMove'] = 0;
                $data['note'] = $this->admin_transfermasuk_model->getNoteMaster($TransactionCode);
                $this->load->view('admin_transfermasuk/edit_note_master_view', $data);
            }
        }
            
    }
    public function show_assigned($TransactionCode, $ERPCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $ERPCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $ERPCode)));
        $this->session->set_userdata('ERPCode', $ERPCode);
        $this->session->set_userdata('TransactionCode', $TransactionCode);
        $data['Assigned'] = $this->admin_transfermasuk_model->getAssigned($TransactionCode);
        $data['Role'] = $this->admin_transfermasuk_model->getRole($TransactionCode);
        $this->load->view('admin_transfermasuk/Assigned_view', $data);
    }

    public function delete_assigned($OperatorCode, $OprRole) {
        $TransactionCode = $this->session->userdata('TransactionCode');
        $OperatorCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $OperatorCode)));
        $OprRole = base64_decode(str_replace('-', '=', str_replace('_', '/', $OprRole)));
        if ($this->admin_transfermasuk_model->deleteAssigned($TransactionCode, $OperatorCode, $OprRole)) {
            $data['Assigned'] = $this->admin_transfermasuk_model->getAssigned($TransactionCode);
            $data['Role'] = $this->admin_transfermasuk_model->getRole($TransactionCode);
            $data['pesan'] = "Penugasan Berhasil Dihapus";
            $this->load->view('admin_transfermasuk/Assigned_view', $data);
        } else {
            $data['Assigned'] = $this->admin_transfermasuk_model->getAssigned($TransactionCode);
            $data['Role'] = $this->admin_transfermasuk_model->getRole($TransactionCode);
            $data['error'] = "Penugasan Gagal Dihapus";
            $this->load->view('admin_transfermasuk/Assigned_view', $data);
        }
    }

    function get_ajax_Operator() {
        //ajax untuk menampilkan informasi SKU barang yang harus dipilih
        $WHRoleCode = $_POST['WHRoleCode'];
        $TransactionCode = $this->session->userdata('TransactionCode');
        $result = $this->admin_transfermasuk_model->getOperator($WHRoleCode, $TransactionCode);

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
                $data['Assigned'] = $this->admin_transfermasuk_model->getAssigned($TransactionCode);
                $data['Role'] = $this->admin_transfermasuk_model->getRole($TransactionCode);
                $this->load->view('admin_transfermasuk/Assigned_view', $data);
            } else {
                $TransactionCode = $this->session->userdata('TransactionCode');
                $OperatorCode = $this->input->post('Operator');
                $OprRole = $this->input->post('Role');
                if ($this->admin_transfermasuk_model->setAssigned($TransactionCode, $OperatorCode, $OprRole)) {
                    $data['Assigned'] = $this->admin_transfermasuk_model->getAssigned($TransactionCode);
                    $data['Role'] = $this->admin_transfermasuk_model->getRole($TransactionCode);
                    $data['pesan'] = "Penugasan Berhasil Ditambah";
                    $this->load->view('admin_transfermasuk/Assigned_view', $data);
                } else {
                    $data['Assigned'] = $this->admin_transfermasuk_model->getAssigned($TransactionCode);
                    $data['Role'] = $this->admin_transfermasuk_model->getRole($TransactionCode);
                    $data['error'] = "Penugasan Gagal Ditambah";
                    $this->load->view('admin_transfermasuk/Assigned_view', $data);
                }
            }
        }
    }
    function getAssigned($TransactionCode){
        $sql = "select d.TransactionCode,o.OperatorCode,o.Name,d.OprRole,r.Name as NamaRole,d.Assigned 
                from wms.DetailTaskOpr d
                inner join wms.WHRole r
                on r.WHRoleCode=d.OprRole
                inner join wms.Operator o
                on o.OperatorCode=d.OperatorCode
                where d.TransactionCode='".$TransactionCode."'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }
    function getRole($TransactionCode){
        $sql = "select r.WHRoleCode,r.Name
                from wms.MasterTaskRcv m
                inner join wms.ProjectWHRole p
                on p.ProjectCode=m.ProjectCode
                inner join wms.WHRole r
                on p.WHRoleCode=r.WHRoleCode
                where m.TransactionCode='".$TransactionCode."'";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }
    function getOperator($WHRoleCode,$TransactionCode){
        $sql = "select o.OperatorCode,o.Name
                from wms.OperatorWHRole ow
                inner join wms.Operator o
                on ow.OperatorCode=o.OperatorCode
                where ow.WHRoleCode='".$WHRoleCode."' and not exists 
                (select * from wms.DetailTaskOpr
                where TransactionCode='".$TransactionCode."' 
                and OprRole='".$WHRoleCode."' and OperatorCode=o.OperatorCode)";
        $result = $this->db->conn_id->prepare($sql);
        $result->execute();
        $result = $result->fetchAll();
        return $result;
    }
    function deleteAssigned($TransactionCode,$OperatorCode,$OprRole)
    {
        $sql = "delete from wms.DetailTaskOpr where TransactionCode='".$TransactionCode."' and OperatorCode='".$OperatorCode."' and OprRole='".$OprRole."'";
        $result = $this->db->conn_id->prepare($sql);
        if($result->execute())
        {
            return TRUE;
        }
        return false;
    }
    function setAssigned($TransactionCode,$OperatorCode,$OprRole)
    {
        $sql = "insert into wms.DetailTaskOpr(TransactionCode,OperatorCode,OprRole) 
            values('".$TransactionCode."','".$OperatorCode."','".$OprRole."')";
        $result = $this->db->conn_id->prepare($sql);
        if($result->execute())
        {
            return TRUE;
        }
        return false;
    }

}

?>
