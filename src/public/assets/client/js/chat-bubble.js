/**
 * Chat Bubble Widget
 * Kết nối với AWS API Gateway → Lambda → Bedrock
 */

(function () {
    'use strict';

    const API_ENDPOINT = '/api/chat';

    // ─── State ────────────────────────────────────────────────────────────────
    let isOpen = false;
    let isTyping = false;
    const conversationHistory = []; // { role: 'user'|'assistant', content: string }

    // ─── DOM refs ─────────────────────────────────────────────────────────────
    const bubbleBtn    = document.getElementById('chat-bubble-btn');
    const chatWindow   = document.getElementById('chat-window');
    const messagesEl   = document.getElementById('chat-messages');
    const inputEl      = document.getElementById('chat-input');
    const sendBtn      = document.getElementById('chat-send-btn');
    const clearBtn     = document.getElementById('btn-clear-chat');
    const suggestionsEl = document.getElementById('chat-suggestions');

    if (!bubbleBtn || !chatWindow) return; // widget chưa mount

    // ─── Toggle mở / đóng ─────────────────────────────────────────────────────
    function toggleChat() {
        isOpen = !isOpen;
        bubbleBtn.classList.toggle('is-open', isOpen);
        chatWindow.classList.toggle('is-visible', isOpen);

        if (isOpen) {
            inputEl.focus();
            scrollToBottom();
        }
    }

    bubbleBtn.addEventListener('click', toggleChat);

    // Đóng khi click ra ngoài
    document.addEventListener('click', function (e) {
        if (isOpen && !chatWindow.contains(e.target) && !bubbleBtn.contains(e.target)) {
            toggleChat();
        }
    });

    // ─── Auto-resize textarea ─────────────────────────────────────────────────
    inputEl.addEventListener('input', function () {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 100) + 'px';
    });

    // Gửi khi nhấn Enter (Shift+Enter = xuống dòng)
    inputEl.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            handleSend();
        }
    });

    sendBtn.addEventListener('click', handleSend);

    // ─── Xoá lịch sử chat ─────────────────────────────────────────────────────
    clearBtn.addEventListener('click', function () {
        conversationHistory.length = 0;
        messagesEl.innerHTML = '';
        appendWelcome();
        showSuggestions();
    });

    // ─── Gợi ý nhanh ──────────────────────────────────────────────────────────
    function showSuggestions() {
        if (suggestionsEl) suggestionsEl.classList.remove('is-hidden');
    }

    function hideSuggestions() {
        if (suggestionsEl) suggestionsEl.classList.add('is-hidden');
    }

    if (suggestionsEl) {
        suggestionsEl.addEventListener('click', function (e) {
            const chip = e.target.closest('.suggestion-chip');
            if (!chip) return;
            const msg = chip.dataset.msg;
            if (msg) {
                inputEl.value = msg;
                hideSuggestions();
                handleSend();
            }
        });
    }

    // ─── Render helpers ───────────────────────────────────────────────────────
    function scrollToBottom() {
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function appendWelcome() {
        const el = document.createElement('div');
        el.className = 'chat-welcome';
        el.textContent = 'Xin chào! Tôi có thể giúp gì cho bạn? 📚';
        messagesEl.appendChild(el);
    }

    function appendMessage(role, text) {
        const wrapper = document.createElement('div');
        wrapper.className = 'chat-message ' + (role === 'user' ? 'user-msg' : 'bot-msg');

        const bubble = document.createElement('div');
        bubble.className = 'msg-bubble';
        bubble.innerHTML = formatText(text);

        if (role === 'assistant') {
            const avatar = document.createElement('div');
            avatar.className = 'msg-avatar';
            avatar.innerHTML = '<i class="fas fa-robot"></i>';
            wrapper.appendChild(avatar);
        }

        wrapper.appendChild(bubble);
        messagesEl.appendChild(wrapper);
        scrollToBottom();
        return bubble;
    }

    // Chuyển **bold**, *italic*, xuống dòng thành HTML cơ bản
    function formatText(text) {
        return text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.+?)\*/g, '<em>$1</em>')
            .replace(/\n/g, '<br>');
    }

    function showTyping() {
        const wrapper = document.createElement('div');
        wrapper.className = 'chat-message bot-msg';
        wrapper.id = 'typing-indicator-wrapper';

        const avatar = document.createElement('div');
        avatar.className = 'msg-avatar';
        avatar.innerHTML = '<i class="fas fa-robot"></i>';

        const bubble = document.createElement('div');
        bubble.className = 'msg-bubble';
        bubble.innerHTML = '<div class="typing-indicator"><span></span><span></span><span></span></div>';

        wrapper.appendChild(avatar);
        wrapper.appendChild(bubble);
        messagesEl.appendChild(wrapper);
        scrollToBottom();
    }

    function removeTyping() {
        const el = document.getElementById('typing-indicator-wrapper');
        if (el) el.remove();
    }

    function setInputDisabled(disabled) {
        inputEl.disabled = disabled;
        sendBtn.disabled = disabled;
    }

    // ─── Gửi tin nhắn ─────────────────────────────────────────────────────────
    async function handleSend() {
        const text = inputEl.value.trim();
        if (!text || isTyping) return;

        // Reset input
        inputEl.value = '';
        inputEl.style.height = 'auto';

        // Ẩn gợi ý khi bắt đầu chat
        hideSuggestions();

        // Hiển thị tin nhắn người dùng
        appendMessage('user', text);
        conversationHistory.push({ role: 'user', content: text });

        // Hiện typing indicator
        isTyping = true;
        setInputDisabled(true);
        showTyping();

        try {
            const reply = await callAPI(text);
            removeTyping();
            appendMessage('assistant', reply);
            conversationHistory.push({ role: 'assistant', content: reply });
        } catch (err) {
            removeTyping();
            appendMessage('assistant', '⚠️ ' + (err.message || 'Lỗi không xác định. Vui lòng thử lại sau.'));
            console.error('[ChatBubble] API error:', err);
        } finally {
            isTyping = false;
            setInputDisabled(false);
            inputEl.focus();
        }
    }

    // ─── Gọi API Gateway ──────────────────────────────────────────────────────
    async function callAPI(message) {
        const payload = {
            prompt: message,
            history: conversationHistory.slice(-10) // Gửi tối đa 10 lượt gần nhất
        };

        const response = await fetch(API_ENDPOINT, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (!response.ok) {
            const errMsg = data?.error || data?.message || data?.reply
                || ('HTTP ' + response.status + ': ' + response.statusText);
            throw new Error(errMsg);
        }

        // Hỗ trợ nhiều dạng response khác nhau từ Lambda
        if (typeof data === 'string') return data;
        if (data.answer)  return data.answer;
        if (data.reply)   return data.reply;
        if (data.message) return data.message;
        if (data.body) {
            // Nếu Lambda wrap trong body string
            const inner = typeof data.body === 'string' ? JSON.parse(data.body) : data.body;
            return inner.answer || inner.reply || inner.message || inner.text || JSON.stringify(inner);
        }
        if (data.text)    return data.text;
        if (data.content) return data.content;

        return JSON.stringify(data);
    }

    // ─── Khởi tạo ─────────────────────────────────────────────────────────────
    appendWelcome();

})();
