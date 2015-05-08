<?php session_start();?>
<div class="header">        

    <h1 class="page-title">Retur</h1>
</div>
<div class="main-content" style="height:100%">
	<div class="panel panel-default">
		<a href="#page-stats" class="panel-heading" data-toggle="collapse"><?php echo $_SESSION["dbase"];?> <span>Gudang <span id="gudang"></span></span> </a>
		<div id="page-stats" class="panel-collapse panel-body collapse in">
					
					<div class="row" style="display: none">
                        <input type="hidden" id="wmsrcpt">
					
						<div class="form-group col-xs-5">
						
							<div class="col-xs-3"><label class="control-label">Tgl Awal : </label></div>
							<div class="col-xs-9"><input type="text" class="form-control" id="tglawal" value="<?php echo date('Y-m-01');?>"></div>
						</div>
						<div class="form-group col-xs-5">
						
							<div class="col-xs-3"><label class="control-label">Tgl Akhir : </label></div>
							<div class="col-xs-9"><input type="text" class="form-control" id="tglakhir" value="<?php echo date('Y-m-t');?>"></div>
						</div>
						
						
						
						<div class="form-group col-xs-2">
						
							<button type="button" class="btn btn-primary" id="Refreshbtn" onclick="senddate('retur/retur.php')">Refresh</button>
						</div>
					
					</div>
					<div class="scrollable-area" style="max-height:350px;overflow:scroll;font-size: 12px;">
						<div class="row" id="RtrTbl">
							<?php include_once "../retur/retur.php";?>
						</div>
					</div>
					<div class="row" style="text-align: center;">
						<button type="button" class="btn btn-primary" id="simpanrkp">Simpan</button>
						<button type="button" class="btn btn-warning" onclick="test()">Batal</button>
					</div>
		</div>
	</div>

	
</div>