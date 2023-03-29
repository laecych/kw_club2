<p><{$smarty.const._MD_KWCLUB_APPLY_NOTE}></p>

<!--套用formValidator驗證機制-->
<form action="index.php" method="post" id="regForm" enctype="multipart/form-data" class="myForm " role="form">

<!--報名者-->
<div class="form-group row">
    <!--openID-->
    <label class="col-sm-2 col-form-label text-sm-right">
        <{$smarty.const._MD_KWCLUB_OPENID}><span class="caption-required">*</span>
    </label>
    <div class="col-sm-4">
        <{$user}>
        <input type="hidden" name="reg_uid" id="reg_uid" value="<{$user}>">
    </div>
    <!--報名者姓名-->
    <label class="col-sm-2 col-form-label text-sm-right">
        <{$smarty.const._MD_KWCLUB_REG_NAME}><span class="caption-required">*</span>
    </label>
    <div class="col-sm-4">
        <{$name}>
        <input type="hidden" name="reg_name" id="reg_name" value="<{$name}>">
    </div>
</div>


<!--報名者年級-->
<div class="form-group row">
    <label for="reg_grade" class="col-sm-2 col-form-label text-sm-right"><{$smarty.const._MD_KWCLUB_REG_GRADE}><span class="caption-required">*</span></label>
    <div class="col-sm-4">
        <{$grade}><{$smarty.const._MD_KWCLUB_G}><{$classnum}><{$smarty.const._MD_KWCLUB_C}>
        <input type="hidden" name="reg_grade" id="reg_grade" value="<{$grade}>">
        <input type="hidden" name="reg_class" id="reg_class" value="<{$classnum}>">
    </div>
    <!--學生座號-->
    <label class="col-sm-2 col-form-label text-sm-right">
        <{$smarty.const._MD_KWCLUB_REG_NUMBER}><span class="caption-required">*</span>
    </label>
    <div class="col-sm-4">
        <input type="text" name="reg_number" id="reg_number" class="form-control validate[required]" value="<{$reg_number}>" placeholder="<{$smarty.const._MD_KWCLUB_REG_NUMBER}>">
    </div>
</div>
<!--家長姓名-->
<div class="form-group row">
    <label class="col-sm-2 col-form-label text-sm-right">
        <{$smarty.const._MD_KWCLUB_REG_PARENT}><span class="caption-required">*</span>
    </label>
    <div class="col-sm-4">
        <input type="text" name="reg_parent" id="reg_parent" class="form-control validate[required]" value="<{$reg_parent}>" placeholder="<{$smarty.const._MD_KWCLUB_KEYIN}><{$smarty.const._MD_KWCLUB_REG_PARENT}>">
    </div>
    <!--連絡電話-->
    <label class="col-sm-2 col-form-label text-sm-right">
        <{$smarty.const._MD_KWCLUB_REG_TEL}><span class="caption-required">*</span>
    </label>
    <div class="col-sm-4">
        <input type="number" name="reg_tel" id="reg_tel" class="form-control validate[required]" value="<{$reg_tel}>" placeholder="<{$smarty.const._MD_KWCLUB_KEYIN}><{$smarty.const._MD_KWCLUB_REG_TEL}>"  maxlength="10" onkeyup="value=value.replace(/[^\d]/g,'')">
    </div>
</div>

<div class="text-center">

    <{$reg_token}>

    <!--類型排序-->
    <input type="hidden" name="class_id"  value="<{$class_id}>" >
    <input type="hidden" name="op" value="insert_reg">
    <button type="submit" class="btn btn-primary"><{$smarty.const._MD_KWCLUB_CHECK_OK}></button>
</div>
</form>