/**
 * Модальное окно.
 */
const modals = () => {
  function bindModal(
    triggerSelector,
    modalSelector,
    closeSelector,
    closeClickOverlay = true,
  ) {
    const trigger = document.querySelectorAll(triggerSelector),
      modal = document.querySelector(modalSelector),
      close = document.querySelector(closeSelector),
      windows = document.querySelectorAll("[data-modal]"),
      scroll = calcScroll()

    trigger.forEach(item => {
      item.addEventListener("click", event => {
        if (event.target) {
          event.preventDefault()
        }

        windows.forEach(item => {
          item.style.display = "none"
        })

        modal.style.display = "block"
        document.body.style.overflow = "hidden"
        document.body.style.marginRight = `${scroll}px`
      })
    })

    close.addEventListener("click", () => {
      windows.forEach(item => {
        item.style.display = "none"
      })

      modal.style.display = "none"
      document.body.style.overflow = ""
      document.body.style.marginRight = "0px"
    })

    modal.addEventListener("click", event => {
      if (event.target === modal && closeClickOverlay) {
        windows.forEach(item => {
          item.style.display = "none"
        })

        modal.style.display = "none"
        document.body.style.overflow = ""
        document.body.style.marginRight = "0px"
      }
    })
  }

  function calcScroll() {
    let div = document.createElement("div")
    div.style.width = "50px"
    div.style.height = "50px"
    div.style.overflowY = "scroll"
    div.style.visibility = "hidden"

    document.body.appendChild(div)

    let scrollWidth = div.offsetWidth - div.clientWidth
    div.remove()

    return scrollWidth
  }

  bindModal(
    ".top__menu-text a",
    ".modal-wrapper",
    ".modal-wrapper .container .close",
  )

  bindModal(
    ".change-city a",
    ".modal-wrapper",
    ".modal-wrapper .container .close",
  )
}



/*
  Slider scripts
*/
const sliders = (slides, dir, prev, next) => {
  let paused = false
  const items = document.querySelectorAll(slides)
  const buttons = document.querySelectorAll(".support__items li")
  let slideIndex = 1

  function showSlides(n) {
    if (n > items.length) {
      slideIndex = 1
    }

    if (n < 1) {
      slideIndex = items.length
    }

    items.forEach(item => {
      item.classList.add("animated")
      item.style.display = "none"
    })

    buttons.forEach(el => el.classList.remove('active'))

    if (items[slideIndex - 1]) {
      items[slideIndex - 1].classList.add("fadeIn")
      items[slideIndex - 1].style.display = "block"
      items[slideIndex - 1].classList.add('active')
      buttons[slideIndex - 1].classList.add('active')
    } else {
      return
    }
  }

  showSlides(slideIndex)

  function incrementSlides(n) {
    showSlides(slideIndex += n)
  }

  function plusSlides(n) {
    showSlides(slideIndex = n)
  }

  try {
    const prevBtn = document.querySelector(prev),
      nextBtn = document.querySelector(next)

    prevBtn.addEventListener("click", () => {
      incrementSlides(-1)
    })

    nextBtn.addEventListener("click", () => {
      incrementSlides(1)
    })

  } catch (err) {}

  if (items[0]) {
    items[0].parentNode.addEventListener("mouseenter", () => {
      clearInterval(paused)
    })
  }

  buttons.forEach(el => {
    el.addEventListener("click", () => {
      plusSlides(+el.id)
    })
  })
}



window.addEventListener("DOMContentLoaded", () => {
  sliders(
    ".swiper-slide",
    "horizontal",
    ".las.la-angle-left",
    ".las.la-angle-right.btn-right",
  )

  modals()

  $(document).ready(function() {
    $(".block__images").magnificPopup({
      delegate: "a",
      type: "image",
    })
  })
    
    $(document).ready(function() {
    $(".gallery__items.about").magnificPopup({
      delegate: "a",
      type: "image",
    })
  })

  $(document).ready(function() {
    $(".gallery__items.about .images").magnificPopup({
      delegate: "a",
      type: "image",
    })
  })

  $(".cart-apply").magnificPopup({
    type: "inline",
    modal: true,
  })
  $(document).on("click", ".form-close", function(e) {
    e.preventDefault()
    $.magnificPopup.close()
  })

/**
 * Модальное окно выбора цвета.
 */
  $(document).ready(function() {
    $(".color-picker .color-button").magnificPopup({
      type: "inline",
      modal: true
    })

    $(this).on("click", ".form-color-close", function(e) {
      e.preventDefault()
      $.magnificPopup.close()
    })
  })


/**
 * Cart scripts.
 */
  let cartCounter = $(".cart-counter")
  let count = $(".top__info-cart-count")
  let price = $(".top__info-cart-price")

  let counter = +cartCounter.val()
  let count1 = +count.val()
  let total = 0

  // Cart ajax
  $(document).ready(function() {
    // обрабатываем событие нажатия на кнопку "Добавить новый товар"
    $("input.quantity").change(function() {
      //console.log(1)
      var count = $("input.quantity").val()
      // отправляем AJAX запрос
      $.ajax({
        type: "POST",
        url: "/ajax/mod/modname-basketapi",
        // url: "/ajax/mod-mod_feedback",
        data: "count=" + count,
        success: function(response) {
          // console.log(response)
        },
      })
    })
  })

/**
 * Адаптивное меню.
 */
  $(document).ready(function() {
    let adaptiveMenu = $(".adaptive-menu1__wrapper")
    $(".burger-button").on("click", () => {
      if (adaptiveMenu.css("display") == "none") {
        adaptiveMenu.css("display", "block")
      } else {
        adaptiveMenu.css("display", "none")
      }
    })
  })

/**
 * Анимация появления картинок сферы применения.
 */
  $(document).ready(function() {
    $(".slide-button").on("click", function() {
      $(".block__text .block__images").css("margin-top", "50px")
      setTimeout(function() {
        $(".block__text .block__images").css("display", "flex")
      }, 1)
      $(".block__text .block__images").toggle("slow", function() {})
    })
  })

/**
 * Дубликат корзины для адаптивного меню.
 */
  let cart = document.querySelector('.adaptive-cart .cart-counter')
  let cartCnt = document.querySelector('.cart .cart-counter')
  cart.innerHTML = cartCnt.textContent;

/**
 * Modal scripts Фирменные магазины.
 */
  $(document).ready(function() {
    $(".magaziny-photo").magnificPopup({
      delegate: "a",
      type: "image",
    })
  })


/**
 * Accordion scripts.
 */
$(document).ready(function() {
	$('#accordeon .acc-head').on('click', f_acc);
});

function f_acc() {
  $('#accordeon .acc-body').not($(this).next()).slideUp(300);
    $(this).next().slideToggle(300);
}


/**
 * Tabs scripts.
 */
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

  /**
   * Parallax scripts.
   */
  $(window).scroll(function() {
    parallax();
  });

  function parallax() {
    var wScroll = $(window).scrollTop();
    $('.parallax').css('background-position', 'center ' + wScroll + 'px');
    }
    

    /**
     * Скрипт скролла на позицию заголовка
     */
    // сперва получаем позицию элемента относительно документа\
    var elem = document.querySelector('.top__content .h1')
    var scrollTop;
    if (elem) {
        scrollTop = $('.top__content .h1').offset().top;
    }
    // скроллим страницу на значение равное позиции элемента
    if (document.documentElement.clientWidth < 768) {
        $('html, body').animate({ scrollTop: scrollTop  - 100 }, 1200);
    }
})

