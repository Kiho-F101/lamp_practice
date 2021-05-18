<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'db.php';
require_once MODEL_PATH . 'cart.php';

//セッションスタート
session_start();

//ログインされていなかったらレダイレクト
if(is_logined() === false){
    redirect_to(LOGIN_URL);
}

//トークンを取得（いらないかも・・・？）
$token = get_post('token');

//DB接続
$db = get_db_connect();

//ログイン情報を取得
$user = get_login_user($db);

//オーダーナンバーを取得
$order_number = get_post('order_number');

//セレクトで明細を取得（一般ユーザはチェックが必要。管理者は全てのデータを見るのでチェックの必要なし
$history = get_purchase_history($db, $order_number);
if($user['type']===USER_TYPE_NORMAL){
    //user_idをチェックして本人かどうか確かめる.他のユーザに侵入される可能性あり
    if($user['user_id']!==$history['user_id']){
        redirect_to(HISTORY_URL);
    }
}

$details = get_purchase_details($db, $order_number);

//VIEWを表示
include_once VIEW_PATH . 'detail_view.php';
?>