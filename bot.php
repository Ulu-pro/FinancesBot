<?php
class Bot {
  private $url;
  private $data;

  public function __construct($token) {
    $this->url = "https://api.telegram.org/bot$token/";
    $this->data = $this->getData();
  }

  public function getData() {
    if (empty($this->data)) {
      $raw_data = file_get_contents('php://input');
      return json_decode($raw_data, true);
    } else {
      return $this->data;
    }
  }

  public function request($method, $params) {
    return file_get_contents($this->url . $method . '?' . http_build_query($params));
  }

  public function sendMessage($chat_id, $text, $reply_markup = []) {
    $_ = $this->request('sendMessage', [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'Markdown',
        'reply_markup' => $reply_markup
    ]);
    unset($_);
  }
}