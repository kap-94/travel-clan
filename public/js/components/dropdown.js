const dropdown = function () {
  const userPhoto = document.querySelector(".user-nav__user-photo");
  const dropdownComponent = document.querySelector(".dropdown");
  const dropdownScrim = document.querySelector(".dropdown__scrim");

  userPhoto.addEventListener("click", () => {
    dropdownComponent.classList.toggle("dropdown--open");
    dropdownScrim.classList.toggle("dropdown__scrim--open");
  });

  dropdownScrim.addEventListener("click", () => {
    dropdownComponent.classList.remove("dropdown--open");
    dropdownScrim.classList.remove("dropdown__scrim--open");
  });
};

export default dropdown;
