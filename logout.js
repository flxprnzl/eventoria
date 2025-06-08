document.addEventListener("DOMContentLoaded", function () {
  var logoutBtn = document.getElementById("logout-link");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", function (e) {
      e.preventDefault();
      fetch("logout_api.php", { method: "POST" }).then((response) => {
        if (response.status === 200) {
          alert("Du wurdest ausgeloggt.");
          window.location.href = "index.html";
        }
        // Bei 204 (nicht eingeloggt): nichts tun
      });
    });
  }
});
