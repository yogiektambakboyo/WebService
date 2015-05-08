<?php session_start();?>
<!doctype html>
<html lang="en"><head>
    <meta charset="utf-8">
    <title>BCP WMS</title>
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="stylesheets/theme.css">
    <link rel="stylesheet" type="text/css" href="stylesheets/premium.css">

    <link rel="stylesheet" type="text/css" href="lib/bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="lib/bootstrap/css/tablesorter.css">
	<link rel="stylesheet" type="text/css" href="lib/bootstrap/css/bootstrap-datetimepicker.css">
    <link rel="stylesheet" href="lib/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="stylesheets/theme.blue.css">
    <link rel="stylesheet" href="lib/pnotify.3.0/pnotify.custom.min.css">

    <link href="lib/jquery-ui-1.11.2/jquery-ui.min.css" rel="stylesheet" type="text/css">
    <script src="lib/jquery-1.11.1.min.js" type="text/javascript"></script>
    <script src="lib/jQuery-Knob/js/jquery.knob.js" type="text/javascript"></script>
    <script src="lib/jquery-ui-1.11.2/jquery-ui.min.js" type="text/javascript"></script>
    <script src="lib/jquery.blockUI.js" type="text/javascript"></script>

</head>

<body class="theme-blue">

    <!-- Demo page code -->

    <script type="text/javascript">
        $(function() {
            var match = document.cookie.match(new RegExp('color=([^;]+)'));
            if(match) var color = match[1];
            if (!color) {
            } else {
                $('body').removeClass(function (index, css) {
                    return (css.match(/\btheme-\S+/g) || []).join(' ')
                });
                $('body').addClass('theme-' + color);
            }

            $('[data-popover="true"]').popover({html: true});

        });
    </script>
    <style type="text/css">
		body, html {
			  height: 100%;
			}

        #line-chart {
            height:300px;
            width:800px;
            margin: 0px auto;
            margin-top: 1em;
        }
        .navbar-default .navbar-brand, .navbar-default .navbar-brand:hover {
            color: #fff;
        }

		#rightbox {
			border: solid 1px black;
			margin: 10px;
			padding: 10px;
			position: absolute;
			background-color: white;
			display:none;
		}
		.menugrup{
			display:none;
		}
        .loading-progress{
            width:100%;
            text-align: center;
        }

       .tblbtn{
           padding: 2px 5px;
       }
       .tbltxtbox{
           border: none;
           background: transparent;
       }
    </style>

    <script type="text/javascript">
        $(function() {
            var uls = $('.sidebar-nav > ul > *').clone();
            uls.addClass('visible-xs');
            $('#main-menu').append(uls.clone());
        });
    </script>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="../assets/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">


  <!--[if lt IE 7 ]> <body class="ie ie6"> <![endif]-->
  <!--[if IE 7 ]> <body class="ie ie7 "> <![endif]-->
  <!--[if IE 8 ]> <body class="ie ie8 "> <![endif]-->
  <!--[if IE 9 ]> <body class="ie ie9 "> <![endif]-->
  <!--[if (gt IE 9)|!(IE)]><!-->

  <!--<![endif]-->

    <div class="navbar navbar-default" role="navigation" style="margin-bottom: 0px">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="" href="index.html"><span class="navbar-brand"><span class="fa fa-cubes"></span> BCP WMS</span></a></div>

        <div class="navbar-collapse collapse" style="height: 1px;">
          <ul id="main-menu" class="nav navbar-nav navbar-right">
            <li class="dropdown hidden-xs">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <span class="glyphicon glyphicon-user padding-right-small" style="position:relative;top: 3px;"></span>
						<?php if(isset($_SESSION['user'])){echo $_SESSION['user'];}else{echo "User";}?>

                    <i class="fa fa-caret-down"></i>
                </a>

              <ul class="dropdown-menu">
                <li><a tabindex="-1" href="script/logout.php">Logout</a></li>
              </ul>
            </li>
          </ul>

        </div>
      </div>
    </div>


    <div class="sidebar-nav">
		<ul>
			<li><a href="javascript:void(0)" class="nav-header" onclick="showinterface('view/cabang.php','#viewarea')"><span class="fa fa-caret-right"></span> Cabang/Divisi</a></li>
			<?php include_once "script/usermenu.php";?>


		</ul>
    </div>

    <div class="content">
        <div id="viewarea">

			<?php //include_once "view/cabang.php";?>

		</div>

		<!-- Default bootstrap modal example -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

              </div>
              <div class="modal-body" style="max-height: 476px;overflow: auto;width: 100%;">
                ...
              </div>

            </div>
          </div>
        </div>

		<footer style="position: absolute;bottom: 0px;width: 97%;">
                <hr>


                <p>© 2015 Borwita Citra Prima</a></p>
        </footer>
	</div>


    <script src="lib/bootstrap/js/bootstrap.js"></script>
    <script src="lib/bootstrap/js/jquery.tablesorter.js"></script>
	<script src="lib/bootstrap/js/jquery.tablesorter.widgets.js"></script>
	<script src="lib/bootstrap/js/jquery.stickytableheaders.js"></script>
	<script src="lib/bootstrap/js/moment.js"></script>
	<script src="lib/bootstrap/js/bootstrap-datetimepicker.js"></script>
	<script src="lib/pnotify.3.0/pnotify.custom.min.js"></script>

	<script type="text/javascript">

		$(document).ready(function()
			{
				 refreshtabel();

                var lastloc=$(location).attr('href').split("#");
                if(lastloc[1]==undefined || lastloc[1]==""){
                    checkdivisi('view/cabang.php','#viewarea');
                }else{
                    checkdivisi(lastloc[1],'#viewarea');
                }

			}
		);


		$(document).click(function(e) {
			$("#rightbox").hide();
		});

		function refreshtabel(){
			$("#RetTable").tablesorter({
					theme : "bootstrap", // this will

                    widthFixed: true,

                    headerTemplate : '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

                    // widget code contained in the jquery.tablesorter.widgets.js file
                    // use the zebra stripe widget if you plan on hiding any rows (filter widget)
                    widgets : [ "uitheme", "filter" ],
                    headers: { },
                    widgetOptions : {
                        // using the default zebra striping class name, so it actually isn't included in the theme variable above
                        // this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
                        zebra : ["even", "odd"],

                        // reset filters button
                        filter_reset : ".reset",
                        filter_formatter : {
                            0 : function(){return false;}
                        }

                        // set the uitheme widget to use the bootstrap theme class names
                        // uitheme : "bootstrap"
					}


				});

			$('#tglawal').datetimepicker({format: 'YYYY-MM-DD'});
			$('#tglakhir').datetimepicker({format: 'YYYY-MM-DD'});
			$("#tglawal").on("dp.change",function (e) {
			$('#tglakhir').data("DateTimePicker").minDate(e.date);

			});
			$("#tglakhir").on("dp.change",function (e) {
			$('#tglawal').data("DateTimePicker").maxDate(e.date);

			});

		}


		$(document).on("click", "#RfsRtr", function () {
            $("#RfsRtr").prop('disabled', true);
            $("#RtrTbl").html("");
            if ($('#tglakhir').val() == "") {
                $('#tglakhir').val($("#tglawal").val())
            }
            if ($('#tglawal').val() == "") {
                $('#tglawal').val($("#tglakhir").val())
            }

            var tglawal = $("#tglawal").val();
            var tglakhir = $("#tglakhir").val();
            var formData = "tglawal=" + tglawal + "&tglakhir=" + tglakhir;

            $.ajax({
                url: "retur/retur.php",
                type: "POST",
                data: formData,
                success: function (html) {

                    //alert(html);
                    //data - response from server
                    $("#RtrTbl").html(html);
                    refreshtabel();
                    $("#RfsRtr").prop('disabled', false)
                }
            });
        });


		$(document).on("click", "#RfsRtrTbl", function () {
            $("#RtrTbl").html("");
            if ($('#tglakhir').val() == "") {
                $('#tglakhir').val($("#tglawal").val())
            }
            if ($('#tglawal').val() == "") {
                $('#tglawal').val($("#tglakhir").val())
            }

            var tglawal = $("#tglawal").val();
            var tglakhir = $("#tglakhir").val();
            var formData = "tglawal=" + tglawal + "&tglakhir=" + tglakhir;

            $.ajax({
                url: "retur/returtbl.php",
                type: "POST",
                data: formData,
                success: function (html) {
                    //alert(data);
                    //data - response from server
                    $("#RtrTbl").html(html);
                    refreshtabel()
                }
            });
        });

		function senddate(url){
			$("#RtrTbl").html("");
			$("#Refreshbtn").prop('disabled', true);
			if($('#tglakhir').val()==""){
				$('#tglakhir').val($("#tglawal").val())
				}
			if($('#tglawal').val()==""){
				$('#tglawal').val($("#tglakhir").val())
				}

			var tglawal=$("#tglawal").val();
			var tglakhir=$("#tglakhir").val();
			var formData="tglawal="+tglawal+"&tglakhir="+tglakhir;

            $('#RtrTbl').html('<div class="loading-progress"><img src="images/loading-icons/squares.svg"></div>');

           	$.ajax({
				url : url,
				type: "POST",
				data : formData,
				success: function(html)
				{
					//alert(data);
					//data - response from server
					$("#RtrTbl").html(html);
					refreshtabel();
					$("#Refreshbtn").prop('disabled', false)
				}
			});
		}


		function test(){
			var chekboxid = $('input[name="rekap"]:checked').map(function() {
				return this.id;
			}).get();
			alert(chekboxid);

			//$('#RetTable tr:has(td#'+checkedid+')').hide()
			//alert(chekboxid.length)

			for (i=0;i<chekboxid.length;i++){
				$('#' + chekboxid[i]).closest("tr").hide();
				$('#'+chekboxid[i]).attr("checked",false)
			}

		}

		 $(document).on("click", "#simpanrkp", function () {
            //alert("asdasd")
            var keterangan = window.prompt("Keterangan Rekap", "");
            var checkedValues = $('input[name="rekap"]:checked').map(function () {
                return this.value;
            }).get();

            var chekboxid = $('input[name="rekap"]:checked').map(function () {
                return this.id;
            }).get();

            //alert(checkedValues)
            var notaedit = $('#notaedit').val();

            var gudang = $("#gudang").html();
            var data = "kodenota=" + checkedValues + "&gudang=" + gudang + "&notaedit=" + notaedit + "&keterangan="+ keterangan;

            $.ajax({
                url: "retur/simpan.php",
                type: "POST",
                data: data,
                success: function (html) {
                    alert(html);
                    $('#notaedit').val('');
                    //showinterface('view/retur.php','#viewarea')

                    for (i = 0; i < chekboxid.length; i++) {
                        $('#' + chekboxid[i]).closest("tr").hide();
                        $('#' + chekboxid[i]).attr("checked", false)
                    }

                }
            });


        });

		function getgudang(kode,elem){
            var gudang = $("#gudang").html();
            var chkid = "#" + $(elem).attr("id");
			if (gudang != "") {
                if (gudang != kode) {
                    alert("Gudang yang dipilih harus sama");

                    $(chkid).attr("checked", false)
                }
            } else $("#gudang").html(kode);

			var checkedValues = $('input[name="rekap"]:checked').map(function() {
				return this.value;
			}).get();

			if(checkedValues==""){
				$("#gudang").html("")
			}
		}

		function showinterface(url,id){
            $(id).html('<div class="loading-progress"><img src="images/loading-icons/squares.svg"></div>');
			$(id).load(url, function(){
					 refreshtabel();
				});
		}

		function setconect(){
			var server=$("#server").val();
			var user=$("#user").val();
			var pass=$("#pass").val();
			var dbase=$("#database").val();
			var data="server="+server+"&user="+user+"&pass="+pass+"&dbase="+dbase;

			$.ajax({
				url : "cabang/cabang.php",
				type: "POST",
				data : data,
				success: function(html)
				{
					//alert(html)
					$("#boxdivisi").html(html);
					$("#divisi").show("slow");
				}
			});

}
        function simpandivis(){

			var data="divisi="+$("#SlctDivisi").val();
			$.ajax({
				url : "cabang/divisi.php",
				type: "POST",
				data : data,
				success: function(html)
				{
					//alert(html)
					location.reload();
				}
			});
		}

		function checkdivisi(menu,div){
			var data="divisi";
            //$("li").removeClass("active");
            //alert($(this).closest("li").html());
            //$(this).closest("li").addClass("active");

			$.ajax({
				url : "cabang/cekdivisi.php",
				type: "POST",
				data : data,
				success: function(html)
				{
					//alert(html)
					if(html=="true"){
					showinterface(menu,div)
					}else{
					showinterface('view/cabang.php','#viewarea')
					}

				}
			});
		}

		$(document).on("contextmenu","#RetTable tbody tr",function(e) {
			e.preventDefault();
            e.stopPropagation();
			$("#rightbox").html("<a href='javascript:void(0)' id='edit" + $(this).attr('id') + "' onclick='editretur(this)'>Edit</a>");
			//$('#rightbox').offset( {top: e.offsetY, left:e.offsetX} ).show();
			//$('#rightbox').offset( {top: e.pageY, left:e.pageX} ).show();
            $('#rightbox').css({
                top:(e.pageY-57) + 'px',
                left:(e.pageX-246) + 'px'
            }).show();
		});

		function editretur(kode){
			var rowid=$(kode).attr("id").substr(4);
            var kodenota = $("#" + rowid).find("td").eq(1).html();
            var wmsrcpt = $("#" + rowid).find("td").eq(8).html();
			var data="kodenota="+kodenota+"&wmsrcpt="+wmsrcpt;
			//alert(kodenota)
			//alert(wmsrcpt)
            $('#viewarea').html('<div class="loading-progress"><img src="images/loading-icons/squares.svg"></div>');
			$.ajax({
				url : "view/retur.php",
				type: "POST",
				data : data,
				success: function(html)
				{

					$("#viewarea").html(html);
					$("#gudang").html($("#gudangedit").val());
					refreshtabel();
                    $("#wmsrcpt").val(wmsrcpt);
                    if (wmsrcpt==1){
                        $('input[name="rekap"]').attr('disabled','disabled');
                    }


				}
			});
		}


		$("#myModal").on("show.bs.modal", function(e) {

			//var link = $(e.relatedTarget);

			//$(this).find(".modal-body").load(link.attr("href"));
		});

    function showdetailmodal(kd){
        //$(".modal-title").html(kd);
        $(".modal-body").load(kd);
        $('#myModal').modal('show');
    }

        $(document).on("click", ".closebtn", function () {
            var isrecipt = $(this).parents('tr').find('td:eq(4)').html();
            var kodepo = $(this).parents('tr').find('td:eq(1)').html();

            if (isrecipt == 1) {
            } else {
                alert("IsRecipt 0 transaksi belum bisa Close");
                return
            }
            var data = "isrecipt=" + isrecipt + "&kodepo=" + kodepo;

            $.ajax({
                url: "taskinbound/update.php",
                type: "POST",
                data: data,
                success: function (html) {
                    alert($(this).parents('tr').find('td:eq(3)').html());
                    $("#refreshbtn").click();
                    $(this).parents('tr').find('td:eq(5)').html('1');
                }
            });

        });
        $(document).on("click","tbody tr",function() {
            $(this).addClass('info').siblings().removeClass('info');
        });

        $(document).on("click", ".nav-list li", function () {
            $(".nav-list li").removeClass("active");
            $(this).addClass("active");

        })



	</script>

</body>
</html>
