$(document).ready(function(){
    $($("span.tag-to-use").text()).each(function(i) {
        //set id to its parent block
        var customIdName = $(this).text().toLowerCase();
        customIdName = customIdName.replace(/ /g, "_");
        $(this).closest("div").attr("id", customIdName);

        var ul = $("#menu-based-on-tag .topic-list");
        if($(this).parents("#menu-based-on-tag").length <= 0) {
            ul.append('<li class="tag-menu"><a href="#'+customIdName+'" class="topic">' + $(this).text() + '</a></li>');
        }
    });

    $("#menu-based-on-tag .topic-list li.tag-menu").click(function (){

        var menuList = $("#menu-based-on-tag .topic-list li");
        $.each(menuList, function(){
            $(this).find("a").removeClass("active");
        });

        $(this).find("a").addClass("active");

        // $('html, body').animate({
        //     scrollTop: $('h4').eq($(this).data("position")).offset().top - 100
        //     // scrollTop: $('h4').eq($(this).data("position"))[0].scrollIntoView({ behavior: 'smooth', block: 'start' })
        // }, 200);
    });
});