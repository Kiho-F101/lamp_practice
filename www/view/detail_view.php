<!DOCTYPE html>
<html lang="ja">
<head>
    <?php include VIEW_PATH . 'templates/head.php'; ?>
    <title>購入明細</title>
    <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'admin.css'); ?>">
</head>
<body>
  <?php 
  include VIEW_PATH . 'templates/header_logined.php'; 
  ?>

  <h1>購入明細</h1>

  <!-- 注文番号、購入日時、合計金額を表示 -->
  <!-- 質問する -->
  注文番号：<?php print $history['order_number'] ?><br>
  購入日時：<?php print $history['purchase_datetime'] ?><br>
  合計金額：<?php print $history['total']?><br>

  <table class="table table-bordered text-center">
    <thead class="thead-light">
        <tr>
            <th>商品名</th>
            <th>価格（購入当時）</th>
            <th>数量</th>
            <th>小計</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($details as $detail){ ?>
            <tr>
                <td><?php print($detail['name']); ?></td>
                <td><?php print($detail['price']); ?></td>
        
                <td><?php print($detail['amount']); ?></td>
       
                <td><?php print($detail['price']*$detail['amount']); ?></td>
            </tr>
        <?php } ?>
    </tbody>
  </table>
</body>