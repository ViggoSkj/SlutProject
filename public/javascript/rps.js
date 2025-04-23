function Rps() {
    const ROCK_BUTTON_ID = "rock_button"
    const PAPER_BUTTON_ID = "rock_button"
    const SCISSOR_BUTTON_ID = "rock_button"

    const OPPONTN_MOVE_ID = "opponent"

    const CHOISE_STAGE_ID = ""
    const RESULT_STAGE_ID = ""

    document.getElementById("ROCK_BUTTON_ID").addEventListener("click", () => {
        Choose("rock")
    })
    document.getElementById("PAPER_BUTTON_ID").addEventListener("click", () => {
        Choose("paper")
    })
    document.getElementById("SCISSOR_BUTTON_ID").addEventListener("click", () => {
        Choose("scissor")
    })

    async function Choose(choise)
    {
        await fetch("rsp-choose.php", {
            method: "POST",
            body: JSON.stringify({
                choise: choise
            })
        })
    }

    function ChoiseStage()
    {
        document.getElementById(CHOISE_STAGE_ID).style.display = ""
        document.getElementById(RESULT_STAGE_ID).style.display = "none"
    }
    
    function ResultStage(opponentMove)
    {
        document.getElementById(CHOISE_STAGE_ID).style.display = "none"
        document.getElementById(RESULT_STAGE_ID).style.display = ""
        document.getElementById(OPPONTN_MOVE_ID).innerText = opponentMove
    }

    function Poll(json) {
        const events = json.events

        if (events.length > 0) {
            const event = events[0]

            const opponentMove = event.move
            ResultStage(opponentMove)
            setTimeout(ChoiseStage, 3000)
        }
    }



    return {

    }
}

let rps
document.addEventListener("load", () => {
    rps = Rps()
})