<?php
  // http://x68000.q-e-d.net/~68user/cloud/tuto-aws-mini-twitter-rds.html で使用する
  // サンプルアプリケーション。

  ini_set('display_errors', "On");

  $db_host = "minitwitterrds.c9oxz8hpltsg.us-east-2.rds.amazonaws.com";
  $db_name = "minitwitterdb";
  $db_user = "minitwitteruser";
  $db_pass = "minitwitterpass";

  $dbh = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
  // クライアント側のプリペアードステートメントエミュレーション機能を無効化
  $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  // DB 関連エラーは例外を発生させる
  $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
  // タイムゾーンを JST に
  $dbh->query("SET SESSION time_zone='Asia/Tokyo'");
  // トランザクション開始
  $dbh->beginTransaction();

  // XSS 対策。& < > " ' などを実体参照化。
  function myesc($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
  }
  // 新規メッセージがあれば
  if ( isset($_REQUEST['new_message']) && $_REQUEST['new_message'] != '' ){
    // 先頭30バイトを取得
    $new_message = mb_substr($_REQUEST['new_message'], 0, 30);

    // レコード INSERT。
    $sql = "INSERT INTO tweet (account_id, message) VALUES (:account_id, :message)";
    $stmt = $dbh->prepare($sql);
    // account_id は当面 1 固定
    $account_id = 1;
    $stmt->bindParam(':account_id', $account_id, PDO::PARAM_INT);
    $stmt->bindParam(':message', $new_message, PDO::PARAM_STR);
    $res = $stmt->execute();
    $dbh->commit();
  }
?>
<html>
<body>
<p>SERVER_ADDR: <?= myesc(getenv('SERVER_ADDR')) ?></p>
<form action="<?= myesc($_SERVER["SCRIPT_NAME"]) ?>?<?= microtime() ?>" method="post">
  メッセージ: <textarea name="new_message" cols=30 rows=3></textarea>
  <input type="submit" value="投稿">
</form>
<?php
  $stmt = $dbh->prepare ('select * from tweet ORDER BY create_timestamp DESC LIMIT 5');
  $stmt->execute();
  foreach ( $stmt->fetchAll () as $row ) {
?>
   <p>
     日時: <?= myesc($row['create_timestamp']) ?><br>
     メッセージ: <?= nl2br(myesc($row['message'])) ?>
   </p>
<?php
  }
?>
</body>
</html>
