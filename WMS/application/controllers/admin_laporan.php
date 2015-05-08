<?php 

class Admin_Laporan extends CI_Controller
{
	
	public function __construct() 
	{	
		parent::__construct();
        if (!$this->session->userdata('OperatorCode')) {
            redirect(base_url() . "index.php/login");
        }
		
		$this->load->model('admin_laporan_model');
	
	}
	
	public function stok_barang()
	{
		$data['brg']=$this->admin_laporan_model->getBarang();
        $this->load->view('admin_laporan/stok_barang',$data);
		
	}
	
	
	
}