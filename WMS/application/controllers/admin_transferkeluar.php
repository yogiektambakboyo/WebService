<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of picking
 *
 * @author USER
 */
class admin_transferkeluar extends CI_Controller {

     function __construct() {
        parent::__construct();
        if (!$this->session->userdata('OperatorCode') || $this->session->userdata("OperatorRole") != '10/WHR/000') {
            redirect(base_url() . "index.php/login");
        }
        $this->load->model('admin_transferkeluar_model');
    }

    function index() {
        $this->load->view('admin_transferkeluar/admin_picking_main_view');
    }

    function tambahpicklist() {
        if ($this->input->post('btnProses')) {
            $this->form_validation->set_rules('picklist[]', 'Picklist', 'required|callback_cekRackSlot');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $data['cabang'] = $this->admin_transferkeluar_model->getCabang();
                if($this->session->userdata('TglPick')){
                    $today=date("Y/m/d",strtotime($this->session->userdata('TglPick')));
                }
                else{
                    $today = date("Y/m/d");
                }
                $data['tgl']=$today;
                if (count($data['cabang']) == 0) {
                    $cabang = '';
                } else {
                    $cabang = $data['cabang'][0]['Cabang'];
                }
                if($this->session->userdata('CabangPick')){
                    $defaultcabang=$this->session->userdata('CabangPick');
                }
                else{
                    $defaultcabang=$cabang;
                }
                $data['picklist'] = $this->admin_transferkeluar_model->getPickingList($today, $defaultcabang);
                $data['defaultcabang']=$defaultcabang;
                $this->load->view('admin_transferkeluar/tambah_picklist_view', $data);
            } else {
                $picklist = $this->input->post('picklist');
                $tahun = date('Y');
                $siteId = $this->session->userdata('siteId');
                $operatorCode = $this->session->userdata('OperatorCode');
                $tanggal = $this->session->userdata('TglPick');
                $kode_proyek = 'PTS'; //kode Picking  
                $status=true;
                foreach ($picklist as $row) {
                    if(!$this->admin_transferkeluar_model->setPicklist($kode_proyek, $row, $tanggal, $siteId, $operatorCode))
                    {
                        $status=false;
                    }
                }
                if($status){
                
                    $this->session->set_flashdata('pesan', 'Transaksi Picking berhasil ditambahkan.');
                    $data['cabang'] = $this->admin_transferkeluar_model->getCabang();
                    if($this->session->userdata('TglPick')){
                        $today=date("Y/m/d",strtotime($this->session->userdata('TglPick')));
                    }
                    else{
                        $today = date("Y/m/d");
                    }
                    if($this->session->userdata('CabangPick')){
                        $defaultcabang=$this->session->userdata('CabangPick');
                    }
                    else{
                        $defaultcabang=$data['cabang'][0]['Cabang'];
                    }
                    $data['picklist'] = $this->admin_transferkeluar_model->getPickingList($today, $defaultcabang);
                    $data['tgl']=$today;
                    $data['defaultcabang']=$defaultcabang;
                    $this->load->view('admin_transferkeluar/tambah_picklist_view', $data);
                }
                else
                {
                    $data['cabang'] = $this->admin_transferkeluar_model->getCabang();
                    if($this->session->userdata('TglPick')){
                        $today=date("Y/m/d",strtotime($this->session->userdata('TglPick')));
                    }
                    else{
                        $today = date("Y/m/d");
                    }
                    $data['tgl']=$today;
                    if (count($data['cabang']) == 0) {
                        $cabang = '';
                    } else {
                        $cabang = $data['cabang'][0]['Cabang'];
                    }
                    if($this->session->userdata('CabangPick')){
                        $defaultcabang=$this->session->userdata('CabangPick');
                    }
                    else{
                        $defaultcabang=$cabang;
                    }
                    $data['picklist'] = $this->admin_transferkeluar_model->getPickingList($today, $defaultcabang);
                    $data['defaultcabang']=$defaultcabang;
                    $this->load->view('admin_transferkeluar/tambah_picklist_view', $data);
                }
            }
        } else {
            $data['cabang'] = $this->admin_transferkeluar_model->getCabang();
            $today = date("Y/m/d");
            if (count($data['cabang']) == 0) {
                $cabang = '';
            } else {
                $cabang = $data['cabang'][0]['Cabang'];
            }
            $data['defaultcabang']=$cabang;
            $data['tgl']=$today;
            $data['picklist'] = $this->admin_transferkeluar_model->getPickingList($today, $cabang);
            $this->load->view('admin_transferkeluar/tambah_picklist_view', $data);
        }
    }

    function get_ajax_tambahpicklist() {
        //ajax untuk menampilkan informasi SKU barang yang harus dipilih
        $tgl = $_POST['tgl'];
        $this->session->set_userdata('TglPick',$tgl);
        $cabang = $_POST['cabang'];
        $this->session->set_userdata('CabangPick',$cabang);
        $result = $this->admin_transferkeluar_model->getPickingList($tgl, $cabang);
        $arr = array();
        foreach ($result as $row) {
            $arr[] = array("NoPicklist" => $row['NoPicklist'], "TglPicklist" => date('d-m-Y', strtotime($row['TglPicklist'])), "JmlInv" => $row['JmlInv']);
        }
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }

    function daftar_task_picking() {
        $data['masterpck'] = $this->admin_transferkeluar_model->getMasterTaskPicking();
        $this->load->view('admin_transferkeluar/daftar_task_pck_view', $data);
    }

    function edit_mastertaskpck($TransactionCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        if ($TransactionCode == '') {
            $TransactionCode = $this->session->userdata('TransactionCode');
        }
        $result = $this->admin_transferkeluar_model->getstatustask($TransactionCode);
        if ($result['isCancel'] == 0) {
            $status = $this->admin_transferkeluar_model->getstatustask($TransactionCode);
            //$this->session->set_userdata('TransactionCode', $TransactionCode);
            $data['isFinishMove'] = $result['isFinishMove'];
            $data['note'] = $this->admin_transferkeluar_model->getNoteMaster($TransactionCode);
            $this->load->view('admin_transferkeluar/edit_note_master_view', $data);
        }
    }

    function detail_transaksi_pck($transactionCode) {
        $transactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $transactionCode)));
        $data['transaksi'] = $this->admin_transferkeluar_model->getDetailTransaction($transactionCode);
        $data['detail_transaksi'] = $this->admin_transferkeluar_model->getDetailTransaction2($transactionCode);
        $this->load->view('admin_transferkeluar/daftar_detail_transaksi_pck_view', $data);
    }

    function simpan_mastertaskpck() {
        if ($this->input->post('btnSimpan')) {
            $TransactionCode = $this->input->post('TransactionCode');
            $NoPickList = $this->input->post('ERPCode');
            $Note = $this->input->post('note');
            $siteId = $this->session->userdata('siteId');
            $operatorCode = $this->session->userdata('OperatorCode');
            $tanggal = date("Y/m/d");
            $kode_proyek = 'PTS'; //kode BPB Principle    

            $this->admin_transferkeluar_model->setPicklist($kode_proyek, $NoPickList, $tanggal, $siteId, $operatorCode);
            $this->admin_transferkeluar_model->setNoteMaster($TransactionCode, $Note);

            $this->session->set_flashdata('pesan', 'Transaksi Picking berhasil ditambahkan.');
            $this->daftar_task_picking();
        }
    }

    function edit_note($TransactionCode, $NoUrut, $Status2) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $NoUrut = base64_decode(str_replace('-', '=', str_replace('_', '/', $NoUrut)));
        $Status2 = base64_decode(str_replace('-', '=', str_replace('_', '/', $Status2)));
        $data['note'] = $this->admin_transferkeluar_model->getNote($TransactionCode, $NoUrut);
        $data['Status2'] = $Status2;
        $this->load->view('admin_transferkeluar/edit_note_view', $data);
    }

    function simpan_note() {
        if ($this->input->post('btnSimpan')) {
            $TransactionCode = $this->input->post('TransactionCode');
            $NoUrut = $this->input->post('NoUrut');
            $Note = $this->input->post('note');

            if ($this->admin_transferkeluar_model->setNote($TransactionCode, $NoUrut, $Note)) {
                $data['transaksi'] = $this->admin_transferkeluar_model->getDetailTransaction($TransactionCode);
                $data['detail_transaksi'] = $this->admin_transferkeluar_model->getDetailTransaction2($TransactionCode);
                $this->load->view('admin_transferkeluar/daftar_detail_transaksi_pck_view', $data);
            } else {
                $data['note'] = $this->admin_transferkeluar_model->getNote($TransactionCode, $NoUrut);
                $data['error'] = "Gagal Menyimpan";
                $this->load->view('admin_transferkeluar/edit_note_view', $data);
            }
        }
    }

    function simpan_pembatalanpck() {
        if ($this->input->post('btnSimpanCancel')) {
            $this->form_validation->set_rules('notecancel', 'Catatan Pembatalan', 'required');
            $this->form_validation->set_rules('TransactionCode', 'TransactionCode', 'required|callback_cek_detailpembatalan');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $TransactionCode = $this->input->post('TransactionCode');
                $result = $this->admin_transferkeluar_model->getstatustask($TransactionCode);
                $data['isFinishMove'] = $result['isFinishMove'];
                $data['note'] = $this->admin_transferkeluar_model->getNoteMaster($TransactionCode);
                $this->load->view('admin_transferkeluar/edit_note_master_view', $data);
            } else {
                $TransactionCode = $this->input->post('TransactionCode');
                $ERPCode = $this->input->post('ERPCode');
                $siteId = $this->session->userdata('siteId');
                $operatorCode = $this->session->userdata('OperatorCode');
                $tanggal = date("Y/m/d");
                $kode_proyek = 'PTS'; //kode BPB Principle 
                $Note = $this->admin_transferkeluar_model->getNoteMaster($TransactionCode);
                $Notelama = $Note['Note'];
                $Notebaru = $this->input->post('notecancel');
                $Notelama.='#' . $Notebaru;


                //$this->admin_retur_model->setBPB($kode_proyek, $ERPCode, $tanggal, $siteId, $operatorCode);
                $this->admin_transferkeluar_model->setNoteMasterPembatalan($TransactionCode, $Notelama, $operatorCode);
                $this->daftar_task_picking();
            }
        }
    }

    function cek_finishmove($TransactionCode, $ERPCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $ERPCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $ERPCode)));
        $OutstandingMove = $this->admin_transferkeluar_model->cekOutstandingFinishMove($TransactionCode);
        if (count($OutstandingMove) > 0) {
            $data['outstanding'] = $OutstandingMove;
            $data['TransactionCode'] = $TransactionCode;
            $this->session->set_userdata('TransactionCode', $TransactionCode);
            $this->session->set_userdata('ERPCode', $ERPCode);
            $data['ERPCode'] = $ERPCode;
            $this->load->view('admin_transferkeluar/outstanding_finishmove_view', $data);
        } else {
            $OperatorCode = $this->session->userdata('OperatorCode');
            if ($this->admin_transferkeluar_model->setFinishMove($TransactionCode, $OperatorCode)) {
                $data['pesan'] = 'Transaksi Picking Berhasil Menyelesaikan Perpindahan Barang.';
                $data['note'] = $this->admin_transferkeluar_model->getNoteMaster($TransactionCode);
                $data['isFinishMove'] = 1;
                $this->load->view('admin_transferkeluar/edit_note_master_view', $data);
            } else {
                $data['error'] = "Transaksi Picking Gagal Menyelesaikan Perpindahan Barang.";
                $data['isFinishMove'] = 0;
                $data['note'] = $this->admin_transferkeluar_model->getNoteMaster($TransactionCode);
                $this->load->view('admin_transferkeluar/edit_note_master_view', $data);
            }
        }
    }

    function cek_finishadmin($TransactionCode, $ERPCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $ERPCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $ERPCode)));
        $OutstandingDERP = $this->admin_transferkeluar_model->cekOutstandingFinishAdmin($TransactionCode);
        if (count($OutstandingDERP) > 0) {
            $data['outstanding'] = $OutstandingDERP;
            $data['TransactionCode'] = $TransactionCode;
            $data['penyelesaian'] = $this->admin_transferkeluar_model->getNotaPenyelesaian($TransactionCode);
            $this->session->set_userdata('TransactionCode', $TransactionCode);
            $this->session->set_userdata('ERPCode', $ERPCode);
            $data['ERPCode'] = $ERPCode;
            $this->load->view('admin_transferkeluar/outstanding_finishadmin_view', $data);
        } else {
            $OperatorCode = $this->session->userdata('OperatorCode');
            if ($this->admin_transferkeluar_model->setFinishAdmin($TransactionCode, $OperatorCode)) {
                $this->session->set_flashdata('pesan', 'Transaksi Picking Berhasil Selesai.');
                $this->daftar_task_picking();
            } else {
                $data['error'] = "Transaksi Picking Gagal Selesai.";
                $data['isFinishMove'] = 0;
                $data['note'] = $this->admin_transferkeluar_model->getNoteMaster($TransactionCode);
                $this->load->view('admin_transferkeluar/edit_note_master_view', $data);
            }
        }
    }

    function cek_detailpembatalan($kode) {
        if ($this->admin_transferkeluar_model->cekPembatalan($kode)) {
            return TRUE;
        }
        $this->form_validation->set_message('cek_detailpembatalan', 'Task Sudah Berjalan Tidak Dapat Dibatalkan');
        return FALSE;
    }

    function tambah_notapenyelesaian() {
        if ($this->input->post('btnSimpan')) {
            $this->form_validation->set_rules('kodenota', 'Kode Nota', 'required|callback_cek_kodenota');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $data['TransactionCode'] = $this->session->userdata('TransactionCode');
                $OutstandingDERP = $this->admin_transferkeluar_model->cekOutstandingFinishAdmin($data['TransactionCode']);
                $data['outstanding'] = $OutstandingDERP;

                $data['penyelesaian'] = $this->admin_transferkeluar_model->getNotaPenyelesaian($data['TransactionCode']);
                $data['ERPCode'] = $this->session->userdata('ERPCode');
                $this->load->view('admin_transferkeluar/outstanding_finishadmin_view', $data);
            } else {
                $TransactionCode = $this->session->userdata('TransactionCode');
                $KodeNota = $this->input->post('kodenota');
                $Tipe = $this->input->post('tipe');
                if ($this->admin_transferkeluar_model->setPenyelesaian($TransactionCode, $KodeNota, $Tipe)) {
                    $data['pesan'] = 'Kode Nota Berhasil Ditambahkan.';
                    $data['TransactionCode'] = $this->session->userdata('TransactionCode');
                    $OutstandingDERP = $this->admin_transferkeluar_model->cekOutstandingFinishAdmin($data['TransactionCode']);
                    if (count($OutstandingDERP) > 0) {
                        $data['outstanding'] = $OutstandingDERP;
                        $data['penyelesaian'] = $this->admin_transferkeluar_model->getNotaPenyelesaian($data['TransactionCode']);
                        $data['ERPCode'] = $this->session->userdata('ERPCode');
                        $this->load->view('admin_transferkeluar/outstanding_finishadmin_view', $data);
                    } else {
                        $this->edit_mastertaskpck(str_replace('=', '-', str_replace('/', '_', base64_encode($this->session->userdata('TransactionCode')))));
                        //$this->cek_finishmove(str_replace('=', '-', str_replace('/', '_', base64_encode($this->session->userdata('TransactionCode'))),str_replace('=', '-', str_replace('/', '_', base64_encode($this->session->userdata('ERPCode'))))));
                    }
                } else {
                    $data['TransactionCode'] = $this->session->userdata('TransactionCode');
                    $data['error'] = 'Kode Nota Gagal Ditambahkan.';
                    $OutstandingDERP = $this->admin_transferkeluar_model->cekOutstandingFinishAdmin($data['TransactionCode']);
                    $data['outstanding'] = $OutstandingDERP;
                    $data['penyelesaian'] = $this->admin_transferkeluar_model->getNotaPenyelesaian($data['TransactionCode']);
                    $data['ERPCode'] = $this->session->userdata('ERPCode');
                    $this->load->view('admin_transferkeluar/outstanding_finishadmin_view', $data);
                }
            }
        }
    }

    function hapus_kodenota($TransactionCode, $KodeNota) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $KodeNota = base64_decode(str_replace('-', '=', str_replace('_', '/', $KodeNota)));
        if ($this->admin_transferkeluar_model->removePenyelesaian($TransactionCode, $KodeNota)) {
            $data['pesan'] = 'Kode Nota Berhasil Dihapus.';
        } else {
            $data['error'] = 'Kode Nota Gagal Dihapus.';
        }
        $data['TransactionCode'] = $this->session->userdata('TransactionCode');
        $OutstandingDERP = $this->admin_transferkeluar_model->cekOutstandingFinishAdmin($data['TransactionCode']);
        $data['outstanding'] = $OutstandingDERP;
        $data['penyelesaian'] = $this->admin_transferkeluar_model->getNotaPenyelesaian($data['TransactionCode']);
        $data['ERPCode'] = $this->session->userdata('ERPCode');
        $this->load->view('admin_transferkeluar/outstanding_finishadmin_view', $data);
    }

    function cek_kodenota($KodeNota) {
        if ($this->admin_transferkeluar_model->cekKodeNota($KodeNota, $this->session->userdata('ERPCode'))) {
            return true;
        }
        $this->form_validation->set_message('cek_kodenota', '%s tidak valid.');
        return false;
    }

    function export_excel($TransactionCode, $ERPCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $ERPCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $ERPCode)));
        $OutstandingDERP = $this->admin_transferkeluar_model->cekOutstandingFinishAdmin($TransactionCode);
        if (count($OutstandingDERP) > 0) {
            /** Include PHPExcel */
            require_once APPPATH . "/third_party/phpexcel/PHPExcel.php";
            // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();

            // Set document properties
            $objPHPExcel->getProperties()->setCreator($this->session->userdata('OperatorCode'))
                    ->setLastModifiedBy($this->session->userdata('OperatorCode'))
                    ->setTitle("Masalah Shipping " . $ERPCode)
                    ->setSubject("Masalah Shipping " . $ERPCode)
                    ->setDescription("Masalah Shipping " . $ERPCode)
                    ->setKeywords("Masalah Shipping " . $ERPCode)
                    ->setCategory("Masalah Shipping " . $ERPCode);



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
            $judul = "\"Masalah Shipping " . date('d-m-Y') . ".xls\"";

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
            $data['penyelesaian'] = $this->admin_transferkeluar_model->getNotaPenyelesaian($TransactionCode);
            $data['ERPCode'] = $ERPCode;
            $this->load->view('admin_transferkeluar/outstanding_finishadmin_view', $data);
        }
    }
	
	public function list_picking()
	{	
		if ($this->input->post('btnYa')){
			$tr  = $this->input->post('TransactionCode');
	
			$this->admin_transferkeluar_model->hapusPickingAll($tr);
			$this->session->set_flashdata('success','Picklist Berhasil Dihapus') ;
			redirect('admin_transferkeluar/list_picking');
		}
		
		$data['cabang']	  = $this->admin_transferkeluar_model->getCabang($this->session->userdata('HariCabang'));
		$data['picklist'] = $this->admin_transferkeluar_model->getListEditPicklist();
		$this->load->view('admin_transferkeluar/list_picking', $data);
	}
	
	public function edit_picking($tr)
	{
		$transactionCode = base64_decode($tr);
		
		if ($this->input->post('btnYa')){
			$tr  = $this->input->post('TransactionCode');
			$pl  = $this->input->post('PickList');
			
			$cek = $this->admin_transferkeluar_model->getEdit($transactionCode);
			// var_dump(count($cek));
			if (count($cek) <= '1') {
				$this->session->set_flashdata('error','Picklist Gagal Dihapus') ;
				redirect('admin_transferkeluar/list_picking');
			}else{
				$this->admin_transferkeluar_model->hapusPicking($tr,$pl);
				$data['sucses'] = 'Picklist Berhasil Dihapus' ;
			}
		}
		
		$data['edit'] = $this->admin_transferkeluar_model->getEdit($transactionCode);
		$this->load->view('admin_transferkeluar/edit_picking',$data);
	}
	
	public function edit_picking2($tr)
	{
		$transactionCode = base64_decode($tr);
		
		$data['edit'] = $transactionCode ;
		$this->load->view('admin_transferkeluar/edit_picking2ajax',$data);
	}
        public function show_assigned($TransactionCode, $ERPCode) {
        $TransactionCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $TransactionCode)));
        $ERPCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $ERPCode)));
        $this->session->set_userdata('ERPCode', $ERPCode);
        $this->session->set_userdata('TransactionCode', $TransactionCode);
        $data['Assigned'] = $this->admin_transferkeluar_model->getAssigned($TransactionCode);
        $data['Role'] = $this->admin_transferkeluar_model->getRole($TransactionCode);
        $this->load->view('admin_transferkeluar/Assigned_view', $data);
    }

    public function delete_assigned($OperatorCode, $OprRole) {
        $TransactionCode = $this->session->userdata('TransactionCode');
        $OperatorCode = base64_decode(str_replace('-', '=', str_replace('_', '/', $OperatorCode)));
        $OprRole = base64_decode(str_replace('-', '=', str_replace('_', '/', $OprRole)));
        if ($this->admin_transferkeluar_model->deleteAssigned($TransactionCode, $OperatorCode, $OprRole)) {
            $data['Assigned'] = $this->admin_transferkeluar_model->getAssigned($TransactionCode);
            $data['Role'] = $this->admin_transferkeluar_model->getRole($TransactionCode);
            $data['pesan'] = "Penugasan Berhasil Dihapus";
            $this->load->view('admin_transferkeluar/Assigned_view', $data);
        } else {
            $data['Assigned'] = $this->admin_transferkeluar_model->getAssigned($TransactionCode);
            $data['Role'] = $this->admin_transferkeluar_model->getRole($TransactionCode);
            $data['error'] = "Penugasan Gagal Dihapus";
            $this->load->view('admin_transferkeluar/Assigned_view', $data);
        }
    }

    function get_ajax_Operator() {
        //ajax untuk menampilkan informasi SKU barang yang harus dipilih
        $WHRoleCode = $_POST['WHRoleCode'];
        $TransactionCode = $this->session->userdata('TransactionCode');
        $result = $this->admin_transferkeluar_model->getOperator($WHRoleCode, $TransactionCode);

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
                $data['Assigned'] = $this->admin_transferkeluar_model->getAssigned($TransactionCode);
                $data['Role'] = $this->admin_transferkeluar_model->getRole($TransactionCode);
                $this->load->view('admin_transferkeluar/Assigned_view', $data);
            } else {
                $TransactionCode = $this->session->userdata('TransactionCode');
                $OperatorCode = $this->input->post('Operator');
                $OprRole = $this->input->post('Role');
                if ($this->admin_transferkeluar_model->setAssigned($TransactionCode, $OperatorCode, $OprRole)) {
                    $data['Assigned'] = $this->admin_transferkeluar_model->getAssigned($TransactionCode);
                    $data['Role'] = $this->admin_transferkeluar_model->getRole($TransactionCode);
                    $data['pesan'] = "Penugasan Berhasil Ditambah";
                    $this->load->view('admin_transferkeluar/Assigned_view', $data);
                } else {
                    $data['Assigned'] = $this->admin_transferkeluar_model->getAssigned($TransactionCode);
                    $data['Role'] = $this->admin_transferkeluar_model->getRole($TransactionCode);
                    $data['error'] = "Penugasan Gagal Ditambah";
                    $this->load->view('admin_transferkeluar/Assigned_view', $data);
                }
            }
        }
    }
	

}

?>
