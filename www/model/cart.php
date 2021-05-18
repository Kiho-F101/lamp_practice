<?php 
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

function get_user_carts($db, $user_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
  ";
  return fetch_all_query($db, $sql, array($user_id));
}

function get_user_cart($db, $user_id, $item_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
    AND
      items.item_id = ?
  ";

  return fetch_query($db, $sql, array($user_id, $item_id));

}

function add_cart($db, $user_id, $item_id ) {
  $cart = get_user_cart($db, $user_id, $item_id);
  if($cart === false){
    return insert_cart($db, $user_id, $item_id);
  }
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

function insert_cart($db, $user_id, $item_id, $amount = 1){
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )
    VALUES(?, ?, ?)
  ";

  return execute_query($db, $sql, array($item_id, $user_id, $amount));
}

function update_cart_amount($db, $cart_id, $amount){
  $sql = "
    UPDATE
      carts
    SET
      amount = ?
    WHERE
      cart_id = ?
    LIMIT 1
  ";
  return execute_query($db, $sql, array($amount, $cart_id));
}

function delete_cart($db, $cart_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = ?
    LIMIT 1
  ";

  return execute_query($db, $sql, array($cart_id));
}

//課題激むずポイント
function purchase_carts($db, $carts){
  //購入できるかどうかの確認をしてる（在庫とか）
  if(validate_cart_purchase($carts) === false){
    return false;
  }
  //購入できそうならトランザクション開始
  $db->beginTransaction();

  //購入日時のための変数を定義
  $datetime = date('Y-m-d H:i:s');
  //$cart[0]['user_id']について。[0]というのは、その人のカートの中の最初の商品のこと。たとえ１つしか商品を買ってなくても、user_idは同じなので、このように描くのが良い。
  if(regist_purchase_histories($db, $carts[0]['user_id'], $datetime)===false){
    set_error('エラーです。');
  }else{
    //履歴に登録するのに支障なければ
    $order_number = $db->lastInsertId();
    //ここの$cartsはコントローラーで定義されてる
    foreach($carts as $cart){
      //在庫数を購入数分減らす(FALSEの場合)
      if(update_item_stock(
        $db, 
        $cart['item_id'], 
        $cart['stock'] - $cart['amount']
        ) === false){
          
          set_error($cart['name'] . 'の購入に失敗しました。');
      }else{
        //TRUEなら購入明細画面に追加（FALSEの場合）
        if(regist_purchase_details($db, $order_number, $cart['item_id'], $cart['amount'], $cart['price'])===false){
          set_error('エラーです。');
        }
      }
    }
  }
  if(has_error()===FALSE){
    //コミット
    $db->commit();
  }else{
    //ロールバック
    $db->rollBack();
    return false;
  }
  delete_user_carts($db, $carts[0]['user_id']);
}

function delete_user_carts($db, $user_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = ?
  ";

  execute_query($db, $sql, array($user_id));
}


function sum_carts($carts){
  $total_price = 0;
  foreach($carts as $cart){
    $total_price += $cart['price'] * $cart['amount'];
  }
  return $total_price;
}

function validate_cart_purchase($carts){
  if(count($carts) === 0){
    set_error('カートに商品が入っていません。');
    return false;
  }
  foreach($carts as $cart){
    if(is_open($cart) === false){
      set_error($cart['name'] . 'は現在購入できません。');
    }
    if($cart['stock'] - $cart['amount'] < 0){
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  if(has_error() === true){
    return false;
  }
  return true;
}

//購入履歴（インサート）
function regist_purchase_histories($db, $user_id, $datetime){
  $sql = "
  INSERT INTO
  purchase_histories(
  user_id,
  purchase_datetime
  )
  VALUES(?, ?);
  ";
  
  return execute_query($db, $sql, array($user_id, $datetime));
  }

  //購入明細（インサート）
  function regist_purchase_details($db, $order_number, $item_id, $amount, $price){
    $sql = "
    INSERT INTO
    purchase_details(
    order_number,
    item_id,
    amount,
    price
    )
    VALUES(?, ?, ?, ?);
    ";
    
    return execute_query($db, $sql, array($order_number, $item_id, $amount, $price));
    }

    //購入履歴（セレクト）（一般ユーザ用）
    function get_purchase_histories($db, $user_id){
      $sql = "
      SELECT
      purchase_histories.order_number, 
      purchase_histories.purchase_datetime,
      SUM(purchase_details.price*purchase_details.amount) AS total
      FROM
      purchase_histories
      JOIN
      purchase_details
      ON
      purchase_histories.order_number=purchase_details.order_number
      WHERE
      purchase_histories.user_id = ?
      GROUP BY
      purchase_histories.order_number
      ORDER BY
      purchase_histories.purchase_datetime DESC
      
      ";

      return fetch_all_query($db, $sql, array($user_id));
    }

    //購入履歴（セレクト）（管理者用）
    function get_purchase_all_histories($db){
      $sql = "
      SELECT
      purchase_histories.order_number, 
      purchase_histories.purchase_datetime,
      SUM(purchase_details.price*purchase_details.amount) AS total
      FROM
      purchase_histories
      JOIN
      purchase_details
      ON
      purchase_histories.order_number=purchase_details.order_number
      GROUP BY
      purchase_histories.order_number
      ORDER BY
      purchase_histories.purchase_datetime DESC
      ";

      return fetch_all_query($db, $sql);
    }

    //購入明細（セレクト）（一般ユーザ）
    function get_purchase_details($db, $order_number){
      $sql = "
      SELECT
      items.name,
      purchase_details.price,
      purchase_details.amount
      FROM
      purchase_details
      JOIN
      items
      ON
      purchase_details.item_id=items.item_id
      WHERE
      order_number = ?

      ";

      return fetch_all_query($db, $sql, array($order_number));
    }

    //購入明細（セレクト）（管理者）
    function get_purchase_all_details($db){
      $sql = "
      SELECT
      items.name,
      purchase_details.price,
      purchase_details.amount
      FROM
      purchase_details
      JOIN
      items
      ON
      purchase_details.item_id=items.item_id
      ";

      return fetch_all_query($db, $sql);
    }

    //購入履歴で使う合計金額???
    function sum_history($histories){
      $total_price = 0;
      foreach($histories as $history){
        $total_price += $history['price'] * $history['amount'];
      }
      return $total_price;
    }
    
    //明細画面上部の注文番号、注文日時、合計金額を表示する
    function get_purchase_history($db, $order_number){
      $sql = "
      SELECT
      purchase_histories.user_id,
      purchase_histories.order_number, 
      purchase_histories.purchase_datetime,
      SUM(purchase_details.price*purchase_details.amount) AS total
      FROM
      purchase_histories
      JOIN
      purchase_details
      ON
      purchase_histories.order_number=purchase_details.order_number
      WHERE
      purchase_histories.order_number = ?
      GROUP BY
      purchase_histories.order_number
      
      ";

      return fetch_query($db, $sql, array($order_number));
    }
