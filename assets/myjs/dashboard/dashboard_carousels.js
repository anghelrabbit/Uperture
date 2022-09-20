/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var emp_today_bdays = new Array();
var colspan = 'col-lg-4 col-md-4 col-sm-4 col-xs-4';
$(function () {

    if ($(window).width() <= 470) {
        console.log('asdasd');
        $('a[name=names]').addClass('hidden');
         colspan = 'col-lg-6 col-md-6 col-sm-6 col-xs-6';
    } else {
        $('a[name=names]').removeClass('hidden');
    }
    $(window).bind("resize", function () {
        if ($(window).width() <= 470) {
            $('a[name=names]').addClass('hidden');
        } else {
            $('a[name=names]').removeClass('hidden');
        }
    });
    var d = (new Date().getMonth()) + 1;
    var month = d < 10 ? "0" + d : d.toString();
    $('select[name=bday_month]').val(month);
    fetchEmployee(0);
    fetchEmployee(1);
    fetchEmployee(2);
});




function fetchEmployee(carousel_index) {
    $.ajax({
        type: 'POST',
        data: {
            index: carousel_index,
            birthdate: $('select[name=bday_month]').val()
        },
        url: 'Employee/FetchEmployeeServiceOrBirthdate',
        dataType: 'json'
    }).done(function (result) {
        var res = result['data'];
        if (carousel_index == 0) {
            initializeContainers(3, res, 'birthday');
        } else if (carousel_index == 1) {
            initializeContainers(6, res, 'service');
        } else {
            emp_today_bdays = result['data'];
            if (emp_today_bdays.length == 0) {
                $('div[name=img_today_bdays]').empty();
                $('div[name=img_today_bdays]').append('No Birthdays Today');
            } else {
                imageAnimation();
                setInterval(() => imageAnimation(), 5000);
            }
        }

    });
}

function initializeContainers(default_length, data, carousel) {
    var container_count = 0;
    var extra = 0;
    var containers = new Array();
    var equals = ((data.length) / default_length).toString().split('.');
    if (equals.length == 2) {
        if (equals[0] == 0) {
            container_count = 0;
        } else {
            container_count = equals[0];
        }
        extra = data.length - (equals[0] * default_length);
    } else {
        container_count = equals[0];
    }

    for (var cv = 0; cv <= container_count; cv++) {
        if (cv == container_count) {
            if (extra != 0) {
                containers[cv] = extra;
            }
        } else {
            containers[cv] = default_length;
        }
    }

    carouselPages(carousel, containers, data);
}
function carouselPages(carousel, pages, data) {
    $('div[name=' + carousel + '_inner]').empty();
    for (var counter = 0; counter < pages.length; counter++) {
        var is_active = '';
        if (counter == 0) {
            is_active = 'active';
        }
        $('div[name=' + carousel + '_inner]').append(
                '<div class="item ' + is_active + '">' +
                '<div   name="' + carousel + '_list' + counter + '">' +
                '</div>' +
                ' </div>'
                );

    }
    carouselItems(carousel, pages, data);
}

function carouselItems(carousel, pages, data) {
    var counter = 0;
    var emp_counter = 0;

    while (counter < pages.length) {
        for (var index = 0; index < pages[counter]; index++) {
            var placement = 'bottom';
            if (index > 2) {
                placement = 'top';
            }
            var text_date = '';
            if (carousel == 'service') {
                text_date = '<span class="users-list-date" style="font-size:12px;text-align:center">' + data[emp_counter]['service'] + '</span>';
            } else {
                text_date = data[emp_counter]['empname'] + '</a>'+'<span class="users-list-date" style="font-size:12px;text-align:center">' + data[emp_counter]['birthday'] + '</span>';
                colspan = 'col-lg-4 col-md-4 col-sm-4 col-xs-4';
            }
            $('div[name=' + carousel + '_list' + counter + "]").append('  <div class="' + colspan + '">' +
                    '<img style="height:100px" class="profile-user-img img-responsive img-circle img-popup" src="data:image;base64,'+data[emp_counter]['profile_image']+'" alt="User Image" data-toggle="tooltip"  title="' + data[emp_counter]['empname'] + '" data-placement="' + placement + '">' +
                    '<a class="users-list-name hidename"  name="names" style="text-align:center" href="#">'  + text_date +
                    '</div>');
            emp_counter++;
        }
        counter++;
    }
    $('[data-toggle="tooltip"]').tooltip();
}




var counter = 0;
function imageAnimation(profile_pic) {
    $('div[name=img_today_bdays]').empty();
    $('span[name=bday_name]').empty();
    $('div[name=img_today_bdays]').append('<img name="" class="profile-user-img img-responsive img-circle img-bday-animation" src="data:image;base64,'+emp_today_bdays[counter]['profile_image']+'" alt="User Image" data-toggle="tooltip"  >');
    $('span[name=bday_name]').append(emp_today_bdays[counter]['empname']);
    $('#restartConfetti').click();
    setTimeout(function () {
        $('#stopConfetti').click();
    }, 3000);
    counter++;
    if (counter == emp_today_bdays.length) {
        counter = 0;
    }
}

$('select[name=bday_month]').change(function () {
    fetchEmployee(0);
});