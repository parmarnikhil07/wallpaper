(function ($) {
    $.fn.setActiveSidebar = function (linkClassName) {
        var selectedLink = $(".sidebar").find("a." + linkClassName).parents("li");
        $.each(selectedLink, function (count, link) {
            $(link).addClass("active");
            if ($(link).parent("ul.treeview-menu").length){
                $(link).parent("ul.treeview-menu").show();
            }
        });
        return this;
    };
}(jQuery));