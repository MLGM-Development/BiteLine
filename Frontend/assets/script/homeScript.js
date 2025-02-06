let titleHome = document.getElementById("titleHome");
let preTitle = document.querySelector(".preTitle");

window.onload = function () {
    setTimeout(function() {
        titleHome.style.fontSize = "";
        preTitle.style.opacity = '1'
        preTitle.style.transform = 'translateY(0)'

    }, 500);
};

document.addEventListener('DOMContentLoaded', () => {
    const video = document.querySelector('main video');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                video.play();
            } else {
                video.pause();
            }
        });
    }, {
        threshold: 0.75 // Il video deve essere almeno al 75% visibile
    });

    observer.observe(video);
});
