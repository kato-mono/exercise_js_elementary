<?php

class Model_Todo extends \Model {

  protected $from;  // テーブル名を保持する
  protected $user_id;

  public function __construct($user_id)
  {
    $this->from = 'ensyu.todo';
    $this->user_id = $user_id;
  }

  public function make_initial_setting($setting_name)
  {
    $initial_settings = [];

    $initial_settings['sort_setting'] = [
      'sort_by' => 'status_code',
      'status_code' => 'asc',
      'deadline' => 'asc'
    ];

    $initial_settings['search_keyword'] = '';

    return $initial_settings[$setting_name];
  }

  public function change_sort_order($sort_by, $sort_setting)
  {
    if(empty(trim($sort_by)))
    {
      return $sort_setting;
    }

    $sort_setting['sort_by'] = $sort_by;

    if ($sort_setting[$sort_by] === 'asc')
    {
      $sort_setting[$sort_by] = 'desc';
    }
    else
    {
      $sort_setting[$sort_by] = 'asc';
    }

    return $sort_setting;
  }

  public function search_task($search_value, $sort_setting)
  {
    $query = $this->select_query();

    $query->where('ensyu.todo.description', 'like', '%'.$search_value.'%');

    $sort_by = $sort_setting['sort_by'];
    $query->order_by($sort_by, $sort_setting[$sort_by]);

    return $query->execute();
  }

  public function insert_task($insert_value)
  {
    $result = DB::insert($this->from)
      ->set($insert_value)
      ->execute();

    return $result;
  }

  private function select_query()
  {
    $query = DB::select(
        'ensyu.todo.id',
        [
          'ensyu.task_status.description',
          'status_description'
        ],
        'ensyu.todo.status_code',
        'ensyu.todo.description',
        'ensyu.todo.deadline'
      )
      ->from($this->from)
      ->join('ensyu.task_status')
      ->on(
        'ensyu.todo.status_code',
        '=',
        'ensyu.task_status.status_code')
      ->where('user_id', $this->user_id);
    return $query;
  }
}
