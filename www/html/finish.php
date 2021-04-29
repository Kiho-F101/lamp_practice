<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';

session_start();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

//トークンをチェック
if(is_valid_csrf_token($token)===FALSE){
  set_error('不正なアクセスです');
  redirect_to(CART_URL);
}

$db = get_db_connect();
$user = get_login_user($db);

$carts = get_user_carts($db, $user['user_id']);

//DBから在庫や、ユーザ情報からカート情報を削除するような関数なんだろうけど・・・読解不足
if(purchase_carts($db, $carts) === false){
  set_error('商品が購入できませんでした。');
  redirect_to(CART_URL);
} 

//カートに入っている商品の値段×数量を商品の種類分足していく関数
$total_price = sum_carts($carts);

include_once '../view/finish_view.php';