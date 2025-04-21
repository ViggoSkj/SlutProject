function Events(pollingInterval, endpoint) {
    const listeners = []

    let currentEventIndex = -1;
    let currentChatIndex = -1;

    async function Poll() {
        const res = await fetch(endpoint, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                currentEventIndex: currentEventIndex,
                currentChatIndex: currentChatIndex
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

        if (json.newEventId) {
            currentEventIndex = json.newEventId
        }
        
        if (json.newMessageId) {
            currentChatIndex = json.newMessageId
        }

        listeners.forEach(listener => {
            listener(json)
        })
    }

    function Listen(listener)
    {
        listeners.push(listener)
    }

    const intervalIndex = setInterval(Poll, pollingInterval)

    return {
        Listen,
        Poll
    }
}