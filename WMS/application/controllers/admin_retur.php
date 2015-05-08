<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of admin_retur
 *
 * @author USER
 */
class Admin_retur extends CI_Controller {

    //put your code here
    function __construct() {
        parent::__construct();

        //$this->set_session();
        if (!$this->session->userdata('OperatorCode') || $this->session->userdata("OperatorRole") != '10/WHR/000') {
            redirect(base_url() . "index.php/login");
        }
        $this->load->model('admin_retur_model');
        $this->load->model('barcode_model');
    }

    function index() {
        $this->load->view('admin_retur/admin_retur_view');
    }

    function tambah_transaksi_retur() {
        if ($this->input->post('btnTambahRetur')) {
            $this->form_validation->set_rules('retur[]', 'No Retur', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $data['cabang'] = $this->admin_retur_model->getCabang();
                $today = date("Y/m/d");
                if (count($data['cabang']) == 0) {
                    $cabang = '';
                } else {
                    $cabang = $data['cabang'][0]['Cabang'];
                }
                $data['retur'] = $this->admin_retur_model->getTodayReturList($today, $cabang);
                $this->load->view('admin_retur/tambah_transaksi_retur_view', $data);
            } else {
                $siteId = $this->session->userdata('siteId');
                $retur = $this->input->post('retur');
                $operatorCode = $this->session->userdata('OperatorCode');
                $tanggal = $this->session->userdata('ERPDate');
                $kode_proyek = 'RJT'; //kode Retur Principle    
                foreach ($retur as $row) {
                    $this->admin_retur_model->setRetur($kode_proyek, $row, $tanggal, $siteId, $operatorCode);
                }
                $this->session->set_flashdata('pesan', 'Transaksi retur berhasil ditambahkan.');
                $data['cabang'] = $this->admin_retur_model->getCabang();
                $today = date("Y/m/d");
                $data['retur'] = $this->admin_retur_model->getTodayReturList($today, $data['cabang'][0]['Cabang']);
                $this->load->view('admin_retur/tambah_transaksi_retur_view', $data);
            }
        } else {
            $data['cabang'] = $this->admin_retur_model->getCabang();
            $today = date("Y/m/d");
            if (count($data['cabang']) == 0) {
                $cabang = '';
            } else {
                $cabang = $data['cabang'][0]['Cabang'];
            }
            $data['retur'] = $this->admin_retur_model->getTodayReturList($today, $cabang);
            $this->load->view('admin_retur/tambah_transaksi_retur_view', $data);
        }
    }

    function get_ajax_tambah_transaksi_retur() {
        //ajax untuk menampilkan informasi SKU barang yang harus dipilih
        $tgl = $_POST['tgl'];
        $this->session->set_userdata('ERPDate',$tgl);
        $cabang = $_POST['cabang'];
        $result = $this->admin_retur_model->getTodayReturList($tgl, $cabang);
        $arr = array();
        foreach ($result as $row) {
            $arr[] = array("NoPicklist" => $row['NoPicklist'], "TglPicklist" => date('d-m-Y', strtotime($row['Tgl'])), "Retur" => $row['Retur'], "Tolakan" => $row['Tolakan']);
        }
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }

    function detail_transaksi_retur($transactionCode) {
        $transactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $transactionCode)));
        $data['transaksi'] = $this->admin_retur_model->getDetailTransaction($transactionCode);
        $data['detail_transaksi'] = $this->admin_retur_model->getDetailTransaction2($transactionCode,  $this->session->userdata('ReceiveSource'));
        $this->load->view('admin_retur/daftar_detail_transaksi_retur_view', $data);
    }

    function history_detail_transaksi($transactionCode, $NoUrut) {
        $transactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $transactionCode)));
        $NoUrut = base64_decode(str_replace('-', '=', str_replace('_', '/', $NoUrut)));
        $data['detail_transaksi'] = $this->admin_retur_model->getDetailTransaction4($transactionCode, $NoUrut);
        $data['history_detail_transaksi'] = $this->admin_retur_model->getDetailTransaction3($transactionCode, $NoUrut);
        $this->load->view('admin_retur/daftar_history_detail_transaksi_retur_view', $data);
    }

    function daftar_transaksi_retur() {
        $data['retur'] = $this->admin_retur_model->getTodayTransaksiReturList();
        $this->load->view('admin_retur/daftar_transaksi_retur_view', $data);
    }

    function edit_note($TransactionCode, $NoUrut, $Status2) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $NoUrut = base64_decode(str_replace('-', '=', str_replace('_', '/', $NoUrut)));
        $Status2 = base64_decode(str_replace('-', '=', str_replace('_', '/', $Status2)));
        $data['note'] = $this->admin_retur_model->getNote($TransactionCode, $NoUrut);
        $data['Status2'] = $Status2;
        $this->load->view('admin_retur/edit_note_view', $data);
    }

    function simpan_note() {
        if ($this->input->post('btnSimpan')) {
            $TransactionCode = $this->input->post('TransactionCode');
            $NoUrut = $this->input->post('NoUrut');
            $Note = $this->input->post('note');

            if ($this->admin_retur_model->setNote($TransactionCode, $NoUrut, $Note)) {
                $data['transaksi'] = $this->admin_retur_model->getDetailTransaction($TransactionCode);
                $data['detail_transaksi'] = $this->admin_retur_model->getDetailTransaction2($TransactionCode,$this->session->userdata('ReceiveSource'));
                $this->load->view('admin_retur/daftar_detail_transaksi_retur_view', $data);
            } else {
                $data['note'] = $this->admin_retur_model->getNote($TransactionCode, $NoUrut);
                $data['error'] = "Gagal Menyimpan";
                $this->load->view('admin_retur/edit_note_view', $data);
            }
        }
    }

    function edit_destrack($TransactionCode, $NoUrut, $DestRackSlot) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $NoUrut = base64_decode(str_replace('-', '=', str_replace('_', '/', $NoUrut)));
        $DestRackSlot = base64_decode(str_replace('-', '=', str_replace('_', '/', $DestRackSlot)));
        $data['TransactionCode'] = $TransactionCode;
        $data['NoUrut'] = $NoUrut;
        $data['DestRackSlot'] = $DestRackSlot;
        $data['rack'] = $this->barcode_model->getAllBarcodeRack();
        $this->load->view('admin_retur/select_rackslot_view', $data);
    }

    function simpan_destrack() {

        if ($this->input->post('btnPilih')) {
            $TransactionCode = $this->input->post('TransactionCode');
            $NoUrut = $this->input->post('NoUrut');
            $RackSlotCode = $this->input->post('rackslotcode');
            $this->admin_retur_model->setDestRackSlot($TransactionCode, $NoUrut, $RackSlotCode);
            $data['transaksi'] = $this->admin_retur_model->getDetailTransaction($TransactionCode);
            $data['detail_transaksi'] = $this->admin_retur_model->getDetailTransaction2($TransactionCode,$this->session->userdata('ReceiveSource'));
            $this->load->view('admin_retur/daftar_detail_transaksi_retur_view', $data);
        }
    }

    function edit_mastertaskrcv($TransactionCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        if ($TransactionCode == '') {
            $TransactionCode = $this->session->userdata('TransactionCode');
        }
        $result = $this->admin_retur_model->getstatustask($TransactionCode);
        if ($result['isCancel'] == 0) {
            $status = $this->admin_retur_model->getstatustask($TransactionCode);
            //$this->session->set_userdata('TransactionCode', $TransactionCode);
            $data['isFinishMove'] = $result['isFinishMove'];
            $data['note'] = $this->admin_retur_model->getNoteMaster($TransactionCode);
            $this->load->view('admin_retur/edit_note_master_view', $data);
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
            $kode_proyek = 'RJT'; //kode BPB Principle    

            $this->admin_retur_model->setRetur($kode_proyek, $ERPCode, $tanggal, $siteId, $operatorCode);
            $this->admin_retur_model->setNoteMaster($TransactionCode, $Note);

            $this->session->set_flashdata('pesan', 'Transaksi Retur berhasil ditambahkan.');
            $this->daftar_transaksi_Retur();
        }
    }

    function cek_finishmove($TransactionCode, $ERPCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $ERPCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $ERPCode)));
        $OutstandingMove = $this->admin_retur_model->cekOutstandingFinishMove($TransactionCode);
        if (count($OutstandingMove) > 0) {
            $data['outstanding'] = $OutstandingMove;
            $data['TransactionCode'] = $TransactionCode;
            $this->session->set_userdata('TransactionCode', $TransactionCode);
            $this->session->set_userdata('ERPCode', $ERPCode);
            $data['ERPCode'] = $ERPCode;
            $this->load->view('admin_retur/outstanding_finishmove_view', $data);
        } else {
            $OperatorCode = $this->session->userdata('OperatorCode');
            if ($this->admin_retur_model->setFinishMove($TransactionCode, $OperatorCode)) {
                $data['pesan'] = 'Transaksi BPB Berhasil Menyelesaikan Perpindahan Barang.';
                $data['note'] = $this->admin_retur_model->getNoteMaster($TransactionCode);
                $data['isFinishMove'] = 1;
                $this->load->view('admin_retur/edit_note_master_view', $data);
            } else {
                $data['error'] = "Transaksi BPB Gagal Menyelesaikan Perpindahan Barang.";
                $data['isFinishMove'] = 0;
                $data['note'] = $this->admin_retur_model->getNoteMaster($TransactionCode);
                $this->load->view('admin_retur/edit_note_master_view', $data);
            }
        }
    }

    function simpan_clear_outstanding_finishmove() {
        if ($this->input->post('btnSimpanClear')) {
            $this->form_validation->set_rules('noteclear', 'Catatan Penyelesaian Outstanding', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $data['outstanding'] = $this->admin_retur_model->cekOutstandingFinishMove($this->session->userdata('TransactionCode'));
                $data['TransactionCode'] = $this->session->userdata('TransactionCode');
                $data['ERPCode'] = $this->session->userdata('ERPCode');
                $this->load->view('admin_retur/outstanding_finishmove_view', $data);
            } else {
                $TransactionCode = $this->input->post('TransactionCode');
                $NoUrut = $this->input->post('NoUrut');
                $QueueNumber = $this->input->post('QueueNumber');
                $OperatorCode = $this->session->userdata('OperatorCode');
                $Note = $this->admin_retur_model->getNote($TransactionCode, $NoUrut);
                $Notelama = $Note['Note'];
                $Notebaru = $this->input->post('noteclear');
                $Notelama.='#' . $Notebaru;
                $this->admin_retur_model->setNote($TransactionCode, $NoUrut, $Notelama);
                $this->admin_retur_model->setNotePembatalan($TransactionCode, $NoUrut, $QueueNumber, $OperatorCode,  $this->session->userdata('ReceiveProblem'));

                $data['pesan'] = 'Outstanding Berhasil DiClear.';
                $data['outstanding'] = $this->admin_retur_model->cekOutstandingFinishMove($this->session->userdata('TransactionCode'));
                if (count($data['outstanding']) > 0) {
                    $data['TransactionCode'] = $this->session->userdata('TransactionCode');
                    $data['ERPCode'] = $this->session->userdata('ERPCode');
                    $this->load->view('admin_retur/outstanding_finishmove_view', $data);
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
        $OutstandingDERP = $this->admin_retur_model->cekOutstandingFinishAdmin($TransactionCode);
        if (count($OutstandingDERP) > 0) {
            $data['outstanding'] = $OutstandingDERP;
            $data['TransactionCode'] = $TransactionCode;
            $data['penyelesaian'] = $this->admin_retur_model->getNotaPenyelesaian($TransactionCode);
            $this->session->set_userdata('TransactionCode', $TransactionCode);
            $this->session->set_userdata('ERPCode', $ERPCode);
            $data['ERPCode'] = $ERPCode;
            $this->load->view('admin_retur/outstanding_finishadmin_view', $data);
        } else {
            $OperatorCode = $this->session->userdata('OperatorCode');
            if ($this->admin_retur_model->setFinishAdmin($TransactionCode, $OperatorCode)) {
                //$this->session->set_flashdata('pesan', 'Transaksi BPB Berhasil Selesai.');
                $this->daftar_transaksi_retur();
            } else {
                $data['error'] = "Transaksi BPB Gagal Selesai.";
                $data['isFinishMove'] = 0;
                $data['note'] = $this->admin_retur_model->getNoteMaster($TransactionCode);
                $this->load->view('admin_retur/edit_note_master_view', $data);
            }
        }
    }

    function tambah_notapenyelesaian() {
        if ($this->input->post('btnSimpan')) {
            $this->form_validation->set_rules('kodenota', 'Kode Nota', 'required|callback_cek_kodenota');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $data['TransactionCode'] = $this->session->userdata('TransactionCode');
                $OutstandingDERP = $this->admin_retur_model->cekOutstandingFinishAdmin($data['TransactionCode']);
                $data['outstanding'] = $OutstandingDERP;

                $data['penyelesaian'] = $this->admin_retur_model->getNotaPenyelesaian($data['TransactionCode']);
                $data['ERPCode'] = $this->session->userdata('ERPCode');
                $this->load->view('admin_retur/outstanding_finishadmin_view', $data);
            } else {
                $TransactionCode = $this->session->userdata('TransactionCode');
                $KodeNota = $this->input->post('kodenota');
                $Tipe = $this->input->post('tipe');
                if ($this->admin_retur_model->setPenyelesaian($TransactionCode, $KodeNota, $Tipe)) {
                    $data['pesan'] = 'Kode Nota Berhasil Ditambahkan.';
                    $data['TransactionCode'] = $this->session->userdata('TransactionCode');
                    $OutstandingDERP = $this->admin_retur_model->cekOutstandingFinishAdmin($data['TransactionCode']);
                    if (count($OutstandingDERP) > 0) {
                        $data['outstanding'] = $OutstandingDERP;
                        $data['penyelesaian'] = $this->admin_retur_model->getNotaPenyelesaian($data['TransactionCode']);
                        $data['ERPCode'] = $this->session->userdata('ERPCode');
                        $this->load->view('admin_retur/outstanding_finishadmin_view', $data);
                    } else {
                        $this->edit_mastertaskrcv(str_replace('=', '-', str_replace('/', '_', base64_encode($this->session->userdata('TransactionCode')))));
                        //$this->cek_finishmove(str_replace('=', '-', str_replace('/', '_', base64_encode($this->session->userdata('TransactionCode'))),str_replace('=', '-', str_replace('/', '_', base64_encode($this->session->userdata('ERPCode'))))));
                    }
                } else {
                    $data['TransactionCode'] = $this->session->userdata('TransactionCode');
                    $data['error'] = 'Kode Nota Gagal Ditambahkan.';
                    $OutstandingDERP = $this->admin_retur_model->cekOutstandingFinishAdmin($data['TransactionCode']);
                    $data['outstanding'] = $OutstandingDERP;
                    $data['penyelesaian'] = $this->admin_retur_model->getNotaPenyelesaian($data['TransactionCode']);
                    $data['ERPCode'] = $this->session->userdata('ERPCode');
                    $this->load->view('admin_retur/outstanding_finishadmin_view', $data);
                }
            }
        }
    }

    function hapus_kodenota($TransactionCode, $KodeNota) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $KodeNota = base64_decode(str_replace('-', '=', str_replace('_', '/', $KodeNota)));
        if ($this->admin_retur_model->removePenyelesaian($TransactionCode, $KodeNota)) {
            $data['pesan'] = 'Kode Nota Berhasil Dihapus.';
        } else {
            $data['error'] = 'Kode Nota Gagal Dihapus.';
        }
        $data['TransactionCode'] = $this->session->userdata('TransactionCode');
        $OutstandingDERP = $this->admin_retur_model->cekOutstandingFinishAdmin($data['TransactionCode']);
        $data['outstanding'] = $OutstandingDERP;
        $data['penyelesaian'] = $this->admin_retur_model->getNotaPenyelesaian($data['TransactionCode']);
        $data['ERPCode'] = $this->session->userdata('ERPCode');
        $this->load->view('admin_retur/outstanding_finishadmin_view', $data);
    }

    function cek_kodenota($KodeNota) {
        $Tipe = $this->input->post('tipe');
        if ($this->admin_retur_model->cekKodeNota($KodeNota, $Tipe, $this->session->userdata('ERPCode'))) {
            return true;
        }
        $this->form_validation->set_message('cek_kodenota', '%s tidak valid.');
        return false;
    }

    function export_excel($TransactionCode, $ERPCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $ERPCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $ERPCode)));
        $OutstandingDERP = $this->admin_retur_model->cekOutstandingFinishAdmin($TransactionCode);
        if (count($OutstandingDERP) > 0) {
            /** Include PHPExcel */
            require_once APPPATH . "/third_party/phpexcel/PHPExcel.php";
            // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();

            // Set document properties
            $objPHPExcel->getProperties()->setCreator($this->session->userdata('OperatorCode'))
                    ->setLastModifiedBy($this->session->userdata('OperatorCode'))
                    ->setTitle("Masalah Receiving Retur " . $ERPCode)
                    ->setSubject("Masalah Receiving Retur " . $ERPCode)
                    ->setDescription("Masalah Receiving Retur " . $ERPCode)
                    ->setKeywords("Masalah Receiving Retur " . $ERPCode)
                    ->setCategory("Masalah Receiving Retur " . $ERPCode);



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
            //$objPHPExcel->getActiveSheet()->setTitle("Masalah Receiving Retur " . $ERPCode);
            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            $judul = "\"Masalah Retur " . date('d-m-Y') . ".xls\"";

            // Redirect output to a client’s web browser (Excel5)
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename=' . $judul);
            header('Cache-Control: max-age=0');


            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
        } else {
            $data['TransactionCode'] = $TransactionCode;
            $data['error'] = 'Tidak Ada Data yang Dapat Dieskport';
            $data['outstanding'] = $OutstandingDERP;
            $data['penyelesaian'] = $this->admin_retur_model->getNotaPenyelesaian($TransactionCode);
            $data['ERPCode'] = $ERPCode;
            $this->load->view('admin_retur/outstanding_finishadmin_view', $data);
        }
    }

    function simpan_pembatalanrcv() {
        if ($this->input->post('btnSimpanCancel')) {
            $this->form_validation->set_rules('notecancel', 'Catatan Pembatalan', 'required');
            $this->form_validation->set_rules('TransactionCode', 'TransactionCode', 'required|callback_cek_detailpembatalan');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $TransactionCode = $this->input->post('TransactionCode');
                $result = $this->admin_retur_model->getstatustask($TransactionCode);
                $data['isFinishMove'] = $result['isFinishMove'];
                $data['note'] = $this->admin_retur_model->getNoteMaster($TransactionCode);
                $this->load->view('admin_retur/edit_note_master_view', $data);
            } else {
                $TransactionCode = $this->input->post('TransactionCode');
                $ERPCode = $this->input->post('ERPCode');
                $siteId = $this->session->userdata('siteId');
                $operatorCode = $this->session->userdata('OperatorCode');
                $tanggal = date("Y/m/d");
                $kode_proyek = 'RJT'; //kode BPB Principle 
                $Note = $this->admin_retur_model->getNoteMaster($TransactionCode);
                $Notelama = $Note['Note'];
                $Notebaru = $this->input->post('notecancel');
                $Notelama.='#' . $Notebaru;


                //$this->admin_retur_model->setBPB($kode_proyek, $ERPCode, $tanggal, $siteId, $operatorCode);
                $this->admin_retur_model->setNoteMasterPembatalan($TransactionCode, $Notelama, $operatorCode);
                $this->daftar_transaksi_retur();
            }
        }
    }

    function batal_finish($TransactionCode, $ERPCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $ERPCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $ERPCode)));
        if ($this->admin_retur_model->buka_finishmove($TransactionCode)) {
            $this->daftar_transaksi_retur();
        } else {
            $data['TransactionCode'] = $TransactionCode;
            $data['error'] = 'Penambahan Receiving Gagal';
            $data['outstanding'] = $OutstandingDERP;
            $data['penyelesaian'] = $this->admin_retur_model->getNotaPenyelesaian($TransactionCode);
            $data['ERPCode'] = $ERPCode;
            $this->load->view('admin_retur/outstanding_finishadmin_view', $data);
        }
    }

    function cek_detailpembatalan($kode) {
        if ($this->admin_retur_model->cekPembatalan($kode)) {
            return TRUE;
        }
        $this->form_validation->set_message('cek_detailpembatalan', 'Task Sudah Berjalan Tidak Dapat Dibatalkan');
        return FALSE;
    }
    
    public function show_assigned($TransactionCode, $ERPCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $ERPCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $ERPCode)));
        $this->session->set_userdata('ERPCode', $ERPCode);
        $this->session->set_userdata('TransactionCode', $TransactionCode);
        $data['Assigned'] = $this->admin_retur_model->getAssigned($TransactionCode);
        $data['Role'] = $this->admin_retur_model->getRole($TransactionCode);
        $this->load->view('admin_retur/Assigned_view', $data);
    }

    public function delete_assigned($OperatorCode, $OprRole) {
        $TransactionCode = $this->session->userdata('TransactionCode');
        $OperatorCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $OperatorCode)));
        $OprRole = base64_decode(str_replace('-', '=', str_replace('_', '/', $OprRole)));
        if ($this->admin_retur_model->deleteAssigned($TransactionCode, $OperatorCode, $OprRole)) {
            $data['Assigned'] = $this->admin_retur_model->getAssigned($TransactionCode);
            $data['Role'] = $this->admin_retur_model->getRole($TransactionCode);
            $data['pesan'] = "Penugasan Berhasil Dihapus";
            $this->load->view('admin_retur/Assigned_view', $data);
        } else {
            $data['Assigned'] = $this->admin_retur_model->getAssigned($TransactionCode);
            $data['Role'] = $this->admin_retur_model->getRole($TransactionCode);
            $data['error'] = "Penugasan Gagal Dihapus";
            $this->load->view('admin_retur/Assigned_view', $data);
        }
    }

    function get_ajax_Operator() {
        //ajax untuk menampilkan informasi SKU barang yang harus dipilih
        $WHRoleCode = $_POST['WHRoleCode'];
        $TransactionCode = $this->session->userdata('TransactionCode');
        $result = $this->admin_retur_model->getOperator($WHRoleCode, $TransactionCode);

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
                $data['Assigned'] = $this->admin_retur_model->getAssigned($TransactionCode);
                $data['Role'] = $this->admin_retur_model->getRole($TransactionCode);
                $this->load->view('admin_retur/Assigned_view', $data);
            } else {
                $TransactionCode = $this->session->userdata('TransactionCode');
                $OperatorCode = $this->input->post('Operator');
                $OprRole = $this->input->post('Role');
                if ($this->admin_retur_model->setAssigned($TransactionCode, $OperatorCode, $OprRole)) {
                    $data['Assigned'] = $this->admin_retur_model->getAssigned($TransactionCode);
                    $data['Role'] = $this->admin_retur_model->getRole($TransactionCode);
                    $data['pesan'] = "Penugasan Berhasil Ditambah";
                    $this->load->view('admin_retur/Assigned_view', $data);
                } else {
                    $data['Assigned'] = $this->admin_retur_model->getAssigned($TransactionCode);
                    $data['Role'] = $this->admin_retur_model->getRole($TransactionCode);
                    $data['error'] = "Penugasan Gagal Ditambah";
                    $this->load->view('admin_retur/Assigned_view', $data);
                }
            }
        }
    }

}

?>
