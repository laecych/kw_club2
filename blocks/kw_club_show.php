<?php
function kw_club_show()
{
    require_once XOOPS_ROOT_PATH . '/modules/kw_club/function.php';
    $block = club_class_list('', 'return');

    return $block;
}
