
// for portfolio
(function ($) {
    var $container = $('.portfolio'),
        colWidth = function () {
            var w = $container.width(),
                columnNum = 1,
                columnWidth = 50;
            if (w > 1200) {
                columnNum = 5;
            }
            else if (w > 900) {
                columnNum = 3;
            }
            else if (w > 600) {
                columnNum = 2;
            }
            else if (w > 300) {
                columnNum = 1;
            }
            columnWidth = Math.floor(w / columnNum);
            $container.find('.pitem').each(function () {
                var $item = $(this),
                    multiplier_w = $item.attr('class').match(/item-w(\d)/),
                    multiplier_h = $item.attr('class').match(/item-h(\d)/),
                    width = multiplier_w ? columnWidth * multiplier_w[1] - 0 : columnWidth - 5,
                    height = multiplier_h ? columnWidth * multiplier_h[1] * 1 - 5 : columnWidth * 0.5 - 5;
                $item.css({
                    width: width,
                    height: height
                });
            });
            return columnWidth;
        }
    function refreshWaypoints() {
        setTimeout(function () {
        }, 3000);
    }
    $('nav.portfolio-filter ul a').on('click', function () {
        var selector = $(this).attr('data-filter');
        $container.isotope({ filter: selector }, refreshWaypoints());
        $('nav.portfolio-filter ul a').removeClass('active');
        $(this).addClass('active');
        return false;
    });
    function setPortfolio() {
        setColumns();
        $container.isotope('reLayout');
    }
    $container.imagesLoaded(function () {
        $container.isotope();
    });
    isotope = function () {
        $container.isotope({
            resizable: true,
            itemSelector: '.pitem',
            layoutMode: 'masonry',
            gutter: 10,
            masonry: {
                columnWidth: colWidth(),
                gutterWidth: 0
            }
        });
    };
    isotope();
    $(window).smartresize(isotope);
}(jQuery));






// // for gglmap
// /* ==============================================
// MAP -->
// =============================================== */

// var locations = [['<div class="infobox"><h3 class="title"><a href="#">OUR USA OFFICE</a></h3><span>NEW YORK CITY 2045 / 65</span><span>+90 555 666 77 88</span></div>',
//     52.370216,
//     4.895168,
//     2]];
// var map = new google.maps.Map(document.getElementById('map'), {
//     zoom: 14, scrollwheel: false, navigationControl: true, mapTypeControl: false, scaleControl: false, draggable: true, styles: [{
//         "featureType": "administrative", "elementType": "labels.text.fill", "stylers": [{ "featureType": "poi.business", "elementType": "geometry.fill", "stylers": [{ "visibility": "on" }] }]
//     }
//     ], center: new google.maps.LatLng(52.370216, 4.895168), mapTypeId: google.maps.MapTypeId.ROADMAP
// }

// );
// var infowindow = new google.maps.InfoWindow();
// var marker,
//     i;
// for (i = 0;
//     i < locations.length;
//     i++) {
//     marker = new google.maps.Marker({
//         position: new google.maps.LatLng(locations[i][1], locations[i][2]), map: map, icon: ''
//     }
//     );
//     google.maps.event.addListener(marker, 'click', (function (marker, i) {
//         return function () {
//             infowindow.setContent(locations[i][0]);
//             infowindow.open(map, marker);
//         }
//     }
//     )(marker, i));
// }

// another srcipt for function on site

/******************************************
    File Name: custom.js
    Template Name: Aven
/****************************************** */

(function ($) {
    "use strict";

    /* ==============================================
    AFFIX
    =============================================== */
    $('.megamenu').affix({
        offset: {
            top: 800,
            bottom: function () {
                return (this.bottom = $('.footer').outerHeight(true))
            }
        }
    })

    /* ==============================================
    BACK TOP
    =============================================== */
    jQuery(window).scroll(function () {
        if (jQuery(this).scrollTop() > 1) {
            jQuery('.dmtop').css({
                bottom: "75px"
            });
        } else {
            jQuery('.dmtop').css({
                bottom: "-100px"
            });
        }
    });

    /* ==============================================
       LOADER -->
        =============================================== */

    $(window).load(function () {
        $("#preloader").on(500).fadeOut();
        $(".preloader").on(600).fadeOut("slow");
    });

    /* ==============================================
     FUN FACTS -->
     =============================================== */

    function count($this) {
        var current = parseInt($this.html(), 10);
        current = current + 50; /* Where 50 is increment */
        $this.html(++current);
        if (current > $this.data('count')) {
            $this.html($this.data('count'));
        } else {
            setTimeout(function () {
                count($this)
            }, 30);
        }
    }
    $(".stat_count, .stat_count_download").each(function () {
        $(this).data('count', parseInt($(this).html(), 10));
        $(this).html('0');
        count($(this));
    });

    /* ==============================================
     TOOLTIP -->
     =============================================== */
    $('[data-toggle="tooltip"]').tooltip()
    $('[data-toggle="popover"]').popover()

    /* ==============================================
     CONTACT -->
     =============================================== */
    // jQuery(document).ready(function() {
    //     $('#contactform').submit(function() {
    //         var action = $(this).attr('action');
    //         $("#message").slideUp(750, function() {
    //             $('#message').hide();
    //             $('#submit')
    //                 .after('<img src="" class="loader" />')
    //                 .attr('disabled', 'disabled');
    //             $.post(action, {
    //                     first_name: $('#first_name').val(),
    //                     last_name: $('#last_name').val(),
    //                     email: $('#email').val(),
    //                     phone: $('#phone').val(),
    //                     select_service: $('#select_service').val(),
    //                     select_price: $('#select_price').val(),
    //                     comments: $('#comments').val(),
    //                     verify: $('#verify').val()
    //                 },
    //                 function(data) {
    //                     document.getElementById('message').innerHTML = data;
    //                     $('#message').slideDown('slow');
    //                     $('#contactform img.loader').fadeOut('slow', function() {
    //                         $(this).remove()
    //                     });
    //                     $('#submit').removeAttr('disabled');
    //                     if (data.match('success') != null) $('#contactform').slideUp('slow');
    //                 }
    //             );
    //         });
    //         return false;
    //     });
    // });

    /* ==============================================
     CODE WRAPPER -->
     =============================================== */

    $('.code-wrapper').on("mousemove", function (e) {
        var offsets = $(this).offset();
        var fullWidth = $(this).width();
        var mouseX = e.pageX - offsets.left;

        if (mouseX < 0) {
            mouseX = 0;
        } else if (mouseX > fullWidth) {
            mouseX = fullWidth
        }

        $(this).parent().find('.divider-bar').css({
            left: mouseX,
            transition: 'none'
        });
        $(this).find('.design-wrapper').css({
            transform: 'translateX(' + (mouseX) + 'px)',
            transition: 'none'
        });
        $(this).find('.design-image').css({
            transform: 'translateX(' + (-1 * mouseX) + 'px)',
            transition: 'none'
        });
    });
    $('.divider-wrapper').on("mouseleave", function () {
        $(this).parent().find('.divider-bar').css({
            left: '50%',
            transition: 'all .3s'
        });
        $(this).find('.design-wrapper').css({
            transform: 'translateX(50%)',
            transition: 'all .3s'
        });
        $(this).find('.design-image').css({
            transform: 'translateX(-50%)',
            transition: 'all .3s'
        });
    });

})(jQuery);