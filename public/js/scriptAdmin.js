import dropdown from "./components/dropdown.js";
import sidebar from "./components/sidebar.js";

// TINY RICH TEXT AREA PLUGIN
tinymce.init({
  selector: "textarea",
  // menubar: false,
  // plugins: "code",
  // toolbar: "bold italic",
});

// DATE TIME PICKER PLUGIN
$("#published_at").datetimepicker({
  format: "Y-m-d H:i:s",
});

// DELETE MODAL CONFIRMATION
// $("a.delete-modal").on("click", function (e) {
//   e.preventDefault();

//   if (confirm("Are you sure")) {
//     const frm = $("<form>");
//     frm.attr("method", "post");
//     frm.attr("action", $(this).attr("href"));
//     frm.appendTo("body");
//     frm.submit();
//   }
// });

// SIDEBAR MENU
sidebar();

// DROPDOWN MENU
dropdown();
