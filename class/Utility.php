<?php

namespace XoopsModules\Kw_club;

/*
Utility Class Definition

You may not change or alter any portion of this comment or credits of
supporting developers from this source code or any supporting source code
which is considered copyrighted (c) material of the original comment or credit
authors.

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @license      http://www.fsf.org/copyleft/gpl.html GNU public license
 * @copyright    https://xoops.org 2001-2017 &copy; XOOPS Project
 * @author       Mamba <mambax7@gmail.com>
 */

use XoopsModules\Kw_club;
use XoopsModules\Kw_club\Common;

require_once dirname(__DIR__) . '/function.php';

/**
 * Class Utility
 */
class Utility
{

    // use Common\VersionChecks; //checkVerXoops, checkVerPhp Traits
    // use Common\ServerStats; // getServerStats Trait
    // use Common\FilesManagement; // Files Management Trait
    //============================
    //
    ////新增檔案欄位
    public static function chk_fc_tag()
    {
        global $xoopsDB;
        $sql    = 'SELECT count(`tag`) FROM ' . $xoopsDB->prefix('kw_club_files_center');
        $result = $xoopsDB->query($sql);
        if (empty($result)) {
            return true;
        }
        return false;
    }
    //
    public static function go_fc_tag()
    {
        global $xoopsDB;
        $sql = 'ALTER TABLE ' . $xoopsDB->prefix('kw_club_files_center') . "
        ADD `upload_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '上傳時間',
        ADD `uid` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上傳者',
        ADD `tag` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '註記'
        ";
        $xoopsDB->queryF($sql) or redirect_header(XOOPS_URL . '/modules/system/admin.php?fct=modulesadmin', 30, $xoopsDB->error());
    }
    
    //檢查報名表中家長欄位是否存在
    public static function chk_db_regParent()
    {
        global $xoopsDB;
        $sql    = 'select count(`reg_parent`) from ' . $xoopsDB->prefix('kw_club_reg');
        $result = $xoopsDB->query($sql);
        if (empty($result)) {
            return true;
        }
        return false;
    }
    
    //執行更新
    public static function go_update_dbReg()
    {
        global $xoopsDB;
        if (chk_db_regParent()) {
            $sql = 'ALTER TABLE ' . $xoopsDB->prefix('kw_club_reg') . "  ADD `reg_parent` varchar(255) NOT NULL  COMMENT '報名者家長' after `reg_number`,  ADD `reg_tel` varchar(255) NOT NULL COMMENT '家長聯絡電話' after `reg_parent` ";
            $xoopsDB->queryF($sql) or web_error($sql);
        }
        return true;
    }
    
    public static function go_update_dbclubYear()
    {
        global $xoopsDB;
        $sql = "SHOW COLUMNS FROM ".$xoopsDB->prefix('kw_club_info') . " WHERE Field = 'club_year' AND Type = 'varchar(255)';";
        $result = $xoopsDB->queryF($sql) or web_error($sql);
        $arr =  $xoopsDB->fetchArray($result);
        if(empty($arr)){
            $sql = 'ALTER TABLE ' . $xoopsDB->prefix('kw_club_info') . " CHANGE `club_year` `club_year` varchar(255) COLLATE 'utf8_general_ci' NOT NULL COMMENT '社團年度' AFTER `club_id`;";
            $xoopsDB->queryF($sql) or web_error($sql);
            $sql = 'ALTER TABLE ' . $xoopsDB->prefix('kw_club_class') . " CHANGE `club_year` `club_year` varchar(255) COLLATE 'utf8_general_ci' NOT NULL COMMENT '社團年度' AFTER `class_id`;";
            $xoopsDB->queryF($sql) or web_error($sql);
        }
        // $sql='ALTER TABLE ' . $xoopsDB->prefix('kw_club_class') . " MODIFY `class_ischecked` enum('1','0','') NOT NULL DEFAULT '' COMMENT '是否開班'; ";
        // $xoopsDB->queryF($sql) or web_error($sql);
        
        return true;
    }

    //
    public static function go_update_teacher()
    {
        global $xoopsDB;
    
        $sql = ' CREATE TABLE if not exists ' . $xoopsDB->prefix('kw_club_teacher') . " (
            `teacher_id` smallint(6) unsigned NOT NULL auto_increment COMMENT '教師編號',
            `teacher_title` varchar(255) NOT NULL default '' COMMENT '教師標題',
            `teacher_desc` text COMMENT '教師簡介',
            `teacher_sort` smallint(6) unsigned NOT NULL default '0' COMMENT '教師排序',
            `teacher_enable` enum('1','0') NOT NULL default '1' COMMENT '狀態',
            PRIMARY KEY  (`teacher_id`)) ENGINE=MyISAM ;";
    
        $xoopsDB->queryF($sql) or web_error($sql);
        return true;
    }
    

     //檢查club_info中新增isshow欄位
     public static function chk_db_infoIsshow()
     {
        global $xoopsDB;

        $sql = "SELECT count(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME= '". $xoopsDB->prefix('kw_club_info') ."' AND column_name = 'club_isshow'";
        $result = $xoopsDB->query($sql);
        list($count) = $xoopsDB->fetchRow($result);
        if (empty($count)) {
            $sql = 'ALTER TABLE ' . $xoopsDB->prefix('kw_club_info') . " ADD `club_isshow` enum('1','0','') NOT NULL DEFAULT '' COMMENT '報名顯示' after `club_isfree`";
            $xoopsDB->queryF($sql) or web_error($sql);
            return true;
        }
        return false;
    }

    //檢查club_info中新增sort欄位
    public static function chk_db_infoSort()
    {
        global $xoopsDB;
        
         //取得MAXID
         $sql = "SELECT MAX(club_id) FROM ". $xoopsDB->prefix('kw_club_info') ; 
         $result = $xoopsDB->query($sql);
         list($max_id) = $xoopsDB->fetchRow($result);

        $sql = "SELECT count(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME= '". $xoopsDB->prefix('kw_club_info') ."' AND column_name = 'club_sort'";
        $result = $xoopsDB->query($sql);
        list($count) = $xoopsDB->fetchRow($result);
        if (empty($count)) {
            $sql = 'ALTER TABLE ' . $xoopsDB->prefix('kw_club_info') . " ADD `club_sort` smallint(6) unsigned NOT NULL DEFAULT '1' COMMENT '顯示順序' after `club_enable` ";
            $xoopsDB->queryF($sql) or web_error($sql);
        }
        $sql = "UPDATE `" . $xoopsDB->prefix('kw_club_info') . "` set `club_sort`=0  WHERE `club_id`='{$max_id}'";
        $xoopsDB->query($sql);
        return true;
    }
    
    //檢查報名表中新增座號欄位
    public static function chk_db_regNumber()
    {
        global $xoopsDB;
    //  $sql    = 'select count(`reg_number`) from ' . $xoopsDB->prefix('kw_club_reg');
        $sql = "SELECT count(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME= '". $xoopsDB->prefix('kw_club_reg') ."' AND column_name = 'reg_number'";
        $result = $xoopsDB->query($sql);
        list($count) = $xoopsDB->fetchRow($result);

        if (empty($count)) {
            $sql = 'ALTER TABLE ' . $xoopsDB->prefix('kw_club_reg') . " ADD `reg_number` tinyint(3) NOT NULL  COMMENT '報名者座號' after `reg_class` ";
            $xoopsDB->queryF($sql) or web_error($sql);
            return true;
        }
    //  die($count);
        return false;
    }

    //檢查class新增sort欄位
    public static function chk_db_classSort()
    {
        global $xoopsDB;
        $sql = "SELECT count(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '". $xoopsDB->prefix('kw_club_class') ."' AND column_name = 'class_sort'";
        $result = $xoopsDB->query($sql);
        list($count) = $xoopsDB->fetchRow($result);
        if (empty($count)) {
        // $sql = " SET global sql_mode = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'";
        // $xoopsDB->queryF($sql) or web_error($sql);
        //先設定date欄位為空(資料表會產生錯誤!)
        $sql = 'ALTER TABLE ' . $xoopsDB->prefix('kw_club_class') . " CHANGE `class_date_open` `class_date_open` date NULL DEFAULT '0000-00-00' COMMENT '上課起始日' AFTER `class_week`,
        CHANGE `class_date_close` `class_date_close` date NULL DEFAULT '0000-00-00' COMMENT '上課終止日' AFTER `class_date_open`";
        $xoopsDB->query($sql) or web_error($sql);  
        //修改
        $sql = "ALTER TABLE `" . $xoopsDB->prefix('kw_club_class') ."`  ADD `class_sort` smallint(6) unsigned NOT NULL DEFAULT '1' COMMENT '課程排序' after `class_ip` ";
        $xoopsDB->queryF($sql) or web_error($sql);
        
        //設定date欄位為非空(資料表會產生錯誤!)
        $sql = 'ALTER TABLE ' . $xoopsDB->prefix('kw_club_class') . " CHANGE `class_date_open` `class_date_open` date NOT NULL DEFAULT '0000-00-00' COMMENT '上課起始日' AFTER `class_week`,
        CHANGE `class_date_close` `class_date_close` date NOT NULL DEFAULT '0000-00-00' COMMENT '上課終止日' AFTER `class_date_open`";
        $xoopsDB->query($sql) or web_error($sql);
        return true;
        // $sql ="SET GLOBAL sql_mode = DEFAULT";
        // $xoopsDB->queryF($sql) or web_error($sql);
        }
    //  die($count);
        return false;
    }
    
    //新增files_center的FK
    public static function dbUpdateFKeyFile(){
        global $xoopsDB;
        $sql = "SELECT REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = '".$xoopsDB->prefix('kw_club_files_center')."'
        ";
        $result = $xoopsDB->query($sql) or web_error($sql);
        
        //如果已有設FKey
        while (false !== (list($table, $colume) = $xoopsDB->fetchRow($result))) {
            if (!empty($table) && !empty($colume))
                return false;
        }
        //否則新增Fkey
        $sql="ALTER TABLE `".$xoopsDB->prefix('kw_club_teacher')."` ENGINE InnoDB";
        $xoopsDB->query($sql);
        $sql="ALTER TABLE `".$xoopsDB->prefix('kw_club_files_center')."` ENGINE InnoDB";
        $xoopsDB->query($sql);
        $sql= "ALTER TABLE `".$xoopsDB->prefix('kw_club_files_center')."` ADD FOREIGN KEY (`col_sn`) REFERENCES `xx_kw_club_teacher` (`teacher_id`) ON DELETE CASCADE ON UPDATE CASCADE ";            
        $result = $xoopsDB->query($sql) or web_error($sql);
        // die("successs:".$sql);
        return true;
    }
    
    //新增class FK
    public static function dbUpdateFKeyClass(){
        global $xoopsDB;
        $sql = "SELECT REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = '".$xoopsDB->prefix('kw_club_class')."'
        ";
        $result = $xoopsDB->query($sql) or web_error($sql);
      
        //如果已有設設FKey
        while (false !== (list($table, $colume) = $xoopsDB->fetchRow($result))) {
            if (!empty($table) && !empty($colume))
                return true;
        }
        //否則新增Fkey
        // $sql = " SET global sql_mode = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'";
        // $xoopsDB->queryF($sql) or web_error($sql);

        $sql="ALTER TABLE `".$xoopsDB->prefix('kw_club_info')."` ENGINE InnoDB";
        $xoopsDB->query($sql);
        $sql="ALTER TABLE `".$xoopsDB->prefix('kw_club_class')."` ENGINE InnoDB";
        $xoopsDB->query($sql);  
        
        $sql = 'ALTER TABLE ' . $xoopsDB->prefix('kw_club_class') . " CHANGE `class_date_open` `class_date_open` date NULL DEFAULT '0000-00-00' COMMENT '上課起始日' AFTER `class_week`,
        CHANGE `class_date_close` `class_date_close` date NULL DEFAULT '0000-00-00' COMMENT '上課終止日' AFTER `class_date_open`";
        $xoopsDB->query($sql) or web_error($sql);  

        //清除孤兒課程資料
        $sql = "delete FROM `".$xoopsDB->prefix('kw_club_class')."` WHERE `club_year` NOT IN (SELECT `club_year` FROM `".$xoopsDB->prefix('kw_club_info')."`)";
        $xoopsDB->query($sql); 

        $sql= "ALTER TABLE `".$xoopsDB->prefix('kw_club_class')."` ADD FOREIGN KEY (`club_year`) REFERENCES `".$xoopsDB->prefix('kw_club_info')."` (`club_year`) ON DELETE CASCADE ON UPDATE CASCADE ";            
        $result = $xoopsDB->query($sql) or web_error($sql);


        $sql = 'ALTER TABLE ' . $xoopsDB->prefix('kw_club_class') . " CHANGE `class_date_open` `class_date_open` date NOT NULL DEFAULT '0000-00-00' COMMENT '上課起始日' AFTER `class_week`,
        CHANGE `class_date_close` `class_date_close` date NOT NULL DEFAULT '0000-00-00' COMMENT '上課終止日' AFTER `class_date_open`";
        $xoopsDB->query($sql) or web_error($sql);

        // $sql ="SET GLOBAL sql_mode = DEFAULT";
        // $xoopsDB->queryF($sql) or web_error($sql);

        //die($sql);
        return true;

    }

     public static function dbUpdateFKeyReg(){
        global $xoopsDB;
        $sql = "SELECT REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = '".$xoopsDB->prefix('kw_club_reg')."'
        ";
        $result = $xoopsDB->query($sql) or web_error($sql);
      
        //如果已有設設FKey
        while (false !== (list($table, $colume) = $xoopsDB->fetchRow($result))) {
            if (!empty($table) && !empty($colume))
                return true;
        }

        //否則修改資料表引擎
        $sql="ALTER TABLE `".$xoopsDB->prefix('kw_club_reg')."` ENGINE InnoDB";
        $xoopsDB->query($sql);
        $sql="ALTER TABLE `".$xoopsDB->prefix('kw_club_class')."` ENGINE InnoDB";
        $xoopsDB->query($sql);  
        
        //清除孤兒報名資料
        $sql = "delete FROM `".$xoopsDB->prefix('kw_club_reg')."` WHERE `class_id` NOT IN (SELECT `class_id` FROM `".$xoopsDB->prefix('kw_club_class')."`)";
        $xoopsDB->query($sql); 
        
        //新增Fkey
        $sql= "ALTER TABLE `".$xoopsDB->prefix('kw_club_reg')."` ADD FOREIGN KEY (`class_id`) REFERENCES `xx_kw_club_class` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE ";            
        $result = $xoopsDB->query($sql) or web_error($sql);

        return true;

    }


    
    /**
     * @param string $name
     * @param string $description
     * @return int
     */
    public static function mk_group($name = '', $description = '')
    {
        global $xoopsDB;
        $sql = 'select `groupid` from `' . $xoopsDB->prefix('groups') . "` where `name`='$name'";
        $result = $xoopsDB->query($sql) or web_error($sql);
        list($groupid) = $xoopsDB->fetchRow($result);
        if (empty($groupid)) {
            $sql = 'insert into `' . $xoopsDB->prefix('groups') . "` (`name`, `description`) values('{$name}','{$description}')";
            $xoopsDB->queryF($sql) or web_error($sql);
            //取得最後新增資料的流水編號
            $groupid = $xoopsDB->getInsertId();
        }
        return $groupid;
    }


    /**
     * @param $name
     */
    public static function rm_group($name)
    {
        /* @var \XoopsMemberHandler $memberHandler */
        // $memberHandler = xoops_getHandler('member');
        // $group         = $memberHandler->createGroup();
        // $group->setVar('name', $name);
        // $memberHandler->insertGroup($group);

        global $xoopsDB;
        $sql = 'select `groupid` from `' . $xoopsDB->prefix('groups') . "` where `name`='$name'";
        $result = $xoopsDB->query($sql) or die("資料庫錯誤!".$sql);
        list($groupid) = $xoopsDB->fetchRow($result);
        if($groupid){//刪除 groups_users_link ->group_permission->groups
            $sql = 'DELETE FROM `'.$xoopsDB->prefix('groups_users_link'). "` WHERE `groupid`={$groupid}";
            $xoopsDB->queryF($sql) or die("資料庫錯誤!".$sql);
            $sql = 'DELETE FROM `'.$xoopsDB->prefix('group_permission'). "` WHERE `gperm_groupid`={$groupid}";
            $xoopsDB->queryF($sql) or die("資料庫錯誤!".$sql);
            $sql = 'DELETE FROM `'.$xoopsDB->prefix('groups'). "` WHERE `groupid`={$groupid}";
            $xoopsDB->queryF($sql) or die("資料庫錯誤!".$sql);
        }
        return true;
    }

    //建立目錄
    public static function mk_dir($dir = "")
    {
        //若無目錄名稱秀出警告訊息
        if (empty($dir)) {
            return;
        }
        //若目錄不存在的話建立目錄
        if (!is_dir($dir)) {
            umask(000);
        //若建立失敗秀出警告訊息
            mkdir($dir, 0777);
        }
    }

    //刪除目錄
    public static function rm_dir($dirname) 
    {
        if (is_dir($dirname))
            $dir_handle = opendir($dirname);
        if (!$dir_handle)
            return false;
        while($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname."/".$file))
                    unlink($dirname."/".$file);
                else
                    Utility::rm_dir($dirname.'/'.$file);
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return true;
    }

    //備份db
    public static function bak_dir() {
        GLOBAL $xoopsDB;
        $date=date("Ymd");
        rename(XOOPS_ROOT_PATH."/uploads/kw_club",XOOPS_ROOT_PATH."/uploads/kw_club_bak_{$date}");
        return true;
    }


    // public static function chk_db_clubYear()
    // {
    //     global $xoopsDB;
    //     $sql    = "select count(`club_year`) from " . $xoopsDB->prefix("kw_club_info");
    //     $result = $xoopsDB->query($sql);
    //     if (empty($result)) {
    //         return true;
    //     }
    
    //     return false;
    // }


    //
    //// //拷貝目錄
    ////public static function full_copy($source = "", $target = "")
    //// {
    ////     if (is_dir($source)) {
    ////         @mkdir($target);
    ////         $d = dir($source);
    ////         while (false !== ($entry = $d->read())) {
    ////             if ($entry == '.' || $entry == '..') {
    ////                 continue;
    ////             }
    //
    ////             $Entry = $source . '/' . $entry;
    ////             if (is_dir($Entry)) {
    ////                 full_copy($Entry, $target . '/' . $entry);
    ////                 continue;
    ////             }
    ////             copy($Entry, $target . '/' . $entry);
    ////         }
    ////         $d->close();
    ////     } else {
    ////         copy($source, $target);
    ////     }
    //// }
    //
    ////public static function rename_win($oldfile, $newfile)
    //// {
    ////     if (!rename($oldfile, $newfile)) {
    ////         if (copy($oldfile, $newfile)) {
    ////             unlink($oldfile);
    ////             return true;
    ////         }
    ////         return false;
    ////     }
    ////     return true;
    //// }
    //
    ////public static function delete_directory($dirname)
    //// {
    ////     if (is_dir($dirname)) {
    ////         $dir_handle = opendir($dirname);
    ////     }
    //
    ////     if (!$dir_handle) {
    ////         return false;
    ////     }
    //
    ////     while ($file = readdir($dir_handle)) {
    ////         if ($file != "." && $file != "..") {
    ////             if (!is_dir($dirname . "/" . $file)) {
    ////                 unlink($dirname . "/" . $file);
    ////             } else {
    ////                 delete_directory($dirname . '/' . $file);
    ////             }
    //
    ////         }
    ////     }
    ////     closedir($dir_handle);
    ////     rmdir($dirname);
    ////     return true;
    //// }
    //
    ///*
    //function xoops_module_update_模組目錄(&$module, $old_version) {
    //GLOBAL $xoopsDB;
    //
    ////if(!chk_chk1()) go_update1();
    //
    //return true;
    //}
    //
    ////檢查某欄位是否存在
    //function chk_chk1(){
    //global $xoopsDB;
    //$sql="select count(`欄位`) from ".$xoopsDB->prefix("資料表");
    //$result=$xoopsDB->query($sql);
    //if(empty($result)) return false;
    //return true;
    //}
    //
    ////執行更新
    //function go_update1(){
    //global $xoopsDB;
    //$sql="ALTER TABLE ".$xoopsDB->prefix("資料表")." ADD `欄位` smallint(5) NOT NULL";
    //$xoopsDB->queryF($sql) or redirect_header(XOOPS_URL,3,  $GLOBALS['xoopsDB']->error());
    //
    //return true;
    //}
    //
    ////建立目錄
    //function mk_dir($dir=""){
    ////若無目錄名稱秀出警告訊息
    //if(empty($dir))return;
    ////若目錄不存在的話建立目錄
    //if (!is_dir($dir)) {
    //umask(000);
    ////若建立失敗秀出警告訊息
    //mkdir($dir, 0777);
    //}
    //}
    //
    ////拷貝目錄
    //function full_copy( $source="", $target=""){
    //if ( is_dir( $source ) ){
    //@mkdir( $target );
    //$d = dir( $source );
    //while ( FALSE !== ( $entry = $d->read() ) ){
    //if ( $entry == '.' || $entry == '..' ){
    //continue;
    //}
    //
    //$Entry = $source . '/' . $entry;
    //if ( is_dir( $Entry ) )    {
    //full_copy( $Entry, $target . '/' . $entry );
    //continue;
    //}
    //copy( $Entry, $target . '/' . $entry );
    //}
    //$d->close();
    //}else{
    //copy( $source, $target );
    //}
    //}
    //
    //function rename_win($oldfile,$newfile) {
    //if (!rename($oldfile,$newfile)) {
    //if (copy ($oldfile,$newfile)) {
    //unlink($oldfile);
    //return TRUE;
    //}
    //return FALSE;
    //}
    //return TRUE;
    //}
    //
    //function delete_directory($dirname) {
    //if (is_dir($dirname))
    //$dir_handle = opendir($dirname);
    //if (!$dir_handle)
    //return false;
    //while($file = readdir($dir_handle)) {
    //if ($file != "." && $file != "..") {
    //if (!is_dir($dirname."/".$file))
    //unlink($dirname."/".$file);
    //else
    //delete_directory($dirname.'/'.$file);
    //}
    //}
    //closedir($dir_handle);
    //rmdir($dirname);
    //return true;
    //}
    //
    // */

    
}
