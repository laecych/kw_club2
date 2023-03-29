<h2><{$smarty.const._MD_KWCLUB_CLUB_YEAR_LIST}></h2>
<{if $all_kw_club_info}>
    <{if $smarty.session.isclubAdmin}>
        <{$delete_kw_club_info_func}>
    <{/if}>

    <div id="kw_club_info_save_msg"></div>

    <div class="vtable" style="margin: 10px;">
        <ul class="vhead">
            <!--社團年度-->
            <li class="w2">
                <{$smarty.const._MD_KWCLUB_ENABLE}>
            </li>
            <!--報名起始日-->
            <li>
                <{$smarty.const._MD_KWCLUB_START_DATE}>
            </li>
            <!--報名終止日-->
            <li>
                <{$smarty.const._MD_KWCLUB_END_DATE}>
            </li>
            <!--報名方式-->
            <li class="w1">
                <{$smarty.const._MD_KWCLUB_ISFREE}>
            </li>
            <!--候補人數-->
            <li class="w1">
                <{$smarty.const._MD_KWCLUB_ISSHOW}><br>
                <{$smarty.const._MD_KWCLUB_BACKUP_NUM}>
            </li>

            <{if $smarty.session.isclubAdmin}>
                <li class="w1"><{$smarty.const._TAD_FUNCTION}></li>
            <{/if}>
        </ul>

        <{foreach from=$all_kw_club_info item=data}>
            <ul id="tr_<{$data.club_id}>" <{if $data.club_start_date <= $today && $today <= $data.club_end_date}> class="list-group-item list-group-item-action list-group-item-success"<{/if}> >

                <!--社團名稱-->
                <li class="vcell"><{$smarty.const._MD_KWCLUB_YEAR}></li>
                <li class="vm w2">
                    <a href="config.php?op=update_enable&club_enable=<{if $data.club_enable==1}>0<{else}>1<{/if}>&club_id=<{$data.club_id}>" data-toggle="tooltip" data-placement="bottom" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<{$smarty.const._MD_KWCLUB_CLICK_TO}><{if $data.club_enable==1}><{$smarty.const._MD_KWCLUB_ENABLE_0}><{else}><{$smarty.const._MD_KWCLUB_ENABLE_1}><{/if}>"><{$data.club_enable_pic}></a>
                    <{if $data.club_year==$now_club_year}>
                       <{$data.club_year_pic}>
                    <{else}>
                        <a href="config.php?op=update_club_year&club_id=<{$data.club_id}>"  data-toggle="tooltip" data-placement="bottom" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<{$smarty.const._MD_KWCLUB_CLICK_TO}>此期別"> <{$data.club_year_pic}> </a>
                    <{/if}>(<{$data.club_sort}>)
                        <span data-toggle="tooltip" data-placement="bottom" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<{$data.club_year}>"><{$data.club_year}></span>

                </li>

                <!--報名起始日-->
                <li class="vcell"><{$smarty.const._MD_KWCLUB_START_DATE}></li>
                <li class="vm text-center">
                    <span style="color:rgb(190, 63, 4);"><{$data.club_start_date|date_format:"%Y/%m/%d %H:%M"}></span>
                </li>

                <!--報名終止日-->
                <li class="vcell"><{$smarty.const._MD_KWCLUB_END_DATE}></li>
                <li class="vm text-center">
                    <span style="color:rgb(190, 63, 4);"><{$data.club_end_date|date_format:"%Y/%m/%d %H:%M"}></span>
                </li>

                <!--報名方式-->
                <li class="vcell"><{$smarty.const._MD_KWCLUB_ISFREE}></li>
                <li class="vm w1 text-center">
                    <{$data.club_isfree_text}>
                </li>

                <!--候補人數-->
                <li class="vcell"><{$smarty.const._MD_KWCLUB_BACKUP_NUM}></li>
                <li class="vm w1 text-center ">
                    <{$data.club_isshow_text}><br><{$data.club_backup_num}>
                </li>


                <{if $smarty.session.isclubAdmin}>
                    <li class="vcell"><{$smarty.const._TAD_FUNCTION}></li>
                    <li class="vm w1 text-center">
                        <a href="javascript:delete_kw_club_info_func(<{$data.club_id}>);" class="btn btn-sm btn-danger"><{$smarty.const._TAD_DEL}></a>
                        <a href="<{$xoops_url}>/modules/kw_club/config.php?op=kw_club_info_form&club_id=<{$data.club_id}>" class="btn btn-sm btn-warning"><{$smarty.const._TAD_EDIT}></a>
                        <a href="club.php?club_year=<{$club_year}>" class="btn btn-sm btn-primary"> <{$smarty.const._MD_KWCLUB_ADD}></a>
                    </li>
                <{/if}>
            </ul>
        <{/foreach}>
    </div>


    <{if $smarty.session.isclubAdmin}>
        <div class="text-right">
            <a href="<{$xoops_url}>/modules/kw_club/config.php?op=kw_club_info_form" class="btn btn-info"><{$smarty.const._MD_KWCLUB_ADD_CLUB_INFO}></a>
        <{if $all_kw_club_info }>
            <a href="<{$xoops_url}>/modules/kw_club/config.php?op=kw_club_info_form&type=copy" class="btn btn-success">
                <{$smarty.const._MD_KWCLUB_COPY}>
            </a>
        <{/if}>
        </div>
        
    <{/if}>

    <{$bar}>
<{else}>
    <div class="jumbotron text-center">
        <{if $smarty.session.isclubAdmin}>
            <a href="<{$xoops_url}>/modules/kw_club/config.php?op=kw_club_info_form" class="btn btn-info"><{$smarty.const._MD_KWCLUB_ADD_CLUB_INFO}></a>
        <{else}>
            <h3><{$smarty.const._MD_KWCLUB_EMPTY_YEAR}></h3>
        <{/if}>
    </div>
<{/if}>

<script type="text/javascript">
function delete_kw_club_info_func(club_id){
    swal({
        title: '確定刪除?!',
        text: "刪除期別會一併刪除所有此期別的課程，和所有學生的報名資料，且無法復原請務必確認!!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '確定刪除',
        closeOnConfirm: false ,
        allowOutsideClick: true
        },
        function(){
            swal('已刪除!','社團期別和相關課程已全部刪除!', 'success');
            location.href='config.php?op=delete_kw_club_info&club_id=' + club_id;
        });
}
</script>