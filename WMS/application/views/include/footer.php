


<!-- Le javascript
        ================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="<?php echo base_url() ?>files/bootstrap/js/jquery.js"></script>
<script src="<?php echo base_url() ?>files/bootstrap/js/bootstrap-transition.js"></script>
<script src="<?php echo base_url() ?>files/bootstrap/js/bootstrap-alert.js"></script>
<script src="<?php echo base_url() ?>files/bootstrap/js/bootstrap-modal.js"></script>
<script src="<?php echo base_url() ?>files/bootstrap/js/bootstrap-dropdown.js"></script>
<script src="<?php echo base_url() ?>files/bootstrap/js/bootstrap-scrollspy.js"></script>
<script src="<?php echo base_url() ?>files/bootstrap/js/bootstrap-tab.js"></script>
<script src="<?php echo base_url() ?>files/bootstrap/js/bootstrap-tooltip.js"></script>
<script src="<?php echo base_url() ?>files/bootstrap/js/bootstrap-popover.js"></script>
<script src="<?php echo base_url() ?>files/bootstrap/js/bootstrap-button.js"></script>
<script src="<?php echo base_url() ?>files/bootstrap/js/bootstrap-collapse.js"></script>
<script src="<?php echo base_url() ?>files/bootstrap/js/bootstrap-carousel.js"></script>
<script src="<?php echo base_url() ?>files/bootstrap/js/bootstrap-typeahead.js"></script>
<script src="<?php echo base_url() ?>files/bootstrap/js/holder/holder.js"></script>
<script src="<?php echo base_url() ?>files/bootstrap/js/jquery.tablesorter.js"></script>
<script src="<?php echo base_url() ?>files/bootstrap/js/jquery.tablesorter.pager.js"></script>
<script src="<?php echo base_url() ?>files/bootstrap/js/jquery.tablesorter.widgets.js"></script>
<!--script src="<?php echo base_url() ?>files/fixheadertable/js/jquery.fixheadertable.js"></script-->


<?php
if ($this->session->userdata('OperatorRole') == '10/WHR/000' or $this->session->userdata('OperatorRole') == '10/WHR/999') {
    ?>
    <script src="<?php echo base_url() ?>files/js/jquery-ui-1.10.3.custom.min.js"></script>
    <?php
}
?>

<script type="text/javascript">
    onload=function(){

        var e=document.getElementById("refreshed");
        if(e.value=="no")e.value="yes";
        else{e.value="no";location.reload();}
    }
</script>
<?php
$this->load->view('include/nav');
?>