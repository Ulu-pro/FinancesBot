<?php
class DB {
  private $db;

  public function __construct() {
    $this->db = new mysqli(Env::$DB_HOST, Env::$DB_USER, Env::$DB_PASS, Env::$DB_NAME);
  }

  public function getAll() {
    $query = $this->db->query("SELECT * FROM `worksheets`");
    $array = [];
    while ($row = $query->fetch_assoc()) {
      $array[] = $row;
    }
    return $array;
  }

  public function getRows($row) {
    $array = [];
    foreach ($this->getAll() as $value) {
      $array[] = $value[$row];
    }
    return $array;
  }

  public function addUser($chat_id, $worksheet_id) {
    $query = $this->db->query("INSERT INTO `users` (`chat_id`, `worksheet_id`) VALUES ($chat_id, $worksheet_id)");
    return (bool)$query;
  }

  public function updateUser($chat_id, $worksheet_id) {
    $query = $this->db->query("UPDATE `users` SET `worksheet_id` = $worksheet_id WHERE `chat_id` = $chat_id");
    return (bool)$query;
  }

  public function searchUserChatID($chat_id) {
    $query = $this->db->query("SELECT `id` FROM `users` WHERE `chat_id` = $chat_id");
    return $query->num_rows > 0;
  }

  public function searchUserWorksheetID($chat_id) {
    $query = $this->db->query("SELECT `worksheet_id` FROM `users` WHERE `chat_id` = $chat_id");
    $worksheet_id = 0;
    while ($row = $query->fetch_assoc()) {
      $worksheet_id = $row['worksheet_id'];
    }
    return $worksheet_id;
  }

  public function getUrlByWorksheetID($worksheet_id) {
    $query = $this->db->query("SELECT `url` FROM worksheets WHERE `id` = $worksheet_id");
    return sprintf(Env::$DOCUMENT, $query->fetch_assoc()['url']);
  }

  public function getWorksheetIDByPassword($worksheet_password) {
    $query = $this->db->query("SELECT `id` FROM worksheets WHERE `password` = '$worksheet_password'");
    return $query->fetch_assoc()['id'];
  }

  public function getNameByWorksheetUrl($worksheet_url) {
    $query = $this->db->query("SELECT `name` FROM worksheets WHERE `url` = '$worksheet_url'");
    return $query->fetch_assoc()['name'];
  }

  public function getWorksheetUrlByUserChatID($chat_id) {
    $query = $this->db->query("SELECT worksheets.`url` FROM worksheets, users WHERE " .
       "users.`chat_id` = $chat_id AND users.`worksheet_id` = worksheets.`id`"
    );
    return $query->fetch_assoc()['url'];
  }
}