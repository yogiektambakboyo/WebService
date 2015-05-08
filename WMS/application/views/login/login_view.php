<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Login</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
        $this->load->view('include/header');
        ?>
        <style type="text/css">
            body {
                padding-top: 40px;
                padding-bottom: 40px;
                background-color: #f5f5f5;
            }

            .form-signin {
                max-width: 300px;
                padding: 19px 29px 29px;
                margin: 0 auto 20px;
                background-color: #fff;
                border: 1px solid #e5e5e5;
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                border-radius: 5px;
                -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
            }
            .form-signin .form-signin-heading,
            .form-signin .checkbox {
                margin-bottom: 10px;
            }
            .form-signin input[type="text"],
            .form-signin input[type="password"] {
                font-size: 16px;
                height: auto;
                margin-bottom: 15px;
                padding: 7px 9px;
            }

        </style>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="span12 center">
                    <form class="form-signin" action="<?php echo base_url() . "index.php/login" ?>" method="POST">
                        <h2 class="form-signin-heading">Login</h2>
                        <?php
                        if (isset($error)) {
                            ?>
                            <div class="alert alert-error">
                                <strong><?php echo $error ?></strong>
                            </div>
                            <?php
                        }
                        echo validation_errors();
                        ?>
                        <input type="text" class="input-block-level" placeholder="Username" name="username">
                        <input type="password" class="input-block-level" placeholder="Password" name="password">
                        <input type="submit" class="btn btn-large btn-primary" name="btnLogin" value="Login"/>
                        
                    </form>
                    <input type="hidden" id="refreshed" value="no">
                </div><!-- /.span4 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
        <?php
        $this->load->view("include/footer");
        ?>
    </body>
</html>
