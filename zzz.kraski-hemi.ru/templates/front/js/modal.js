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


  // Модальное окно выбора цвета
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


  // Cart scripts
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
          console.log(response)
          // if(response == "OK") {
          //   location.reload();
          // } else {
          //   alert("Ошибка в запросе! Сервер вернул вот что: " + response);
          // }
        },
      })
    })
  })

  // адаптивное меню
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

  // анимация появления картинок сферы применения.
  $(document).ready(function() {
    $(".slide-button").on("click", function() {
      $(".block__text .block__images").css("margin-top", "50px")
      setTimeout(function() {
        $(".block__text .block__images").css("display", "flex")
      }, 1)
      $(".block__text .block__images").toggle("slow", function() {})
    })
  })

  // Дубликат корзины для адаптивного меню
  let cart = document.querySelector('.adaptive-cart .cart-counter')
  let cartCnt = document.querySelector('.cart .cart-counter')
  cart.innerHTML = cartCnt.textContent;


  // Модальное окно страницы Фирменные магазины
  $(document).ready(function() {
    $(".magaziny-photo").magnificPopup({
      delegate: "a",
      type: "image",
    })
  })
  
})

// Аккордион
$(document).ready(function() {
	$('#accordeon .acc-head').on('click', f_acc);
});

function f_acc(){
  $('#accordeon .acc-body').not($(this).next()).slideUp(300);
    $(this).next().slideToggle(300);
}


