replacements = [
    ('#e74c3c', '#3d6fe8'),
    ('#E74C3C', '#3d6fe8'),
    ('#c0392b', '#2555c8'),
    ('#C0392B', '#2555c8'),
    ('rgba(231, 76, 60,', 'rgba(61, 111, 232,'),
    ('rgba(231,76,60,', 'rgba(61,111,232,'),
    ('rgba(220, 53, 69,', 'rgba(61, 111, 232,'),
    ('rgba(220,53,69,', 'rgba(61,111,232,'),
    ('#dc3545', '#3d6fe8'),
    ('#DC3545', '#3d6fe8'),
    ('#fdf1f0', '#eef2fd'),
    ('#fff2f0', '#eef2fd'),
    ('#ffdbd5', '#d6e0fb'),
]

files = [
    'd:/do_an/internship/RakiBookery/src/public/assets/client/css/style.css',
    'd:/do_an/internship/RakiBookery/src/public/assets/client/css/cart-offcanvas.css',
    'd:/do_an/internship/RakiBookery/src/public/assets/client/css/auth-modal.css',
]

# chat-bubble: replace orange/red gradient with blue gradient
chat_replacements = [
    ('#e67e22', '#2980b9'),
    ('#c0392b', '#2555c8'),
    ('#e74c3c', '#3d6fe8'),
    ('rgba(231, 76, 60,', 'rgba(61, 111, 232,'),
    ('rgba(231,76,60,', 'rgba(61,111,232,'),
    ('#fff5ee', '#eef2fd'),
    ('#f5cba7', '#b3c8f5'),
]

for path in files:
    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()
    for old, new in replacements:
        content = content.replace(old, new)
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f'Done: {path}')

chat_path = 'd:/do_an/internship/RakiBookery/src/public/assets/client/css/chat-bubble.css'
with open(chat_path, 'r', encoding='utf-8') as f:
    content = f.read()
for old, new in chat_replacements:
    content = content.replace(old, new)
with open(chat_path, 'w', encoding='utf-8') as f:
    f.write(content)
print(f'Done: {chat_path}')
