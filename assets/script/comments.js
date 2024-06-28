function sendMessage() {
    var messageInput = document.getElementById('comment-input');
    var message = messageInput.value.trim();

    if (message !== '') {
        displayMessage(message, 'self');
        messageInput.value = '';
        messageInput.focus();
    }
}

function displayMessage(message, type) {
    var chatMessages = document.getElementById('chat-messages');
    var messageDiv = document.createElement('div');
    messageDiv.classList.add('message', type === 'self' ? 'self' : 'other');
    messageDiv.innerHTML = '<p>' + message + '</p>';
    chatMessages.appendChild(messageDiv);

    // Fait défiler vers le bas pour afficher le dernier message
    chatMessages.scrollTop = chatMessages.scrollHeight;
}
