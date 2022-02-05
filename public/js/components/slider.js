//SLIDER
const slider = function () {
  const slides = document.querySelectorAll(".slide");
  const buttonLeft = document.querySelector(".slider__button--left");
  const buttonRight = document.querySelector(".slider__button--right");

  let curSlide = 0;
  const maxSlide = slides.length;

  //Functions
  const goToSlide = (slide) => {
    slides.forEach(
      (s, i) => (s.style.transform = `translateX(${100 * (i - slide)}%)`)
    );
  };

  const nextSlide = () => {
    if (curSlide === maxSlide - 1) {
      curSlide = 0;
    } else {
      curSlide++;
    }

    goToSlide(curSlide);
  };

  const prevSlide = () => {
    if (curSlide === 0) {
      curSlide = maxSlide - 1;
    } else {
      curSlide--;
    }

    goToSlide(curSlide);
  };

  const init = () => {
    goToSlide(0);
  };

  init();

  //Event Handlers
  buttonRight.addEventListener("click", nextSlide);
  buttonLeft.addEventListener("click", prevSlide);
};
slider();
