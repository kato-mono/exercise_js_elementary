(function() {
  const DATE_FORMAT = 'YYYY-MM-DD HH:mm:00';
  const INPUT_DATE_ALLERT_MESSAGE = '不正な日付です.<br>再入力してください';
  const USER_AUTHENTICATION_ALLERT_MESSAGE = 'ユーザ情報が読み取れません.';
  const INSERT_SUCCESS_MESSAGE = '作成成功しました！';
  const UPDATE_STATUS_SUCCESS_MESSAGE = '状態が更新されました！';
  const UPDATE_DESCRIPTION_SUCCESS_MESSAGE = '内容が更新されました！';
  const UPDATE_DEADLINE_SUCCESS_MESSAGE = '期限が更新されました！';
  const DELETE_SUCCESS_MESSAGE = '削除成功しました！';

  // TODO要素の変更前の値を一時的に保持する
  var focusedValue;

  // apiから取得したTODO一覧データを保持する
  var todoJson;

  function bindDateTimePicker() {
    // カレンダーでの日時選択機能を付与する
    $('.date-time-picker').datetimepicker({
      format: DATE_FORMAT,
      inline: true,
      sideBySide: true
    });
  }

  function deleteButtonHandler() {
    var id = $(this).attr('name');
    sendChange('DELETE', {id: id}, DELETE_SUCCESS_MESSAGE);
  }

  function displayColumns() {
    var columnsSetting = {
      data: todoJson,
      schema: [
        {'header': 'ID', 'key': 'id', 'hide': true},
        {'header': '状態', 'key': 'status_description', 'template': makeSelectHtmlString(fetchStatusList())},
        {'header': 'STATUS_CODE', 'key': 'status_code', 'hide': true},
        {'header': '内容', 'key': 'description', 'template': '<input type="text" class="table-row-element table-row-description" name="{{id}}" value="{{description}}" maxlength="100"></input>'},
        {'header': '期限', 'key': 'deadline', 'template': '<input type="text" class="table-row-element table-row-deadline date-time-picker" name="{{id}}" value="{{deadline}}"></input>'},
        {'header': '', 'key': '', 'template': '<button type="button" class="table-row-element table-row-delete-button" name="{{id}}"><span class="glyphicon glyphicon-trash"></span></button>'}
      ],
      paginating: false
    };

    // Columnsプラグインを使ってビューを生成する
    if ($('#columns').hasClass('columns')) {
      $('#columns').columns('setMaster', todoJson);
      $('#columns').columns('create');
    } else {
      $('#columns').columns(columnsSetting);
    }

    // Status選択部の値を初期化する
    initStatusOnTodoTable();

    // DeadLine選択部にカレンダー選択機能を付与する
    bindDateTimePicker();
  }

  function displayToastSuccess(displaySentence) {
    displayToast(displaySentence, 'success');
  }

  function displayToastWarning(displaySentence) {
    displayToast(displaySentence, 'warning');
  }

  function displayToast(displaySentence, iconStatus) {
    $.toast({
      text: displaySentence,
      heading: '',
      showHideTransition: 'slide',
      allowToastClose: true,
      hideAfter: 2000,
      stack: 5,
      position: 'bottom-right',
      icon: iconStatus
    });
  }

  function displayTodo() {
    $.ajax({
      url: '/rest/todo/list?sort_by=&search_keyword=',
      dataType: 'json',
      success: function(json) {
        todoJson = json;
        displayColumns();
      }
    });
  }

  /**
   * statusの表示内容を取得する
   */
  function fetchStatusList() {
    return {
      '0': '未完了',
      '10': '作業中',
      '20': '完了',
      '30': '保留'
    };
  }

  function focusValueHandler() {
    focusedValue = $(this).val();
  }

  function grayOutClosestTableRow() {
    var targetTableRow = $(this).closest('tr');
    targetTableRow.children().toggleClass('gray-out-table-background');
    targetTableRow.children().children().toggleClass('gray-out-child-opacity');
  }

  /**
   * TODOリスト上のStatus選択部のname属性(idを格納)とを選択状態を初期化する.
   * 事前条件：Columnsプラグインによってビューが生成されている.
   */
  function initStatusOnTodoTable() {
    $.each(todoJson, function(index, rowData) {
      var id = rowData.id;
      var description = $('.table-row-description[name=' + id + ']');
      var status = description.closest('tr').find('.table-row-status');
      status.attr('name', id);
      status.val(rowData.status_code);
    });
  }

  function insertButtonHandler() {
    var description = $('#description').val();
    var deadline = $('#deadline').val();
    if (!isValidDate(deadline)) {
      displayToastWarning(DATE_INPUT_ALLERT_MESSAGE);
    }
    sendChange('POST', {description: description, deadline: deadline}, INSERT_SUCCESS_MESSAGE);
  }

  function isValidDate(dateString) {
    var momentObj = moment(dateString, DATE_FORMAT);
    var isValid = momentObj.isValid();
    var date = momentObj.format(DATE_FORMAT);
    var notZeroDate = date != '0000-00-00 00:00:00';
    return isValid && notZeroDate;
  }

  /**
   * selectのhtml要素を構成する
   */
  function makeSelectHtmlString(optionValueArray) {
    var select = document.createElement('select');
    select.setAttribute('class', 'table-row-status form-control table-row-element');

    for (var optionValue in optionValueArray) {
      var option = document.createElement('option');

      option.setAttribute('value', optionValue);
      option.innerHTML = optionValueArray[optionValue];

      select.appendChild(option);
    }

    var root = document.createElement('div');
    root.appendChild(select);

    return root.innerHTML;
  }

  function makeToday() {
    return moment(new Date()).format(DATE_FORMAT);
  }

  /**
   * ビューで起こった変更を送信する
   */
  function sendChange(type, dataArray, successMessage) {
    $.ajax({
      type: type,
      data: dataArray,
      url: 'rest/todo/task',
      dataType: 'json',
      success: function(json) {
        todoJson = json;
        displayTodo();
        displayToastSuccess(successMessage);
      }
    });
  }

  function updateStatusHandler() {
    var test = $(this).children();
    var id = $(this).attr('name');
    var statusCode = $(this).val();
    sendChange('PUT', {id: id, status_code: statusCode}, UPDATE_STATUS_SUCCESS_MESSAGE);
  }

  function updateDescriptionHandler() {
    var id = $(this).attr('name');
    var description = $(this).val();
    if (description === focusedValue) {
      return;
    }

    sendChange('PUT', {id: id, description: description}, UPDATE_DESCRIPTION_SUCCESS_MESSAGE);
  }

  function updateDeadlineHandler() {
    var deadline = $(this).val();
    if (!isValidDate(deadline)) {
      displayToastWarning(INPUT_DATE_ALLERT_MESSAGE);
      $(this).val(focusedValue);
      return;
    }

    if (deadline === focusedValue) {
      return;
    }

    var id = $(this).attr('name');
    sendChange('PUT', {id: id, deadline: deadline}, UPDATE_DEADLINE_SUCCESS_MESSAGE);
  }

  $(function() {
    displayTodo();

    // 期限入力欄に現在日時を表示して、カレンダー選択機能を付与する
    $('.date-today').val(makeToday());
    bindDateTimePicker();

    $(document).on('click', '#insert-button', insertButtonHandler);
    $(document).on('click', '.table-row-delete-button', deleteButtonHandler);
    $(document).on('change', '.table-row-status', updateStatusHandler);
    $(document).on('focus', '.table-row-description', focusValueHandler);
    $(document).on('blur', '.table-row-description', updateDescriptionHandler);
    $(document).on('focus', '.table-row-deadline', focusValueHandler);
    $(document).on('blur', '.table-row-deadline', updateDeadlineHandler);

    // Columnsプラグインがビューを再生成する方式のため、再生性時にもイベントを付与する
    $(document).on('click', '.ui-table-sortable', bindDateTimePicker);
    $(document).on('click', '.ui-table-sortable', initStatusOnTodoTable);
    $(document).on('keyup', '.ui-table-search', bindDateTimePicker);
    $(document).on('keyup', '.ui-table-search', initStatusOnTodoTable);
  });

  $.ajaxSetup({
    statusCode: {
      401: function() {
        displayToastWarning(USER_AUTHENTICATION_ALLERT_MESSAGE);
      }
    }
  });

})();
