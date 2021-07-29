<?php
  // http://x68000.q-e-d.net/~68user/cloud/tuto-aws-mini-twitter.html で使用する
  // サンプルアプリケーション。

  ini_set('display_errors', "On");
  date_default_timezone_set('Asia/Tokyo');
  
  // XSS 対策。& < > " ' などを実体参照化。
  function myesc($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
  }
  // 新規メッセージがあれば
  if ( isset($_REQUEST['new_message']) && $_REQUEST['new_message'] != '' ){
    // 先頭30バイトを取得し、改行コードを <br> に置換
    $new_message = mb_substr($_REQUEST['new_message'], 0, 30);
    $new_message = str_replace(["\r\n", "\r", "\n"], '<br>', $new_message);

    // 新規メッセージを、配列の先頭にセット
    $buf = date("Y/m/d H:i:s") . "," . $new_message . "\n";
    $lines = [];
    array_push($lines, $buf);

    // 既存メッセージ先頭 4 件を、配列に追加
    $fp = fopen("tweets.txt", "r");
    while (( $line = fgets($fp)) !== false ) {
      array_push($lines, $line);
      if ( count($lines) > 4 ){
        break;
      }
    }
    fclose($fp);

    // 配列の内容をファイルに出力
    $fp = fopen("tweets.txt", "w");
    foreach ( $lines as $line ){
      fwrite($fp, $line);
    }
    fclose($fp);
  }
?>
<html>
<body>
<form action="<?= $_SERVER["SCRIPT_NAME"]?>?<?= microtime() ?>" method="post">
  メッセージ: <textarea name="new_message" cols=30 rows=3></textarea>
  <input type="submit" value="投稿">
</form>
<?php
  $fp = fopen("tweets.txt", "r");
  while ($line = fgets($fp)) {
    $cols = explode(',', $line, 2);
    $date = $cols[0];
    $message = str_replace('<br>', "\n", $cols[1]);
?>
   <p>
     日時: <?= myesc($date) ?><br>
     メッセージ: <?= nl2br(myesc($message)) ?>
   </p>
<?php
  }
?>
</body>
</html>

