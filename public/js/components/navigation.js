const navigation = function () {
  const navButton = document.querySelector(".navbar__button");
  const fixedNavigation = document.querySelector(".fixed-navigation");

  navButton.addEventListener("click", () => {
    if (fixedNavigation.getAttribute("opened") === "") {
      fixedNavigation.removeAttribute("opened");
      navButton.style.backgroundColor = "#FFFFFF";
    } else {
      fixedNavigation.setAttribute("opened", "");
      navButton.style.backgroundColor = "#000000";
    }
  });
};

export default navigation;
