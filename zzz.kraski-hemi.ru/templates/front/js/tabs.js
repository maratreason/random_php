
(function($) {
  $(function() {
    $(".tabs-header").on("click", function() {
      $(".tabs-header").removeClass("active")
      $(this).addClass("active")
      $(".tabs__content-item").removeClass("active");
      $("#contentId"+$(this).attr("data-id")).addClass("active");
    });
  });
})(jQuery);

// tab
(function($) {
  $(function() {
    $(".top__content-block-subimg > div").on("click", function() {
      let src = $(this).css('background')
      $(".top__content-block-img").css("background", src)
      $(".top__content-block-subimg > div").removeClass("active")
      $(this).addClass("active")
    });
  });
})(jQuery);