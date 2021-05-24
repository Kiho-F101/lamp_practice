<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';

session_start();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

//トークンを作成
$token = get_csrf_token();

$db = get_db_connect();
$user = get_login_user($db);

//デフォルトは新着順
$items = get_all_items_by_newest($db);
//$items = get_open_items($db);

//商品の表示順によって並び替える
$order = get_get('order');
if($order==='新着順'){
  $items = get_all_items_by_newest($db);
}else if($order==='価格の安い順'){
  $items = get_all_items_by_ascending_order($db);
}else if($order==='価格の高い順'){
  $items = get_all_items_by_descending_order($db);
}


//人気商品上位３つを取得
$popular_items = get_all_items_by_popularity($db);

include_once VIEW_PATH . 'index_view.php';