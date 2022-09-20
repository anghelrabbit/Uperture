/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function saveAccount1() {


    var data = $('#register_form').serializeArray();
    for (var cv = 0; cv < data.length; cv++) {

//        console.log($('input[name='+ data[cv].name +']').val());

        if ($('input[name=' + data[cv].name + ']').val() == '') {
            swal({title: "Error",
                text: "All fields should not be empty!",
                type: "error",
                show: true,
                backdrop: 'static',
                timer: 2000,
                showConfirmButton: true,
                keyboard: true},
                    function ()
                    {

                    });
        } else {

            $.ajax({
                type: 'POST',
                url: "Registry/SaveUpdateRegistry",
                data: $('#register_form').serialize(),
                dataType: 'json'
            }).done(function (data) {
                swal({title: "Success",
                    text: "Account registered. Processing account activation",
                    type: "success",
                    show: true,
                    backdrop: 'static',
                    timer: 2000,
                    showConfirmButton: false,
                    keyboard: false},
                        function ()
                        {
                            window.location.href = "LandingPage";
                        });
            });
            
            break;

        }


    }



}

function cancelAccount() {
    window.location.href = "LandingPage";
}