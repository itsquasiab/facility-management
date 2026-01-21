fetch('menu.php')
    .then(response => response.text())
    .then(data => {
        document.getElementById('menu').innerHTML = data;
    })
    .catch(error => {
        console.error('Lỗi khi tải menu:', error);
    });

// Toggle mobile nav
function toggleNav() {
    const nav = document.getElementById("nav-content");
    nav.classList.toggle("responsive");
}

// Close nav when clicking elsewhere
document.addEventListener("click", e => {
    const nav = document.getElementById("nav-content");
    const menuIcon = document.querySelector(".menu-icon");

    // Close nav-content if clicking outside nav and menu icon
    if (
        nav.classList.contains("responsive") &&
        !nav.contains(e.target) &&
        !menuIcon.contains(e.target)
    ) {
        nav.classList.remove("responsive");
    }
});