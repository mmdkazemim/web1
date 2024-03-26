<?php

// تعیین توکن ربات تلگرام
define('BOT_TOKEN', '6639695980:AAENJGt0p34JaxO0Rg8BGSLbiQ-BoHbuns4');

// تعیین آدرس API تلگرام
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');

// دریافت اطلاعات درخواست ارسال شده
$update = json_decode(file_get_contents('php://input'), TRUE);

// اگر اطلاعات درخواست خالی باشد، خروجی دهید
if(empty($update)) {
  exit;
}

// پیام ارسالی توسط کاربر
$message = $update['message'];

// شناسه چت
$chat_id = $message['chat']['id'];

// متن پیام ارسالی توسط کاربر
$text = $message['text'];

// آیدی کاربر
$user_id = $message['from']['id'];

// نام کاربر
$user_first_name = $message['from']['first_name'];

// تنظیم پیام جوابی
$response = "سلام $user_first_name! پیام شما را دریافت کردم: $text";

// تنظیم اطلاعات برای ارسال پیام به تلگرام
$data = array(
  'chat_id' => $chat_id,
  'text' => $response
);

// ارسال پیام به تلگرام
file_get_contents(API_URL.'sendMessage?'.http_build_query($data));

// اطلاعات کاربر برای ارسال
$user_data = "آیدی کاربر: $user_id\nنام کاربر: $user_first_name";

// تنظیم اطلاعات برای ارسال اطلاعات کاربر به تلگرام
$user_info = array(
  'chat_id' => $chat_id,
  'text' => $user_data
);

// ارسال اطلاعات کاربر به تلگرام
file_get_contents(API_URL.'sendMessage?'.http_build_query($user_info));

// تابع برای دریافت عکس پروفایل
function getProfilePhoto($user_id, $chat_id) {
    $profile_photos = json_decode(file_get_contents(API_URL.'getUserProfilePhotos?user_id='.$user_id), TRUE);
    $photos = $profile_photos['result']['photos'];
    
    // چک کردن برای وجود عکس پروفایل
    if (!empty($photos)) {
      // آدرس عکس پروفایل
      $photo_url = API_URL.'file/bot'.BOT_TOKEN.'/'.$photos[0][0]['file_id'];
      
      // تنظیمات برای ارسال عکس به تلگرام
      $photo_data = array(
        'chat_id' => $chat_id,
        'photo' => $photo_url
      );
      
      // ارسال عکس به تلگرام
      file_get_contents(API_URL.'sendPhoto?'.http_build_query($photo_data));
    } else {
      // اگر عکس پروفایلی وجود نداشت، پیام مناسب ارسال شود
      $no_photo_message = "عکس پروفایلی برای شما یافت نشد.";
      
      // تنظیمات برای ارسال پیام به تلگرام
      $no_photo_data = array(
        'chat_id' => $chat_id,
        'text' => $no_photo_message
      );
      
      // ارسال پیام به تلگرام
      file_get_contents(API_URL.'sendMessage?'.http_build_query($no_photo_data));
    }
  }
  
  // فراخوانی تابع برای دریافت عکس پروفایل
  getProfilePhoto($user_id, $chat_id);
  
