/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$('.nav-sidebar li a').each(function () {
    var location = window.location.href;
    var link = this.href;
    if (location == link) {
        $(this).parent('li').addClass("active");
    }

});