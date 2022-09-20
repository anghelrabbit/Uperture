var temp = '';

$(function () {
    tab_selection(0);
});
function showWifi(category) {
    $('span[name=wifipass]').empty();
    if (temp == '') {
        $('span[name=wifipass]').append('Fetching please wait...');
        $.ajax({
            type: 'POST',
            url: 'Settings/DecryptWifiPass',
            data: {wifipass: $('input[name=wp]').val()},
            dataType: 'json'

        }).done(function (result) {
            temp = result;
            $('span[name=wifipass]').empty();
            $('span[name=wifipass]').append(temp);
        });
    } else {
        if (category) {
            $('span[name=wifipass]').append(temp);
        } else {
            $('span[name=wifipass]').append('********');
        }
    }

}

function tab_selection(tab) {
  
}

var holderEnter = 0;
function enable_enterkey() {
    holderEnter = 0;
}
function check_profile_account() {
    $.ajax
            ({
                type: 'POST',
                url: "LandingPage/checkPassword",
                data: {
                    password: $('input[name=profile_password]').val()
                },
                dataType: 'json'
            })
            .done(function (data)
            {
                if (data) {
                    $('div[name=lock_overlay]').addClass('hidden');
                }
            });
}
function profile_account_keypress(e) {
    e = e || window.event;
    var key = e.keyCode;
    if (key == 13)
    {
        if (holderEnter == 0) {
            check_profile_account();
            holderEnter = 1;
        }
        return false;
    }
}

function changeAccount() {
    if ($('input[name=new_password]').val() == '') {
        $('label[name=new_password_error]').removeClass('hidden');
    } else if ($('input[name=new_password]').val() == $('input[name=confirm_password]').val()) {
        update_password();
        $('label[name=confirm_new_password_error]').addClass('hidden');
        $('label[name=new_password_error]').addClass('hidden');
    } else {
        $('label[name=confirm_new_password_error]').removeClass('hidden');
    }
}





function update_password() {
    $.ajax
            ({
                type: 'POST',
                url: "Employee/UpdatePassword",
                data: {
                    newpass: $('input[name=new_password]').val()
                },
                dataType: 'json'
            })
            .done(function (data)
            {
                swal({title: "Success", text: "Password changed.", type: "success"},
                        function ()
                        {
                            $('div[name=lock_overlay]').removeClass('hidden');
                            $('input[name=new_password]').val('');
                            $('input[name=confirm_password]').val('');
                            $('input[name=profile_password]').val('');

                        });
            });
}
