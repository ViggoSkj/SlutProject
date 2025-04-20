const ROLUETTE_COLORS = {
    GREEN: "green",
    RED: "red",
    BLACK: "black"
};

const ROLUETTE_STATES = {
    SPINNING: "roluette_spinning",
    BETTING: "roluette_betting"
};

const INDEX_TO_COLOR = {
    0: ROLUETTE_COLORS.GREEN,
    1: ROLUETTE_COLORS.RED,
    2: ROLUETTE_COLORS.BLACK,
    3: ROLUETTE_COLORS.RED,
    4: ROLUETTE_COLORS.BLACK,
    5: ROLUETTE_COLORS.RED,
    6: ROLUETTE_COLORS.BLACK,
    7: ROLUETTE_COLORS.RED,
    8: ROLUETTE_COLORS.BLACK,
    9: ROLUETTE_COLORS.RED,
    10: ROLUETTE_COLORS.BLACK,
    11: ROLUETTE_COLORS.BLACK,
    12: ROLUETTE_COLORS.RED,
    13: ROLUETTE_COLORS.BLACK,
    14: ROLUETTE_COLORS.RED,
    15: ROLUETTE_COLORS.BLACK,
    16: ROLUETTE_COLORS.RED,
    17: ROLUETTE_COLORS.BLACK,
    18: ROLUETTE_COLORS.RED,
    19: ROLUETTE_COLORS.RED,
    20: ROLUETTE_COLORS.BLACK,
    21: ROLUETTE_COLORS.RED,
    22: ROLUETTE_COLORS.BLACK,
    23: ROLUETTE_COLORS.RED,
    24: ROLUETTE_COLORS.BLACK,
    25: ROLUETTE_COLORS.RED,
    26: ROLUETTE_COLORS.BLACK,
    27: ROLUETTE_COLORS.RED,
    28: ROLUETTE_COLORS.BLACK,
    29: ROLUETTE_COLORS.BLACK,
    30: ROLUETTE_COLORS.RED,
    31: ROLUETTE_COLORS.BLACK,
    32: ROLUETTE_COLORS.RED,
    33: ROLUETTE_COLORS.BLACK,
    34: ROLUETTE_COLORS.RED,
    35: ROLUETTE_COLORS.BLACK,
    36: ROLUETTE_COLORS.RED,
}

function RoluetteWheel() {
    const numbers = []
    const FPS = 1000
    let offset = 0
    let prevTime = Date.now()
    let blockWidth = 0
    let blockMargin = 20

    let target = 0
    let speed = 0

    Array.from(document.getElementById("roluette-bar").children).forEach((number, i) => {
        number.style.backgroundColor = INDEX_TO_COLOR[i]

        blockWidth = number.clientWidth

        numbers.push({
            index: i,
            element: number
        })
    })

    function Animate(dt) {
        const totalWidth = 37 * (blockWidth + blockMargin)

        if (target !== -1) {
            const current = offset * 37 / totalWidth
            speed = Math.abs(current - target) * 7 / 37
        } else {
            speed = 7
        }


        offset += speed * dt
        offset = offset % totalWidth

        numbers.forEach(number => {
            const totalWidth = 37 * (blockWidth + blockMargin)
            const o = (37 - (number.index - 19)) * (blockWidth + blockMargin)
            number.element.style.transform = `translateX(${(totalWidth + blockWidth) / 2 - ((o + offset) % totalWidth)}px)`
        })
    }

    function Control() {
        const time = Date.now()
        const dt = time - prevTime

        if (dt >= 1000 / FPS) {
            prevTime = time;
            Animate(dt)
        }

        requestAnimationFrame(Control)
    }

    requestAnimationFrame(Control);


    return {
        set target(value) { target = value }
    }
}

function RoluetteHandler(pollInterval) {

    const SELECT_GREEN_ID = "roluette-color-green";
    const SELECT_RED_ID = "roluette-color-red";
    const SELECT_BLACK_ID = "roluette-color-black";

    const BET_AMOUNT_ID = "bet-amount"
    const BET_BUTTON_ID = "bet"

    let currentEventIndex = -1;

    let state = ROLUETTE_STATES.BETTING;

    const wheel = RoluetteWheel()

    async function Poll() {
        const res = await fetch("/poll-event-roluette.php", {
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

        if (json.newEventId)
        {
            currentEventIndex = json.newEventId
        }

        const events = json.events
        if (events.length > 0) {
            currentEventIndex = events[events.length - 1].Id

            const lastEvent = events[events.length - 1]

            if (lastEvent.eventType = "roluette_spin_result") {
                wheel.target = -1
                setTimeout(() => {
                    wheel.target = lastEvent.Content.index
                }, 2000)
                setTimeout(() => {
                    
                }, 5000)
            }
        }
    }

    async function Bet(amount, color) {
        const res = await fetch("/roluette-bet.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                color: color,
                amount: amount
            })
        });

        const json = await res.json();
        if (res.status == 400) {
            console.log(json.message);
            return;
        } else if (res.status >= 500) {
            console.log("betting resulted in 500");
            return;
        }
    }

    document.getElementById(BET_BUTTON_ID).addEventListener("click", async () => {
        if (state !== ROLUETTE_STATES.BETTING)
            return

        const amount = Number(document.getElementById(BET_AMOUNT_ID).value);

        const isGreen = document.getElementById(SELECT_GREEN_ID).checked;
        const isBlack = document.getElementById(SELECT_BLACK_ID).checked;
        const isRed = document.getElementById(SELECT_RED_ID).checked;

        const color = isGreen ? "green" : isBlack ? "black" : "red";

        Bet(amount, color)
    })

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