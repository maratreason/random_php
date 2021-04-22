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
