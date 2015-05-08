<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Barcode extends CI_Controller {

    function __construct() {
        parent::__construct();

        if (!$this->session->userdata('OperatorCode') || $this->session->userdata("OperatorRole") != '10/WHR/000' && $this->session->userdata("OperatorRole") != '10/WHR/999') {
            redirect(base_url() . "index.php/login");
        }
        $this->load->model('storing_model');
        $this->load->model('barcode_model');
    }

    /**
     * Return code39 1D barcode image
     * 
     * Get variable(s) required: text, size
     */
    public function index() {
        $this->load->view('barcode/barcode_main_view');
    }

    public function code39() {
        /*$text = $this->input->get('text');
        $text = $text ? $text : 'CODE39 1234567890';
        $size = $this->input->get('size');
        $size = $size ? intval($size) : 40;

        // Path to our font file
        $font = './fonts/FRE3OF9X.TTF';

        $bbox = imagettfbbox($size, 0, $font, $text);
        //printf("%d, %d, %d, %d, %d, %d, %d, %d",$bbox[0],$bbox[1],$bbox[2],$bbox[3],$bbox[4],$bbox[5],$bbox[6],$bbox[7]);
        // Create a 300x150 image
        $x = abs($bbox[4] - $bbox[0]);
        $y = abs($bbox[5] - $bbox[1]);
        $im = imagecreate($x, $y); //$bbox[2]+2, $bbox[3]+2
        $backColor = imagecolorallocate($im, 255, 255, 255);
        $textColor = imagecolorallocate($im, 0, 0, 0);
        imagealphablending($im, true); // set alpha blending on
        imagesavealpha($im, true); // save alphablending setting (important)
        // Write it
        imagettftext($im, $size, 0, 1, $y - $bbox[1], $textColor, $font, $text);

        // Output to browser
        header('Content-Type: image/png');

        imagepng($im);
        imagedestroy($im);*/
        /* 
 *  Author:  David S. Tufts 
 *  Company: Rocketwood.LLC 
 *      www.rocketwood.com 
 *  Date:    05/25/2003 
 *  Usage: 
 *      <img src="/barcode.php&text=testing" alt="testing" /> 
 */ 
     
    // Get pararameters that are passed in through $_GET or set to the default value 
    $text = (isset($_GET["text"])?$_GET["text"]:"0"); 
    $size = (isset($_GET["size"])?$_GET["size"]:"20"); 
    $orientation = (isset($_GET["orientation"])?$_GET["orientation"]:"horizontal"); 
    $code_type = (isset($_GET["codetype"])?$_GET["codetype"]:"code128"); 
    $code_string = ""; 

    // Translate the $text into barcode the correct $code_type 
    if(strtolower($code_type) == "code128") 
    { 
        $chksum = 104; 
        // Must not change order of array elements as the checksum depends on the array's key to validate final code 
        $code_array = array(" "=>"212222","!"=>"222122","\""=>"222221","#"=>"121223","$"=>"121322","%"=>"131222","&"=>"122213","'"=>"122312","("=>"132212",")"=>"221213","*"=>"221312","+"=>"231212",","=>"112232","-"=>"122132","."=>"122231","/"=>"113222","0"=>"123122","1"=>"123221","2"=>"223211","3"=>"221132","4"=>"221231","5"=>"213212","6"=>"223112","7"=>"312131","8"=>"311222","9"=>"321122",":"=>"321221",";"=>"312212","<"=>"322112","="=>"322211",">"=>"212123","?"=>"212321","@"=>"232121","A"=>"111323","B"=>"131123","C"=>"131321","D"=>"112313","E"=>"132113","F"=>"132311","G"=>"211313","H"=>"231113","I"=>"231311","J"=>"112133","K"=>"112331","L"=>"132131","M"=>"113123","N"=>"113321","O"=>"133121","P"=>"313121","Q"=>"211331","R"=>"231131","S"=>"213113","T"=>"213311","U"=>"213131","V"=>"311123","W"=>"311321","X"=>"331121","Y"=>"312113","Z"=>"312311","["=>"332111","\\"=>"314111","]"=>"221411","^"=>"431111","_"=>"111224","\`"=>"111422","a"=>"121124","b"=>"121421","c"=>"141122","d"=>"141221","e"=>"112214","f"=>"112412","g"=>"122114","h"=>"122411","i"=>"142112","j"=>"142211","k"=>"241211","l"=>"221114","m"=>"413111","n"=>"241112","o"=>"134111","p"=>"111242","q"=>"121142","r"=>"121241","s"=>"114212","t"=>"124112","u"=>"124211","v"=>"411212","w"=>"421112","x"=>"421211","y"=>"212141","z"=>"214121","{"=>"412121","|"=>"111143","}"=>"111341","~"=>"131141","DEL"=>"114113","FNC 3"=>"114311","FNC 2"=>"411113","SHIFT"=>"411311","CODE C"=>"113141","FNC 4"=>"114131","CODE A"=>"311141","FNC 1"=>"411131","Start A"=>"211412","Start B"=>"211214","Start C"=>"211232","Stop"=>"2331112");
        $code_keys = array_keys($code_array); 
        $code_values = array_flip($code_keys); 
        for($X = 1; $X <= strlen($text); $X++) 
        { 
            $activeKey = substr( $text, ($X-1), 1); 
            $code_string .= $code_array[$activeKey]; 
            $chksum=($chksum + ($code_values[$activeKey] * $X)); 
        } 
        $code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]]; 

        $code_string = "211214" . $code_string . "2331112"; 
    } 
    elseif(strtolower($code_type) == "code39") 
    { 
        $code_array = array("0"=>"111221211","1"=>"211211112","2"=>"112211112","3"=>"212211111","4"=>"111221112","5"=>"211221111","6"=>"112221111","7"=>"111211212","8"=>"211211211","9"=>"112211211","A"=>"211112112","B"=>"112112112","C"=>"212112111","D"=>"111122112","E"=>"211122111","F"=>"112122111","G"=>"111112212","H"=>"211112211","I"=>"112112211","J"=>"111122211","K"=>"211111122","L"=>"112111122","M"=>"212111121","N"=>"111121122","O"=>"211121121","P"=>"112121121","Q"=>"111111222","R"=>"211111221","S"=>"112111221","T"=>"111121221","U"=>"221111112","V"=>"122111112","W"=>"222111111","X"=>"121121112","Y"=>"221121111","Z"=>"122121111","-"=>"121111212","."=>"221111211"," "=>"122111211","$"=>"121212111","/"=>"121211121","+"=>"121112121","%"=>"111212121","*"=>"121121211"); 

        // Convert to uppercase 
        $upper_text = strtoupper($text); 

        for($X = 1; $X<=strlen($upper_text); $X++) 
        { 
            $code_string .= $code_array[substr( $upper_text, ($X-1), 1)] . "1"; 
        } 

        $code_string = "1211212111" . $code_string . "121121211"; 
    } 
    elseif(strtolower($code_type) == "code25") 
    { 
        $code_array1 = array("1","2","3","4","5","6","7","8","9","0"); 
        $code_array2 = array("3-1-1-1-3","1-3-1-1-3","3-3-1-1-1","1-1-3-1-3","3-1-3-1-1","1-3-3-1-1","1-1-1-3-3","3-1-1-3-1","1-3-1-3-1","1-1-3-3-1"); 

        for($X = 1; $X <= strlen($text); $X++) 
        { 
            for($Y = 0; $Y < count($code_array1); $Y++) 
            { 
                if(substr($text, ($X-1), 1) == $code_array1[$Y]) 
                    $temp[$X] = $code_array2[$Y]; 
            } 
        } 

        for($X=1; $X<=strlen($text); $X+=2) 
        { 
            $temp1 = explode( "-", $temp[$X] ); 
            $temp2 = explode( "-", $temp[($X + 1)] ); 
            for($Y = 0; $Y < count($temp1); $Y++) 
                $code_string .= $temp1[$Y] . $temp2[$Y]; 
        } 

        $code_string = "1111" . $code_string . "311"; 
    } 
    elseif(strtolower($code_type) == "codabar") 
    { 
        $code_array1 = array("1","2","3","4","5","6","7","8","9","0","-","$",":","/",".","+","A","B","C","D"); 
        $code_array2 = array("1111221","1112112","2211111","1121121","2111121","1211112","1211211","1221111","2112111","1111122","1112211","1122111","2111212","2121112","2121211","1121212","1122121","1212112","1112122","1112221");

        // Convert to uppercase 
        $upper_text = strtoupper($text); 

        for($X = 1; $X<=strlen($upper_text); $X++) 
        { 
            for($Y = 0; $Y<count($code_array1); $Y++) 
            { 
                if(substr($upper_text, ($X-1), 1) == $code_array1[$Y] ) 
                    $code_string .= $code_array2[$Y] . "1"; 
            } 
        } 
        $code_string = "11221211" . $code_string . "1122121"; 
    } 

    // Pad the edges of the barcode 
    $code_length = 20; 
    for($i=1; $i <= strlen($code_string); $i++) 
        $code_length = $code_length + (integer)(substr($code_string,($i-1),1)); 

    if(strtolower($orientation) == "horizontal") 
    { 
        $img_width = $code_length; 
        $img_height = $size; 
    } 
    else 
    { 
        $img_width = $size; 
        $img_height = $code_length; 
    } 

    $image = imagecreate($img_width, $img_height); 
    $black = imagecolorallocate ($image, 0, 0, 0); 
    $white = imagecolorallocate ($image, 255, 255, 255); 

    imagefill( $image, 0, 0, $white ); 

    $location = 10; 
    for($position = 1 ; $position <= strlen($code_string); $position++) 
    { 
        $cur_size = $location + ( substr($code_string, ($position-1), 1) ); 
        if(strtolower($orientation) == "horizontal") 
            imagefilledrectangle( $image, $location, 0, $cur_size, $img_height, ($position % 2 == 0 ? $white : $black) ); 
        else 
            imagefilledrectangle( $image, 0, $location, $img_width, $cur_size, ($position % 2 == 0 ? $white : $black) ); 
        $location = $cur_size; 
    } 
    // Draw barcode to the screen 
    header ('Content-type: image/png'); 
    imagepng($image); 
    imagedestroy($image); 
    }

    /**
     * Return qrcode 2D barcode image
     * 
     * Get variable(s) required: text, size
     */
    public function qrcode() {
        $text = $this->input->get('text');
        $text = $text ? $text : 'CODE39 1234567890';
        $size = $this->input->get('size');
        $size = $size ? intval($size) : 3;
        // Output to browser
        header('Content-Type: image/png');
        require_once('./tcpdf/2dbarcodes.php');
        $barcodeobj = new TCPDF2DBarcode($text, 'QRCODE,M');
        $barcodeobj->getBarcodePNG($size, $size, array(0, 0, 0));
    }

    /**
     * Return rack slot barcode label
     * 
     * Get variable(s) required: rackslotid, rackslotidend
     * Session variable(s) required: site_id
     */
    function cek_kodebinexist2($kodebin) {
        //cek apakah kodebin benar-benar ada
        if ($this->storing_model->cekvalidasiexistkodebin($kodebin) == false) {
            $this->form_validation->set_message('cek_kodebinexist2', 'Kode Bin Akhir Salah!');
            return false;
        }
        return true;
    }

    function cek_kodebinexist1($kodebin) {
        //cek apakah kodebin benar-benar ada
        if ($this->storing_model->cekvalidasiexistkodebin($kodebin) == false) {
            $this->form_validation->set_message('cek_kodebinexist1', 'Kode Bin Awal Salah!');
            return false;
        }
        return true;
    }

    function get_ajax_rack() {
        //ajax untuk menampilkan informasi bin yang di scan
        $RackName = $_POST['racknama'];
        $RackSlotCode = $_POST['rackkode'];
        //$bpb=$_POST['bpb'];
        $result = $this->barcode_model->getBarcodeRack($RackSlotCode, $RackName);
        $arr = array();
        foreach ($result as $row) {
            $arr[] = array("RackSlotCode" => $row["RackSlotCode"], "RackName" => $row["RackName"]);
        }
        echo $_GET['jsoncallback'] . '(' . json_encode($arr) . ');';
    }

    public function rack_barcode() {
        if ($this->input->post('btnPrint')) {
            $this->form_validation->set_rules('rackslotcode', 'Rack', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) { 
                $data['rack'] = $this->barcode_model->getAllBarcodeRack();
                $this->load->view('barcode/rack_barcode_view', $data);
            }
            else{
                $rackslotcode = $this->input->post('rackslotcode');
                $i = 0;
                $str = "";
                foreach ($rackslotcode as $row) {
                    $str.=$row;
                    if ($i < count($rackslotcode) - 1) {
                        $str.=",";
                    }
                    $i++;
                }
                $data['RackCode'] = $this->barcode_model->getBarcodeRackPrint($str);
                $this->load->view('barcode/print_rack_view', $data);
            }
        } else {
            $data['rack'] = $this->barcode_model->getAllBarcodeRack();
            $this->load->view('barcode/rack_barcode_view', $data);
        }
    }

    public function rack_barcode2() {
        if ($this->input->post('btnPrint')) {
            $rackslotcode = $this->input->post('rackslotcode');
            $i = 0;
            $str = "";
            foreach ($rackslotcode as $row) {
                $str.=$row;
                if ($i < count($rackslotcode) - 1) {
                    $str.=",";
                }
                $i++;
            }
            $data['RackCode'] = $this->barcode_model->getBarcodeRackPrint($str);
            $this->load->view('barcode/print_rack_view2', $data);
        } else {
            $data['rack'] = $this->barcode_model->getAllBarcodeRack();
            $this->load->view('barcode/rack_barcode_view2', $data);
        }
    }
    public function rack_barcode3() {
        if ($this->input->post('btnPrint')) {
            $this->form_validation->set_rules('rackslotcode', 'Rack', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) { 
                $data['rack'] = $this->barcode_model->getAllBarcodeRack();
                $this->load->view('barcode/rack_barcode_view3', $data);
            }
            else{
                $rackslotcode = $this->input->post('rackslotcode');
                $i = 0;
                $str = "";
                foreach ($rackslotcode as $row) {
                    $str.=$row;
                    if ($i < count($rackslotcode) - 1) {
                        $str.=",";
                    }
                    $i++;
                }
                $data['RackCode'] = $this->barcode_model->getBarcodeRackPrint($str);
                $this->load->view('barcode/print_rack_view3', $data);
            }
        } else {
            $data['rack'] = $this->barcode_model->getAllBarcodeRack();
            $this->load->view('barcode/rack_barcode_view3', $data);
        }
    }
    
    public function bin_barcode() {
        if ($this->input->post('btnPrint')) {
            $this->form_validation->set_rules('BinCode', 'Kode Bin', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $data['bin']=$this->barcode_model->getAllBarcodeBin();
                $this->load->view('barcode/barcode_view',$data);
            } else {
                $BinCode = $this->input->post('BinCode');
                $i = 0;
                $str = "";
                foreach ($BinCode as $row) {
                    $str.=$row;
                    if ($i < count($BinCode) - 1) {
                        $str.=",";
                    }
                    $i++;
                }
                $data['BinCode'] = $this->barcode_model->getBarcodeBin($str);
                $this->load->view('barcode/print_view', $data);
            }
        } else {
            $data['bin']=$this->barcode_model->getAllBarcodeBin();
                $this->load->view('barcode/barcode_view',$data);
        }
    }

    public function generate_barcode() {
        if ($this->input->post('btnGenerate')) {
            $this->form_validation->set_rules('gang', 'Gang', 'required');
            $this->form_validation->set_rules('kolom1', 'Kolom Awal', 'required|numeric');
            $this->form_validation->set_rules('kolom2', 'Kolom Akhir', 'required|numeric');
            $this->form_validation->set_rules('level1', 'Level Awal', 'required|numeric');
            $this->form_validation->set_rules('level2', 'Level Akhir', 'required|numeric');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $this->load->view('barcode/generate_barcode');
            } else {
                $gang = $this->input->post('gang');
                $kolom1 = $this->input->post('kolom1');
                $kolom2 = $this->input->post('kolom2');
                $level1 = $this->input->post('level1');
                $level2 = $this->input->post('level2');
                $tipe = $this->input->post('tipe');
                $kanankiri = $this->input->post('kanankiri');

                //$level1 = 1;
                //$level2 = 6;
                if ($tipe == 'S' || $tipe == 'H') {
                    //$level1 = 1;
                    //$level2 = 1;
                    if ($level1 > 1 || $level2 > 1) {
                        $data['error'] = 'Self / Half cuman boleh di level 1';
                        $this->load->view('barcode/generate_barcode', $data);
                        return;
                    }
                }
                $jumShelfnum = 1;
                if ($tipe == 'S') {
                    $jumShelfnum = 16;
                }
                if ($tipe == 'H') {
                    $jumShelfnum = 2;
                }
                $error = true;
                $i = $kolom1;
                $j = $level1;
                $k = 1;
                while ($i <= $kolom2 && $error == true) {
                    $j = $level1;
                    while ($j <= $level2 && $error == true) {
                        $k = 1;
                        while ($k <= $jumShelfnum && $error == true) {
                            $error = $this->barcode_model->setRackSlot($gang, $i, $kanankiri, $j, $tipe, $k);
                            $k++;
                        }
                        $j++;
                    }
                    $i++;
                }
                //generate general
                /*if ($tipe == 'H' || $tipe == 'S') {
                    $i = $kolom1;
                    $j = 2;
                    while ($i <= $kolom2 && $error == true) {
                        $j = 2;
                        while ($j <= 6 && $error == true) {
                            $k = 1;
                            while ($k <= 1 && $error == true) {
                                $error = $this->barcode_model->setRackSlot($gang, $i, $kanankiri, $j, 'G', $k);
                                $k++;
                            }
                            $j++;
                        }
                        $i++;
                    }
                }*/
                if ($error == false) {
                    $data['error'] = 'Input Gagal';
                    $this->load->view('barcode/generate_barcode', $data);
                } else {

                    $this->session->set_flashdata('pesan', 'Rack berhasil ditambahkan.');
                    $this->load->view('barcode/generate_barcode');
                }
            }
        } else {
            $this->load->view('barcode/generate_barcode');
        }
    }
    public function generate_bin()
    {
        if ($this->input->post('btnGenerate')) {
            $this->form_validation->set_rules('kodebin1', 'Bin Awal', 'required|numeric|callback_cek_BinGenerate');
            $this->form_validation->set_rules('kodebin2', 'Bin Akhir', 'required|numeric');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            if ($this->form_validation->run() == FALSE) {
                $data['type']=$this->barcode_model->gettypebin();
                $this->load->view('barcode/generate_bin',$data);
            } else {
                $kodebin1=  $this->input->post('kodebin1');
                $kodebin2=  $this->input->post('kodebin2');
                $type=$this->input->post('type');
                $status=true;
                while($kodebin1<=$kodebin2 && $status==TRUE)
                {
                    if(!$this->barcode_model->generateBin($kodebin1,$type,$this->session->userdata('OperatorCode'))){
                        $status=false;
                    }
                    $kodebin1++;
                }
                if($status==false)
                {
                    $data['type']=$this->barcode_model->gettypebin();
                    $data['error']='Bin Gagal Digenerate sampai '.($kodebin1-1);
                    $this->load->view('barcode/generate_bin',$data);
                }
                else
                {
                    $this->session->set_flashdata('pesan','Bin Berhasil Digenerate');
                    redirect(base_url().'index.php/barcode/generate_bin');
                }
            }
        }
        else{
            $data['type']=$this->barcode_model->gettypebin();
            $this->load->view('barcode/generate_bin',$data);
        }
    }
    function cek_BinGenerate($kodebin1)
    {
        $kodebin2=  $this->input->post('kodebin2');
        if ($kodebin1>$kodebin2) {
            $this->form_validation->set_message('cek_BinGenerate', 'Kode Bin Awal Salah!');
            return false;
        }
        return true;
    }
    public function bpb() {
		
		if ($this->input->post('btnPrint')){
			$this->form_validation->set_rules('keterangan', 'Note' ,'required');
			$this->form_validation->set_rules('jmlPrint', 'Jumlah','required|numeric');
			$this->form_validation->set_error_delimiters('<div class="alert alert-error"><h5>', '</h5></div>');
            
			if ($this->form_validation->run() == FALSE) {
				$data['bpb'] = $this->barcode_model->getAllBpb();
				$this->load->view('barcode/bpb_view', $data);
			}else{
				$explode = explode('/', $this->input->post('kodeNota'));
				
				$data['kodeNotaExplode'] 	= $explode[4];
				$data['kodeNota'] 			= $this->input->post('kodeNota');
				$data['keterangan'] 		= $this->input->post('keterangan');
				$data['jmlPrint'] 			= $this->input->post('jmlPrint') * 15;
				
				
				// var_dump($explode);
				$this->load->view('barcode/print_bpb_view', $data);
			}
			
		}else {
		// var_dump($this->input->post());
			$data['bpb'] = $this->barcode_model->getAllBpb();
			$this->load->view('barcode/bpb_view', $data);
		}
	}
	
	public function barang()
	{
		if($this->input->post('btnPrint')){
		
			$data['barang'] = $this->barcode_model->getBarang();
			$this->load->view('barcode/print_barang', $data);
		}else{		
		
			$data['barang'] = $this->barcode_model->getBarang();
			$this->load->view('barcode/barang',$data);
		}
	}

}

?>
