<!-- =============================================
     Chat Bubble Widget
     ============================================= -->

<!-- Nút mở chat -->
<button id="chat-bubble-btn" aria-label="Mở hộp thoại chat">
    <i class="fas fa-comment-dots chat-icon"></i>
    <i class="fas fa-times close-icon"></i>
</button>

<!-- Cửa sổ chat -->
<div id="chat-window" role="dialog" aria-label="Chat với trợ lý RakiBookery" aria-hidden="true">

    <!-- Header -->
    <div id="chat-header">
        <div class="chat-avatar">
            <i class="fas fa-robot"></i>
        </div>
        <div class="chat-header-info">
            <div class="bot-name">Trợ lý RakiBookery</div>
            <div class="bot-status"><i class="fas fa-circle me-1" style="font-size:8px;color:#2ecc71;"></i>Đang hoạt động</div>
        </div>
        <button id="btn-clear-chat" class="btn-clear-chat" title="Xoá lịch sử chat">
            <i class="fas fa-trash-alt"></i>
        </button>
    </div>

    <!-- Tin nhắn -->
    <div id="chat-messages" role="log" aria-live="polite"></div>

    <!-- Gợi ý nhanh -->
    <div id="chat-suggestions">
        <button class="suggestion-chip" data-msg="RakiBookery có những thể loại sách nào?">📚 Thể loại sách</button>
        <button class="suggestion-chip" data-msg="Gợi ý cho tôi một cuốn sách hay nên đọc.">✨ Gợi ý sách hay</button>
        <button class="suggestion-chip" data-msg="Làm sao để đặt hàng trên RakiBookery?">🛒 Cách đặt hàng</button>
        <button class="suggestion-chip" data-msg="Chính sách đổi trả của RakiBookery là gì?">🔄 Đổi trả hàng</button>
    </div>

    <!-- Footer / input -->
    <div id="chat-footer">
        <textarea
            id="chat-input"
            rows="1"
            placeholder="Nhập câu hỏi của bạn…"
            aria-label="Nhập tin nhắn"
            maxlength="2000"
        ></textarea>
        <button id="chat-send-btn" aria-label="Gửi tin nhắn">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>

</div>
