/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function view_sens() {
    $.ajax({
        type: 'POST',
        cache: false,
        url: "ajax/sens_view.php",
        beforeSend: function () {
            $('#all_object').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
        },
        success: function (html) {
            $('#all_object').html(html);

            frame_hieght();

        }
    });
}


function add_all_object() {
    $.ajax({
        type: 'POST',
        cache: false,
        url: "ajax/obj_add_all.php",
        beforeSend: function () {
            $('#all_object').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
        },
        success: function (html) {
            view_settings_add();
        }
    });
}


function del_all_object() {
    $.ajax({
        type: 'POST',
        cache: false,
        url: "ajax/obj_del_all.php",
        beforeSend: function () {
            $('#all_object').html('<div id="circularG"> <div id="circularG_1" class="circularG"> </div> <div id="circularG_2" class="circularG"> </div> <div id="circularG_3" class="circularG"> </div> <div id="circularG_4" class="circularG"> </div> <div id="circularG_5" class="circularG"> </div> <div id="circularG_6" class="circularG"> </div> <div id="circularG_7" class="circularG"> </div> <div id="circularG_8" class="circularG"> </div> </div>');
        },
        success: function (html) {
            view_settings_add();
        }
    });
}