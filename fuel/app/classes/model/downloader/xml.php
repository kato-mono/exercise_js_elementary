<?php

class Model_Downloader_Xml extends Model_Downloader
{
  public function __construct()
  {
    $headers = [
      'Content-Type' => 'application/xml',
      'Content-Disposition' => 'attachment; filename="todo.xml"',
      'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate'
    ];

    parent::__construct($headers);
  }

  public function make_data($records)
  {
    $root = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8' ?><records></records>");
    foreach ($records as $record) {
      $row = $root->addChild('record');
      $row->addChild('id', $record['id']);
      $row->addChild('status_description', $record['status_description']);
      $row->addChild('status_code', $record['status_code']);
      $row->addChild('description', $record['description']);
      $row->addChild('deadline', $record['deadline']);
    }

    return $root->asXML();
  }
}
