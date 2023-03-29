
<div class="row">
    <div class="col-sm-10">
        <h2><{$smarty.const._MD_KWCLUB_APPLY_CLASS|sprintf:$class.class_title}></h2>
    </div>
    <div class="col-sm-2" style="padding-top: 40px;">
      <{if $language=="english"}>
        <a href="index.php?op=reg_form&class_id=<{$class_id}>&language=tchinese_utf8" class="btn btn-primary btn-block" ><i class="fa fa-refresh" aria-hidden="true"></i>
        <{else}>
        <a href="index.php?op=reg_form&class_id=<{$class_id}>&language=english" class="btn btn-primary btn-block" ><i class="fa fa-refresh" aria-hidden="true"></i>
        <{/if}>    
        <{$smarty.const._MD_KWCLUB_LANGUAGE}></a>
    </div>   
</div>

<{if $reg_isfree==1}>
  <{includeq file="$xoops_rootpath/modules/kw_club/templates/op_reg_form_id.tpl"}>
<{else}>
  <{includeq file="$xoops_rootpath/modules/kw_club/templates/op_reg_form_openid.tpl"}>
<{/if}>
