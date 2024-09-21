$(document).ready(function() {

    /*X-CSRF-TOKEN*/
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    /*X-CSRF-TOKEN*/

    $(document).on("click", '[data-plugin="ajaxGetRequest"]', function(e) {

        //--  data-plugin="ajaxGetRequest"  //--  add this to work the event //--  *Required

        //--  data-conf-message="Are you sure to do the action..?"	//--  Confirm the action by this  //--  Optional

        //--  data-type="DELETE"  //--  To specify the type of the request  //--  Optional  //--  Default "GET"

        //--  data-action="id/action"  //--  TO specify the request action  //--  Optional  //--  Default in "href"

        //--  data-formdata="{'name' : 'value', 'email' : 'value'}"  //--  To specify the data of the request  //--  Optional  //--  Default ""



        e.preventDefault();
        var $this = $(this);
        var ladda = Ladda.create(this);
        var url = $(this).attr('href'); // for anchor tag
        if(typeof(url)  === "undefined" || url == '') {
            url = $(this).data('action'); // if not anchor tag
        }

        var type = $(this).data('type');
        if(typeof(type)  === "undefined") {
            type = "GET";
        }

        var formData1 = $(this).data('formdata');
        if(typeof formData1 == "object") {
            var formData = formData1[0];
        }
        else {
            formData = {'value' : formData1};
        }

        var confdata = $(this).data('confdata');

        //var formData = {'value' : formData1};
        var dataType = 'json';
        if(typeof(formData1)  === "undefined") {
            formData = "";
            dataType = '';
            confdata = '';
        }

        var message = $this.attr('data-conf-message');
        if(typeof(message)  === "undefined" || message=='') { // if no confirmation message needed

            ladda.start();
            $.ajax({
                type: type,
                url: url,
                data 		: formData,
                dataType 	: dataType,
                success: function (data) {
                    $this.trigger('ajax-get-request.success', data);

                },
                error: function (data) {
//						console.log('Error:', data);
                    $this.trigger('ajax-get-request.error', data);

                }
            }).always(function () {
                ladda.stop();
            });
        }
        else { // case need to display alert

            if(typeof(confdata)  === "undefined" || confdata=='') { // case need to display alert for every value

                alertify.confirm(message, function() {
                    ladda.start();
                    $.ajax({
                        type: type,
                        url: url,
                        data 		: formData,
                        dataType 	: dataType,
                        success: function (data) {
                            $this.trigger('ajax-get-request.success', data);

                        },
                        error: function (data) {
//						console.log('Error:', data);
                            $this.trigger('ajax-get-request.error', data);

                        }
                    }).always(function () {
                        ladda.stop();
                    });
                }, function () {
                    // user clicked "cancel"
                });

            }
            else { //case need to display alert for particular values.

                var arr = $.map(formData, function(el) { return el });
                if(arr[0] == confdata) { // case when clicked a value, which needs to show alert
                    alertify.confirm(message, function() {
                        ladda.start();
                        $.ajax({
                            type: type,
                            url: url,
                            data 		: formData,
                            dataType 	: dataType,
                            success: function (data) {
                                $this.trigger('ajax-get-request.success', data);

                            },
                            error: function (data) {
//						console.log('Error:', data);
                                $this.trigger('ajax-get-request.error', data);

                            }
                        }).always(function () {
                            ladda.stop();
                        });
                    }, function () {
                        // user clicked "cancel"
                    })
                }
                else { // case when clicked a value, which deosn't need to show alert

                    ladda.start();
                    $.ajax({
                        type: type,
                        url: url,
                        data 		: formData,
                        dataType 	: dataType,
                        success: function (data) {
                            $this.trigger('ajax-get-request.success', data);
                        },
                        error: function (data) {
//						console.log('Error:', data);
                            $this.trigger('ajax-get-request.error', data);

                        }
                    }).always(function () {
                        ladda.stop();
                    });

                }

            }



        }
    });
    /*----------------- ajaxForm -------------------*/
    $(document).on("submit", 'form[data-plugin="ajaxForm"]', function (e) {
        e.preventDefault();
        $('#jq-loader').show();

        var $this = $(this);
        var url = $this.attr("action");
        var method = $this.attr("method");
        var data = {};
        var processData = true;
        var contentType = "application/x-www-form-urlencoded";
        if ("POST" == method.toUpperCase() && $this.attr('enctype') == "multipart/form-data") {
            data = new FormData($this[0]);
            processData = false;
            contentType = false;
            //contentType = "multipart/form-data";
        } else {
            data = $this.serialize();
        }

        var laddabutton = $(this).data('laddabutton');
        if(typeof(laddabutton)  === "undefined" || laddabutton=='') {

        }else {
            var ladda = Ladda.create(document.querySelector(laddabutton));
            ladda.start();
        }

        $.ajax({
            type: method,
            url: url,
            data:  data,
            dataType: 'json',
            processData : processData,
            contentType : contentType,
            success : function(data, textStatus, jqXHR) {
                if(typeof(laddabutton)  === "undefined" || laddabutton=='') {

                }else {
                    ladda.stop();
                }

                $this.trigger('af.success', data, textStatus, jqXHR);
            },
            error : function(jqXHR, textStatus, errorThrown) {
                if(typeof(laddabutton)  === "undefined" || laddabutton=='') {

                }else {
                    ladda.stop();
                }
                //alert("Error : " + errorThrown);
                var validator = $this.data("validator");
                if (validator && jqXHR.status == 422) {
                    var resposeJSON = $.parseJSON(jqXHR.responseText);
                    var errors = {};
                    $.each(resposeJSON, function (k, v) {
                        if(k == 'error'){

                        }

                        errors[k] = v;
                    });

                    if(!$.isEmptyObject(errors)) validator.showErrors(errors);
                } else {

                }
                $('#jq-loader').hide();
                $this.trigger('af.error', jqXHR.responseText, textStatus, jqXHR, errorThrown);
            },
            complete : function(jqXHR, textStatus) {
                if(typeof(laddabutton)  === "undefined" || laddabutton=='') {

                }else {
                    ladda.stop();
                }
                var validator = $this.data("validator");
                $('#jq-loader').hide();
                $this.trigger('af.complete', jqXHR, textStatus);
            }
        });
    });
    /*----------------- ajaxForm END-------------------*/

});




/*SIDE MENU START*/

function openNav() {
    document.getElementById("mySidenav").style.width = "70%";
    // document.getElementById("healthmart-navbar").style.width = "50%";
    /*document.body.style.backgroundColor = "rgba(0,0,0,0.4)";*/
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
    /*document.body.style.backgroundColor = "rgba(0,0,0,0)";*/
}

/*
SIDE MENU END*/

/*COROUSAL START*/

$('#myCarousel').carousel({
 interval: 3000,
    wrap : true
 });

/*$('#myCarousel2').carousel({
    interval: 2000
});*/

$('#offer-corousel').carousel({
    interval: false,
    wrap : false
});

$('#dealer-corousel').carousel({
    interval: false,
    wrap : false,
    touch : true
});

$('#hospital-corousel').carousel({
    interval: false,
    wrap : false
});

$('#lab-corousel').carousel({
    interval: false,
    wrap : false
});

$('#featured-corousel').carousel({
    interval: false,
    wrap : false
});

$('[id^="all-corousel-"]').carousel({
    wrap : false
});

    /*$('#offer-corousel .carousel .item').each(function(){
        var itemToClone = $(this);

        for (var i=1;i<4;i++) {
            itemToClone = itemToClone.next();

            // wrap around if at end of item collection
            if (!itemToClone.length) {
                itemToClone = $(this).siblings(':first');
            }

            // grab item, clone, add marker class, add to collection
            itemToClone.children(':first-child').clone()
                .addClass("cloneditem-"+(i))
                .appendTo($(this));
        }
    });*/





$('div[data-plugin="khm-corousel"] .carousel .item').each(function(){
    var itemToClone = $(this);

    if($( window ).width() < 768) {
        var x = 2;
    }else {
         x = 4;
    }
    /*var x = 4;*/

    for (var i=1; i < x; i++) {
        itemToClone = itemToClone.next();

        // wrap around if at end of item collection
        if (!itemToClone.length) {
            itemToClone = $(this).siblings(':first');
        }

        // grab item, clone, add marker class, add to collection
        itemToClone.children(':first-child').clone()
            .addClass("cloneditem-"+(i))
            .appendTo($(this));
    }
});


$('#dvoffer_corousel .carousel .item').each(function(){
    var itemToClone = $(this);

    if($( window ).width() < 768) {
        var x = 1;
    }else {
        x = 2;
    }
    /*var x = 4;*/

    for (var i=1; i < x; i++) {
        itemToClone = itemToClone.next();

        // wrap around if at end of item collection
        if (!itemToClone.length) {
            itemToClone = $(this).siblings(':first');
        }

        // grab item, clone, add marker class, add to collection
        itemToClone.children(':first-child').clone()
            .addClass("cloneditem-"+(i))
            .appendTo($(this));
    }
});

$('#dvdealer_corousel .carousel .item').each(function(){
    var itemToClone = $(this);

    if($( window ).width() < 768) {
        var x = 1;
    }else {
        x = 2;
    }
    /*var x = 4;*/

    for (var i=1; i < x; i++) {
        itemToClone = itemToClone.next();

        // wrap around if at end of item collection
        if (!itemToClone.length) {
            itemToClone = $(this).siblings(':first');
        }

        // grab item, clone, add marker class, add to collection
        itemToClone.children(':first-child').clone()
            .addClass("cloneditem-"+(i))
            .appendTo($(this));
    }
});


/*$('#myCarouselnw').carousel({
    interval: 10000
});

$('#myCarouselnw.carousel .item').each(function(){
    var next = $(this).next();
    if (!next.length) {
        next = $(this).siblings(':first');
    }
    next.children(':first-child').clone().appendTo($(this));

    if (next.next().length>0) {
        next.next().children(':first-child').clone().appendTo($(this));
    }
    else {
        $(this).siblings(':first').children(':first-child').clone().appendTo($(this));
    }
});*/


/*
COROUSAL START*/


// Hide Header on on scroll down
var didScroll;
var lastScrollTop = 0;
var delta = 5;
var navbarHeight = $('header').outerHeight();

$(window).scroll(function(event){
    didScroll = true;
});

setInterval(function() {
    if (didScroll) {
        hasScrolled();
        didScroll = false;
    }
}, 50);

function hasScrolled() {
    var st = $(this).scrollTop();

// Make sure they scroll more than delta
    if(Math.abs(lastScrollTop - st) <= delta)
        return;

// If they scrolled down and are past the navbar, add class .nav-up.
// This is necessary so you never see what is "behind" the navbar.
    if (st > lastScrollTop || st > navbarHeight){
// Scroll Down

        $('#scroll-hide-top').removeClass('nav-down').addClass('nav-up');
        $('#scroll-device').removeClass('nav-down').addClass('nav-up');
        $('#scroll-mobile').removeClass('nav-up').addClass('nav-down');
        /*$('#scroll-hide-top').hide();
        $('#scroll-device').hide();
        $('#scroll-mobile').show();*/
        $('#scroll-hide-top').hide();

    } else {
// Scroll Up
        if(st + $(window).height() < $(document).height()) {
            $('#scroll-hide-top').removeClass('nav-up').addClass('nav-down');
            $('#scroll-device').removeClass('nav-up').addClass('nav-down');
            $('#scroll-mobile').removeClass('nav-down').addClass('nav-up');
            /*$('#scroll-hide-top').show();
            $('#scroll-device').show();
            $('#scroll-mobile').hide();*/
            $('#scroll-hide-top').show();
        }
    }

    lastScrollTop = st;
}



$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});
