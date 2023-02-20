<?php
require "data.php";
require "bot.php";
require "db.php";

$db = new DB();
$bot = new Bot(Env::$TOKEN);
$data = $bot->getData();

if (isset($data['message'])) {
  $message = $data['message'];
  $chat_id = $message['chat']['id'];
  $text = $message['text'];

  if ($text == '/start' || $text == Env::$START) {
    botStartUp($chat_id);
  }

  else if ($text == Env::$SUPPORT) {
    $bot->sendMessage($chat_id,
        "[" . Env::$COMPANY_MANAGER . "](" . Env::$COMPANY_LINK . ")" .
        "\n[" . Env::$TECHNICAL_MANAGER . "](" . Env::$TECHNICAL_LINK .")"
    );
  }

  else if (in_array($text, $db->getRows('name'))) {
    $bot->sendMessage($chat_id, Env::$PASSWORD);
  }

  else if (in_array($text, $db->getRows('password'))) {
    $worksheet_id = $db->getWorksheetIDByPassword($text);
    if ($db->searchUserChatID($chat_id)) {
      $db->updateUser($chat_id, $worksheet_id);
    } else {
      $db->addUser($chat_id, $worksheet_id);
    }
    statistics($chat_id);
  }

  else if (in_array($text, Env::$KEYBOARD)) {
    $worksheet_id = $db->searchUserWorksheetID($chat_id);
    $url = $db->getUrlByWorksheetID($worksheet_id);
    $content = "";
    switch (array_search($text, Env::$KEYBOARD)) {
      case 0:
        $content = Data::getLast($url);
        break;
      case 1:
        $content = Data::getTotal($url);
        break;
      case 2:
        $arg = $db->getWorksheetUrlByUserChatID($chat_id);
        $web_url = "https://" . $_SERVER['HTTP_HOST'] . "/stats.php?url=" . $arg;
        $bot->sendMessage($chat_id, Env::$DIAGRAM, json_encode([
            'inline_keyboard' => [
              [
                [
                  'text' => Env::$FORWARD,
                  'web_app' => ['url' => $web_url]
                ]
              ]
            ]
        ], true));
        break;
      case 3:
        chooseWorksheet($chat_id);
    }
    if ($content != "") {
      $bot->sendMessage($chat_id, $content);
    }
  }

  else {
    $bot->sendMessage($chat_id, Env::$PASSWORD_ERROR);
    botStartUp($chat_id);
  }
}

function botStartUp($chat_id) {
  global $db;
  $worksheet_id = $db->searchUserWorksheetID($chat_id);

  if ($worksheet_id != 0) {
    statistics($chat_id);
  } else {
    chooseWorksheet($chat_id);
  }
}

function statistics($chat_id) {
  global $bot;
  $bot->sendMessage($chat_id, Env::$STATS, json_encode([
      'resize_keyboard' => true,
      'keyboard' => [
          [
              ['text' => Env::$KEYBOARD[0]],
              ['text' => Env::$KEYBOARD[1]]
          ],
          [
              ['text' => Env::$KEYBOARD[2]],
              ['text' => Env::$KEYBOARD[3]]
          ],
          [
              ['text' => Env::$START],
              ['text' => Env::$SUPPORT]
          ]
      ]
  ], true));
}

function chooseWorksheet($chat_id) {
  global $bot, $db;

  $keyboard = [];
  foreach ($db->getRows('name') as $name) {
    $keyboard[] = [['text' => $name]];
  }

  $bot->sendMessage($chat_id, Env::$TABLE, json_encode([
      'resize_keyboard' => true,
      'one_time_keyboard' => true,
      'keyboard' => $keyboard
  ], true));
}