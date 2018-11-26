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
        $.ajax({
            url: window.Laravel.baseUrl + "asd",
            type: "post",
            headers: {
                'X-CSRF-TOKEN': window.Laravel.csrfToken
            },
            success: function(data) {
                console.log(data);
            }
        });
        if(!$(this).attr("class").includes("cal-box-gray"))
            $('#favoritesModal').modal('toggle');
    });

});