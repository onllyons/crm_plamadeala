let intervalAnalytics = setInterval(() => supplementAnalytics(1), 10000);

window.addEventListener("load", () => supplementAnalytics());
window.addEventListener("blur", () => clearInterval(intervalAnalytics));
window.addEventListener("focus", () => {
    supplementAnalytics()
    intervalAnalytics = setInterval(() => supplementAnalytics(1), 10000)
});

function supplementAnalytics(plusTime = 0) {
    fetch(location.pathname, {
        method: "POST",
        headers: {
            "Content-type": "application/x-www-form-urlencoded",
        },
        body: `width=${innerWidth}x${innerHeight}&plusTime=${plusTime}`,
    })
}