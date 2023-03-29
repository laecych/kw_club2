<?php
use XoopsModules\Kw_club\Utility as Utility;

/**
 * @param $module
 * @return bool
 */
function xoops_module_uninstall_kw_club(&$module)
{
    global $xoopsDB;

    Utility::bak_dir(); //備份uploads/kw_leave_bak
    Utility::rm_group(_MI_KWCLUB_ADMIN_GROUP); //移除
    // Utility::rm_group(_MI_KWCLUB_TEACHER_GROUP); //移除
    Utility::rm_dir(XOOPS_ROOT_PATH . "/uploads/kw_club");

    return true;
}



