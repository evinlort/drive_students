var url = window.Laravel.baseUrl;
var csrf = window.Laravel.csrfToken;

function check_date_in_borders(date) {
    var return_value = false;
    var user_id = $("#lessons input[name='user_id']").val();
    $.ajax({
        type: "post",
        url: url + "check_date_is_in_borders",
        async: false,
        data: {
            date: date,
            user_id: user_id,
            _token: csrf
        },
        success: function (ret) {
            return_value = ret.status;
        }
    });
    return return_value;
}

function get_lessons(date) {
    $.ajax({
        url: url + "get_lessons",
        type: "post",
        data: {
            day: date,
            user_id: $("#lessons input[name=user_id]").val()
        },
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': window.Laravel.csrfToken
        },
        success: function (response) {
            $("#lessons input[name=this_date]").val(date);
            clean_modal();
            $("#lessons div").show();
            response.data.forEach(function (element) {
                if (element[1]) {
                    $("#lessons div.time-string").each(function () {
                        if ($(this).data("time") == element[0]) {
                            if ($(this).data("time") < "07:00" && element[1] == 2) {
                                $(this).hide();
                            }
                            if ($(this).data("time") > "19:00" && element[1] == 2) {
                                $(this).hide();
                            }
                            $(this).find("input[type=checkbox]").prop("checked", "checked").prop("disabled", "disabled").val(-1);
                            $(this).find("input[type=checkbox]").parent(".switch").siblings(".time_info").text(element[2]);
                        }
                    });
                }
                else {
                    $("#lessons div.time-string").each(function () {
                        if ($(this).data("time") == element[0]) {
                            if ($(this).data("time") < "07:00" && element[1] == 0) {
                                $(this).hide();
                            }
                            if ($(this).data("time") > "19:00" && element[1] == 0) {
                                $(this).hide();
                            }

                            if ($(this).data("time") > response.half_day_end_time && element[3] == true) {
                                $(this).hide();
                            }
                            $(this).find(".delete_lesson").hide();
                        }
                    });
                }
            });
        }
    });
}

function clean_modal() {
    $("#lessons div").each(function () {
        $(this).find("input[type=checkbox]").prop("checked", "").prop("disabled", "").val(0);
        $(this).find("input[type=checkbox]").parent(".switch").siblings(".time_info").text("");
    });
    $(".modal_errors").hide();
    $("span.errors").text("");
}

function get_checked_lessons() {
    var choosen = [];
    choosen.push($("#lessons input[name='this_date']").val());
    $("#lessons div").each(function () {
        if ($(this).find("input[type=checkbox]").val() == "1") {
            if ($(this).data("time") !== undefined) {
                choosen.push($(this).data("time"));
            }
        }
    });
    return choosen;
}

function check_lesson_and_free(that) {
    // var that = $(this);
    var url = window.Laravel.baseUrl;
    var to_send = [];
    to_send.push($("#lessons input[name=this_date]").val());
    to_send.push(that.parents(".time-string").data("time"));
    var data = { date_n_times: to_send, '_token': window.Laravel.csrfToken };
    $.post(url + "is_lesson_free", data, function (res) {
        if (res.status == 'yes') {
            that.val(1);
            let student_checked_unsaved_lessons_array = get_checked_lessons();
            let user_id = $("#lessons input[name=user_id]").val()
            $.post(
                url + "is_has_free_lessons",
                { "checked_lessons": student_checked_unsaved_lessons_array, "user_id": user_id, "_token": window.Laravel.csrfToken },
                function (ret) {
                    if (!ret.status) {
                        that.val(0);
                        that.prop("checked", false);
                        $(".errors").text(ret.error);
                        $('#favoritesModal').animate({ scrollTop: 0 }, 'slow', function () {
                            $(".modal_errors").show();
                        });
                        $(window).scrollTop(0);
                    }
                    else {
                        // All fine
                    }
                }
            );
        }
    })
        .fail(function (res) {
            if (res.status == 422) {
                that.val(-1);
                that.prop("checked", true);
                that.prop("disabled", "disabled");
                $(".errors").text(res.responseJSON.errors.date_n_times);
                $('#favoritesModal').animate({ scrollTop: 0 }, 'slow', function () {
                    $(".modal_errors").show();
                });
                $(window).scrollTop(0);
                return;
            }
            else
                return false;
        });
}

function save_lessons(that) {
    var to_send = [];
    var date = $("#lessons input[name=this_date]").val();
    var user_id = $("#lessons input[name=user_id]").val();
    to_send.push(date);
    $("#lessons div input[type=checkbox][value=1]:checked").each(function () {
        to_send.push($(this).parents(".time-string").data("time"));
    });

    $.ajax({
        url: window.Laravel.baseUrl + "set_lessons",
        type: "post",
        data: {
            date_n_times: to_send,
            user_id: user_id
        },
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': window.Laravel.csrfToken
        },
        success: function (res) {
            if (res.success) {
                if (res.message != undefined && res.message != "") {
                    get_lessons(date);
                    $("span.errors").text(res.message);
                    $('#favoritesModal').animate({ scrollTop: 0 }, 'slow', function () {
                        $(".modal_errors").show();
                    });
                    $(window).scrollTop(0);
                }
                else {
                    $('#favoritesModal').modal('toggle');
                }
            }
            return false;
        }
    })
        .fail(function (res) {
            if (res.status == 422) {
                $(".errors").text(res.responseJSON.errors.date_n_times);
                $('#favoritesModal').animate({ scrollTop: 0 }, 'slow', function () {
                    $(".modal_errors").show();
                });
                $(window).scrollTop(0);
                return;
            }
            else
                return false;
        });
}

function delete_lesson(delete_button) {

    var date = $("#lessons input[name=this_date]").val();
    var user_id = $("#lessons input[name=user_id]").val();
    var time = delete_button.parents(".time-string").data("time");
    $.ajax({
        url: window.Laravel.baseUrl + "delete_lesson",
        type: "post",
        data: {
            date: date,
            time: time,
            user_id: user_id
        },
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': window.Laravel.csrfToken
        },
        success: function (res) {
            console.log(res);
            if (res.success) {
                delete_button.siblings(".switch").find("input").removeAttr("disabled").trigger("click");
                delete_button.siblings(".time_info").text("");
                delete_button.hide();
            }
            return false;
        }
    })
}