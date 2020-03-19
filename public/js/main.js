
(function ($) {
    "use strict";


    /*==================================================================
     [ Validate ]*/

    $('.validate-form').on('submit', function (e) {
        e.preventDefault();
        $("input[name=signature_val]").val($('#signature')[0].toDataURL());

        $('.validate-form')[0].submit();

    });





    // CANVAS HANDLING

    var canvas = $('#signature')[0];
    canvas.addEventListener('mousedown', ev_mousedown, false);
    canvas.addEventListener('mousemove', ev_mousemove, false);
    window.addEventListener('mouseup', ev_mouseup, false);

    canvas.addEventListener('touchstart', ev_touchstart, false);
    canvas.addEventListener('touchmove', ev_touchmove, false);
    window.addEventListener('touchend', ev_mouseup, false);

    var ctx = canvas.getContext('2d');

    var started = false;

    function ev_mouseup(ev) {
        started = false;
    }

    function ev_touchstart(ev) {
        if (navigator.userAgent.match(/Android/i)) {
            ev.preventDefault();
        }
        started = true;
        var x = ev.touches[0].clientX;
        var y = ev.touches[0].clientY;
        x = x - canvas.offsetLeft;
        y = y - canvas.offsetTop;

        ctx.beginPath();
        ctx.moveTo(x, y);
    }

    function ev_touchmove(ev) {
        ev.preventDefault();

        var x = ev.touches[0].clientX;
        var y = ev.touches[0].clientY;
        x = x - canvas.offsetLeft;
        y = y - canvas.offsetTop;

        if (started) {
            ctx.lineTo(x, y);
            ctx.stroke();
        }
    }

    function ev_mousedown(ev) {
        started = true;
        ctx.beginPath();
        ctx.moveTo(ev.offsetX, ev.offsetY);
    }

    function ev_mousemove(ev) {
        if (started) {
            ctx.lineTo(ev.offsetX, ev.offsetY);
            ctx.stroke();
        }
    }

    $("#clear_btn").click(function () {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    });




})(jQuery);