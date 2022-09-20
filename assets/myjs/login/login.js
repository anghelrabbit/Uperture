$(function () {
});

function loginacoount() {
    $.ajax({
        type: 'POST',
        url: "LandingPage/LoginAccount",
        data: $('#sign-in-form').serialize(),
        dataType: 'json'
    }).done(function (data) {
        if (data['result'] == true) {
            window.location.href = "Homepage";
        } else {
            if (data['approved'] != 1) {
                var addon_text = (data['approved'] == 2) ? 'Declined.' : 'is not yet approved by HR.';
                swal({title: "Account " + addon_text,
                    type: "error",
                    show: true,
                    backdrop: 'static',
                    timer: 3000,
                    showConfirmButton: false,
                    keyboard: false});
            } else {
                verifyform(data['data']);
            }

        }

    });
}

function registeracc() {
    window.location.href = "Registry";
}
function cancelAccount() {
    window.location.href = "LandingPage";
}





function sample() {
    $.ajax({
        type: 'POST',
        url: BASE_URL + "Login/decryptpass",
        data: {
            pass: $('#passw').val()
        },
        dataType: 'json'
    }).done(function (data) {

    });

}

