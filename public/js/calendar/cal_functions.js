var url = window.Laravel.baseUrl;
var csrf = window.Laravel.csrfToken;

function check_date_in_borders(date) {
    var return_value = false;
    $.ajax({
        type: "post",
        url: url + "check_date_is_in_borders", 
        async: false,
        data: { 
            date: date,
             _token: csrf 
            },
        success: function(ret) {
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
            day: date
        },
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': window.Laravel.csrfToken
        },
        success: function(response) {
            $("#lessons input[name=this_date]").val(date);
            clean_modal();
            response.data.forEach(function(element, index){
                if(element[1]) {
                    $("#lessons div").each(function() {
                        if($(this).data("time") == element[0]) {
                            $(this).find("input[type=checkbox]").prop("checked","checked").prop("disabled","disabled").val(-1);
                            $(this).find("input[type=checkbox]").parent(".switch").siblings(".time_info").text(element[2]);
                        }
                    });
                }
            });
        }
    });
}

function clean_modal() {
    $("#lessons div").each(function() {
        $(this).find("input[type=checkbox]").prop("checked","").prop("disabled","").val(0);
        $(this).find("input[type=checkbox]").parent(".switch").siblings(".time_info").text("");
        $(".modal_errors").hide();
        $("span.errors").text("");
    });
}

function get_checked_lessons() {
    var choosen = [];
    choosen.push($("#lessons input[name='this_date']").val());
    $("#lessons div").each(function() {
        if($(this).find("input[type=checkbox]").val() == "1") {
            if($(this).data("time") !== undefined) {
                choosen.push($(this).data("time"));
            }
        }
    });
    return choosen;
}