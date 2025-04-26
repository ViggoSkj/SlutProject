
function RefreshTimer() {
    const e = document.getElementById("daily-reward-button");
    let next = Number(e.getAttribute("data-next"))
    next--;
    next = Math.max(0, next)
    e.setAttribute("data-next", next)

    if (next === 0)
        e.classList.add("button-pos")
    else
        e.classList.remove("button-pos")

    const h = Math.floor(next/3600)
    const m = Math.floor(next/60)%60
    const s = Math.floor(next)%60
    const dateString = `${h}h ${m}m ${s}s`

    e.innerHTML = "Daily Reward " + (next ? dateString : "");
}


window.addEventListener("load", () => {
    const e = document.getElementById("daily-reward-button")
    if (e !== null) {
        e.addEventListener("click", async () => {
            const res = await fetch("claim-daily.php", {
                method: "POST",
            })

            if (res.status !== 200)
                return

            const json = await res.json()

            if (!json.claimed)
                return

            document.getElementById("daily-reward-button").setAttribute("data-next", 3600 * 24);
        })

        setInterval(() => {
            RefreshTimer()
        }, 1000);
    }
})