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
                <div class="col-md-12 col-lg-4 text-center">
                    <div class="login-wrap p-0">
                        <div  style=" border: solid 1px #57534A;
                              border-radius:25px;
                              box-shadow: 12px 0 15px -4px rgba(46, 49, 49, 1), -12px 0 8px -4px rgba(46, 49, 49, 1);
                              -moz-box-shadow: 12px 0 15px -4px rgba(46, 49, 49, 1), -12px 0 8px -4px rgba(46, 49, 49, 1);
                              -webkit-box-shadow: 01 0 50px 2px rgba(46, 49, 49, 1), -12px 0 20px 2px rgba(46, 49, 49, 1);
                              -o-box-shadow: 12px 0 15px -4px rgba(46, 49, 49, 1  ), -12px 0 8px -4px rgba(46, 49, 49, 1);">
                            <img src="assets/images/mainlogo-1.png" style="width:200px">
                            <div class="col-md-12">
                                <form id="sign-in-form" autocomplete="off">
                                    <div class="form-group">
                                        <input type="text" name="username" class="form-control input-focus " placeholder="Username">
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="password" class="form-control input-focus" placeholder="Password">
                                        <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                    </div>

                                    <div class="form-group">
                                        <button type="button" class=" btn submit px-3 btn-block" style="background-color:#00334D; color: #ffffff" name="subtmit_btn" onclick="loginacoount()">Sign In</button>
                                        <button type="button" class="btn submit px-3 btn-block"  style="background-color:#e3a71b;" name="register_btn" onclick="registeracc()">Sign Up</button>
                                    </div>
                                    <div class="form-group">

                                        <center style="color:white;margin-top: -10%">  
                                            <br> <p>Copyright © 2022 Silver Summit Consulting.<br> All rights reserved.</p>

                                        </center>   
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

