import telebot
from flask import Flask, request

TOKEN = "6833384544:AAF8o9Yd9R0hd87WhJgaE20fym2PtW_4qnw"  # توکن ربات شما
YOUR_TELEGRAM_ID = "@kazzemii"  # آیدی تلگرام شما

bot = telebot.TeleBot(TOKEN)

# آدرس وب‌هوک بات
WEBHOOK_URL = "https://example.com/kazemii_bot"

# ساخت برنامه Flask
app = Flask(__name__)

# وب‌هوک برای دریافت پیام‌ها
@app.route("/kazemii_bot", methods=['POST'])
def webhook():
    update = telebot.types.Update.de_json(request.stream.read().decode('utf-8'))
    bot.process_new_updates([update])
    return "!", 200

# یک دیکشنری برای ذخیره پیام‌های ناشناس
anonymous_messages = {}

@bot.message_handler(func=lambda message: True)
def handle_message(message):
    chat_id = message.chat.id
    if chat_id not in anonymous_messages:
        anonymous_messages[chat_id] = []
    
    # ذخیره پیام در دیکشنری
    anonymous_messages[chat_id].append(message.text)
    
    # ارسال پیام به شما
    bot.send_message(YOUR_TELEGRAM_ID, f"کاربر با آیدی {message.from_user.id} و عکس پروفایل {message.from_user.profile_photos} یک پیام ارسال کرد.")
    bot.reply_to(message, "پیام شما ذخیره شد.")

# ارسال پیام‌های ناشناس به کاربر
@bot.message_handler(commands=['get_messages'])
def get_messages(message):
    chat_id = message.chat.id
    if chat_id in anonymous_messages:
        for msg in anonymous_messages[chat_id]:
            bot.send_message(chat_id, f"پیام ناشناس: {msg}")
    else:
        bot.reply_to(message, "پیام ناشناسی برای شما موجود نیست.")

# تنظیم وب‌هوک
bot.remove_webhook()
bot.set_webhook(url=WEBHOOK_URL)

if __name__ == "__main__":
    app.run(port=8443)
