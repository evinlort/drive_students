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
            else {
                alert(res.message);
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
        var student_name = $(this).parents(".students_list_row").data("name");
        console.log(student_name);
        var time = $(this).parents(".students_list_row").data("time");
        $("#favoritesModal").modal("show");
        if(!identity) {
            $("div.student_remove").hide();
            $("div.student_add").show();
            $("#favoritesModalLabel").text($("div.card-header strong").text()+" "+time+" "+$("div.student_add div.col-12 button").text());
            $("#student_name").text("");
            $("#student_identity").text("");
            $("div.student_credentials").hide();
        }
        else {
            $("input.hidden_input_identity").val(identity);
            // $("input.hidden_input_name").val(student_name);
            $("div.student_remove").show();
            $("div.student_add").hide();
            $("#favoritesModalLabel").text($("div.card-header strong").text()+" "+time+" "+$("div.student_remove div.col-12 button").text());
            $("#student_name").text(student_name);
            $("#student_identity").text(identity);
            $("div.student_credentials").show();
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

    $(".delete_student").on("click", function(e) {
        e.stopPropagation();
        if(confirm($(this).data("text"))) {
            console.log(e);
            $(e.target).submit();
            return true;
        }
        return false;
    });

});