// ==============================
// HERO SLIDESHOW CONTROLLER
// ==============================

let slides = document.querySelectorAll(".slide");
let index = 0;

function showNextSlide() {
    slides[index].classList.remove("active");
    index = (index + 1) % slides.length;
    slides[index].classList.add("active");
}

// Change slide every 5s
setInterval(showNextSlide, 5000);
