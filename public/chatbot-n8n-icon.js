
// chatbot.js - Updated with icon trigger

(function () {
    const root = document.getElementById("bitkode-chatbot-root");
    if (!root) return;

    const sessionId = localStorage.getItem("bitkode-chatbot-session") || crypto.randomUUID();
    localStorage.setItem("bitkode-chatbot-session", sessionId);

    let chatHistory = [];

    const iconButton = document.createElement("div");
    iconButton.className = "bitkode-chatbot-launcher";
    iconButton.innerHTML = "💬";
    iconButton.setAttribute("aria-label", "Open chat");
    iconButton.setAttribute("role", "button");
    iconButton.setAttribute("tabindex", "0");
    root.appendChild(iconButton);

    const container = document.createElement("div");
    container.className = "bitkode-chatbot-container hidden"; // hidden initially

    container.innerHTML = `
        <div class="bitkode-chatbot-header">
            <span>💬 Marketing Assistant</span>
            <div class="bitkode-chatbot-toggle" aria-label="Close chat" role="button" tabindex="0">×</div>
        </div>
        <div class="bitkode-chatbot-body"></div>
        <div class="bitkode-chatbot-footer">
            <input type="text" placeholder="Type your message…" />
            <button>Send</button>
        </div>
    `;

    root.appendChild(container);

    const body = container.querySelector(".bitkode-chatbot-body");
    const input = container.querySelector("input");
    const sendButton = container.querySelector("button");
    const toggleButton = container.querySelector(".bitkode-chatbot-toggle");

    function appendMessage(msg, sender) {
        const div = document.createElement("div");
        div.className = "bitkode-chatbot-msg " + sender;
        div.innerText = msg;
        body.appendChild(div);
        body.scrollTop = body.scrollHeight;
        chatHistory.push({ sender, msg });
    }

    function saveHistory() {
        localStorage.setItem("bitkode-chatbot-history", JSON.stringify(chatHistory));
    }

    function loadHistory() {
        const saved = JSON.parse(localStorage.getItem("bitkode-chatbot-history") || "[]");
        saved.forEach(({ sender, msg }) => appendMessage(msg, sender));
    }

    sendButton.addEventListener("click", () => {
        const msg = input.value.trim();
        if (!msg) return;
        appendMessage(msg, "user");
        input.value = "";

        fetch("https://ai.bitkode.com/webhook/f5a6815d-075e-431f-bd74-d6b3960b6d98", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                sessionid: sessionId,
                message: msg
            })
        })
        .then(res => res.json())
        .then(data => {
            const reply = data.output || data.response || "Sorry, I didn't understand that.";
            appendMessage(reply, "bot");
            saveHistory();
        })
        .catch(err => {
            console.error("Fetch error:", err);
            appendMessage("⚠️ Error: Could not reach the server.", "bot");
        });
    });

    input.addEventListener("keypress", e => {
        if (e.key === "Enter") sendButton.click();
    });

    iconButton.addEventListener("click", () => {
        container.classList.remove("hidden");
        iconButton.style.display = "none";
    });
    iconButton.addEventListener("keydown", (e) => {
        if (e.key === "Enter" || e.key === " ") {
            e.preventDefault();
            iconButton.click();
        }
    });

    toggleButton.addEventListener("click", () => {
        container.classList.add("hidden");
        iconButton.style.display = "flex";
    });
    toggleButton.addEventListener("keydown", (e) => {
        if (e.key === "Enter" || e.key === " ") {
            e.preventDefault();
            toggleButton.click();
        }
    });

    loadHistory();
})();
