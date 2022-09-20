<!doctype html>
<html lang="en">
    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

        <title><?= $page_title ?></title>
        <!--        <link rel = "icon" href =  
                      "assets/images/logo.png" 
                      type = "image/x-icon"> -->
        <?php foreach ($css as $data) { ?>
            <link href="<?= $data ?>" rel="stylesheet"/>
        <?php } ?>

        <link rel="icon" type="image/png" sizes="32x32" href="assets/images/logo.png"><!-- comment -->

    </head>
</head>
<body  class="img js-fullheight"  style=" background-image: url('assets/logins/images/bg.png');

       background-repeat: no-repeat;
       background-attachment: fixed;
       background-size: 100% 100%;" >

    <section class="ftco-section">
        <div class="container">

            <div class="row justify-content-center">

            </div>
            <div class="row justify-content-center">
                <div class="col-md-12 col-lg-12 text-center">
                    <div class="login-wrap p-0">
                        <div  style=" border: solid 1px #57534A;
                              border-radius:25px;
                              box-shadow: 12px 0 15px -4px rgba(46, 49, 49, 1), -12px 0 8px -4px rgba(46, 49, 49, 1);
                              -moz-box-shadow: 12px 0 15px -4px rgba(46, 49, 49, 1), -12px 0 8px -4px rgba(46, 49, 49, 1);
                              -webkit-box-shadow: 01 0 50px 2px rgba(46, 49, 49, 1), -12px 0 20px 2px rgba(46, 49, 49, 1);
                              -o-box-shadow: 12px 0 15px -4px rgba(46, 49, 49, 1  ), -12px 0 8px -4px rgba(46, 49, 49, 1);">
                            <img src="assets/images/mainlogo-1.png" style="width:200px">
                            <div class="col-md-12">
                                <form id="register_form" autocomplete="off">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group has-feedback">
                                                <input type="text" class="form-control" placeholder="Last Name" name="register_lastname">
                                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group has-feedback">
                                                <input type="text" class="form-control" placeholder="First Name" name="register_firstname">
                                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group has-feedback">
                                                <input type="text" class="form-control" placeholder="Middle Name" name="register_midname">
                                                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group has-feedback">
                                                <input type="date" class="form-control"   placeholder="Date Of Birth" name="register_dob">
                                                <span class="glyphicon glyphicon-home form-control-feedback"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group has-feedback">
                                                <input type="email" class="form-control" placeholder="Email Address" name="register_email">
                                                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group has-feedback">
                                                <input type="number" class="form-control" placeholder="Contact Number" name="register_contact_num">
                                                <span class="glyphicon glyphicon-phone form-control-feedback"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group has-feedback">
                                                <input type="text" class="form-control" placeholder="Username" name="register_username">
                                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group has-feedback">
                                                <input type="password" class="form-control" placeholder="Password" name="register_password">
                                                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group has-feedback">
                                                <input type="password" class="form-control" placeholder="Retype password" name="register_retype_pass">
                                                <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 ">
                                            <div class="form-group has-feedback">
                                                <button type="button" class="btn  btn-block" style="background-color:#e3a71b;" name="subtmit_btn" onclick="cancelAccount()">Cancel</button>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 ">
                                            <div class="form-group has-feedback">
                                                <button type="button" class="btn  btn-block"  style="background-color:#00334D; color: #ffffff" name="register_btn" onclick="saveAccount1()">Register</button>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 ">
                                                <center style="color:white;">  
                                                    <p>Copyright © 2022 Silver Summit.<br> All rights reserved.</p>

                                                </center>   

                                           
                                        </div>
                                    </div>


                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
        $(document).keypress(function (e) {
        if (e.which == 13) {
        $('button[name=subtmit_btn]').click();
        }


        $("#sign-in-form input[name=username]").focus();
        });
    </script>

</body>
<?php foreach ($js as $data) { ?>
    <script src="<?= $data ?>"></script>
<?php } ?>  
</html>

