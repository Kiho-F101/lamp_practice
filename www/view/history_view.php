<!DOCTYPE html>
<html lang="ja">
<head>
    <?php include VIEW_PATH . 'templates/head.php'; ?>
    <title>購入履歴</title>
    <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'admin.css'); ?>">
</head>
<body>
<?php 
  include VIEW_PATH . 'templates/header_logined.php'; 
  ?>

  <h1>購入履歴</h1>

  <?php if(count($histories) > 0){ ?>
  <table class="table table-bordered text-center">
    <thead class="thead-light">
        <tr>
            <th>注文番号</th>
            <th>購入日時</th>
            <th>合計金額</th>
            <th>購入明細</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($histories as $history){ ?>
            <tr>
                <td><?php print($history['order_number']); ?></td>
                <td><?php print($history['purchase_datetime']); ?></td>
        
                <td><?php print($history['total']); ?>円</td>
       
                <td>
                    <form method="post" action="detail.php">
                        <!-- トークンを送る -->
                        <input type="hidden" name="token" value="<?php print $token; ?>">
                        <input type="submit" value="購入明細">
                        <input type="hidden" name="order_number" value="<?php print($history['order_number']); ?>">
                    </form>
                </td>
            </tr>
        <?php } ?>
    </tbody>
  </table>
  <?php }else { ?>
    <p>まだ購入したことがありません。</p>
  <?php } ?>
</body>
</html>