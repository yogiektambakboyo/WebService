<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Assignment</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
        $this->load->view('include/header');
        ?>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <h1>Assignment</h1>
                    <hr />
                    <?php
                    foreach ($Assignment as $row) {
                        ?>
                        <p>
                            <form method="post" action="<?php echo base_url() ?>index.php/assigned/selectAssignment"> 
                                <input name="LinkAddress" type="hidden" value="<?php echo $row['LinkAddress']; ?>"/>
                                <input name="OprRole" type="hidden" value="<?php echo $row['OprRole']; ?>"/>
                                <input name="RoleName" type="hidden" value="<?php echo $row['RoleName']; ?>"/>
                                <input type="submit" class="btn btn-large" value="<?php echo $row['NameLinkAddress']; ?>" name="btnSubmit"/>
                            </form>
                        </p>
                        <?php
                    }
                    ?>
                    <p><a href="<?php echo base_url() ?>index.php/login" class="btn btn-inverse btn-large">Kembali</a></p>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
    </body>
</html>
