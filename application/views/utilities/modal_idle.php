<style>
    /*    .modal-backdrop-orange {
            background-color: black;
    
        }*/
    .modal-backdrop-orange {
        animation: colorchange 10s; /* animation-name followed by duration in seconds*/
        /* you could also use milliseconds (ms) or something like 2.5s */
        -webkit-animation: colorchange 8s; /* Chrome and Safari */
        -webkit-animation-iteration-count: infinite;
        animation-iteration-count: infinite;
    }

    @keyframes colorchange
    {
        0%   {background: #072E5C;}
        25%  {background: #115E9C;}
        50%  {background: #072E5C;}
        75%  {background: #115E9C;}
        100% {background: #072E5C;}
    }

    @-webkit-keyframes colorchange /* Safari and Chrome - necessary duplicate */
    {
        0%   {background: red;}
        25%  {background: yellow;}
        50%  {background: blue;}
        75%  {background: green;}
        100% {background: red;}
    }    
</style>
<div class="modal fade modal-backdrop-orange  "id="idleModal" data-backdrop="static"   >
    <div class="modal-dialog "  id="forshake">
        <div class="modal-content"style="border-radius: 20px;background-image: linear-gradient(to bottom left, #357CA5, #00C0EF);" >
            <div class="modal-body bg-primary " style="border-radius: 20px;background-image: linear-gradient(to bottom left, #357CA5, #00C0EF);">
                <body class=" lockscreen hold-transition ">
                    <div class="lockscreen-wrapper">
                        <div class="lockscreen-logo">
                            <!--<a href="" style="font-size: 25px">Solea Hotel Cebu Corporation</a>-->

                        </div>
                        <div class="lockscreen-name text-center" style="margin-top:-5%;letter-spacing:0.5px;">
                            <?= $this->session->userdata('empname') ?><br>
                            <span style="font-size: 12px;  letter-spacing: 0.5px;"><?php echo $this->session->userdata('compname') ?></span>
                        </div>

                        <div class="lockscreen-item" style="margin-top:6.6%">
                            <div class="lockscreen-image ">
                                <img  src="data:image;base64,<?php echo $this->session->userdata('profilepic'); ?>" alt="User Image">
                            </div>

                            <form class="lockscreen-credentials has-danger" >
                                <div class="input-group ">
                                    <input type="password" class="form-control " placeholder="password" id="idlepassword" onkeypress="return  tableInputKeyPress(event)" onkeyup="enableShake()">
                                    <div class="input-group-btn" >
                                        <button type="button" class="btn "  id="idlebtn" onclick="checkpassword()" ><i class="fa fa-arrow-right text-muted"></i></button>
                                    </div>
                                </div>
                            </form>

                        </div>
                        <div class=" text-center">
                            Enter your password to retrieve your session
                        </div>
                        <div class="text-center">
                            <a href="Signout" style="color:red">Or sign in as a different user</a>
                        </div>
                        <div class="lockscreen-footer text-center">
                            Copyright &copy; 2022 <b><a href="" class="text-black">SILVER SUMMIT CONSULTING</a></b><br>
                            All rights reserved
                        </div>
                    </div>
            </div>

        </div>
    </div>
</div>