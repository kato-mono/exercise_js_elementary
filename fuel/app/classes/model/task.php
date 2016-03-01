<?php

class Model_Task extends Model_Todo {
  private $id;

  public function __construct($id, $user_id)
  {
    parent::__construct($user_id);
    $this->id = $id;
  }

  // $set_valueに入力される連想配列のキーはDB上のカラム名とする
  public function update_query($set_value)
  {
    $result = DB::update($this->from)
      ->set($set_value)
      ->where('id', $this->id)
      ->and_where('user_id', $this->user_id);
    return $result;
  }

  public function delete_query()
  {
    $result = DB::delete($this->from)
      ->where('id', $this->id)
      ->and_where('user_id', $this->user_id);
    return $result;
  }
}
