function Chat(events) {
    const MESSAGE_SEND_ID = "send-message"
    const MESSAGE_INPUT_ID = "message-input"
    const MESSAGES_CONTAINER_ID = "chat-messages"

    document.getElementById(MESSAGE_SEND_ID).addEventListener("click", () => {
        const message = document.getElementById(MESSAGE_INPUT_ID).value
        SendMessage(message)
    })

    events.Listen(Poll)

    async function Poll(json) {
        const newMessages = json.newMessages

        newMessages.forEach(message => {
            AppendMessage(message.message, message.user, message.you)
        });
    }

    function AppendMessage(message, user, you) {
        const div = document.createElement("div")
        const nameP = document.createElement("p")
        const messageP = document.createElement("p")
        nameP.innerText = user
        messageP.innerText = message

        nameP.classList.add("name")
        messageP.classList.add("message")

        div.appendChild(nameP)
        div.appendChild(messageP)

        if (you)
            div.setAttribute("you", "")

        document.getElementById(MESSAGES_CONTAINER_ID).appendChild(div)
    }

    async function SendMessage(message) {
        await fetch("send-message.php", {
            method: "POST",
            body: JSON.stringify({
                message: message
            })
        })
    }

    return {

    }
}