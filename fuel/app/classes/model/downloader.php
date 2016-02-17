<?php

abstract class Model_Downloader extends \Model {

  protected $headers;

  public function __construct($headers)
  {
    $this->headers = $headers;
  }

  public function make_response($records)
  {
    $formated_data = $this->make_data($records);

    return new Response($formated_data, 200, $this->headers);
  }

  /**
   * 各フォーマット独自のデータ生成処理
   */
  abstract public function make_data($records);

}
