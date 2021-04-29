<?php
//require_once関数→PHPでライブラリや他のPHPファイルを読み込み利用する。require_onceはファイルがすでに読み込まれていると再読み込みしない
//DEFINE集
require_once '../conf/const.php';
//基本的な関数集
require_once MODEL_PATH . 'functions.php';
//userに関する関数集
require_once MODEL_PATH . 'user.php';
//商品登録のための関数集
require_once MODEL_PATH . 'item.php';

//ユーザのログインを確認するためセッションスタート
session_start();

//ログインしてなかったら？どこに行くんだ？
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

//トークンをチェック
if(is_valid_csrf_token($token)===FALSE){
  set_error('不正なアクセスです');
  redirect_to(ADMIN_URL);
}

//get_db_connect関数はitem.phpとuser.phpの上でrequire_onceされているdb.phpのなかに定義されてる
//データベースに接続する関数
$db = get_db_connect();

//セッションに登録されているユーザの情報・・・？
$user = get_login_user($db);

//userのtypeが１ではなかったらLOGIN_URLへリダイレクト
if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}
//get_postはPOSTされたユーザ登録情報を取得する関数
$item_id = get_post('item_id');
//chages_toはステータスが変更されるように作られたボタン
$changes_to = get_post('changes_to');

//公開なら
if($changes_to === 'open'){
  update_item_status($db, $item_id, ITEM_STATUS_OPEN);
  set_message('ステータスを変更しました。');
}else if($changes_to === 'close'){
  update_item_status($db, $item_id, ITEM_STATUS_CLOSE);
  set_message('ステータスを変更しました。');
}else {
  set_error('不正なリクエストです。');
}


redirect_to(ADMIN_URL);