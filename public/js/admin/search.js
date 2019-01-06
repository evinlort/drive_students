$(document).ready(function() {

    $(".add_student").on("click", function() {
        var identity = $("input[name='student_identity']").val();
        var time = $("input.hidden_input_time").val();
        var date = $("input.hidden_input_date").val();
        var url = window.Laravel.baseUrl + "add_student_to_lesson";
        $.post(url,{"identity":identity,"time":time,"date":date,"_token":window.Laravel.csrfToken},function(res) {
            if(res.status == "success") {
                location.reload();
            }
        });
    });

    $(".remove_student").on("click", function() {
        if(!confirm($(this).text())) {
            return false;
        }
        else {
            var identity = $("input.hidden_input_identity").val();
            var time = $("input.hidden_input_time").val();
            var date = $("input.hidden_input_date").val();
            var url = window.Laravel.baseUrl + "remove_student_from_lesson";
            $.post(url,{"identity":identity,"time":time,"date":date,"_token":window.Laravel.csrfToken},function(res) {
                if(res.status == "success") {
                    location.reload();
                }
            });
        }
    });

    $(".student_edit a").on("click", function(e){
        e.preventDefault();
        e.stopPropagation();
        var identity = $(this).parents(".students_list_row").data("identity");
        var time = $(this).parents(".students_list_row").data("time");
        $("#favoritesModal").modal("show");
        if(!identity) {
            $("div.student_remove").hide();
            $("div.student_add").show();
            $("#favoritesModalLabel").text("Text");
        }
        else {
            $("input.hidden_input_identity").val(identity);
            $("div.student_remove").show();
            $("div.student_add").hide();
        } 
        $("input.hidden_input_time").val(time);
        
    });

    $("#search").on("keyup", function() {
        var search = $(this).val();
        if(search == "") {
            $("#students_list div.students_list_row").show()
        }
        $("#students_list div.students_list_row").hide();
        $("#students_list div").each(function(index, element) {
            if(($(this).data("identity")+"").includes(search)) {
                $(this).show();
                return true;
            }
            if(($(this).data("time")+"").includes(search)) {
                $(this).show();
                return true;
            }
            if(($(this).data("name")+"").includes(search)) {
                $(this).show();
                return true;
            }
        });
    });

    $(".search_clear").on("click", function() {
        $("#students_list div.students_list_row").show();
        $("#search").val("").focus();
    });

    $(".search_submit").on("click", function() {
        if($("#search").val() != "") {
            var search = $("#search").val();
            $("#students_list div.students_list_row").hide();
            $("#students_list div").each(function(index, element) {
                if($(this).data("identity") == search || $(this).data("time") == search || $(this).data("name") == search)
                    $(this).show();
            });
        }
    });

});