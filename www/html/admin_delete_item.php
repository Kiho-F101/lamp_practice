<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';

//ログイン確認のためセッションスタート
session_start();

//ログインされていなかったらリダイレクト
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

//トークンをチェック
if(is_valid_csrf_token($tokem)===FALSE){
  set_error('不正なアクセスです');
  redirect_to(ADMIN_URL);
}
//DB接続
$db = get_db_connect();
//ログインしているユーザ情報を取得
$user = get_login_user($db);

//userのタイプが１出なければリダイレクト
if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}

//item_idを取得
$item_id = get_post('item_id');

//destroy_itemは登録されている商品の削除
if(destroy_item($db, $item_id) === true){
  set_message('商品を削除しました。');
} else {
  set_error('商品削除に失敗しました。');
}



redirect_to(ADMIN_URL);