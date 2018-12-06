$(document).ready(function(){

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
        if(!$(this).attr("class").includes("cal-box-gray") && !$(this).attr("class").includes("cal-box-holiday")) {
            var day_num = $(this).data("dayNo");
            $.ajax({
                url: window.Laravel.baseUrl + "get_lessons",
                type: "post",
                data: {
                    day:$(this).data("dayNo")
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': window.Laravel.csrfToken
                },
                success: function(response) {
                    $("#lessons input[name=this_date]").val(day_num);
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
                        // console.log(element[1]);
                    });
                }
            });
            
            $('#favoritesModal').modal('toggle');
        }
    });

    $("#lessons input[type=checkbox]").on("click", function() {
        if($(this).is(":checked"))
            $(this).val(1);
        else
            $(this).val(0);
    });

    $("#send_time").on("click", function() {
        console.log("clicked");
        var to_send = [];
        to_send.push($("#lessons input[name=this_date]").val());
        $("#lessons div input[type=checkbox][value=1]:checked").each(function() {
            to_send.push($(this).parents(".time-string").data("time"));
        });
        
        $.ajax({
            url: window.Laravel.baseUrl + "set_lessons",
            type: "post",
            data: {
                date_n_times: to_send
            },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': window.Laravel.csrfToken
            },
            success: function(res) {
                if(res.success)
                    $('#favoritesModal').modal('toggle');
                return false;
            }
        })
        .fail(function (res) {
            if (res.status == 422) {
                // some other error. Just render a popup or something.
                $(".errors").text(res.responseJSON.errors.date_n_times);
                return;
            }
        });
    });
});