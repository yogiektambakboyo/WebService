<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of transaksi
 *
 * @author USER
 */
class transaksi extends CI_Controller {

    //put your code here
    function __construct() {
        parent::__construct();

        //$this->set_session();
        if (!$this->session->userdata('OperatorCode') || $this->session->userdata("OperatorRole") != '10/WHR/000') {
            redirect(base_url() . "index.php/login");
        }
        $this->load->model('transaksi_model');
        $this->load->model('barcode_model');
    }

    function index() {
        $this->load->view('transaksi/transaksi_main_view');
    }

    function bpb() {
        $this->load->view('transaksi/transaksi_bpb_view');
    }

    function tambah_transaksi_bpb() {
        if ($this->input->post('btnTambahBPB')) {
            $this->form_validation->set_rules('bpb[]', 'No BPB', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $data['cabang'] = $this->transaksi_model->getCabang();
                $today = date("Y/m/d");
                if (count($data['cabang']) == 0) {
                    $cabang = '';
                } else {
                    $cabang = $data['cabang'][0]['Cabang'];
                }
                $data['bpb'] = $this->transaksi_model->getTodayBPBList($today, $cabang);
                $this->load->view('transaksi/tambah_transaksi_bpb_view', $data);
            } else {
                $siteId = $this->session->userdata('siteId');
                $bpb = $this->input->post('bpb');
                $operatorCode = $this->session->userdata('OperatorCode');
                $tanggal = $this->session->userdata('ERPDate');
                $kode_proyek = 'BPB'; //kode BPB Principle    
                foreach ($bpb as $row) {
                    $this->transaksi_model->setBPB($kode_proyek, $row, $tanggal, $siteId, $operatorCode);
                }
                $this->session->set_flashdata('pesan', 'Transaksi BPB berhasil ditambahkan.');
                //redirect(base_url() . "index.php/transaksi");
                $data['cabang'] = $this->transaksi_model->getCabang();
                $today = date("Y/m/d");
                $data['bpb'] = $this->transaksi_model->getTodayBPBList($today, $data['cabang'][0]['Cabang']);
                $this->load->view('transaksi/tambah_transaksi_bpb_view', $data);
            }
        } else {
            $data['cabang'] = $this->transaksi_model->getCabang();
            $today = date("Y/m/d");
            if (count($data['cabang']) == 0) {
                $cabang = '';
            } else {
                $cabang = $data['cabang'][0]['Cabang'];
            }
            $data['bpb'] = $this->transaksi_model->getTodayBPBList($today, $cabang);
            $this->load->view('transaksi/tambah_transaksi_bpb_view', $data);
        }
    }

    function get_ajax_tambah_transaksi_bpb() {
        //ajax untuk menampilkan informasi SKU barang yang harus dipilih
        $tgl = $_POST['tgl'];
        $this->session->set_userdata('ERPDate', $tgl);
        $cabang = $_POST['cabang'];
        $result = $this->transaksi_model->getTodayBPBList($tgl, $cabang);
        $arr = array();
        foreach ($result as $row) {
            $arr[] = array("Kodenota" => $row['Kodenota'], "Perusahaan" => $row['Perusahaan'], "Keterangan" => $row['Keterangan']);
        }
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }

    function detail_transaksi_bpb($transactionCode) {
        $transactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $transactionCode)));
        $data['transaksi'] = $this->transaksi_model->getDetailTransaction($transactionCode);
        $data['detail_transaksi'] = $this->transaksi_model->getDetailTransaction2($transactionCode, $this->session->userdata('ReceiveSource'));
        $this->load->view('transaksi/daftar_detail_transaksi_bpb_view', $data);
    }

    function history_detail_transaksi($transactionCode, $NoUrut) {
        $transactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $transactionCode)));
        $NoUrut = base64_decode(str_replace('-', '=', str_replace('_', '/', $NoUrut)));
        $data['detail_transaksi'] = $this->transaksi_model->getDetailTransaction4($transactionCode, $NoUrut);
        $data['history_detail_transaksi'] = $this->transaksi_model->getDetailTransaction3($transactionCode, $NoUrut);
        $this->load->view('transaksi/daftar_history_detail_transaksi_bpb_view', $data);
    }

    function daftar_transaksi_bpb() {
        $data['bpb'] = $this->transaksi_model->getTodayTransaksiBPBList();
        $this->load->view('transaksi/daftar_transaksi_bpb_view', $data);
    }

    function edit_note($TransactionCode, $NoUrut, $Status2) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $NoUrut = base64_decode(str_replace('-', '=', str_replace('_', '/', $NoUrut)));
        $Status2 = base64_decode(str_replace('-', '=', str_replace('_', '/', $Status2)));
        $data['note'] = $this->transaksi_model->getNote($TransactionCode, $NoUrut);
        $data['Status2'] = $Status2;
        $this->load->view('transaksi/edit_note_view', $data);
    }

    function simpan_note() {
        if ($this->input->post('btnSimpan')) {
            $TransactionCode = $this->input->post('TransactionCode');
            $NoUrut = $this->input->post('NoUrut');
            $Note = $this->input->post('note');

            if ($this->transaksi_model->setNote($TransactionCode, $NoUrut, $Note)) {
                $data['transaksi'] = $this->transaksi_model->getDetailTransaction($TransactionCode);
                $data['detail_transaksi'] = $this->transaksi_model->getDetailTransaction2($TransactionCode, $this->session->userdata('ReceiveSource'));
                $this->load->view('transaksi/daftar_detail_transaksi_bpb_view', $data);
            } else {
                $data['note'] = $this->transaksi_model->getNote($TransactionCode, $NoUrut);
                $data['error'] = "Gagal Menyimpan";
                $this->load->view('transaksi/edit_note_view', $data);
            }
        }
    }

    function cek_detailpembatalan($kode) {
        if ($this->transaksi_model->cekPembatalan($kode)) {
            return TRUE;
        }
        $this->form_validation->set_message('cek_detailpembatalan', 'Task Sudah Berjalan Tidak Dapat Dibatalkan');
        return FALSE;
    }

    function edit_destrack($TransactionCode, $NoUrut, $DestRackSlot = '') {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $NoUrut = base64_decode(str_replace('-', '=', str_replace('_', '/', $NoUrut)));
        if ($DestRackSlot != '') {
            $DestRackSlot = base64_decode(str_replace('-', '=', str_replace('_', '/', $DestRackSlot)));
        } else {
            $DestRackSlot = 'kosong';
        }
        $data['TransactionCode'] = $TransactionCode;
        $data['NoUrut'] = $NoUrut;
        $data['DestRackSlot'] = $DestRackSlot;
        $data['rack'] = $this->barcode_model->getAllBarcodeRack();
        $this->load->view('transaksi/select_rackslot_view', $data);
    }

    function simpan_destrack() {

        if ($this->input->post('btnPilih')) {
            $TransactionCode = $this->input->post('TransactionCode');
            $NoUrut = $this->input->post('NoUrut');
            $RackSlotCode = $this->input->post('rackslotcode');
            $this->transaksi_model->setDestRackSlot($TransactionCode, $NoUrut, $RackSlotCode);
            $data['transaksi'] = $this->transaksi_model->getDetailTransaction($TransactionCode);
            $data['detail_transaksi'] = $this->transaksi_model->getDetailTransaction2($TransactionCode, $this->session->userdata('ReceiveSource'));
            $this->load->view('transaksi/daftar_detail_transaksi_bpb_view', $data);
        }
    }

    function edit_mastertaskrcv($TransactionCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        if ($TransactionCode == '') {
            $TransactionCode = $this->session->userdata('TransactionCode');
        }
        $result = $this->transaksi_model->getstatustask($TransactionCode);
        if ($result['isCancel'] == 0) {
            // $status = $this->transaksi_model->getstatustask($TransactionCode);
            $this->session->set_userdata('TransactionCode', $TransactionCode);
            $data['isFinishMove'] = $result['isFinishMove'];
            $data['note'] = $this->transaksi_model->getNoteMaster($TransactionCode);
            $this->load->view('transaksi/edit_note_master_view', $data);
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
            $kode_proyek = 'BPB'; //kode BPB Principle    

            $this->transaksi_model->setBPB($kode_proyek, $ERPCode, $tanggal, $siteId, $operatorCode);
            $this->transaksi_model->setNoteMaster($TransactionCode, $Note);

            $this->session->set_flashdata('pesan', 'Transaksi BPB berhasil ditambahkan.');
            $data['pesan'] = 'Transaksi BPB berhasil ditambahkan.';
            $this->daftar_transaksi_bpb();
        }
    }

    function cek_finishmove($TransactionCode, $ERPCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $ERPCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $ERPCode)));
        $OutstandingMove = $this->transaksi_model->cekOutstandingFinishMove($TransactionCode);
        if (count($OutstandingMove) > 0) {
            $data['outstanding'] = $OutstandingMove;
            $data['TransactionCode'] = $TransactionCode;
            $this->session->set_userdata('TransactionCode', $TransactionCode);
            $this->session->set_userdata('ERPCode', $ERPCode);
            $data['ERPCode'] = $ERPCode;
            $this->load->view('transaksi/outstanding_finishmove_view', $data);
        } else {
            $OperatorCode = $this->session->userdata('OperatorCode');
            if ($this->transaksi_model->setFinishMove($TransactionCode, $OperatorCode)) {
                $data['pesan'] = 'Transaksi BPB Berhasil Menyelesaikan Perpindahan Barang.';
                $data['note'] = $this->transaksi_model->getNoteMaster($TransactionCode);
                $data['isFinishMove'] = 1;
                $this->load->view('transaksi/edit_note_master_view', $data);
            } else {
                $data['error'] = "Transaksi BPB Gagal Menyelesaikan Perpindahan Barang.";
                $data['isFinishMove'] = 0;
                $data['note'] = $this->transaksi_model->getNoteMaster($TransactionCode);
                $this->load->view('transaksi/edit_note_master_view', $data);
            }
        }
    }

    function simpan_clear_outstanding_finishmove() {
        if ($this->input->post('btnSimpanClear')) {
            $this->form_validation->set_rules('noteclear', 'Catatan Penyelesaian Outstanding', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $data['outstanding'] = $this->transaksi_model->cekOutstandingFinishMove($this->session->userdata('TransactionCode'));
                $data['TransactionCode'] = $this->session->userdata('TransactionCode');
                $data['ERPCode'] = $this->session->userdata('ERPCode');
                $this->load->view('transaksi/outstanding_finishmove_view', $data);
            } else {
                $TransactionCode = $this->input->post('TransactionCode');
                $NoUrut = $this->input->post('NoUrut');
                $QueueNumber = $this->input->post('QueueNumber');
                $OperatorCode = $this->session->userdata('OperatorCode');
                $Note = $this->transaksi_model->getNote($TransactionCode, $NoUrut);
                $Notelama = $Note['Note'];
                $Notebaru = $this->input->post('noteclear');
                $Notelama.='#' . $Notebaru;
                $this->transaksi_model->setNote($TransactionCode, $NoUrut, $Notelama);
                $this->transaksi_model->setNotePembatalan($TransactionCode, $NoUrut, $QueueNumber, $OperatorCode, $this->session->userdata('ReceiveProblem'));

                $data['pesan'] = 'Outstanding Berhasil DiClear.';
                $data['outstanding'] = $this->transaksi_model->cekOutstandingFinishMove($this->session->userdata('TransactionCode'));
                if (count($data['outstanding']) > 0) {
                    $data['TransactionCode'] = $this->session->userdata('TransactionCode');
                    $data['ERPCode'] = $this->session->userdata('ERPCode');
                    $this->load->view('transaksi/outstanding_finishmove_view', $data);
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
        $tanggal = $this->transaksi_model->getTransactionDate($TransactionCode);
        $kode_proyek = 'BPB'; //kode BPB Principle    
        $this->transaksi_model->setBPB($kode_proyek, $ERPCode, $tanggal, $siteId, $operatorCode);

        $OutstandingDERP = $this->transaksi_model->cekOutstandingFinishAdmin($TransactionCode);
        if (count($OutstandingDERP) > 0) {
            $data['outstanding'] = $OutstandingDERP;
            $data['TransactionCode'] = $TransactionCode;
            $data['penyelesaian'] = $this->transaksi_model->getNotaPenyelesaian($TransactionCode);
            $this->session->set_userdata('TransactionCode', $TransactionCode);
            $this->session->set_userdata('ERPCode', $ERPCode);
            $data['ERPCode'] = $ERPCode;

            $this->load->view('transaksi/outstanding_finishadmin_view', $data);
        } else {
            $OperatorCode = $this->session->userdata('OperatorCode');
            if ($this->transaksi_model->setFinishAdmin($TransactionCode, $OperatorCode)) {
                $this->session->set_flashdata('pesan', 'Transaksi BPB Berhasil Selesai.');
                $this->daftar_transaksi_bpb();
            } else {
                $data['error'] = "Transaksi BPB Gagal Selesai.";
                $data['isFinishMove'] = 0;
                $data['note'] = $this->transaksi_model->getNoteMaster($TransactionCode);
                $this->load->view('transaksi/edit_note_master_view', $data);
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
                $result = $this->transaksi_model->getstatustask($TransactionCode);
                $data['isFinishMove'] = $result['isFinishMove'];
                $data['note'] = $this->transaksi_model->getNoteMaster($TransactionCode);
                $this->load->view('transaksi/edit_note_master_view', $data);
            } else {
                $TransactionCode = $this->input->post('TransactionCode');
                $ERPCode = $this->input->post('ERPCode');
                $siteId = $this->session->userdata('siteId');
                $operatorCode = $this->session->userdata('OperatorCode');
                $tanggal = date("Y/m/d");
                $kode_proyek = 'BPB'; //kode BPB Principle 
                $Note = $this->transaksi_model->getNoteMaster($TransactionCode);
                $Notelama = $Note['Note'];
                $Notebaru = $this->input->post('notecancel');
                $Notelama.='#' . $Notebaru;


                //$this->transaksi_model->setBPB($kode_proyek, $ERPCode, $tanggal, $siteId, $operatorCode);
                $this->transaksi_model->setNoteMasterPembatalan($TransactionCode, $Notelama, $operatorCode);
                $this->daftar_transaksi_bpb();
            }
        }
    }

    function tambah_notapenyelesaian() {
        if ($this->input->post('btnSimpan')) {
            $this->form_validation->set_rules('kodenota', 'Kode Nota', 'required|callback_cek_kodenota');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $data['TransactionCode'] = $this->session->userdata('TransactionCode');
                $OutstandingDERP = $this->transaksi_model->cekOutstandingFinishAdmin($data['TransactionCode']);
                $data['outstanding'] = $OutstandingDERP;

                $data['penyelesaian'] = $this->transaksi_model->getNotaPenyelesaian($data['TransactionCode']);
                $data['ERPCode'] = $this->session->userdata('ERPCode');
                $this->load->view('transaksi/outstanding_finishadmin_view', $data);
            } else {
                $TransactionCode = $this->session->userdata('TransactionCode');
                $KodeNota = $this->input->post('kodenota');
                $Tipe = $this->input->post('tipe');
                if ($this->transaksi_model->setPenyelesaian($TransactionCode, $KodeNota, $Tipe)) {
                    $data['pesan'] = 'Kode Nota Berhasil Ditambahkan.';
                    $data['TransactionCode'] = $this->session->userdata('TransactionCode');
                    $OutstandingDERP = $this->transaksi_model->cekOutstandingFinishAdmin($data['TransactionCode']);
                    if (count($OutstandingDERP) > 0) {
                        $data['outstanding'] = $OutstandingDERP;
                        $data['penyelesaian'] = $this->transaksi_model->getNotaPenyelesaian($data['TransactionCode']);
                        $data['ERPCode'] = $this->session->userdata('ERPCode');
                        $this->load->view('transaksi/outstanding_finishadmin_view', $data);
                    } else {
                        $this->edit_mastertaskrcv(str_replace('=', '-', str_replace('/', '_', base64_encode($this->session->userdata('TransactionCode')))));
                        //$this->cek_finishmove(str_replace('=', '-', str_replace('/', '_', base64_encode($this->session->userdata('TransactionCode'))),str_replace('=', '-', str_replace('/', '_', base64_encode($this->session->userdata('ERPCode'))))));
                    }
                } else {
                    $data['TransactionCode'] = $this->session->userdata('TransactionCode');
                    $data['error'] = 'Kode Nota Gagal Ditambahkan.';
                    $OutstandingDERP = $this->transaksi_model->cekOutstandingFinishAdmin($data['TransactionCode']);
                    $data['outstanding'] = $OutstandingDERP;
                    $data['penyelesaian'] = $this->transaksi_model->getNotaPenyelesaian($data['TransactionCode']);
                    $data['ERPCode'] = $this->session->userdata('ERPCode');
                    $this->load->view('transaksi/outstanding_finishadmin_view', $data);
                }
            }
        }
    }

    function hapus_kodenota($TransactionCode, $KodeNota) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $KodeNota = base64_decode(str_replace('-', '=', str_replace('_', '/', $KodeNota)));
        if ($this->transaksi_model->removePenyelesaian($TransactionCode, $KodeNota)) {
            $data['pesan'] = 'Kode Nota Berhasil Dihapus.';
        } else {
            $data['error'] = 'Kode Nota Gagal Dihapus.';
        }
        $data['TransactionCode'] = $this->session->userdata('TransactionCode');
        $OutstandingDERP = $this->transaksi_model->cekOutstandingFinishAdmin($data['TransactionCode']);
        $data['outstanding'] = $OutstandingDERP;
        $data['penyelesaian'] = $this->transaksi_model->getNotaPenyelesaian($data['TransactionCode']);
        $data['ERPCode'] = $this->session->userdata('ERPCode');
        $this->load->view('transaksi/outstanding_finishadmin_view', $data);
    }

    function cek_kodenota($KodeNota) {
        $Tipe = $this->input->post('tipe');
        if ($this->transaksi_model->cekKodeNota($KodeNota, $Tipe, $this->session->userdata('ERPCode'))) {
            return true;
        }
        $this->form_validation->set_message('cek_kodenota', '%s tidak valid.');
        return false;
    }

    function export_excel($TransactionCode, $ERPCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $ERPCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $ERPCode)));
        $OutstandingDERP = $this->transaksi_model->cekOutstandingFinishAdmin($TransactionCode);
        if (count($OutstandingDERP) > 0) {
            /** Include PHPExcel */
            require_once APPPATH . "/third_party/phpexcel/PHPExcel.php";
            // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();

            // Set document properties
            $objPHPExcel->getProperties()->setCreator($this->session->userdata('OperatorCode'))
                    ->setLastModifiedBy($this->session->userdata('OperatorCode'))
                    ->setTitle("Masalah Receiving BPB " . $ERPCode)
                    ->setSubject("Masalah Receiving BPB " . $ERPCode)
                    ->setDescription("Masalah Receiving BPB " . $ERPCode)
                    ->setKeywords("Masalah Receiving BPB " . $ERPCode)
                    ->setCategory("Masalah Receiving BPB " . $ERPCode);



            // Add some data
            $PHPExcel = $objPHPExcel->setActiveSheetIndex(0);
            $PHPExcel->setCellValue('A1', 'TransactionCode');
            $PHPExcel->setCellValue('B1', 'BPB');
            $PHPExcel->setCellValue('C1', 'SKU');
            $PHPExcel->setCellValue('D1', 'Barang');
            $PHPExcel->setCellValue('E1', 'Jumlah');
            $PHPExcel->setCellValue('F1', 'Status');

            $rowexcel = 2;
            foreach ($OutstandingDERP as $row) {
                $PHPExcel->setCellValue('A' . $rowexcel, $TransactionCode);
                $PHPExcel->setCellValue('B' . $rowexcel, $ERPCode);
                $PHPExcel->setCellValue('C' . $rowexcel, $row['Kode']);
                $PHPExcel->setCellValue('D' . $rowexcel, $row['Keterangan']);
                $PHPExcel->setCellValue('E' . $rowexcel, $row['Jml']);
                $PHPExcel->setCellValue('F' . $rowexcel, $row['Status']);
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
            $OutstandingDERP = $this->transaksi_model->cekOutstandingFinishAdmin($data['TransactionCode']);
            $data['outstanding'] = $OutstandingDERP;
            $data['penyelesaian'] = $this->transaksi_model->getNotaPenyelesaian($TransactionCode);
            $data['ERPCode'] = $ERPCode;
            $this->load->view('transaksi/outstanding_finishadmin_view', $data);
        }
    }

    function batal_finish($TransactionCode, $ERPCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $ERPCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $ERPCode)));
        if ($this->transaksi_model->buka_finishmove($TransactionCode)) {
            $this->daftar_transaksi_bpb();
        } else {
            $siteId = $this->session->userdata('siteId');
            $operatorCode = $this->session->userdata('OperatorCode');
            $tanggal = $this->transaksi_model->getTransactionDate($TransactionCode);
            $kode_proyek = 'BPB'; //kode BPB Principle    
            $this->transaksi_model->setBPB($kode_proyek, $ERPCode, $tanggal, $siteId, $operatorCode);
            $data['TransactionCode'] = $TransactionCode;
            $data['error'] = 'Penambahan Receiving Gagal';
            $OutstandingDERP = $this->transaksi_model->cekOutstandingFinishAdmin($data['TransactionCode']);
            $data['outstanding'] = $OutstandingDERP;
            $data['penyelesaian'] = $this->transaksi_model->getNotaPenyelesaian($TransactionCode);
            $data['ERPCode'] = $ERPCode;
            $this->load->view('transaksi/outstanding_finishadmin_view', $data);
        }
    }

    function tambah_OverRcv() {
        if ($this->input->post('btnAmbil')) {
            $TransactionCode = $this->input->post('TransactionCode');
            $SKUCode = $this->input->post('SKUCode');
            $Qty = $this->input->post('Qty');
            $ERPCode = $this->input->post('ERPCode');
            if ($this->transaksi_model->setOverRcv($TransactionCode, $SKUCode, $Qty)) {
                $data['pesan'] = 'Penambahan Tugas Pengambilan Berlebih Berhasil';
            } else {
                $data['error'] = 'Penambahan Tugas Pengambilan Berlebih Gagal';
            }
            $siteId = $this->session->userdata('siteId');
            $operatorCode = $this->session->userdata('OperatorCode');
            $tanggal = $this->transaksi_model->getTransactionDate($TransactionCode);
            $kode_proyek = 'BPB'; //kode BPB Principle    
            $this->transaksi_model->setBPB($kode_proyek, $ERPCode, $tanggal, $siteId, $operatorCode);

            $OutstandingDERP = $this->transaksi_model->cekOutstandingFinishAdmin($TransactionCode);

            if (count($OutstandingDERP) > 0) {
                $data['outstanding'] = $OutstandingDERP;
                $data['TransactionCode'] = $TransactionCode;
                $data['penyelesaian'] = $this->transaksi_model->getNotaPenyelesaian($TransactionCode);
                $this->session->set_userdata('TransactionCode', $TransactionCode);
                $this->session->set_userdata('ERPCode', $ERPCode);
                $data['ERPCode'] = $ERPCode;

                $this->load->view('transaksi/outstanding_finishadmin_view', $data);
            } else {
                $data['isFinishMove'] = 0;
                $data['note'] = $this->transaksi_model->getNoteMaster($TransactionCode);
                $this->load->view('transaksi/edit_note_master_view', $data);
            }
        }
    }

    public function receiving_berjalan() {
        $data['rcv'] = $this->transaksi_model->getTodayTransaksiBPBList();
        $this->load->view('transaksi/receiving_berjalan', $data);
    }

    public function detail_receiving_berjalan($transactionCode) {
        $transactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $transactionCode)));
        $data['transaksi'] = $this->transaksi_model->getDetailTransaction($transactionCode);
        $data['detail_transaksi'] = $this->transaksi_model->getDetailReceivingBerjalan($transactionCode);
        $this->load->view('transaksi/detail_receiving_berjalan', $data);
    }

    public function show_assigned($TransactionCode, $ERPCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $ERPCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $ERPCode)));
        $this->session->set_userdata('ERPCode', $ERPCode);
        $this->session->set_userdata('TransactionCode', $TransactionCode);
        $data['Assigned'] = $this->transaksi_model->getAssigned($TransactionCode);
        $data['Role'] = $this->transaksi_model->getRole($TransactionCode);
        $this->load->view('transaksi/Assigned_view', $data);
    }

    public function delete_assigned($OperatorCode, $OprRole) {
        $TransactionCode = $this->session->userdata('TransactionCode');
        $OperatorCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $OperatorCode)));
        $OprRole = base64_decode(str_replace('-', '=', str_replace('_', '/', $OprRole)));
        if ($this->transaksi_model->deleteAssigned($TransactionCode, $OperatorCode, $OprRole)) {
            $data['Assigned'] = $this->transaksi_model->getAssigned($TransactionCode);
            $data['Role'] = $this->transaksi_model->getRole($TransactionCode);
            $data['pesan'] = "Penugasan Berhasil Dihapus";
            $this->load->view('transaksi/Assigned_view', $data);
        } else {
            $data['Assigned'] = $this->transaksi_model->getAssigned($TransactionCode);
            $data['Role'] = $this->transaksi_model->getRole($TransactionCode);
            $data['error'] = "Penugasan Gagal Dihapus";
            $this->load->view('transaksi/Assigned_view', $data);
        }
    }

    function get_ajax_Operator() {
        //ajax untuk menampilkan informasi SKU barang yang harus dipilih
        $WHRoleCode = $_POST['WHRoleCode'];
        $TransactionCode = $this->session->userdata('TransactionCode');
        $result = $this->transaksi_model->getOperator($WHRoleCode, $TransactionCode);

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
                $data['Assigned'] = $this->transaksi_model->getAssigned($TransactionCode);
                $data['Role'] = $this->transaksi_model->getRole($TransactionCode);
                $this->load->view('transaksi/Assigned_view', $data);
            } else {
                $TransactionCode = $this->session->userdata('TransactionCode');
                $OperatorCode = $this->input->post('Operator');
                $OprRole = $this->input->post('Role');
                if ($this->transaksi_model->setAssigned($TransactionCode, $OperatorCode, $OprRole)) {
                    $data['Assigned'] = $this->transaksi_model->getAssigned($TransactionCode);
                    $data['Role'] = $this->transaksi_model->getRole($TransactionCode);
                    $data['pesan'] = "Penugasan Berhasil Ditambah";
                    $this->load->view('transaksi/Assigned_view', $data);
                } else {
                    $data['Assigned'] = $this->transaksi_model->getAssigned($TransactionCode);
                    $data['Role'] = $this->transaksi_model->getRole($TransactionCode);
                    $data['error'] = "Penugasan Gagal Ditambah";
                    $this->load->view('transaksi/Assigned_view', $data);
                }
            }
        }
    }

}

?>
