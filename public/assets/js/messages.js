// public/assets/js/messages.js
// Minimal AJAX client for messaging
(function () {
    const convEl = document.getElementById('conversations');
    const messagesList = document.getElementById('messages-list');
    const sendBtn = document.getElementById('send-btn');
    const input = document.getElementById('message-input');

    let currentCid = messagesList ? parseInt(messagesList.dataset.cid) : null;
    let lastMessageId = 0;

    function scrollToBottom() {
        if (messagesList) messagesList.scrollTop = messagesList.scrollHeight;
    }

    function updateLastMessageId() {
        if (!messagesList) return;
        const items = messagesList.querySelectorAll('.msg[data-mid]');
        if (items.length) lastMessageId = parseInt(items[items.length - 1].dataset.mid);
    }

    updateLastMessageId();
    scrollToBottom();

    // click conversation
    if (convEl) {
        convEl.addEventListener('click', function (e) {
            const node = e.target.closest('.conv-item');
            if (!node) return;
            const cid = node.dataset.cid;
            if (!cid) return;
            window.location.href = 'index.php?controller=message&action=inbox&cid=' + cid;
        });
    }

    // send message via AJAX
    if (sendBtn) {
        sendBtn.addEventListener('click', sendMessage);
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }

    function sendMessage() {
        const content = input.value.trim();
        if (!content || !currentCid) return;
        sendBtn.disabled = true;
        fetch('index.php?controller=message&action=send', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ conversation_id: currentCid, content: content })
        }).then(r => r.json()).then(data => {
            sendBtn.disabled = false;
            if (data.success) {
                // append message(s)
                const msgs = data.message;
                if (Array.isArray(msgs)) {
                    msgs.forEach(appendMessage);
                } else if (msgs.length && msgs[0]) {
                    appendMessage(msgs[0]);
                }
                input.value = '';
                updateLastMessageId();
                scrollToBottom();
                // refresh conversation list
                fetchConversations();
            } else {
                ModalUI.alert(data.error || 'Failed to send', 'Error');
            }
        }).catch(err => {
            sendBtn.disabled = false;
            console.error(err);
        });
    }

    function appendMessage(m) {
        if (!messagesList) return;
        const out = (m.sender_id == CURRENT_USER_ID);
        const div = document.createElement('div');
        div.className = 'msg ' + (out ? 'out' : 'in');
        div.dataset.mid = m.id;
        div.innerHTML = '<div style="font-size:13px;">' + escapeHtml(m.content).replace(/\n/g, '<br/>') + '</div>' +
            '<div style="font-size:11px;color:var(--text-faint);margin-top:6px;">' + formatDate(m.created_at) + '</div>';
        messagesList.appendChild(div);
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function formatDate(dt) {
        const d = new Date(dt);
        return d.toLocaleString();
    }

    // polling for new messages
    function pollMessages() {
        if (!currentCid) return;
        fetch('index.php?controller=message&action=fetch&cid=' + currentCid + '&after=' + (lastMessageId || 0))
            .then(r => r.json())
            .then(data => {
                if (data.messages && data.messages.length) {
                    data.messages.forEach(appendMessage);
                    updateLastMessageId();
                    scrollToBottom();
                    fetchConversations();
                }
            }).catch(console.error);
    }

    function fetchConversations() {
        fetch('index.php?controller=message&action=conversations')
            .then(r => r.json())
            .then(data => {
                if (!data.conversations) return;
                // For simplicity, reload the left column HTML by replacing innerHTML
                let html = '<div class="header">Messages</div>';
                data.conversations.forEach(c => {
                    html += '<div class="conv-item" data-cid="' + c.conversation_id + '">' +
                        '<div class="avatar" style="width:40px;height:40px;border-radius:50%;background:#ddd;display:flex;align-items:center;justify-content:center;">' +
                        (c.other_user_name ? escapeHtml(c.other_user_name.charAt(0).toUpperCase()) : 'U') +
                        '</div>' +
                        '<div class="meta"><div style="display:flex;justify-content:space-between"><div class="name">' + escapeHtml(c.other_user_name) + '</div><div class="time" style="color:var(--text-faint);font-size:12px">' + (c.last_message_at ? new Date(c.last_message_at).toLocaleTimeString() : '') + '</div></div>' +
                        '<div style="color:var(--text-secondary);font-size:14px;margin-top:6px;">' + escapeHtml((c.last_message || '').slice(0, 50)) + '</div></div>' +
                        (c.unread_count > 0 ? '<div class="unread">' + c.unread_count + '</div>' : '') +
                        '</div>';
                });
                convEl.innerHTML = html;
            }).catch(console.error);
    }

    setInterval(pollMessages, 3000);
    setInterval(fetchConversations, 5000);
})();