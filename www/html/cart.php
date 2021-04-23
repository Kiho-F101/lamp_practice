<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';

//ログインを確認
session_start();
//ログインできていなければリダイレクト
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}
//DB接続
$db = get_db_connect();
//ユーザ情報獲得
$user = get_login_user($db);

//ユーザ情報とカート情報を結合されたテーブルで取得
$carts = get_user_carts($db, $user['user_id']);
//カートに入っている商品の数×数量を計算
$total_price = sum_carts($carts);

//htmlを表示
include_once VIEW_PATH . 'cart_view.php';