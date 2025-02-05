let titleHome = document.getElementById("titleHome");
let pt1 = document.getElementById("pt1");
let pt2 = document.getElementById("pt2");

window.onload = function () {
    setTimeout(function() {
        titleHome.style.fontSize = "20vw";
        pt1.style.opacity = '1'
        pt2.style.opacity = '1'
        pt1.style.transform = 'translateY(0)'
        pt2.style.opacity = 'translateY(0)'

    }, 500);
};
