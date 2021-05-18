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

$items = get_open_items($db);

//人気商品上位３つを取得
$popular_items = get_all_items_by_popularity($db);

include_once VIEW_PATH . 'index_view.php';