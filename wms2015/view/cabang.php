<?php session_start();
 //print_r($_COOKIE);?>
<div class="header">        

    <h1 class="page-title">Cabang/Divisi</h1>
</div>
<div class="main-content col-xs-6 col-xs-offset-3" style="height:100%">
	<div class="panel panel-default">
		<a href="#page-stats" class="panel-heading" data-toggle="collapse">BCP Conection</a>
		<div id="page-stats" class="panel-collapse panel-body collapse in">
					
					<div class="row">
					
						<div class="form-group col-xs-12">
						
							<div class="col-xs-4"><label class="control-label">Ip/Nama Server : </label></div>
							<div class="col-xs-8"><input type="text" class="form-control" id="server" value="<?php if(isset($_COOKIE['server'])) echo $_COOKIE['server']?>">
								

							</div>
						</div>
						<div class="form-group col-xs-12">
						
							<div class="col-xs-4"><label class="control-label">Username : </label></div>
							<div class="col-xs-8"><input type="text" class="form-control" id="user" value="<?php if(isset($_SESSION['user'])) echo $_SESSION['user']?>"></div>
						</div>
						<div class="form-group col-xs-12">
						
							<div class="col-xs-4"><label class="control-label">Password : </label></div>
							<div class="col-xs-8"><input type="password" class="form-control" id="pass" value="<?php if(isset($_SESSION['pass'])) echo $_SESSION['pass']?>"></div>
						</div>
						<div class="form-group col-xs-12">
						
							<div class="col-xs-4"><label class="control-label">Database : </label></div>
							<div class="col-xs-8"><input type="text" class="form-control" id="database" value="<?php if(isset($_COOKIE['dbase'])) echo $_COOKIE['dbase']?>"></div>
						</div>
												
											
					</div>
					
					<div class="row" style="text-align: center;">
						<button type="button" class="btn btn-primary" id="simpancon" onclick="setconect()">Simpan</button>
						<button type="button" class="btn btn-warning">Batal</button>
					</div>
		</div>
	</div>

	
</div>

<div class="main-content col-xs-6 col-xs-offset-3" id="divisi" style="height:100%;display:none">
	<div class="panel panel-default">
		<a href="#page-stats" class="panel-heading" data-toggle="collapse">Divisi</a>
		<div id="page-stats" class="panel-collapse panel-body collapse in">
					
					<div class="row" id="boxdivisi">					
												
											
					</div>
					
					<div class="row" style="text-align: center;">
						<button type="button" class="btn btn-primary" id="simpandiv" onclick="simpandivis()">Simpan</button>
						<button type="button" class="btn btn-warning">Batal</button>
					</div>
		</div>
	</div>

	
</div>