<?php
if ($this->session->userdata('OperatorCode') && $this->session->userdata("OperatorRole") != '10/WHR/000') {
    ?>

    <div class="container">
        <div class="row">
            <div class="span12 center">
                <hr />
                <strong style="margin: 5px"><i class="icon-user" ></i> : <?php echo $this->session->userdata('login_name'); ?> </strong>
                <a href="<?php echo base_url(); ?>index.php/reset/bin" class="btn btn-warning btn-small" style="margin: 5px"><i class="icon-inbox" ></i> Reset Bin </a>
                <a href="<?php echo base_url(); ?>index.php/login" class="btn btn-primary btn-small" style="margin: 5px"><i class="icon-home" ></i> Home </a>
                <a href="<?php echo base_url(); ?>index.php/umum" class="btn btn-primary btn-small" style="margin: 5px"><i class="icon-search" ></i> Cari </a>
                <button id="logout" class="btn btn-danger btn-small" style="margin: 5px"><i class="icon-off"></i> Log Out </button>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="span12 center">
                <hr />

            </div>
        </div>
    </div>
    <?php
}
?>
<script type="text/javascript">
    $("#logout").click(function(){
        //alert("masuk");
        $.post("<?php echo base_url(); ?>index.php/login/getstatusoutstanding",
        function(data,status){
          
            if(data==1)
            {
                window.location = "<?php echo base_url(); ?>index.php/login/logout"; 
            }
            else
            {
                if (!confirm("Anda Masih Punya Outstanding, Anda Yakin Mau Keluar?"))
                {
                    return false;
                }
                else
                {
                    window.location = "<?php echo base_url(); ?>index.php/login/logout";
                }
                 
            }
        });
    });

</script>