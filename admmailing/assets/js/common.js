(function($){
    $(function(){
        $(document).on("click",".btn-change-to-alias,.btn-change-to-link", function(){
            var $el = $(this);
            var $cont = $el.closest(".admpage-alias-cont");
            var $contAlias  = $(".btn-change-to-link", $cont).closest(".form-group");
            var $contUrl    = $(".btn-change-to-alias", $cont).closest(".form-group");
            if($el.is(".btn-change-to-link")){
                $contUrl.removeClass("hide");
                $contAlias.addClass("hide");
                $contAlias.find(":input").val("");
            } else {
                $contAlias.removeClass("hide");
                $contUrl.find(":input").val("");
                $contUrl.addClass("hide");
            }
        });
    });
})(jQuery);