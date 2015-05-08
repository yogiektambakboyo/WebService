<!-- Le styles -->
<link href="<?php echo base_url() ?>files/bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="<?php echo base_url() ?>files/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
<link href="<?php echo base_url() ?>files/css/style.css" rel="stylesheet">
<link href="<?php echo base_url() ?>files/bootstrap/css/jquery.tablesorter.pager.css" rel="stylesheet">
<link href="<?php echo base_url() ?>files/bootstrap/css/theme.bootstrap.css" rel="stylesheet">
<!--link href="<?php echo base_url() ?>files/fixheadertable/css/base.css" rel="stylesheet" /-->

<!--link href="<?php echo base_url() ?>files/wysihtml5/bootstrap-wysihtml5-0.0.2.css" rel="stylesheet" /-->
<?php
if ($this->session->userdata('OperatorRole') == '10/WHR/000' or $this->session->userdata('OperatorRole') == '10/WHR/999') {
    ?>
    <link href="<?php echo base_url() ?>files/css/ui-lightness/jquery-ui-1.10.3.custom.min.css" rel="stylesheet" />
    <?php
}
?>
<?php
if ($this->session->userdata("OperatorRole") == '10/WHR/000' or $this->session->userdata("OperatorRole") == '10/WHR/999') {
    $this->load->view('include/admin_nav');
}
?>