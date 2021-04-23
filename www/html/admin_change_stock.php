<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';

//ユーザログインを確認
session_start();

//ログインできていなかったらリダイレクト
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

//データベースに接続
$db = get_db_connect();
//ユーザ情報取得
$user = get_login_user($db);

//ユーザのタイプが１出なければリダイレクト
if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}

//get_postはPOSTされた情報を取得
$item_id = get_post('item_id');
$stock = get_post('stock');

//在庫の変更
if(update_item_stock($db, $item_id, $stock)){
  set_message('在庫数を変更しました。');
} else {
  set_error('在庫数の変更に失敗しました。');
}

redirect_to(ADMIN_URL);