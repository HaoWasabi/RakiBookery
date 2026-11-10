files = [
    'd:/do_an/internship/RakiBookery/src/public/assets/client/css/style.css',
    'd:/do_an/internship/RakiBookery/src/public/assets/client/css/cart-offcanvas.css',
    'd:/do_an/internship/RakiBookery/src/public/assets/client/css/auth-modal.css',
    'd:/do_an/internship/RakiBookery/src/public/assets/client/css/chat-bubble.css',
]
for f in files:
    with open(f, 'r', encoding='utf-8') as fh:
        c = fh.read()
    red = c.count('#e74c3c') + c.count('#c0392b') + c.count('#E74C3C') + c.count('#C0392B')
    blue = c.count('#3d6fe8') + c.count('#2555c8')
    print(f.split('/')[-1], '| old_red_remains:', red, '| new_blue_count:', blue)
