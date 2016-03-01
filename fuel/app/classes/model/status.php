<?php

class Model_Status extends \Model {

  private $from;

  public function __construct()
  {
    $this->from = 'task_status';
  }

  public function select()
  {
    $query = DB::select('*')
      ->from($this->from)
      ->execute();
    return $query;
  }
}
