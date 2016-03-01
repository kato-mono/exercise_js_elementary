<?php

/**
 * Model_Todo class test
 *
 * @group App
 */
class Tests_Model_Todo extends TestCase {

  const USER_999 = 999; // テスト用ユーザのuser_id
  private $model_todo_999;

  public function __construct($name = NULL, array $data = array(), $dataName = '')
  {
    $this->model_todo_999 = new Model_Todo(self::USER_999);

    parent::__construct($name, $data, $dataName);
  }

  public function test_ソート順の初期設定値を取得できる()
  {
    $expect_settings = [
      'sort_by' => 'status_code',
      'status_code' => 'asc',
      'deadline' => 'asc'
    ];
    $output_settings = $this->model_todo_999->make_initial_setting('sort_setting');
    $this->assertEquals($output_settings, $expect_settings);
  }

  public function test_検索キーワードの初期設定値を取得できる()
  {
    $output_keyword = $this->model_todo_999->make_initial_setting('search_keyword');
    $this->assertEquals($output_keyword, '');
  }

  /**
   * @dataProvider data_provider_Todoテーブルからレコードを外部結合した状態で取得できる
   */
  public function test_Todoテーブルからレコードを外部結合した状態で取得できる($expect_records, $search_keyword, $sort_settings)
  {
    $output_records = $this->model_todo_999->search_task($search_keyword, $sort_settings)->as_array();
    foreach ($output_records as $id => $output_record_datas) {
      unset($output_record_datas['id']);
      $output_records[$id] = $output_record_datas;
    }
    $this->assertEquals($output_records, $expect_records);
  }

  public function data_provider_Todoテーブルからレコードを外部結合した状態で取得できる()
  {
    return [
      [
        '検索キーワードが設定されていない　かつ　ステータスがasc順でソートされている場合' => [
          [
            'status_code' => '0',
            'status_description' => '未完了',
            'description' => '999_02',
            'deadline' => '2016-02-02 02:02:02'
          ],
          [
            'status_code' => '10',
            'status_description' => '作業中',
            'description' => '999_01',
            'deadline' => '2016-01-01 01:01:01'
          ],
          [
            'status_code' => '20',
            'status_description' => '完了',
            'description' => '999_02b',
            'deadline' => '2016-02-10 10:10:10'
          ]
        ],
        '',
        [
          'sort_by' => 'status_code',
          'status_code' => 'asc',
          'deadline' => 'asc'
        ]
      ],

      [
        '検索キーワードが設定されていない　かつ　ステータスがdesc順でソートされている場合' => [
          [
            'status_code' => '20',
            'status_description' => '完了',
            'description' => '999_02b',
            'deadline' => '2016-02-10 10:10:10'
          ],
          [
            'status_code' => '10',
            'status_description' => '作業中',
            'description' => '999_01',
            'deadline' => '2016-01-01 01:01:01'
          ],
          [
            'status_code' => '0',
            'status_description' => '未完了',
            'description' => '999_02',
            'deadline' => '2016-02-02 02:02:02'
          ]
        ],
        '',
        [
          'sort_by' => 'status_code',
          'status_code' => 'desc',
          'deadline' => 'asc'
        ]
      ],

      [
        '検索キーワードが指定されている　かつ　ステータスがasc順でソートされている場合' => [
          [
            'status_code' => '0',
            'status_description' => '未完了',
            'description' => '999_02',
            'deadline' => '2016-02-02 02:02:02'
          ],
          [
            'status_code' => '20',
            'status_description' => '完了',
            'description' => '999_02b',
            'deadline' => '2016-02-10 10:10:10'
          ]
        ],
        '02',
        [
          'sort_by' => 'status_code',
          'status_code' => 'asc',
          'deadline' => 'asc'
        ]
      ],

      [
        '検索キーワードが指定されている　かつ　ステータスがdesc順でソートされている場合' => [
          [
            'status_code' => '20',
            'status_description' => '完了',
            'description' => '999_02b',
            'deadline' => '2016-02-10 10:10:10'
          ],
          [
            'status_code' => '0',
            'status_description' => '未完了',
            'description' => '999_02',
            'deadline' => '2016-02-02 02:02:02'
          ]
        ],
        '02',
        [
          'sort_by' => 'status_code',
          'status_code' => 'desc',
          'deadline' => 'asc'
        ]
      ]
    ];
  }

  /**
   * @dataProvider data_provider_指定したソート順に設定値が書きかわる
   */
  public function test_指定したソート順に設定値が書きかわる($expect_settings, $sort_by, $sort_settings)
  {
    $output_settings = $this->model_todo_999
      ->change_sort_order(
        $sort_by,
        $sort_settings
      );

    $this->assertEquals($output_settings, $expect_settings);
  }

  public function data_provider_指定したソート順に設定値が書きかわる()
  {
    return [
      'ソート順が指定されていない場合' => [
        [
          'sort_by' => 'none',
          'status_code' => 'asc',
          'deadline' => 'asc'
        ],
        '',
        [
          'sort_by' => 'none',
          'status_code' => 'asc',
          'deadline' => 'asc'
        ]
      ],

      'ソート順が指定されている　かつ　ソート対象がascである場合' => [
        [
          'sort_by' => 'status_code',
          'status_code' => 'desc',
          'deadline' => 'asc'
        ],
        'status_code',
        [
          'sort_by' => 'none',
          'status_code' => 'asc',
          'deadline' => 'asc'
        ]
      ],

      'ソート順が指定されている　かつ　ソート対象がdescである場合' => [
        [
          'sort_by' => 'status_code',
          'status_code' => 'asc',
          'deadline' => 'asc'
        ],
        'status_code',
        [
          'sort_by' => 'none',
          'status_code' => 'desc',
          'deadline' => 'asc'
        ]
      ]
    ];
  }

  /**
   * テストデータを生成する
   */
  protected function setUp()
  {
    $insert_records = [
      [
        'user_id' => self::USER_999,
        'status_code' => 10,
        'description' => '999_01',
        'deadline' => '2016-01-01 01:01:01'
      ],
      [
        'user_id' => self::USER_999,
        'status_code' => 0,
        'description' => '999_02',
        'deadline' => '2016-02-02 02:02:02'
      ],
      [
        'user_id' => self::USER_999,
        'status_code' => 20,
        'description' => '999_02b',
        'deadline' => '2016-02-10 10:10:10'
      ]
    ];
    foreach ($insert_records as $insert_record_datas) {
      DB::insert('ensyu.todo')
        ->set($insert_record_datas)
        ->execute();
    }
  }

  /**
   * テストデータを削除する
   */
  protected function tearDown()
  {
    DB::delete('ensyu.todo')
      ->where('user_id', self::USER_999)
      ->execute();
  }
}
