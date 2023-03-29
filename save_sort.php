<?php
require_once dirname(dirname(__DIR__)) . '/mainfile.php';
// include_once XOOPS_ROOT_PATH . "/header.php";
require_once $GLOBALS['xoops']->path('/modules/system/include/functions.php');
require_once  __DIR__ . '/header.php';


$op   = system_CleanVars($_REQUEST, 'op', '', 'string');
$sort = 1;

if ('update_kw_club_cate_sort' === $op) {
    foreach ($_POST['cateli'] as $cate_id) {
        $sql = 'update ' . $xoopsDB->prefix('kw_club_cate') . " set `cate_sort`='{$sort}' where `cate_id`='{$cate_id}'";
        $xoopsDB->queryF($sql) or die(_TAD_SORT_FAIL . ' (' . date('Y-m-d H:i:s') . ')');
        $sort++;
    }
} elseif ('update_kw_club_place_sort' === $op) {
    foreach ($_POST['placeli'] as $place_id) {
        $sql = 'update ' . $xoopsDB->prefix('kw_club_place') . " set `place_sort`='{$sort}' where `place_id`='{$place_id}'";
        $xoopsDB->queryF($sql) or die(_TAD_SORT_FAIL . ' (' . date('Y-m-d H:i:s') . ')');
        $sort++;
    }
} elseif ('update_kw_club_teacher_sort' === $op) {
    foreach ($_POST['teacherli'] as $teacher_id) {
        $sql = 'update ' . $xoopsDB->prefix('kw_club_teacher') . " set `teacher_sort`='{$sort}' where `teacher_id`='{$teacher_id}'";
        $xoopsDB->queryF($sql) or die(_TAD_SORT_FAIL . ' (' . date('Y-m-d H:i:s') . ')');
        $sort++;
    }
}
elseif ('update_kw_club_class_sort' === $op) {
    foreach ($_POST['classli'] as $class_id) {
        $sql = 'update ' . $xoopsDB->prefix('kw_club_class') . " set `class_sort`='{$sort}' where `class_id`='{$class_id}'";
        $xoopsDB->queryF($sql) or die(_TAD_SORT_FAIL . ' (' . date('Y-m-d H:i:s') . ')');
        $sort++;
    }
}

echo 'Sort saved! (' . date('Y-m-d H:i:s') . ')';
