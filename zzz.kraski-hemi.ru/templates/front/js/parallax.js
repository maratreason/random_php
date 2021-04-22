$(window).scroll(function() {
  parallax()
})

function parallax() {
  var wScroll = $(window).scrollTop()

  $('.parallax').css('background-position', 'center ' + wScroll + 'px')
}
