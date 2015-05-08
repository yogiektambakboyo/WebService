<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Waktu Team</title>
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
                    <h1>Waktu Team</h1>
                    <hr />
                    <?php
                    echo validation_errors();
                    ?>
                    <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>index.php/receiving/bpb">
                        <div class="control-group">
                            <select class="selectpicker">
								<?php
                                foreach ($zone as $row) {
                                    ?>
                                    <option value="<?php echo $row[''] ?>"></option>
                                    <?php
                                }
                                ?>
							  </select>
                            <div class="controls">
                                
                            </div>
                        </div>
						<div class="control-group">
                            <select class="selectpicker">
								<?php
                                foreach ($whrole as $row) {
                                    ?>
                                    <option value="<?php echo $row[''] ?>"></option>
                                    <?php
                                }
                                ?>
							  </select>
                            <div class="controls">
                                
                            </div>
                        </div>
                        <div class="control-group">
                            <input type="submit" name="btnProses" class="btn btn-large" value="Proses"/>
                            <div class="controls">
                                
                            </div>
                        </div>
                    </form>
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
    </body>
</html>
