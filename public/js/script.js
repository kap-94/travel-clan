const flashMessage = document.querySelector(".flash-message");

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
