<h2><{$smarty.const._MA_KWCLUB_ADMIN_SETUP}></h2>
<form action="config.php" method="post" id="adminForm" enctype="multipart/form-data" class="myForm " role="form">
    <div class="form-group row">
        <label class="sr-only col-form-label text-sm-right">
            <{$smarty.const._MA_KWCLUB_ADMIN_SETUP}>
        </label>
        <div class="col-sm-12">
            <{includeq file="$xoops_rootpath/modules/kw_club/templates/sub_kw_club_user_picker.tpl"}>
        </div>
    </div>
    <div class="text-center">
        <{$admin_token}>
        <input type="hidden" name="op" value="save_club_admin">
        <button type="submit" class="btn btn-primary"><{$smarty.const._TAD_SAVE}></button>
    </div>
</form>