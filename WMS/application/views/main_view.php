<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?php echo $this->session->userdata("RoleName"); ?> WMS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
        $this->load->view('include/header');
        ?>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1><?php echo $this->session->userdata("RoleName"); ?> WMS</h1>
                    <hr />
                    <?php
                    foreach ($linktujuan as $row) {
                        ?>
                        <p><a href="<?php echo base_url() . $row['LinkAddress']; ?>" class="btn btn-large"><?php echo $row['NameLinkAddress']; ?></a></p>

                        <?php
                    }
                    if ($this->session->userdata("OperatorRole") == '10/WHR/000') {
                        ?>
                        <p><a href="<?php echo base_url(); ?>index.php/admin/List_Summary_Outstanding" class="btn btn-large">List Outstanding</a></p>
                        <?php
                    }
                    ?>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
    </body>
</html>
