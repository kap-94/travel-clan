"use strict";

const flashMessage = document.querySelector(".flash-message");

// LAZY LOADING IMAGES
const imageTargets = document.querySelector("img[data-srcset]");

const loadImage = function (entries, observer) {
  const [entry] = entries;

  if (!entry.isIntersecting) return;

  // Replace src with data-src
  entry.target.src = entry.target.dataset.src;
  entry.target.addEventListener("load", () => {
    entry.target.classList.remove("lazy-img");
  });

  observer.unobserve(entry.target);
};

const observer = new IntersectionObserver(loadImage, {
  root: null,
  threshold: 0,
});

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
