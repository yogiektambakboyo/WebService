<?php session_start();?>
<div class="header">        

    <h1 class="page-title">Inbound Task</h1>
</div>
<div class="main-content" style="height:100%">
	<div class="panel panel-default">
		<a href="#page-stats" class="panel-heading" data-toggle="collapse"></span></span> </a>
		<div id="page-stats" class="panel-collapse panel-body collapse in">
					
					<div class="row">
					
						<div class="form-group col-xs-5">
						
							<div class="col-xs-3"><label class="control-label">Tgl Awal : </label></div>
							<div class="col-xs-9"><input type="text" class="form-control" id="tglawal" value="<?php echo date('Y-m-01');?>"></div>
						</div>
						<div class="form-group col-xs-5">
						
							<div class="col-xs-3"><label class="control-label">Tgl Akhir : </label></div>
							<div class="col-xs-9"><input type="text" class="form-control" id="tglakhir" value="<?php echo date('Y-m-t');?>"></div>
						</div>


						
						<div class="form-group col-xs-2">
						
							<button type="button" class="btn btn-primary" id="Refreshbtn" onclick="senddate('taskinbound/<?php echo $_GET["menu"];?>')">Refresh</button>
						</div>
					
					</div>
					<div class="scrollable-area" style="max-height:350px;overflow:scroll;font-size: 12px;">
						<div class="row" id="RtrTbl" style="margin: 0px">
							<?php include_once "../taskinbound/".$_GET["menu"];?>
						</div>
					</div>
					
		</div>
	</div>

	
</div>

<script>
    $(document).on("click",".editship",function(){
        var shiptoawal=$(this).parents('tr').find('td:eq(2)').html();

        $(this).removeClass("editship");
        $(this).addClass("simpanship");
        $(this).text("Simpan");
        $(this).parents('tr').find('td:eq(2)').html('<input type="text" id="txtShipto">');
    })
    $(document).on("click",".simpanship",function(){
        $(this).parents('tr').find('td:eq(2)').html($("#txtShipto").val());

        //alert($(this).parents('tr').find('.closebtn').attr("onclick"));

        //$(this).addClass("editship");
        //$(this).removeClass("simpanship");
        //$(this).text("Edit Shipto")
        var shipto=$(this).parents('tr').find('td:eq(2)').html();
        var POnumber=$(this).parents('tr').find('td:eq(1)').html();
        var data="shipto="+shipto+"&POnumber="+POnumber;
        //alert(data);
        $.ajax({
            url: "taskinbound/update.php",
            type: "POST",
            data: data,
            success: function (html) {
                alert(html);
            }
        });

    })
</script>