<?php

class Abb extends CI_Controller
{
	public function __construct() {
		parent::__construct();
		
		if (!$this->session->userdata('OperatorCode')){
			redirect('index.php/login');
		}
		
		$this->load->model('abb_model');
	}
	
	public function index()
	{	
		// var_dump($this->abb_model->listAbb());
		$this->session->unset_userdata('rackSrc');
		$this->session->unset_userdata('rackNm');
		$this->session->unset_userdata('binSrc');
		$this->session->unset_userdata('qty');
		$this->session->unset_userdata('trCode');
		$this->session->unset_userdata('sku');
		$this->session->unset_userdata('qtyKonv');
		$this->session->unset_userdata('expDate');
		$this->session->unset_userdata('noUrut');
		
		$data['listAbb'] = $this->abb_model->listAbb();
		$this->load->view('abb/index', $data);
	}
	
	public function get_ajax_suggestion() 
	{
		$TransactionCode	= $_POST['TransactionCode'];
		$SKUCode 			= $_POST['SKUCode'];
        $Qty 				= $_POST['Qty'];
        $bin 				= $_POST['bin'];
        $result = $this->abb_model->getSuggestion($TransactionCode,$SKUCode,$Qty,$bin);
		// var_dump($result);
        $arr=array();
        foreach ($result as $row)
        {
			$this->session->set_userdata('rackNm',$row['name']);
			$maxNourut = $row['maxnourut'] + '1';
			$this->session->set_userdata('noUrut',$maxNourut);
			// var_dump($row);
            $arr[] = array("name" => $row['name'],"level" => $row['RackLevel'],"rackSlot" => $row['DestRackSlot'],"ExpDate" => date("M Y",strtotime($row['ExpDate'])),"Qty" => $row['qtyKonv'], "maxNourut" => $row['maxnourut'], 'qtyBinSku' => $row['qtyBinSku']);
        }
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }
	
	public function ambil_barang()
	{
		// if ($this->input->post('proses')){
		// }else{
		
		// $trCode = $this->input->post('Transact');
		
		if ($this->input->post('btnProsesIndex')){
			$this->session->set_userdata('rackSrc',$this->input->post('rackSrc'));
			$this->session->set_userdata('binSrc',$this->input->post('binSrc'));
			$this->session->set_userdata('qty',$this->input->post('Qty'));
			$this->session->set_userdata('trCode',$this->input->post('TransactionCode'));
			$this->session->set_userdata('sku',$this->input->post('SKUCode'));
			$this->session->set_userdata('qtyKonv',$this->input->post('qtyKonv'));
			$this->session->set_userdata('expDate',$this->input->post('ExpDate'));
			// $this->session->set_userdata('noUrut',$this->input->post('noUrut'));
			
		}
		
		if ($this->input->post('btnProses')){
		
			$this->form_validation->set_rules('rackSrc', 'Rack Src', 'required|matches[cekRackSrc]');
			$this->form_validation->set_rules('binSrc', 'Bin Src', 'required|matches[cekBinSrc]');
			$this->form_validation->set_rules('binDest', 'Bin Dest', 'required|callback_cekBin');
			$this->form_validation->set_rules('qty', 'qty', 'required');
			$this->form_validation->set_error_delimiters("<div class='alert alert-error'><h5>","</h5></div>");
			if ($this->form_validation->run() == FALSE){   
		
			}else{
				$rackSrc = $this->input->post('rackSrc');
				$binSrc  = $this->input->post('binSrc');
				$binDest = $this->input->post('binDest');
				$qty     = $this->input->post('qty');
				$satuan  = $this->input->post('satuan');
				
				$totQty  = $qty * $satuan;
				
				if ($totQty > $this->session->userdata('qty')){
					$data['error'] = 'Quantity Lebih Besar';
				}else{
				
					$this->abb_model->setDetailTaskRcv($rackSrc,$binSrc,$binDest,$totQty,$this->session->userdata('trCode'), $this->session->userdata('sku'),$this->session->userdata('expDate'), $this->session->userdata('noUrut'));
					
					$this->abb_model->updateDetailTaskHistory($rackSrc,$binSrc,$binDest,$totQty,$this->session->userdata('trCode'), $this->session->userdata('sku'),$this->session->userdata('expDate'), $this->session->userdata('noUrut'), $this->session->userdata('OperatorCode'));
					
					$totNew = $this->session->userdata('noUrut') + '1';
					// var_dump($totNew);
					$this->abb_model->setDetailTaskRcv2($rackSrc,$binSrc,$binDest,$totQty,$this->session->userdata('trCode'), $this->session->userdata('sku'),$this->session->userdata('expDate'), $totNew);
					
					$cek = $this->abb_model->cekDetailOver($this->session->userdata('trCode'), $this->session->userdata('sku'));
					
					if ($cek['Pick'] == '0'){
						$totAll = $totQty;
					}else {
						$totAll = $cek['Pick'] + $totQty;
						
					}
					
					$this->abb_model->updateDetailOver($this->session->userdata('trCode'), $this->session->userdata('sku'), $totAll);
					
					$this->session->set_flashdata('succes', 'Data Berhasil Di Simpan');
                    redirect(base_url().'index.php/abb');  
				}
				
				
				// var_dump($totQty);
				
			}
		}
		
			
		
		// var_dump($this->session->userdata('qty'));
		$data['satuan'] = $this->abb_model->getSatuan($this->session->userdata('trCode'), $this->session->userdata('sku'));
		$this->load->view('abb/ambil_barang',$data);
	}
	
	function cekBin($kodeBin)
    {
       
        if ($this->abb_model->cekBin($kodeBin) == '0'){
            $this->form_validation->set_message('cekBin', 'Kode Bin Tidak Ada');
            return FALSE;
        }
        return TRUE;
    }
	
	function get_ajax_rack() {
        //ajax untuk menampilkan informasi SKU barang yang harus dipilih
        $RackSlotCode = $_POST['RackSlotCode'];
        $result = $this->abb_model->getRackName($RackSlotCode);
        
        $arr = array("RackName" => $result['RackName']);
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }
	
}


















