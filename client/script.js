const currentUser = document.getElementById('currentUser').dataset.user;
const ws = new WebSocket(`ws://localhost:8080?user=${encodeURIComponent(currentUser)}`);

ws.onmessage = function(event) {
    const msg = JSON.parse(event.data);
    if (msg.type === 'message') {
        addMessage(msg);
    }
};

function addMessage(msg) {
    const container = document.getElementById('messages');
    const div = document.createElement('div');
    div.className = `message ${msg.sender === currentUser ? 'own-message' : 'other-message'}`;
    div.innerHTML = `
        <div class="message-content">
            <strong>${msg.sender}</strong>
            <div>${msg.message}</div>
            <div class="message-time">${formatTimestamp(msg.timestamp)}</div>
        </div>
    `;
    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
}

function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    if (message === '') return;

    ws.send(JSON.stringify({
        type: 'message',
        message: message
    }));
    
    input.value = '';
}

document.getElementById('messageInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});

function formatTimestamp(timestamp) {
    const date = new Date(timestamp * 1000);
    
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    
    return `${day}-${month}-${year} ${hours}:${minutes}`;
}