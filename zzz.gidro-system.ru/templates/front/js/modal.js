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

    if (close) {
      close.addEventListener("click", () => {
        windows.forEach(item => {
          item.style.display = "none"
        })

        modal.style.display = "none"
        document.body.style.overflow = ""
        document.body.style.marginRight = "0px"
      })
    }

    if (modal) {
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
    ".menu-city a",
    ".modal-wrapper",
    ".modal-wrapper .container .close",
  )

  bindModal(
    ".change-city a",
    ".modal-wrapper",
    ".modal-wrapper .container .close",
  )
}

window.addEventListener("DOMContentLoaded", () => {
  modals()

  $(document).ready(function() {
    $(".block__images").magnificPopup({
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
   * Modal scripts Фирменные магазины.
   */
  $(document).ready(function() {
    $(".about-top").magnificPopup({
      delegate: "a",
      type: "image",
    })
  })

  $(document).ready(function() {
    $(".about-top-1").off('click');
  })

  // Удаление пустого li из меню сайтбара
    var emptyLi = document.querySelectorAll(".sidebar-menu li");
    emptyLi.forEach(el => {
        if (el.textContent == "") {
            document.querySelector(".sidebar-menu").removeChild(el);
        }
    })

})

/**
 * Accordion scripts.
 */
$(document).ready(function() {
  $("#accordeon .acc-head").on("click", f_acc)
})

function f_acc() {
  $("#accordeon .acc-body")
    .not($(this).next())
    .slideUp(300)
  $(this)
    .next()
    .slideToggle(300)
}

/**
 * Tabs scripts.
 */
;(function($) {
  $(function() {
    $(".tabs-header").on("click", function() {
      $(".tabs-header").removeClass("active")
      $(this).addClass("active")
      $(".tabs__content-item").removeClass("active")
      $("#contentId" + $(this).attr("data-id")).addClass("active")
    })
  })
})(jQuery)

// tab
;(function($) {
  $(function() {
    $(".flexslider-items > div").on("click", function() {
      let src = $(this).css("background")
      $(".flexslider-top").css("background", src)
      $(".flexslider-items > div").removeClass("active")
      $(this).addClass("active")
    })
  })
})(jQuery)

;(function($) {
  $(function() {
    $(".adaptive-menu > svg").on("click", function() {
      let src = $('body').css("background", "#fff")
      if ($(".middle-menu-unwrap").css("display") == "block") {
        $(".middle-menu-unwrap").css("display", "none")
      } else {
        $(".middle-menu-unwrap").css("display", "block")
      }
    })
  })
})(jQuery)