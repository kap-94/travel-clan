"use strict";

const flashMessage = document.querySelector(".flash-message");

// LAZY LOADING IMAGES
const imageTargets = document.querySelectorAll("img[data-src]");

const loadImage = function (entries, observer) {
  const [entry] = entries;

  if (!entry.isIntersecting) return;

  // Replace src with data-src
  entry.target.src = entry.target.dataset.src;
  entry.target.addEventListener("load", () => {
    entry.target.classList.remove("lazy-image");
  });

  observer.unobserve(entry.target);
};

const imageObserver = new IntersectionObserver(loadImage, {
  root: null,
  threshold: 0,
  rootMargin: "200px",
});

imageTargets.forEach((image) => imageObserver.observe(image));

// FLASH MESSAGES FADE OUT
window.addEventListener("load", () => {
  setTimeout(() => {
    if (flashMessage) {
      flashMessage.style.opacity = 0;
      flashMessage.style.visibility = "hidden";
      flashMessage.style.transform = "translateY(-2.5rem)";
    }
  }, 2375);
});
