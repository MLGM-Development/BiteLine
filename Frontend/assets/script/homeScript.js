let titleHome = document.getElementById("titleHome");
let preTitle = document.querySelector(".preTitle");

let isMock1 = true;
let isMock2 = false;
let isMock3 = false;
let mock1 = document.getElementById("mock1");
let mock2 = document.getElementById("mock2");
let mock3 = document.getElementById("mock3");

let rest1 = document.getElementById("sponsorEl1");
let rest2 = document.getElementById("sponsorEl2");
let rest3 = document.getElementById("sponsorEl3");

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

rest1.addEventListener("click", function() {
    if (!isMock1) {
        mock1.style.display = "block";
        mock2.style.display = "none";
        mock3.style.display = "none";
        isMock1 = true;
        isMock2 = false;
        isMock3 = false;
    }
})

rest2.addEventListener("click", function() {
    if (!isMock2) {
        mock1.style.display = "none";
        mock2.style.display = "block";
        mock3.style.display = "none";
        isMock1 = false;
        isMock2 = true;
        isMock3 = false;
    }
})

rest3.addEventListener("click", function() {
    if (!isMock3) {
        mock1.style.display = "none";
        mock2.style.display = "none";
        mock3.style.display = "block";
        isMock1 = false;
        isMock2 = false;
        isMock3 = true;
    }
})

