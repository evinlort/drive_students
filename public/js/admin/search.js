$(document).ready(function() {

    $(".student_edit a").on("click", function(e){
        e.preventDefault();
        e.stopPropagation();
        var identity = $(this).parents(".students_list_row").data("identity");
        $("#favoritesModal").modal("show");
        console.log(identity);
        $("#favoritesModal.remove_student label").text("Huekst");
        $("#favoritesModal").data("sidentity",identity);
        
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