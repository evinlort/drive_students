// Load helper functions file
$.getScript("js/calendar/cal_functions.js");

$(document).ready(function(){
    $(".close-errors").on("click", function(e) {
        e.stopPropagation();
        $(".modal_errors").hide();
    });
    
    $(".cal-box").hover(
        function() {
            if(!$(this).attr("class").includes("cal-box-gray") && !$(this).attr("class").includes("cal-box-holiday"))
                $(this).addClass("cal-box-highlight");
        },
        function() {
            if(!$(this).attr("class").includes("cal-box-gray" && !$(this).attr("class").includes("cal-box-holiday")))
                $(this).removeClass("cal-box-highlight");
        }
    );

    $(".cal-box").on("click", function() {
        var date = $(this).data("dayNo");

        if(
            check_date_in_borders(date) && 
            !$(this).attr("class").includes("cal-box-gray") && 
            !$(this).attr("class").includes("cal-box-holiday")
        ) {
            get_lessons(date);
            
            $('#favoritesModal').modal('toggle');
        }
    });
    $("#lessons input[type=checkbox]").on("click", function() {
        if($(this).is(":checked")) {
            var that = $(this);
            var url = window.Laravel.baseUrl + "is_lesson_free";
            // var this_date = $("#lessons input[name='this_date']").val();
            var to_send = [];
            to_send.push($("#lessons input[name=this_date]").val());
            // var time = $(this).parents(".time-string").data("time");
            to_send.push($(this).parents(".time-string").data("time"));
            var data = { /* 'lesson_date': this_date, "lesson_time": time, */date_n_times: to_send, '_token': window.Laravel.csrfToken };
            $.post(url, data, function(res) {
                if(res.status == 'yes')
                    that.val(1);
                else {
                    that.val(0);
                    that.click();
                    that.parent(".switch").siblings(".time_info").text("Already taken");
                }
            })
            .fail(function (res) {
                if (res.status == 422) {
                    that.val(0);
                    that.click();
                    $(".errors").text(res.responseJSON.errors.date_n_times);
                    $('#favoritesModal').animate({ scrollTop: 0 }, 'slow', function() {
                        $(".modal_errors").show();
                    });
                    $(window).scrollTop(0);
                    return;
                }
                else
                    return false;
            });
            
        }
        else
            $(this).val(0);
    });
});