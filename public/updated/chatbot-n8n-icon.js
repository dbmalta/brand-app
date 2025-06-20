
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
    root.appendChild(iconButton);

    const container = document.createElement("div");
    container.className = "bitkode-chatbot-container hidden"; // hidden initially

    container.innerHTML = `
        <div class="bitkode-chatbot-header">
            <span>💬 Marketing Assistant</span>
            <div class="bitkode-chatbot-actions">
                <span class="bitkode-chatbot-copy" title="Copy conversation">📋</span>
                <span class="bitkode-chatbot-zoom-in" title="Increase size">＋</span>
                <span class="bitkode-chatbot-zoom-out" title="Decrease size">－</span>
                <span class="bitkode-chatbot-toggle">×</span>
            </div>
        </div>
        <div class="bitkode-chatbot-body"></div>
        <div class="bitkode-chatbot-footer">
            <input type="text" placeholder="Type your message…" />
            <button class="bitkode-chatbot-send">Send</button>
        </div>
    `;

    root.appendChild(container);

    const body = container.querySelector(".bitkode-chatbot-body");
    const input = container.querySelector("input");
    const sendButton = container.querySelector(".bitkode-chatbot-send");
    const toggleButton = container.querySelector(".bitkode-chatbot-toggle");
    const zoomInBtn = container.querySelector(".bitkode-chatbot-zoom-in");
    const zoomOutBtn = container.querySelector(".bitkode-chatbot-zoom-out");
    const copyBtn = container.querySelector(".bitkode-chatbot-copy");

    let scale = parseFloat(localStorage.getItem("bitkode-chatbot-scale")) || 1;

    function applyScale() {
        container.style.transformOrigin = "bottom right";
        container.style.transform = `scale(${scale})`;
        localStorage.setItem("bitkode-chatbot-scale", scale);
    }

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

    toggleButton.addEventListener("click", () => {
        container.classList.add("hidden");
        iconButton.style.display = "flex";
    });

    loadHistory();
})();
