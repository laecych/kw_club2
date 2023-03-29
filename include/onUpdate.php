<?php

// use XoopsModules\Tadtools;
// use XoopsModules\Kw_club;
use XoopsModules\Kw_club\Utility as Utility ;
/**
 * @param $module
 * @param $old_version
 * @return bool
 */
if (!class_exists('XoopsModules\kw_club\Utility')) {
    require XOOPS_ROOT_PATH . '/modules/kw_club/class/Utility.php';
}


function xoops_module_update_kw_club(&$module, $old_version)
{
    global $xoopsDB;

    Utility::mk_group(_MI_KWCLUB_ADMIN_GROUP, _MI_KWCLUB_ADMIN_GROUP . _MI_KWCLUB_GROUP_NOTE);
    // Kw_club\Utility::mk_group(_MI_KWCLUB_TEACHER_GROUP, _MI_KWCLUB_TEACHER_GROUP . _MI_KWCLUB_GROUP_NOTE);
    Utility::mk_dir(XOOPS_ROOT_PATH . '/uploads/kw_club/class');
    Utility::mk_dir(XOOPS_ROOT_PATH . '/uploads/kw_club/teacher');

    if (Utility::chk_fc_tag()) {
        Utility::go_fc_tag();
    }
    if (Utility::chk_db_regParent()) {
        Utility::go_update_dbReg();
    }
    Utility::go_update_dbclubYear();
    Utility::go_update_teacher();

    //檢查後直接更新
    Utility::chk_db_infoIsshow();
    Utility::chk_db_infoSort();
    Utility::chk_db_regNumber();
    Utility::chk_db_classSort(); 
    Utility::dbUpdateFKeyFile();
    Utility::dbUpdateFKeyClass();
    Utility::dbUpdateFKeyReg();
    return true;
}
