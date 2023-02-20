<?php
require "simple_html_dom.php";
require "env.php";

class Data {
  public static function parseData($url) {
    $page = file_get_html($url);
    $array = [];

    foreach ($page->find('table.waffle')[0]->find('tr') as $row) {
      $list = [];
      foreach ($row->find('td') as $cell) {
        $list[] = $cell->plaintext;
      }
      $array[] = $list;
    }

    return $array;
  }

  public static function formatData($data) {
    $array = [];
    foreach ($data as $value) {
      $array[] = ltrim($value);
    }
    return $array;
  }

  public static function getLast($url) {
    $data = self::parseData($url);
    $data = array_reverse($data);

    foreach ($data as $row) {
      if (
          preg_match('~[0-9]+~', $row[7]) and
          @(strpos($row[0], '.') != false)
      ) {
        $data = $row;
        break;
      }
    }

    $data = self::formatData($data);
    return Env::$LAST_TITLE . sprintf(Env::$DATA_BODY,
      $data[0],
      $data[1],
      $data[2],
      $data[3],
      $data[4],
      $data[7]
    );
  }

  public static function getTotal($url) {
    $data = self::parseData($url);
    $data = array_reverse($data);

    foreach ($data as $row) {
      if (@(strpos($row[0], ':') != false)) {
        $data = $row;
        break;
      }
    }

    $data = self::formatData($data);
    return Env::$TOTAL_TITLE . sprintf(Env::$DATA_BODY,
      Env::$COMMON_DATES,
      $data[1],
      $data[2],
      $data[3],
      $data[4],
      $data[7]
    );
  }

  public static function getStats($url) {
    $data = self::parseData($url);
    $data = array_reverse($data);
    $content = [];

    foreach ($data as $row) {
      if (count($content) == 7) {
        break;
      } else if (
          @(strpos($row[0], ':') != false) or
          @(strpos($row[0], '.') == false) or
          @(strpos($row[7], '-') != false)
      ) {
        continue;
      }

      $content[] = [$row[0], $row[7]];
    }

    return array_reverse($content);
  }
}