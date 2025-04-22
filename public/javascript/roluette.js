function RoluetteHandler(pollInterval) {
    let currentEventIndex = 0;

    async function Poll() {
        const res = await fetch("poll-event-roluette.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                currentEventIndex: currentEventIndex
            })
        });

        const json = await res.json();
        if (res.status == 400) {
            console.log(json.message);
            return;
        } else if (res.status >= 500) {
            console.log("polling resulted in 500");
            return;
        }
    }

    const pollIntervalIndex = setInterval(() => {
        Poll()
    }, pollInterval);

    return {
        get currentEventIndex() { return currentEventIndex; }
    }
}

let roluette;

window.addEventListener("load", async () => {
    roluette = RoluetteHandler(1000);
})