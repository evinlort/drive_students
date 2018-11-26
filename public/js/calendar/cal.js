$(document).ready(function(){

    $(".cal-box").hover(
        function() {
            if(!$(this).attr("class").includes("cal-box-gray"))
                $(this).addClass("cal-box-highlight");
        },
        function() {
            if(!$(this).attr("class").includes("cal-box-gray"))
                $(this).removeClass("cal-box-highlight");
        }
    );

    $(".cal-box").on("click", function() {
        if(!$(this).attr("class").includes("cal-box-gray")) {
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
                success: function(data) {
                    $("#lessons").empty();
                    console.log(data);
                }
            });
            
            $('#favoritesModal').modal('toggle');
        }
    });

});