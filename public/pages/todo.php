<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>TODO</title>
  <?php echo Asset::css('bootstrap.min.css'); ?>
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/3.0.0/css/bootstrap-datetimepicker.min.css" />
  <?php echo Asset::css('jquery.toast.min.css'); ?>
  <?php echo Asset::css('clean.min.css'); ?>
  <?php echo Asset::css('todo.min.css'); ?>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
  <?php echo Asset::js('bootstrap.min.js'); ?>
  <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.6.0/moment.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.6.0/lang/ja.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/3.0.0/js/bootstrap-datetimepicker.min.js"></script>
  <?php echo Asset::js('jquery.toast.min.js'); ?>
  <?php echo Asset::js('mustache.min.js'); ?>
  <?php echo Asset::js('jquery.columns.min.js'); ?>
  <?php echo Asset::js('todo.min.js'); ?>
</head>
<body>

  <!-- 新規タスク入力 -->
  <section id="input-section">
    <div class="linearlayout-horizontal">
      <div class="linearlayout-child">
        <div class="input-group">
        	<span class="input-group-addon">内容</span>
        	<input id="description" type="text" class="form-control" maxlength="100" placeholder="最大100文字">
        </div>

        <div class="input-group">
        	<span class="input-group-addon">期限</span>
        	<input id="deadline" type="text" class="form-control date-today date-time-picker" placeholder="YYYY-MM-DD HH:MM:SS">
        </div>
      </div>

      <button id="insert-button" type="button" class="btn btn-default linearlayout-child">
        <span class="glyphicon glyphicon-pencil"></span>
      </button>
    </div>
  </section>

  <!-- searchバー、TODOリスト表示 -->
  <section>
    <div id="columns"></div>
  </section>

</body>
</html>
