<?php

class Replenish extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
       if (!$this->session->userdata('OperatorCode')){
           redirect(base_url(). 'index.php/login');
       }
        $this->load->model('replenish_model');
    }
    function selectreplenish(){
        $this->load->view('replenish/choose_replenish_view');
    }
    
    function index()
    {	
		$this->delSession();
		
		$this->session->unset_userdata('transactionCode');
		$transactionCode = $this->input->post('idTc');
		$operatorCode	 = $this->session->userdata('OperatorCode');
		$operatorRole	 = $this->session->userdata('OperatorRole');
		
		if ($this->input->post('btnProsesRpl')){
			$this->form_validation->set_rules('idTc[]', 'CheckBox', 'required');
			$this->form_validation->set_error_delimiters("<div class='alert alert-error'><h5>", "</h5></div>");
			if ($this->form_validation->run() == FAlSE){
		
			}else{
				$this->session->set_userdata('transactionCode', $transactionCode);
				foreach ($transactionCode as $tr){
					$this->replenish_model->setDetailTaskOpr($tr,$this->session->userdata('OperatorCode'), $this->session->userdata('OperatorRole'));
				}
				redirect('replenish/all_outstanding');
			}
		}
		
		$data['replenish'] = $this->replenish_model->getListReplenish($this->session->userdata('OperatorCode'), $this->session->userdata('OperatorRole'), 'BPB');
		$this->load->view('replenish/index', $data);
		
    }
    
    function all_outstanding()
    {	
		$this->delSession();
		
		if ($this->input->post()) {
			$this->setSession();
			redirect('replenish/ambil_bin');
		}
		
		// $i=1;
		foreach ($this->session->userdata('transactionCode') as $tr){
		
			$ambilBin = $this->replenish_model->getDetailTaskRplHis($tr, $this->session->userdata('OperatorCode'), $this->session->userdata('OperatorRole'));
			// var_dump(array_merge($ambilBin[0],$ambilBin[$i]));
		}
		
		$data['ambilBin'] = $ambilBin;
		$this->load->view('replenish/all_outstanding', $data);
    }
	
	public function ambil_bin()
	{
		
		if ($this->input->post('btnProses')){
			$this->form_validation->set_rules('kodebin2','Bin Temp', 'required|callback_cekBin');
                        $this->form_validation->set_rules('kodebin','Kode Bin', 'required|callback_cekBin');
			$this->form_validation->set_rules('kodebin','Kode Bin', 'required|matches[SrcBin]');
                        $this->form_validation->set_rules('RackSlotCode','Kode Rack', 'required|matches[SrcRack]');
                        $this->form_validation->set_rules('jumlahSKU','Jumlah', 'required|integer|callback_cekQty');
			$this->form_validation->set_error_delimiters("<div class='alert alert-error'><h5>", "</h5></div>");
			if ($this->form_validation->run() == false){
				
			}else{
				$this->replenish_model->setUser1stBinCode($this->session->userdata('trCode'), $this->session->userdata('QueueNumber'), $this->session->userdata('NoUrut'), $this->session->userdata('OperatorCode'), $this->input->post('kodebin2'));
				
				$this->delSession();
				
				$this->session->set_flashdata('succes', 'Bin Berhasil Di Simpan');
				redirect('replenish/all_outstanding');
			}
		}
		$data['Satuan'] = $this->replenish_model->getsatuan2($this->session->userdata('sku'));
		$this->load->view('replenish/ambil_bin',$data);
		
	}
	
	public function my_outstanding()
	{
		$this->delSession();
		
		if ($this->input->post()) {
			$this->setSession();
			redirect('replenish/taruh_bin');
		}
		
		
		$data['myoutstanding'] = $this->replenish_model->getMyOutstanding($this->session->userdata('OperatorCode'));
		$this->load->view('replenish/my_outstanding',$data);
	}
    
    function taruh_bin()
    {   
        if ($this->input->post('btnProses')){
            $this->form_validation->set_rules('binAwal', 'Kode Bin', 'required|callback_cekBin');
            $this->form_validation->set_rules('binAwal', 'Kode Bin', 'required|matches[SrcBin]');
            $this->form_validation->set_rules('rackTjn','Kode Rack', 'required|callback_cekKodeRack');
            $this->form_validation->set_rules('rackTjn','Kode Rack', 'required|matches[DestRack]');
            $this->form_validation->set_error_delimiters("<div class='alert alert-error'><h5>","</h5></div>");
                if ($this->form_validation->run() == FALSE){   
             
                }else {
                    $binAwal    = $this->input->post('binAwal');
                    $qty        = $this->session->userdata('qty');
                    $binTujuan  = $this->input->post('binAwal');
                    $rackTujuan = $this->input->post('rackTjn');
                    $onAisle    = 0;
                    $trCode 	= $this->input->post('trCode');
                    $queue	    = $this->input->post('QueueNumber');
                    $noUrut    	= $this->input->post('NoUrut');
                    $user       = $this->session->userdata('OperatorCode');
                    
                    $update = $this->replenish_model->updateReplenish($trCode,$binAwal, $qty, $binTujuan, $rackTujuan, $onAisle, $user, $queue, $noUrut);
                    if ($update == TRUE){
						$this->delSession();
                        $this->session->set_flashdata('succes', 'Bin Berhasil Di Simpan');
                        redirect(base_url().'index.php/replenish/my_outstanding');  
                    }else {
						$this->delSession();
                        $this->session->set_flashdata('error', 'Bin Gagal Di Simpan');
                        redirect(base_url().'index.php/replenish/my_outstanding');
                    }
                    
                }
        }
		
		// var_dump($this->session->userdata('qty'));
		
		$this->load->view('replenish/taruh_bin');
        
    }
	
	public function setSession()
	{
		$this->session->set_userdata('trCode',$this->input->post('trCode'));
		$this->session->set_userdata('ket',$this->input->post('ket'));
		$this->session->set_userdata('sku',$this->input->post('sku'));
		$this->session->set_userdata('binAwal',$this->input->post('binAwal'));
		$this->session->set_userdata('binTjn',$this->input->post('binTjn'));
		$this->session->set_userdata('rackAwal',$this->input->post('rackAwal'));
		$this->session->set_userdata('rackTjnNama',$this->input->post('rackTjnNama'));
		$this->session->set_userdata('rackTjnKode',$this->input->post('rackTjnKode'));
		$this->session->set_userdata('qty',$this->input->post('qty'));
		$this->session->set_userdata('qtyKonv',$this->input->post('qtyKonv'));
		$this->session->set_userdata('NoUrut',$this->input->post('NoUrut'));
		$this->session->set_userdata('QueueNumber',$this->input->post('QueueNumber'));
                $this->session->set_userdata('rackAwalKode',$this->input->post('rackAwalKode'));
		
	}
	
	public function delSession()
	{
		$this->session->unset_userdata('trCode');
		$this->session->unset_userdata('ket');
		$this->session->unset_userdata('sku');
		$this->session->unset_userdata('binAwal');
		$this->session->unset_userdata('binTjn');
		$this->session->unset_userdata('rackAwal');
		$this->session->unset_userdata('rackTjnKode');
		$this->session->unset_userdata('rackTjnNama');
		$this->session->unset_userdata('qty');
		$this->session->unset_userdata('qtyKonv');
		$this->session->unset_userdata('NoUrut');
		$this->session->unset_userdata('QueueNumber');
		
	}
    
    function get_nama_barang()
    {
        $kodeBin    = $_POST['kodeBinAwal'];
        
        $result     = $this->replenish_model->getNamaBarang($kodeBin);
        $array      = array();
        foreach ($result as $row){

            $array[] = array("RackSlot" => $row['RackSlotCode'], "SkuCode" => $row['SKUCode'], "Keterangan" => $row['Keterangan']);
        }
        echo $_GET['jsoncallback'].'('.json_encode($array).');'; 
     
    }
    
    function cekKodeRack($kodeRack)
    {
        if ($this->replenish_model->queryCekKodeRack($kodeRack)){
            $this->form_validation->set_message('cekKodeRack', 'Kode Rack Salah');
            return FALSE;
        }
        return TRUE;
    }
    
    function cekBin($kodeBin)
    {
       
        if ($this->replenish_model->cekBinReplenish($kodeBin) == '0'){
            $this->form_validation->set_message('cekBin', 'Kode Bin Tidak Ada');
            return FALSE;
        }
        return TRUE;
    }
    function cekQty($Qty)
    {
       $Rasio=  $this->input->post('satuan');
       
        if ($this->session->userdata('qty')!=$Qty*$Rasio){
            $this->form_validation->set_message('cekQty', 'Jumlah Salah');
            return FALSE;
        }
        return TRUE;
    }
    function get_data_replenish()
    {
        $kodeBin    = $_POST['kodeBinAwal'];
        
        $result     = $this->replenish_model->getDataRp($kodeBin);
        $array      = array();
        
        foreach ($result as $row){
            //var_dump($row);
//            $array[]    = array("skuCode" => $row['SKUCode'], "qtyAwal" => $row['QtyStart'], "rackSlotAwal" => $row['SrcRackSlot'], "nama" => $row['Note'], "id" => $row['NoUrut']);
            $array    = array("skuCode" => $row['SKUCode'], "qtyAwal" => $row['QtyStart'], "rackSlotAwal" => $row['SrcRackSlot'], "nama" => $row['Note'], "id" => $row['NoUrut']);
        } 
        echo $_GET['jsoncallback'].'('.json_encode($array).');';
    }
}

?>
