<?php

class Model_Downloader_Json extends Model_Downloader
{
  public function __construct()
  {
    $headers = [
      'Content-Type' => 'application/json',
      'Content-Disposition' => 'attachment; filename="todo.json"',
      'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate'
    ];

    parent::__construct($headers);
  }

  public function make_data($records)
  {
    $formated_data = null;

    foreach ($records as $record) {
      $formated_data[] = [
          'id' => $record['id'],
          'status_description' => $record['status_description'],
          'status_code' => $record['status_code'],
          'description' => $record['description'],
          'deadline' => $record['deadline']
        ];
    }
    return json_encode($formated_data);
  }
}
