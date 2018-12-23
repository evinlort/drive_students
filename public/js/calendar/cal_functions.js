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
            console.log(response);
            return false;
            $("#lessons input[name=this_date]").val(date);
            $("#lessons div").each(function() {
                $(this).find("input[type=checkbox]").prop("checked","").prop("disabled","").val(0);
            });
            data = response.data;
            data.forEach(function(element, index){
                if(element[1] == 1) {
                    $("#lessons div").each(function() {
                        if($(this).data("time") == element[0])
                            $(this).find("input[type=checkbox]").prop("checked","checked").prop("disabled","disabled").val(-1);
                    });
                }
                else if(element[1] == 2) {
                    $("#lessons div").each(function() {
                        if($(this).data("time") == element[0]) {
                            $(this).find("input[type=checkbox]").prop("checked","checked").prop("disabled","disabled").val(-1);
                            let text = "Already taken";
                            if(element[2] != undefined && element[2] == 1)
                                text += " by you";
                            $(this).find("input[type=checkbox]").parent(".switch").siblings(".time_info").text(text);
                        }
                    });
                }
            });
        }
    });
}