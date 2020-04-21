// Load helper functions file
$.getScript("js/admin/calendar/cal_functions.js");

$(document).ready(function () {
    $(".close-errors").on("click", function (e) {
        e.stopPropagation();
        $(".modal_errors").hide();
    });

    $(".cal-box").hover(
        function () {
            if (!$(this).attr("class").includes("cal-box-gray") && !$(this).attr("class").includes("cal-box-holiday"))
                $(this).addClass("cal-box-highlight");
        },
        function () {
            if (!$(this).attr("class").includes("cal-box-gray" && !$(this).attr("class").includes("cal-box-holiday")))
                $(this).removeClass("cal-box-highlight");
        }
    );

    $(".cal-box").on("click", function () {
        var date = $(this).data("dayNo");

        if (
            check_date_in_borders(date) &&
            !$(this).attr("class").includes("cal-box-gray") &&
            !$(this).attr("class").includes("cal-box-holiday")
        ) {
            get_lessons(date);
            $('#favoritesModal').modal('toggle');
        }
    });

    $(document).on("click", "#lessons input[type=checkbox]", function () {
        if ($(this).is(":checked")) {
            check_lesson_and_free($(this));
        }
        else
            $(this).val(0);
    });

    $("#send_time").on("click", function () {
        save_lessons($(this));
        location.reload();
        // $("#calendar").load(location.href + " #calendar");
    });

    $(document).on("click", ".delete_lesson", function () {
        console.log($(this));
        delete_lesson($(this));
    });

});