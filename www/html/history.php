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

//トークンを取得
$token = get_post('token');

//トークンをチェック
//if(is_valid_csrf_token($token)===FALSE){
    //set_error('不正なアクセスです');
    //redirect_to(ADMIN_URL);
//}

//DB接続
$db = get_db_connect();

//ログインしてるユーザ情報を取得
$user = get_login_user($db);

//管理者か一般ユーザかで条件分岐　購入履歴一覧を取得
//一般ユーザだったら
if($user['type']===USER_TYPE_NORMAL){
    $histories = get_purchase_histories($db, $user['user_id']);
    //新着順にする
    $histories = array_reverse($histories);
}else if($user['type']===USER_TYPE_ADMIN){
    //管理者ユーザだったら
    $histories = get_purchase_all_histories($db);
}



//明細一覧を取得
//$details = get_purchase_details($db, $histories['order_number']);

include_once VIEW_PATH . 'history_view.php';
?>