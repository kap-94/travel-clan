const sidebar = function () {
  const sidebarScrim = document.querySelector(".sidebar__scrim");
  const sidebarContent = document.querySelector(".sidebar__content");
  const navDrawerButtons = document.querySelectorAll(
    ".button-hamburger__wrapper"
  );

  sidebarScrim.addEventListener("click", () => {
    sidebarContent.removeAttribute("opened");
    sidebarScrim.removeAttribute("opened");
  });

  navDrawerButtons.forEach((el) => {
    el.addEventListener("click", () => {
      if (sidebarContent.getAttribute("opened") === "") {
        sidebarScrim.removeAttribute("opened");
        sidebarContent.removeAttribute("opened");
      } else {
        sidebarScrim.setAttribute("opened", "");
        sidebarContent.setAttribute("opened", "");
      }
    });
  });
};

export default sidebar;
