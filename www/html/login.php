<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';

session_start();

if(is_logined() === true){
  redirect_to(HOME_URL);
}

//トークンを作成
$token = get_csrf_token();

//includeは外部ファイルを読み込む
include_once VIEW_PATH . 'login_view.php';