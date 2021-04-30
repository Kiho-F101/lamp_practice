<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
//カートに関する関数集
require_once MODEL_PATH . 'cart.php';

//ログイン確認
session_start();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

//トークンを取得
$token = get_post('token');

//トークンをチェック
if(is_valid_csrf_token($token)===FALSE){
  set_error('不正なアクセスです');
  redirect_to(CART_URL);
}

$db = get_db_connect();
$user = get_login_user($db);

$cart_id = get_post('cart_id');
$amount = get_post('amount');

//カートの数量をアップデートするSQL
if(update_cart_amount($db, $cart_id, $amount)){
  set_message('購入数を更新しました。');
} else {
  set_error('購入数の更新に失敗しました。');
}

//ここのCART＿URLはhtmlの方
redirect_to(CART_URL);