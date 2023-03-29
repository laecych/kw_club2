<?php
require_once __DIR__ . '/header.php';

/*-----------執行動作判斷區----------*/
require_once $GLOBALS['xoops']->path('/modules/system/include/functions.php');
$op      = system_CleanVars($_REQUEST, 'op', '', 'string');
$keyman  = system_CleanVars($_REQUEST, 'keyman', '', 'string');
$reg_sn  = system_CleanVars($_REQUEST, 'reg_sn', '', 'int');
$uid     = system_CleanVars($_REQUEST, 'uid', '', 'int');
$id      = system_CleanVars($_POST, 'id', '', 'string');
$value   = system_CleanVars($_POST, 'value', '', 'string');
$reg_uid = system_CleanVars($_POST, 'reg_uid', '', 'string');
$file_id = system_CleanVars($_REQUEST, 'file_id', '', 'int');
$teacher_id = system_CleanVars($_POST, 'teacher_id', '', 'int');

switch ($op) {
    //更新教師簡介
    case 'search_reg_uid':
        die(search_reg_uid($reg_uid));

    //更新教師簡介
    case 'update_bio':
        die(update_bio($value, $uid));

    //更新註冊資訊
    case 'update_reg':
        die(update_reg($id, $value, $reg_sn));

    //篩選使用者
    case 'keyman':
        die(keyman($keyman));
    
    //教師資料上傳
    case 'upload':
        die(teacher_upload($teacher_id));
    
    //教師資料上傳
    case 'delete':
        die(teacher_delete($file_id));
}

//上傳教師資料刪除
/**
 * @param $file_id
 * @return mixed
 */
function teacher_delete($file_id){
    global $xoopsDB;
    if (!($_SESSION['is_kw_club_Admin']))
        return "{\"msg\":\""._MD_KWCLUB_FORBBIDEN."\"}";

    if(empty($file_id))
        return "{\"msg\":\"no file_id\"}";
    
    $sql = 'select `sub_dir` from ' . $xoopsDB->prefix('kw_club_files_center') . " where `files_sn`='{$file_id}' ";
    $result = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
    list($sub_dir) = $xoopsDB->fetchRow($result);
    $path = XOOPS_ROOT_PATH. $sub_dir;
    
    if (file_exists($path)) {
        unlink($path);
        $sql = 'delete from `' . $xoopsDB->prefix('kw_club_files_center') . "`
        where `files_sn` = '{$file_id}'";
        $xoopsDB->queryF($sql) or web_error($sql);
        return "{\"msg\":\"success\"}";
    }
    else{
        return "{\"msg\":\"error\"}";
    }   
}

//上傳教師資料包含影像、pdf檔案、影片(mp4 mpeg)
/**
 * @param $teacher_id
 * @return json(msg)
 */
function teacher_upload($teacher_id){
    global $xoopsDB, $xoopsUser;
    if (!($_SESSION['is_kw_club_Admin']))
        return "{\"msg\":\""._MD_KWCLUB_FORBBIDEN."\"}";
    if(empty($teacher_id))
        return "{\"msg\":\"no_id\"}";
    
    $uid = $xoopsUser->uid();
    $today = date("Y-m-d H:i:s");
    $upload_dir = "/uploads/kw_club/teacher/";
    
    $fileCount = count($_FILES['files']['name']);
    $myts = MyTextSanitizer::getInstance();
    
    for ($i = 0; $i < $fileCount; $i++) {
        # 檢查檔案是否上傳成功
        if ($_FILES['files']['error'][$i] === UPLOAD_ERR_OK){
            // echo '檔案名稱: ' . $_FILES['files']['name'][$i] . '<br/>';
            // echo '檔案類型: ' . $_FILES['files']['type'][$i] . '<br/>';
            // echo '檔案大小: ' . ($_FILES['files']['size'][$i] / 1024) . ' KB<br/>';
            // echo '暫存名稱: ' . $_FILES['files']['tmp_name'][$i] . '<br/>';
            $type =$myts->addSlashes($_FILES['files']['type'][$i]);
            $name = $myts->addSlashes($_FILES['files']['name'][$i]);
            $size = $_FILES['files']['size'][$i] ; 
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION)); 
            if(  $ext == "jpg" || $ext == "jepg" || $ext == "png" )
                $kind = 'img';
            else
                $kind='file';
            //rename files    
            $path = $upload_dir. strtotime($today)."_". $name ;
            $file = $_FILES['files']['tmp_name'][$i];
            $dest =  XOOPS_ROOT_PATH . $path;

            # 將檔案移至指定位置
            move_uploaded_file($file, $dest);

            $sql = "INSERT INTO `xx_kw_club_files_center` 
            (`col_name`, `col_sn`, `sort`, `kind`, `file_name`, `file_type`, `file_size`, `description`, `counter`, `original_filename`, `hash_filename`, `sub_dir`, `upload_date`, `uid`, `tag`)
            VALUES ('teacher_id', '{$teacher_id}', '{$i}', '{$kind}', '{$name}', '{$type}', '{$size}', '', '0', '', '', '{$path}', '{$today}', '$uid', 'teacher');";
             $xoopsDB->query($sql) or web_error($sql);
             $file_id = $xoopsDB->getInsertId();
           
            // # 檢查檔案是否已經存在
            // if (file_exists('upload/' . $_FILES['files']['name'][$i])){
            //     echo '檔案已存在。<br/>';
            // } else {
                // $file = $_FILES['my_file']['tmp_name'][$i];
                // $dest = 'upload/' . $_FILES['my_file']['name'][$i];

                // # 將檔案移至指定位置
                // move_uploaded_file($file, $dest);
            // }

        } else {
            return "{\"msg\": \"error " . $_FILES['files']['error'] . "\"}";
        }
    }//end for
    return "{\"msg\": \"success\"}";
}



//以身份證號自動取得姓名
/**
 * @param $reg_uid
 * @return mixed
 */
function search_reg_uid($reg_uid)
{
    global $xoopsDB;

    $myts = MyTextSanitizer::getInstance();

    $sql = 'select `reg_name` from ' . $xoopsDB->prefix('kw_club_reg') . " where `reg_uid`='{$reg_uid}' order by reg_datetime desc limit 0,1";
    $result = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
    list($reg_name) = $xoopsDB->fetchRow($result);
    $reg_name = $myts->htmlSpecialChars($reg_name);

    return $reg_name;
}

/**
 * @param $value
 * @param $uid
 * @return mixed
 */
function update_bio($value, $uid)
{
    global $xoopsDB;
    if (!$_SESSION['isclubAdmin']) {
        die(_MD_KWCLUB_FORBBIDEN);
    }

    $myts = MyTextSanitizer::getInstance();
    $val  = $myts->htmlSpecialChars($value);
    // $val = strip_tags($value);
    $sql = 'update ' . $xoopsDB->prefix('kw_club_teacher') . " set `teacher_desc`='{$val}' where `teacher_id`='{$uid}'";
    $xoopsDB->queryF($sql);

    return $value;
}

/**
 * @param $id
 * @param $value
 * @param $reg_sn
 * @return string|void
 */
function update_reg($id, $value, $reg_sn)
{
    global $xoopsDB;
    if (!$_SESSION['isclubAdmin']) {
        die(_MD_KWCLUB_FORBBIDEN);
    }
    if (false !== mb_strpos($id, 'reg_name')) {
        $col = 'reg_name';
    } elseif (false !== mb_strpos($id, 'reg_isreg')) {
        $col = 'reg_isreg';
    } elseif (false !== mb_strpos($id, 'reg_grade')) {
        $col = 'reg_grade';
    } elseif (false !== mb_strpos($id, 'reg_class')) {
        $col = 'reg_class';
    } elseif (false !== mb_strpos($id, 'reg_number')) {
        $col = 'reg_number';
    } elseif (false !== mb_strpos($id, 'reg_uid')) {
        $col = 'reg_uid';
    } elseif (false !== mb_strpos($id, 'reg_parent')) {
        $col = 'reg_parent';
    } elseif (false !== mb_strpos($id, 'reg_tel')) {
        $col = 'reg_tel';
    } else {
        return;
    }

    $myts = MyTextSanitizer::getInstance();
    $val  = $myts->htmlSpecialChars(strip_tags($value));
    $sql  = 'update ' . $xoopsDB->prefix('kw_club_reg') . " set `{$col}`='{$val}' where `reg_sn`='{$reg_sn}'";
    $xoopsDB->queryF($sql);

    if ('reg_grade' === $col) {
        if (_MD_KWCLUB_KG === $val) {
            $value = _MD_KWCLUB_KINDERGARTEN;
        } else {
            $value = $val . _MD_KWCLUB_G;
        }
    }

    return $value;
}

/**
 * @param $keyman
 * @return string
 */
function keyman($keyman)
{
    global $xoopsDB;
    $groupid  = group_id_from_name(_MD_KWCLUB_TEACHER_GROUP);
    $user_arr = [];
    //列出群組中有哪些人
    if ($groupid) {
        /* @var \XoopsMemberHandler $memberHandler */
        $memberHandler = xoops_getHandler('member');
        $user_arr      = $memberHandler->getUsersByGroup($groupid);
    }

    $where = !empty($keyman) ? "where name like '%{$keyman}%' or uname like '%{$keyman}%' or email like '%{$keyman}%'" : '';

    $sql = 'select uid,uname,name from ' . $xoopsDB->prefix('users') . " $where order by uname";
    $result = $xoopsDB->query($sql) or web_error($sql);

    $myts    = MyTextSanitizer::getInstance();
    $user_ok = $user_yet = '';
    while (false !== ($all = $xoopsDB->fetchArray($result))) {
        foreach ($all as $k => $v) {
            $$k = $v;
        }
        $name  = $myts->htmlSpecialChars($name);
        $uname = $myts->htmlSpecialChars($uname);
        $name  = empty($name) ? '' : " ({$name})";
        if (!empty($user_arr) and in_array($uid, $user_arr)) {
            $user_ok .= "<option value=\"$uid\">{$uid} {$name} {$uname} </option>";
        } else {
            $user_yet .= "<option value=\"$uid\">{$uid} {$name} {$uname} </option>";
        }
    }

    return $user_yet;
}
