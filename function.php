<?php

//引入TadTools的函式庫
if (!file_exists(XOOPS_ROOT_PATH . '/modules/tadtools/tad_function.php')) {
    redirect_header('http://www.tad0616.net/modules/tad_uploader/index.php?of_cat_sn=50', 3, _TAD_NEED_TADTOOLS);
}
require_once XOOPS_ROOT_PATH . '/modules/tadtools/tad_function.php';

//其他自訂的共同的函數
// require_once __DIR__ . '/function_block.php';

//以流水號取得某筆資料
/**
 * @param $cate_id
 * @param $type
 * @return array|false|void
 */
function get_cate($cate_id, $type)
{
    global $xoopsDB;

    if (empty($cate_id) || empty($type)) {
        return;
    }

    $type_id = $type . '_id';
    $sql     = 'select * from `' . $xoopsDB->prefix('kw_club_' . $type) . '`
    where `' . $type . "_id` = '{$cate_id}'";

    $result = $xoopsDB->query($sql) or web_error($sql);
    $data = $xoopsDB->fetchArray($result);

    return $data;
}

//取得所有報名者的uid
/**
 * @param $club_year
 * @return array
 */
function get_reg_uid_all($club_year)
{
    global $xoopsDB;
    if (empty($club_year)) {
        redirect_header($_SERVER['PHP_SELF'], 3, _MD_KWCLUB_NEED_CLUB_YEAR);
    } else {
        // $data['money'] = $data['in_money'] = $data['un_money'] = 0;

        $myts = MyTextSanitizer::getInstance();
        $arr_reg = [];
        $sql  = 'select a.*, b.*, c.`club_end_date` from `' . $xoopsDB->prefix('kw_club_reg') . '` as a
        join `' . $xoopsDB->prefix('kw_club_class') . '` as b on a.`class_id` = b.`class_id`
        join `' . $xoopsDB->prefix('kw_club_info') . "` as c on b.`club_year` = c.`club_year`
        where b.`club_year`='{$club_year}'";
        $result = $xoopsDB->query($sql) or web_error($sql);

        while (false !== ($data = $xoopsDB->fetchArray($result))) {
            $reg_uid  = $data['reg_uid'] = mb_strtoupper($data['reg_uid']);
            $class_id = $data['class_id'];

            if (!isset($arr_reg[$reg_uid]['money'])) {
                $arr_reg[$reg_uid]['in_money'] = $arr_reg[$reg_uid]['un_money'] = $arr_reg[$reg_uid]['money'] = 0;
            }

            $data['end_date'] = strtotime($data['club_end_date']);

            $class_pay                  = $data['class_money'] + $data['class_fee'];
            $data['class_pay']          = $class_pay;
            $arr_reg[$reg_uid]['money'] += $class_pay;

            if ('1' == $data['reg_isfee']) {
                $arr_reg[$reg_uid]['in_money'] += $class_pay;
            } else {
                $arr_reg[$reg_uid]['un_money'] += $class_pay;
            }
            $arr_reg[$reg_uid]['name'] = $data['reg_name'];

            if (_MD_KWCLUB_KG == $data['reg_grade']) {
                $grade = _MD_KWCLUB_KINDERGARTEN;
            } else {
                $grade = $data['reg_grade'] . '年';
            }

            $arr_reg[$reg_uid]['class'] = $grade . $data['reg_class'];
            $arr_reg[$reg_uid]['number'] = $data['reg_number'];
            $arr_reg[$reg_uid]['data'][$class_id] = $data;
        }

        return $arr_reg;
    }
}

//取得的所有社團編號(已存在的社團)
/**
 * @return array|bool
 */
function get_club_class_num()
{
    global $xoopsDB;
    //確認期別
    if (!isset($_SESSION['club_year'])) {
        return false;
    }
    $data = [];
    $year = $_SESSION['club_year'];
    $sql  = 'select `class_num` from `' . $xoopsDB->prefix('kw_club_class') . '` '; // where `club_year` = '{$year}'";
    // echo $sql;
    $result = $xoopsDB->query($sql) or web_error($sql);
    while (list($class_num) = $xoopsDB->fetchRow($result)) {
        $data[] = $class_num;
    }

    //  die($data[0]);
    return $data;
}

//以流水號取得某筆社團資料
/**
 * @param string $class_id
 * @return array|bool|false
 */
function get_club_class($class_id = '')
{
    global $xoopsDB;

    if (empty($class_id)) {
        return false;
    }

    $sql = 'select * from `' . $xoopsDB->prefix('kw_club_class') . "`  where `class_id` = '{$class_id}'";
    $result = $xoopsDB->query($sql) or web_error($sql);
    $data = $xoopsDB->fetchArray($result);

    return $data;
}

//以class_id取得多筆kw_club_reg資料(報名人數)
/**
 * @param string $class_id
 */
function check_class_reg($class_id = '')
{
    global $xoopsDB;

    if (empty($class_id)) {
        return;
    }
    $sql = 'select count(*) from `' . $xoopsDB->prefix('kw_club_reg') . "`  where `class_id` = '{$class_id}'";
    $result = $xoopsDB->query($sql) or web_error($sql);
    list($count) = $xoopsDB->fetchRow($result);

    return $count;
}

//以流水號取得某筆kw_club_reg報名資料
/**
 * @param string $reg_sn
 * @return array|false|void
 */
function get_reg($reg_sn = '')
{
    global $xoopsDB;

    if (empty($reg_sn)) {
        return;
    }

    $sql = 'select * from `' . $xoopsDB->prefix('kw_club_reg') . "`  where `reg_sn` = '{$reg_sn}'";
    $result = $xoopsDB->query($sql) or web_error($sql);
    $data = $xoopsDB->fetchArray($result);

    return $data;
}

//取得所有社團資料陣列
/**
 * @return array|string
 */
function get_club_class_all($club_year ='')
{
    global $xoopsDB;
    if(empty($club_year)){
        if (isset($_SESSION['club_year'])){ 
            $club_year = $_SESSION['club_year'];
        }
        else{return _MD_KWCLUB_NEED_CLUB_YEAR;}
    }

    $sql = 'select * from `' . $xoopsDB->prefix('kw_club_class') . "` where `club_year`= '{$club_year}'";
    $result = $xoopsDB->query($sql) or web_error($sql);
    $data_arr = [];
    while (false !== ($data = $xoopsDB->fetchArray($result))) {
        $class_id            = $data['class_id'];
        $data_arr[$class_id] = $data;
    }

    return $data_arr;

}

//取得學期
/**
 * @return mixed
 */
function get_semester()
{
    global $semester_name_arr, $xoopsDB;

    $sql = 'select `club_year`, `club_start_date`, `club_end_date` from `' . $xoopsDB->prefix('kw_club_info') . '` order by `club_year` desc';
    $result = $xoopsDB->query($sql) or web_error($sql);
    while (list($club_year, $club_start_date, $club_end_date) = $xoopsDB->fetchRow($result)) {
        $all_semester[$club_year] = mb_substr($club_start_date, 0, 10) . '~' . mb_substr($club_end_date, 0, 10);
    }

    //semester and year
    $arr_time  = getdate();
    $this_week = $arr_time['wday'];

    if ($arr_time['mon'] >= 1 && $arr_time['mon'] <= 4) { // 2 3 4 (第二學期)
        $this_semester = '02';
        $this_year     = $arr_time['year'] - 1912;
    } elseif ($arr_time['mon'] > 7 && $arr_time['mon'] <= 11) { //8-11 (第一學期)
        $this_semester = '01';
        $this_year     = $arr_time['year'] - 1911;
    } elseif (12 == $arr_time['mon']) { //12-01 (寒假)
        $this_semester = '11';
        $this_year     = $arr_time['year'] - 1911;
    } elseif ($arr_time['mon'] > 4 && $arr_time['mon'] <= 7) { //5-7 (暑假)
        $this_semester = '00';
        $this_year     = $arr_time['year'] - 1911;
    }

    $last_year = $this_year - 1;
    $next_year = $this_year + 1;
    // $summer_semester='00';
    // $first_semester='01';
    // $winter_semester='11';
    // $second_semester='02';

    foreach ($semester_name_arr as $k => $v) {
        $semester[$this_year . $k]['opt'] = $this_year . ' ' . $v;
        if (isset($all_semester[$this_year . $k])) {
            $semester[$this_year . $k]['opt']      .= " ({$all_semester[$this_year . $k]})";
            $semester[$this_year . $k]['disabled'] = true;
        }
    }
    foreach ($semester_name_arr as $k => $v) {
        $semester[$next_year . $k]['opt'] = $next_year . ' ' . $v;
        if (isset($all_semester[$next_year . $k])) {
            $semester[$next_year . $k]['opt']      .= " ({$all_semester[$next_year . $k]})";
            $semester[$next_year . $k]['disabled'] = true;
        }
    }

    return $semester;
}

/**
 * @return mixed
 */
function get_ip()
{
    $ip = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
        if ($ip) {
            array_unshift($ips, $ip);
            $ip = false;
        }
        foreach ($ips as $i => $iValue) {
            if (!preg_match("#^(10|172\.16|192\.168)\.#i", $ips[$i])) {
                $ip = $iValue;
                break;
            }
        }
    }

    return ($ip ?: $_SERVER['REMOTE_ADDR']);
}



/**
 * @param $class_id
 * @return bool
 */
function mk_club_json($class_id)
{
    global $xoopsDB, $TadUpFiles;
    if (empty($class_id)) {
        return false;
    }
    $myts = MyTextSanitizer::getInstance();

    $tbl = $xoopsDB->prefix('kw_club_class');
    $sql = "SELECT * FROM `$tbl` where `class_id`={$class_id} ";
    $result = $xoopsDB->query($sql) or web_error($sql);
    $class     = $xoopsDB->fetchArray($result);
    $class_num = $class['class_num'];
    $json      = json_encode($class, JSON_UNESCAPED_UNICODE);
    file_put_contents(XOOPS_ROOT_PATH . "/uploads/kw_club/class/{$class_num}.json", $json);

    return true;
}

//取得某一篇js_class
/**
 * @param $class_num
 * @return bool|mixed
 */
function js_class($class_num)
{
    global $xoopsDB, $xoopsTpl;

    if (file_exists(XOOPS_ROOT_PATH . "/uploads/kw_club/class/$class_num.json")) {
        $json = file_get_contents(XOOPS_URL . "/uploads/kw_club/class/$class_num.json");
        $arr  = json_decode($json, true);

        return $arr;
    }

    return false;
}

//列出所有kw_club_cate資料
/**
 * @param $type
 */
function cate_list($type)
{
    global $xoopsDB, $xoopsTpl;

    if(empty($type)){
        redirect_header($_SERVER['PHP_SELF'], 3, _MD_NEED_TYPE);
    }
    $myts = MyTextSanitizer::getInstance();

    if($type == 'class')
        $sql = 'select `class_id`,`class_title` from `' . $xoopsDB->prefix('kw_club_' . $type) . "`where `club_year`= '{$_SESSION['club_year']}' order by " . $type . '_sort';
    else
        $sql = 'select * from `' . $xoopsDB->prefix('kw_club_' . $type) . '` order by ' . $type . '_sort';
    $result = $xoopsDB->query($sql) or web_error($sql);

    $all_content = [];
    while (false !== ($all = $xoopsDB->fetchArray($result))) {
        //過濾讀出的變數值
        $all["{$type}_title"] = $myts->htmlSpecialChars($all["{$type}_title"]);
        $all["{$type}_desc"]  = $myts->htmlSpecialChars($all["{$type}_desc"]);
        $all_content[]        = $all;
    }

    //刪除確認的JS
    if (!file_exists(XOOPS_ROOT_PATH . '/modules/tadtools/sweet_alert.php')) {
        redirect_header('index.php', 3, _MD_NEED_TADTOOLS);
    }
    require_once XOOPS_ROOT_PATH . '/modules/tadtools/sweet_alert.php';
    $sweet_alert_obj = new sweet_alert();
    $sweet_alert_obj->render("delete_{$type}_func", "{$_SERVER['PHP_SELF']}?type={$type}&op=delete_{$type}&{$type}_id=", "{$type}_id");

    $xoopsTpl->assign('action', "{$_SERVER['PHP_SELF']}?type={$type}");
    $xoopsTpl->assign("all_{$type}_content", $all_content);
}

//刪除reg某筆資料資料
/**
 * @return mixed
 */
function delete_reg()
{
    global $xoopsDB;
    // if (!$_SESSION['isclubAdmin'] and !$_SESSION['isclubUser']) {
    //     redirect_header($_SERVER['PHP_SELF'], 3, _TAD_PERMISSION_DENIED);
    // }
    $reg_sn   = system_CleanVars($_REQUEST, 'reg_sn', '0', 'int');
    $class_id = system_CleanVars($_REQUEST, 'class_id', '0', 'int');
    $uid      = system_CleanVars($_REQUEST, 'uid', '0', 'string');

    if (empty($reg_sn)) {
        redirect_header("{$_SERVER['PHP_SELF']}?op=myclass&uid={$uid}", 3, _MD_KWCLUB_NEED_REG_SN);
    } else {
        $arr      = get_reg($reg_sn);
        $class_id = $arr['class_id'];
    }

    $sql = 'update `' . $xoopsDB->prefix('kw_club_class') . "`
    set `class_regnum` =`class_regnum`-1   where `class_id` = '{$class_id}'";
    $xoopsDB->queryF($sql);

    $sql = 'delete from `' . $xoopsDB->prefix('kw_club_reg') . "`  where `reg_sn` = '{$reg_sn}'";
    $xoopsDB->queryF($sql) or web_error($sql);

    return $class_id;
}

//判斷身份
/**
 * @param string $group_name
 * @return bool
 */
function isclub($group_name = '')
{
    global $xoopsUser;
    if ($xoopsUser) {
        $groupid = group_id_from_name($group_name);
        var_dump($groupid);
        if ($groupid) {
            $groups = $xoopsUser->getGroups();
            var_dump($groups);
            if (in_array($groupid, $groups)) {
                return true;
            }
        }
    }

    return false;
}

//取得報名資料
/**
 * @param        $club_year
 * @param string $class_id
 * @param string $order
 * @param bool   $show_PageBar
 * @return array
 */
function get_club_class_reg($club_year, $class_id = '', $order = '', $show_PageBar = false)
{
    global $xoopsDB, $xoopsTpl, $xoopsModuleConfig;

    //預設排序依報名時間
    if (empty($order)) {
        $order = 'ORDER BY a.`reg_datetime`, b.`class_id`';
    }

    $myts = MyTextSanitizer::getInstance();

    $and_class_id = $class_id ? " and a.`class_id`='{$class_id}'" : '';

    $sql = 'select a.*,b.* from `' . $xoopsDB->prefix('kw_club_reg') . '` as a
    join `' . $xoopsDB->prefix('kw_club_class') . "` as b on a.`class_id` = b.`class_id`
    where b.`club_year`='{$club_year}' {$and_class_id} {$order}";

    if ($show_PageBar) {
        //getPageBar($原sql語法, 每頁顯示幾筆資料, 最多顯示幾個頁數選項);
        $PageBar = getPageBar($sql, 20, 10);
        $bar     = $PageBar['bar'];
        $sql     = $PageBar['sql'];
        $total   = $PageBar['total'];

        if ($xoopsTpl) {
            $xoopsTpl->assign('bar', $bar);
            $xoopsTpl->assign('total', $total);
        }
    }
    $result = $xoopsDB->query($sql) or web_error($sql);

    require_once XOOPS_ROOT_PATH . '/modules/tadtools/jeditable.php';
    $file      = 'save.php';
    $jeditable = new jeditable();
    //此處加入欲直接點擊編輯的欄位設定
    $file = 'ajax.php';

    //製作年級選單
    foreach ($xoopsModuleConfig['school_grade'] as $grade) {
        if (_MD_KWCLUB_KG == $grade) {
            $grade_name = _MD_KWCLUB_KINDERGARTEN;
        } else {
            $grade_name = $grade . _MD_KWCLUB_GRADE;
        }
        $g_arr[$grade] = $grade_name;
    }
    $grade_opt = json_encode($g_arr, 256);
    $grade_opt = mb_substr(str_replace('"', "'", $grade_opt), 1, -1);

    //製作班級選單
    $reg_class_arr = explode(';', $xoopsModuleConfig['school_class']);
    foreach ($reg_class_arr as $class_name) {
        $class_name         = trim($class_name);
        $c_arr[$class_name] = $class_name;
    }
    $class_opt = json_encode($c_arr, 256);
    $class_opt = mb_substr(str_replace('"', "'", $class_opt), 1, -1);

    //製作座號選單
    for ( $i=1 ; $i<=35 ; $i++ ) {
        $n_arr[$i] = $i;
    }
    $number_opt = json_encode($n_arr, 256);
    $number_opt = mb_substr(str_replace('"', "'", $number_opt), 1, -1);


    $all_reg = [];
    while (false !== ($all = $xoopsDB->fetchArray($result))) {
        //將是/否選項轉換為圖示
        $all['reg_isfee_pic'] = 1 == $all['reg_isfee'] ? '<img src="' . XOOPS_URL . '/modules/kw_club/assets/images/yes.gif" alt="' . _MD_KWCLUB_PAID . '" title="' . _MD_KWCLUB_PAID . '">' : '<img src="'. XOOPS_URL . '/modules/kw_club/assets/images/no.gif" alt="'. _MD_KWCLUB_NOT_PAY . '" title="' . _MD_KWCLUB_NOT_PAY . '">';
        $all['class_pay']     = $all['class_money'] + $all['class_fee'];
        $all['reg_part_name'] = substr_replace($all['reg_name'], '○', 3, 3);

        $all_reg[] = $all;

        $jeditable->setTextCol("#reg_name_{$all['reg_sn']}", $file, '80px', '1em', "{reg_sn: {$all['reg_sn']} ,op : 'update_reg'}", _MD_KWCLUB_CLICK_TO_EDIT);
        $jeditable->setSelectCol("#reg_isreg_{$all['reg_sn']}", $file, "{'" . _MD_KWCLUB_OFFICIALLY_ENROLL . "':'" . _MD_KWCLUB_OFFICIALLY_ENROLL . "' , '" . _MD_KWCLUB_CANDIDATE . "':'" . _MD_KWCLUB_CANDIDATE . "' , 'selected':'" . _MD_KWCLUB_OFFICIALLY_ENROLL . "'}",
        "{reg_sn: {$all['reg_sn']} ,op : 'update_reg'}", _MD_KWCLUB_CLICK_TO_EDIT);
        $jeditable->setSelectCol("#reg_grade_{$all['reg_sn']}", $file, "{ $grade_opt , 'selected':'{$all['reg_grade']}'}", "{reg_sn: {$all['reg_sn']} ,op : 'update_reg'}", _MD_KWCLUB_CLICK_TO_EDIT);
        $jeditable->setSelectCol("#reg_class_{$all['reg_sn']}", $file, "{ $class_opt , 'selected':'{$all['reg_grade']}'}", "{reg_sn: {$all['reg_sn']} ,op : 'update_reg'}", _MD_KWCLUB_CLICK_TO_EDIT);

        $jeditable->setSelectCol("#reg_number_{$all['reg_sn']}", $file, "{ $number_opt , 'selected':'{$all['reg_number']}'}", "{reg_sn: {$all['reg_sn']} ,op : 'update_reg'}", _MD_KWCLUB_CLICK_TO_EDIT);
        
        $jeditable->setTextCol("#reg_uid_{$all['reg_sn']}", $file, '100px', '1em', "{reg_sn: {$all['reg_sn']} ,op : 'update_reg'}", _MD_KWCLUB_CLICK_TO_EDIT);
        $jeditable->setTextCol("#reg_parent_{$all['reg_sn']}", $file, '80px', '1em', "{reg_sn: {$all['reg_sn']} ,op : 'update_reg'}", _MD_KWCLUB_CLICK_TO_EDIT);
        $jeditable->setTextCol("#reg_tel_{$all['reg_sn']}", $file, '100px', '1em', "{reg_sn: {$all['reg_sn']} ,op : 'update_reg'}", _MD_KWCLUB_CLICK_TO_EDIT);
    }
    $jeditable->render();

    //刪除確認的JS
    {
        if (!file_exists(XOOPS_ROOT_PATH . '/modules/tadtools/sweet_alert.php')) {
            redirect_header('index.php', 3, _MD_NEED_TADTOOLS);
        }
    }

    require_once XOOPS_ROOT_PATH . '/modules/tadtools/sweet_alert.php';
    $sweet_alert_obj = new sweet_alert();
    $sweet_alert_obj->render('delete_reg_func', "{$_SERVER['PHP_SELF']}?op=delete_reg&reg_sn=", 'reg_sn');

    return $all_reg;
}

//列出所有社團資料
if (!function_exists('club_class_list')) {
    /**
     * @param string $club_year
     * @param string $mode
     */
    function club_class_list($club_year = '', $mode = '')
    {
        global $xoopsDB, $xoopsUser, $xoopsTpl, $today;
        $arr_year = get_all_year();
        //這要在前面，才能產生 $_SESSION['club_year']
        $club_info = get_club_info($club_year);
        $club_year = empty($club_year) ? $club_info['club_year'] : $club_year;
        $chk_time  = kw_club_chk_time('return');
        // die(var_export($club_year));
        //已有設定社團期別
        if (!empty($club_year)) {
            //社團列表
            $and_enable = $_SESSION['isclubAdmin'] ? '' : "and class_isopen='1'";
            $myts       = MyTextSanitizer::getInstance();
            $sql        = 'select * from `' . $xoopsDB->prefix('kw_club_class') . "` where `club_year`= '{$club_year}' $and_enable order by `class_sort` ";
            $result = $xoopsDB->query($sql) or web_error($sql);
            $total = $xoopsDB->getRowsNum($result);

            //取得分類所有資料陣列
            $all_cate_arr      = get_cate_all();
            $all_place_arr     = get_place_all();
            $all_teacher_arr   = get_teacher_all();
            $all_class_content = [];
            $i                 = 0;
            while (false !== ($all = $xoopsDB->fetchArray($result))) {
                //以下會產生這些變數： $class_id, $club_year, $class_num, $cate_id, $class_title, $teacher_id, $class_week, $class_date_open, $class_date_close, $class_time_start, $class_time_end, $place_id, $class_member, $class_money, $class_fee, $class_regnum, $class_note, $class_date_start, $class_date_end, $class_ischecked, $class_isopen, $class_desc
                foreach ($all as $k => $v) {
                    $$k = $v;
                }
                $all_class_content[$i]['class_id']         = (int) $class_id;
                $all_class_content[$i]['club_year']        = $myts->htmlSpecialChars($club_year);
                $all_class_content[$i]['class_num']        = (int) $class_num;
                $all_class_content[$i]['class_title']      = $myts->htmlSpecialChars($class_title);
                $all_class_content[$i]['class_week']       = $myts->htmlSpecialChars($class_week);
                $all_class_content[$i]['class_grade']      = $myts->htmlSpecialChars($class_grade);
                $all_class_content[$i]['class_date_open']  = $myts->htmlSpecialChars($class_date_open);
                $all_class_content[$i]['class_date_close'] = $myts->htmlSpecialChars($class_date_close);
                $all_class_content[$i]['class_time_start'] = $myts->htmlSpecialChars($class_time_start);
                $all_class_content[$i]['class_time_end']   = $myts->htmlSpecialChars($class_time_end);
                $all_class_content[$i]['cate_id']          = $myts->htmlSpecialChars($all_cate_arr[$cate_id]);
                $all_class_content[$i]['teacher_id']       = (int) $teacher_id;
                $all_class_content[$i]['teacher_id_title'] = $myts->htmlSpecialChars($all_teacher_arr[$teacher_id]);
                $all_class_content[$i]['place_id']         = $myts->htmlSpecialChars($all_place_arr[$place_id]);
                $all_class_content[$i]['class_member']     = (int) $class_member;
                $all_class_content[$i]['class_money']      = (int) $class_money;
                $all_class_content[$i]['class_fee']        = (int) $class_fee;
                $all_class_content[$i]['class_pay']        = $class_money + $class_fee;
                $all_class_content[$i]['class_regnum']     = (int) $class_regnum;
                $all_class_content[$i]['class_note']       = $myts->htmlSpecialChars($class_note);
                $all_class_content[$i]['class_date_start'] = isset($class_date_start) ? $myts->htmlSpecialChars($class_date_start) : '';
                $all_class_content[$i]['class_date_end']   = isset($class_date_end) ? $myts->htmlSpecialChars($class_date_end) : '';
                $all_class_content[$i]['class_ischecked']  = (int) $class_ischecked;
                $all_class_content[$i]['class_isopen']     = (int) $class_isopen;
                $all_class_content[$i]['class_isopen_pic'] = $class_isopen ? '<img src="' . XOOPS_URL . '/modules/kw_club/assets/images/yes.gif" alt="' . _YES . '" title="' . _YES . '">' : '<img src="' . XOOPS_URL . '/modules/kw_club/assets/images/no.gif" alt="' . _NO . '" title="' . _NO . '">';
                $all_class_content[$i]['class_desc']       = $myts->displayTarea($class_desc, 1, 1, 0, 1, 0);
                $all_class_content[$i]['class_uid']        = (int) $class_uid;
                $all_class_content[$i]['class_sort']        = (int) $class_sort;
                //是否報名額滿
                $all_class_content[$i]['is_full'] = (($class_member + $club_info['club_backup_num']) <= $class_regnum) ? true : false;
                $i++;
            }

            //刪除確認的JS
            if (!file_exists(XOOPS_ROOT_PATH . '/modules/tadtools/sweet_alert.php')) {
                redirect_header('index.php', 3, _MD_NEED_TADTOOLS);
            }
            require_once XOOPS_ROOT_PATH . '/modules/tadtools/sweet_alert.php';
            $sweet_alert_obj = new sweet_alert();
            $sweet_alert_obj->render('delete_class_func', 'club.php?op=delete_class&class_id=', 'class_id');
        } else {
            if ('return' === $mode) {
                return;
            }
            $xoopsTpl->assign('error', _MD_KWCLUB_NEED_CONFIG);//沒有期別課程可報
        }

        if ('return' === $mode) {
            $block['arr_year']  = $arr_year;
            $block['club_info'] = $club_info;
            $block['club_year'] = $club_year;
            // $block['club_year_text']    = $club_year_text;
            $block['chk_time']          = $chk_time;
            $block['can_operate']       = true;
            $block['all_class_content'] = $all_class_content;
            $block['total']             = $total;

            return $block;
        }
        $uid = $xoopsUser ? $xoopsUser->uid() : '';
        $xoopsTpl->assign('uid', $uid);
        //取得社團期別陣列
        $xoopsTpl->assign('arr_year', $arr_year);
        $xoopsTpl->assign('club_info', $club_info);
        $xoopsTpl->assign('club_year', $club_year);
        //檢查報名是否可行
        $xoopsTpl->assign('chk_time', $chk_time);
        $xoopsTpl->assign('language', $_SESSION['language']);

        //超過報名截止時間即停止報名及修改
        // $xoopsTpl->assign('can_operate',kw_club_chk_time('return', true));
        $xoopsTpl->assign('can_operate', true);
        $xoopsTpl->assign('all_class_content', $all_class_content);
        $xoopsTpl->assign('total', $total);

    }//end of function
}

//將期別編號轉為文字
if (!function_exists('club_year_text')) {
    /**
     * @param string $club_year
     * @return string
     */
    function club_year_text($club_year = '')
    {
        global $semester_name_arr;
        $year           = mb_substr($club_year, 0, 3);
        $st             = mb_substr($club_year, -2);
        $club_year_text = $year . _MD_KWCLUB_SCHOOL_YEAR . $semester_name_arr[$st];

        return $club_year_text;
    }
}

//取得社團開課所有期別
if (!function_exists('get_all_year')) {
    /**
     * @param bool $only_enable
     * @return array
     */
    function get_all_year($only_enable = true)
    {
        global $xoopsDB;
        $and_enable = $only_enable ? "and club_enable='1'" : '';
        $sql        = 'select club_year from `' . $xoopsDB->prefix('kw_club_info') . "` where 1=1 {$and_enable} order by `club_sort` ";
        $result = $xoopsDB->query($sql) or web_error($sql);
        $arr_year = [];
        while (list($club_year) = $xoopsDB->fetchRow($result)) {
            // $club_year_text       = club_year_text($club_year);
            $arr_year[$club_year] = $club_year;
        }

        return $arr_year;
    }
}

//從json中取得社團期別資料（會在header.php中讀取）
if (!function_exists('get_club_info')) {
    /**
     * @param string $club_year
     * @return array|false
     */
    function get_club_info($club_year = '')
    {
        global $xoopsDB;

        if (empty($_SESSION['club_year']) || empty($club_year)) {//預設
            $sql = 'select * from `' . $xoopsDB->prefix('kw_club_info') . "` where `club_enable`='1' and `club_sort`='0' order by `club_sort` ";
            $result = $xoopsDB->query($sql) or web_error($sql);
            $club_info = $xoopsDB->fetchArray($result);

            $_SESSION['club_year']          = $club_info['club_year'];
            $_SESSION['club_start_date']    = $club_info['club_start_date'];
            $_SESSION['club_start_date_ts'] = strtotime($club_info['club_start_date']);
            $_SESSION['club_end_date']      = $club_info['club_end_date'];
            $_SESSION['club_end_date_ts']   = strtotime($club_info['club_end_date']);
            $_SESSION['club_isfree']        = $club_info['club_isfree'];
            $_SESSION['club_isshow']        = $club_info['club_isshow'];
            $_SESSION['club_backup_num']    = $club_info['club_backup_num'];


        } else {//多重報名

            $sql = 'select * from `' . $xoopsDB->prefix('kw_club_info') . "` where `club_enable`='1' and `club_year`='{$club_year}'";
            $result = $xoopsDB->query($sql) or web_error($sql);
            $club_info = $xoopsDB->fetchArray($result);

            $_SESSION['club_year']          = $club_info['club_year'];
            $_SESSION['club_start_date']    = $club_info['club_start_date'];
            $_SESSION['club_start_date_ts'] = strtotime($club_info['club_start_date']);
            $_SESSION['club_end_date']      = $club_info['club_end_date'];
            $_SESSION['club_end_date_ts']   = strtotime($club_info['club_end_date']);
            $_SESSION['club_isfree']        = $club_info['club_isfree'];
            $_SESSION['club_isshow']        = $club_info['club_isshow'];
            $_SESSION['club_backup_num']    = $club_info['club_backup_num'];
        }
        
        return $club_info;
    }
}

//檢查是否為報名時間
if (!function_exists('kw_club_chk_time')) {
    /**
     * @param string $mode
     * @param bool   $only_end
     * @param string $club_start_date
     * @param string $club_end_date
     * @return bool
     */
    function kw_club_chk_time($mode = '', $only_end = false, $club_start_date = '', $club_end_date = '')
    {
        $today              = time();
        $club_start_date_ts = (empty($club_start_date) && isset($_SESSION['club_start_date_ts'])) ? $_SESSION['club_start_date_ts'] : strtotime($club_start_date);
        $club_end_date_ts   = (empty($club_end_date) && isset($_SESSION['club_end_date_ts'])) ? $_SESSION['club_end_date_ts'] : strtotime($club_end_date);

        if (($only_end and $club_end_date_ts < $today) or ($club_start_date_ts > $today || $club_end_date_ts < $today)) {
            if ('return' === $mode) {
                return false;
            }
            if ($only_end) {
                redirect_header(XOOPS_URL . '/modules/kw_club/index.php', 5, _MD_KWCLUB_OVER_END_TIME);
            } else {
                redirect_header(XOOPS_URL . '/modules/kw_club/index.php', 5, _MD_KWCLUB_NOT_REG_TIME . " {$club_start_date} ~ {$club_end_date}");
            }
        } else {
            return true;
        }
    }
}

//取得所有社團類型陣列
if (!function_exists('get_cate_all')) {
    /**
     * @return mixed
     */
    function get_cate_all()
    {
        global $xoopsDB;
        $sql = 'select `cate_id`, `cate_title` from `' . $xoopsDB->prefix('kw_club_cate') . "` where `cate_enable`='1' order by `cate_sort`";
        $result = $xoopsDB->query($sql) or web_error($sql);
        while (list($cate_id, $cate_title) = $xoopsDB->fetchRow($result)) {
            $options_array_cate[$cate_id] = $cate_title;
        }

        return $options_array_cate;
        // global $xoopsDB;
        // $sql      = "select * from `" . $xoopsDB->prefix("kw_club_cate") . "`";
        // $result   = $xoopsDB->query($sql) or web_error($sql);
        // $data_arr = array();
        // while (false !== ($data = $xoopsDB->fetchArray($result))) {
        //     $cate_id            = $data['cate_id'];
        //     $data_arr[$cate_id] = $data;
        // }
        // return $data_arr;
    }
}

//取得所有社團地點陣列
if (!function_exists('get_place_all')) {
    /**
     * @return mixed
     */
    function get_place_all()
    {
        global $xoopsDB;
        $sql = 'select `place_id`, `place_title` from `' . $xoopsDB->prefix('kw_club_place') . "` where `place_enable`='1' order by `place_sort`";
        $result = $xoopsDB->query($sql) or web_error($sql);
        while (list($place_id, $place_title) = $xoopsDB->fetchRow($result)) {
            $options_array_place[$place_id] = $place_title;
        }

        return $options_array_place;
        // $sql      = "select * from `" . $xoopsDB->prefix("kw_club_place") . "`";
        // $result   = $xoopsDB->query($sql) or web_error($sql);
        // $data_arr = array();
        // while (false !== ($data = $xoopsDB->fetchArray($result))) {
        //     $cate_id            = $data['place_id'];
        //     $data_arr[$cate_id] = $data;
        // }
        // return $data_arr;
    }
}

//取得所有社團老師陣列
if (!function_exists('get_teacher_all')) {
    /**
     * @return mixed
     */
    function get_teacher_all()
    {
        global $xoopsDB;
        $sql = 'select `teacher_id`, `teacher_title` from `' . $xoopsDB->prefix('kw_club_teacher') . "` where `teacher_enable`='1' order by `teacher_sort`";
        $result = $xoopsDB->query($sql) or web_error($sql);
        while (list($teacher_id, $teacher_title) = $xoopsDB->fetchRow($result)) {
            $options_array_teacher[$teacher_id] = $teacher_title;
        }

        return $options_array_teacher;
    }
}

//取得所有社團老師陣列
if (!function_exists('get_innerteacher_all')) {
    /**
     * @return array
     */
    function get_innerteacher_all()
    {
        global $xoopsDB;
        /* @var \XoopsMemberHandler $memberHandler */
        $memberHandler = xoops_getHandler('member');
        //開課教師
        $groupid = group_id_from_name(_MD_KWCLUB_TEACHER_GROUP);
        $sql     = 'select b.* from `' . $xoopsDB->prefix('groups_users_link') . '` as a
    join ' . $xoopsDB->prefix('users') . " as b on a.`uid`=b.`uid`
    where a.`groupid`='{$groupid}' order by b.`name`";
        $result = $xoopsDB->query($sql) or web_error($sql);
        $arr_teacher = [];
        while (false !== ($teacher = $xoopsDB->fetchArray($result))) {
            $uid            = $teacher['uid'];
            $user           = $memberHandler->getUser($uid);
            $user_avatar    = $user->user_avatar();
            $teacher['bio'] = $teacher['bio'];
            $teacher['pic'] = ('blank.gif' !== $user_avatar) ? XOOPS_URL . '/uploads/' . $user_avatar : XOOPS_URL . '/uploads/avatars/blank.gif';

            $arr_teacher[$uid] = $teacher;
        }

        return $arr_teacher;
    }
}

//根據名稱找群組編號
if (!function_exists('group_id_from_name')) {
    /**
     * @param string $group_name
     * @return mixed
     */
    function group_id_from_name($group_name = '')
    {
        global $xoopsDB;
        $sql = 'select groupid from ' . $xoopsDB->prefix('groups') . " where `name`='{$group_name}'";
        $result = $xoopsDB->queryF($sql) or web_error($sql);
        list($groupid) = $xoopsDB->fetchRow($result);

        return $groupid;
    }
}