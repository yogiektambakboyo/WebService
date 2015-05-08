<?php

require_once APPPATH."third_party/fpdf17/fpdf".EXT;

class Pdf extends FPDF
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function header()
	{
		global $title;
		global $user;
		
		$user  = $this->author;
		$title = $this->title;
		
		$this->setFont('Arial','B',14);
		$w = $this->GetStringWidth($title)+6;// get width
		//$this->SetX((210-$w)/2);
		$this->SetX(10);// set margin left
		
		// Colors of frame, background and text
		$this->SetDrawColor(0,0,0);
		$this->SetFillColor(255,255,255);
		$this->SetTextColor(0,0,0);
		// Thickness of frame (1 mm) //ketebalan
		$this->SetLineWidth(1);
		
		// Title
		//$pdf->Cell(width, height, text, border, position-next-cell, alignment);
		$this->Cell($w,9,$title,1,0,'C',true);
		$this->setFont('Arial','B',9);
		$this->Cell(125,1,'Tanggal Cetak : ' .date('d M Y H:i:s'),0,1,'R');
		$this->Cell(189,10,'Petugas : ' .$user,0,2,'R');
		
		// Line break
		$this->Ln(10);
	}
	
	public function basicTable($header, $data, $width)
	{
		
		for($i=0;$i<count($header);$i++)
			$this->Cell($width[$i],7,$header[$i],1,0,'C');
		$this->Ln();
		
		for($i=0;$i<count($header);$i++)
			$this->Cell($width[$i],7,$data[$i],1,0,'C');
		$this->Ln();
		
	}
	
	public function Footer()
	{
		// Position at 1.5 cm from bottom
		// $this->SetY(-1.5);
		// Arial italic 8
		// $this->SetFont('Arial','',12);
		// Page number
		// $this->Cell(0,25,'Keterangan : .........................................................................................................................................',0,2);
		// $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}
	
	/*
	public function ImprovedTable($tableFooter,$name, $width2)
	{
		   // Column widths
		$width2 = array(40, 35, 40, 45);
		// Header
		for($i=0;$i<count($tableFooter);$i++)
			$this->Cell($width2[$i],7,$tableFooter[$i],1,0,'C');
		$this->Ln();
		// Data
		foreach($name as $row)
		{
			$this->Cell($width2[0],6,$row[0],'LR');
			$this->Cell($width2[1],6,$row[1],'LR');
			$this->Cell($width2[2],6,$row[2],'LR',0,'R');
			$this->Cell($width2[3],6,$row[3],'LR',0,'R');
			$this->Ln();
		}
		// Closing line
		$this->Cell(array_sum($width2),0,'','T');
	}
	
	
	*/
	
	
	
	
}





