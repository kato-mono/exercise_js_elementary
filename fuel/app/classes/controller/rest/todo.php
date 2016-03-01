<?php

session_start();

class Controller_Rest_Todo extends Controller_Rest
{
    private $user;
    private $model_todo;

    public function before()
    {
        parent::before();

    // 利用者を識別する
    if (isset($_SESSION['user'])) {
        $this->user = $_SESSION['user'];
    } else {
        header('HTTP/1.1 401 Authorization Required');
        exit;
    }

        $this->model_todo = new Model_Todo($this->user);

        if (!isset($_SESSION['sort_setting'])) {
            $_SESSION['sort_setting'] = $this->model_todo
        ->make_initial_setting('sort_setting');
        }

        if (!isset($_SESSION['search_keyword'])) {
            $_SESSION['search_keyword'] = $this->model_todo
        ->make_initial_setting('search_keyword');
        }
    }

  /**
   * 指定された条件に合致するタスクをjson形式で返す.
   */
  public function get_list()
  {
      $parameter = Input::all();

      $_SESSION['sort_setting'] = $this->model_todo
      ->change_sort_order($parameter['sort_by'], $_SESSION['sort_setting']);

      $_SESSION['search_keyword'] = trim($parameter['search_keyword']);

      $downloader = new Model_Downloader_Json();

    // 書き出すデータを取得する
    $records = $this->model_todo
      ->search_task($_SESSION['search_keyword'], $_SESSION['sort_setting']);

      return $downloader->make_response($records);
  }

  /**
   * 新規タスクをDBに保存する.
   */
  public function post_task()
  {
      $insert_parameter = $this->recieve_correct_post_data();
      $insert_parameter += ['user_id' => $this->user];

      $result = $this->model_todo
      ->insert_task($insert_parameter);

      $result[0] = 'id:'.$result[0];
      $result[1] = $result[1].' inserted.';

      return new Response(json_encode($result));
  }

  /**
   * タスクの内容を更新する.
   */
  public function put_task()
  {
      $update_parameter = $this->recieve_correct_post_data();
      $id = $update_parameter['id'];
      unset($update_parameter['id']);

      $result = (new Model_Task($id, $this->user))
      ->update_query($update_parameter)
      ->execute();

      return new Response(json_encode($result.' updated.'));
  }

  /**
   * タスクを削除する.
   */
  public function delete_task()
  {
      $delete_parameter = Input::all();

      $result = (new Model_Task($delete_parameter['id'], $this->user))
      ->delete_query()
      ->execute();

      return new Response(json_encode($result.' deleted.'));
  }

  /**
   * 送られたデータを修正した状態で受け取る.
   */
  private function recieve_correct_post_data()
  {
      $post_data = Input::all();

      if (array_key_exists('deadline', $post_data)
      && !($this->validate_datetime($post_data['deadline']))) {
          $post_data['deadline'] = '0';  // 日付の書式が不正な場合の規定値
      }

      return $post_data;
  }

  /**
   * mysqlに可換な日付書式であるか判定する.
   */
  private function validate_datetime($datetime_str)
  {
      return
      $datetime_str === date(
        'Y-m-d',
        strtotime($datetime_str)
      )
      or
      $datetime_str === date(
        'Y-m-d H:i:s',
        strtotime($datetime_str)
      );
  }
}
