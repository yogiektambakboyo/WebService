<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Pilih Peran</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <?php
        $this->load->view('include/header');
        ?>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Pilih Peran</h1>
                    <hr />
                    <?php
                    //$key = 'wmsrole';
                    foreach ($Role as $row) {
                        ?>
                        <p><form method="post" action="<?php echo base_url() ?>index.php/login/selectRole"> 
                            <input name="rolecode" type="hidden" value="<?php echo $row['WHRoleCode']; ?>"/>
                            <input name="rolename" type="hidden" value="<?php echo $row['Name']; ?>"/>
                            <input type="submit" class="btn btn-large" value="<?php echo $row['Name']; ?>" name="btnSubmit"/>
                            
                        </form>
                        </p>
                        <?php
                    }
                    ?>
                    <p><a href="<?php echo base_url() ?>index.php/assigned/getAllAssigment" class="btn btn-large btn-warning">Assignment</a></p>    
                    <input type="hidden" id="refreshed" value="no">    
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>

    </body>
</html>
