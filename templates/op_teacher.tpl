
<h2><{$smarty.const._MD_KWCLUB_INDEX_TEACHER}></h2>


<div class="vtable">
    <ul class="vhead">
        <!-- <li class="w1"><{$smarty.const._MD_KWCLUB_TEACHER_ID}></li> -->
        <li class="w1"><{$smarty.const._MD_KWCLUB_TEACHER_NAME}></li>
        <li class="w3"><{$smarty.const._MD_KWCLUB_TEACHER_CLASS}></li>
        <li class="w4"><{$smarty.const._MD_KWCLUB_CATE_DESC}></li>
        <li class="w2"><{$smarty.const._MD_KWCLUB_TEACHER_UPLOAD}></li>
    </ul>


    <{foreach from=$teachers key=uid item=tea}>
        <ul>
            <li class="vcell"><{$smarty.const._MD_KWCLUB_TEACHER_ID}></li>
            <!-- <li class="vm w1 text-center">
                <a name="<{$tea.teacher_title}>">
                    <img src="<{$tea.pic}>" alt="<{$tea.teacher_title}>" class="img-fluid">
                </a>
            </li> -->
            <li class="vm w1 text-center">
                <{$tea.teacher_title}><{$tea.teacher_id}>
            </li>
            <li class="vm w3">
                <{foreach from=$tea_class.$uid key=class_id item=class}>
                    <div style="font-size: 0.9em; list-style-position: inside;"><{$class.club_year}> <a href="index.php?class_id=<{$class_id}>"><{$class.class_title}></a></div>
                <{/foreach}>
            </li>

            <li class="vm w4">
                <pre style="white-space: pre-wrap; background: transparent; border: none; padding: 2px;" id="bio_<{$uid}>" title="<{$smarty.const._MD_KWCLUB_CLICK_TO_EDIT}>"><{$tea.teacher_desc}><{$uid.bio}></pre>
            </li>

           
            <li class="vm w2">
                <{if $smarty.session.isclubAdmin || $smarty.session.isclubUser}>
                <form action="" method="post" id="uploadForm_<{$tea.teacher_id}>" enctype="multipart/form-data" class="myForm " role="form">
                    <div class="col-sm-12">
                        <{$teacher_token}>
                        <input type="hidden" name="teacher_id" id="teacher_id" value="<{$tea.teacher_id}>">
                        <input type="hidden" name="op"  id="op" value="upload">
                        <input type="file" name="files[]" id="files_<{$tea.teacher_id}>" class="form-control form-control-sm btn-sm btn-primary bg-primary validate[required]"  accept="image/*,video/*,.pdf"  multiple />
                    </div>
                </form>
                <{/if}>
                <{foreach from=$tea.files key=i item=file}>
                    <div class="row" id="list_file">
                        <{if $smarty.session.isclubAdmin || $smarty.session.isclubUser}>
                        <div class="col col-sm-1" ><img src="assets/images/del.png" class='image' id="del_<{$file.files_sn}>" data-value="<{$file.files_sn}>" title="刪除<{$file.file_name}> " alt="刪除<{$file.file_name}>"></div>
                        <{/if}>
                        <div class="col"><a href="<{$web_url}><{$file.sub_dir}>" target="_blank" title="<{$file.file_name}>" alt="<{$file.file_name}>"><{$file.file_name}></a></div>
                    </div>
                <{/foreach}>
            </li>
        </ul>
<{/foreach}>
</div>

<{if $smarty.session.isclubAdmin || $smarty.session.isclubUser}>
    <div class="text-right" style="font-size:0.9em;margin: 2px auto 30px;color:rgb(97, 29, 63);">
        <i class="fa fa-lightbulb-o" aria-hidden="true"></i>
        <{if $smarty.session.isclubUser}>
            <{$smarty.const._MD_KWCLUB_TEACHER_DESC}>
        <{/if}>
        <{if $smarty.session.isclubAdmin}>
            <{$smarty.const._MD_KWCLUB_CLICK_BIO_TO_EDIT_DESC}>
        <{/if}>
    </div>
<{/if}>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
$(document).ready(function() {
    
    $('.image').click(function () {
        let file_id = $(this).data("value");
        //alert("click!"+file_id);
        $.get("ajax.php", {op:"delete", file_id: file_id })
        .then(function(res){
            data = JSON.parse(res); //parser JSON物件
            if(data.msg=="success"){
                swal.fire("成功","刪除成功!","success");
                setTimeout(function() { 
                    window.parent.location.reload();
                }, 1000);
            }  
            else
                swal.fire("失敗",data.msg,"error");
        }).fail( function(hxr, error) {
            console.log(error);
            swal.fire("錯誤","網路連線錯誤!!","error");
        });

       /* $.ajax({
            url: 'ajax.php?op=delete&file_id='+file_id, // 伺服器端處理上傳的URL
            type: 'GET',
            dataType: "json",
            success: function (data) {
                console.log(data);
                if(data.msg=='success')
                    swal("成功","成功","success");
                else
                    swal("失敗",data.msg,"error");
            }
        });//end ajax*/
    });
    $('input[type="file"]').change(function () {
        checkfile(this);
        let files = $(this).prop('files');
        let form = $(this).closest('form');
        const formData = new FormData(form[0]);
        
        //var formData = new FormData();
        //var files = this.files;
        // 迴圈將每個檔案加入到FormData物件中
        //for (var i = 0; i < files.length; i++) {
          //  formData.append('files[]', files[i]);
        //}
      
        $.ajax({
            url: 'ajax.php', // 伺服器端處理上傳的URL
            type: 'POST',
            data: formData,
            dataType: "json",
            contentType: false, // 設定contentType為false，讓瀏覽器自動設定正確的內容類型
            processData: false, // 設定processData為false，讓瀏覽器不要對資料進行序列化或轉換
            success: function (data) {
                if(data.msg=="success"){
                    swal.fire("成功","上傳成功!!" ,"success");
                    setTimeout(function() { 
                        window.parent.location.reload();
                    }, 1000);
                }
                else if(data.msg == "error")
                    swal.fire("失敗","上傳失敗", "error");
                else
                    swal.fire("錯誤", msg, "error");
            },
            error: function (xhr, status, error) {
                console.log(xhr, status, error);
                swal.fire("失敗","網路錯誤連線失敗", "error");
            }
        });//end ajax
    });//end change

});//end document


function checkfile(sender) {
    // 可接受的附檔名
    var validExts = new Array(".jpg", ".jpeg",".png", ".mp4", ".mpeg", ".pdf");
    
    var fileExt = sender.value;
    fileExt = fileExt.substring(fileExt.lastIndexOf('.'));
    if (validExts.indexOf(fileExt) < 0) {
        swal.fire("檔案類型錯誤", "，可接受的副檔名有："+validExts.toString(), "error");
        // alert("檔案類型錯誤，可接受的副檔名有：" + validExts.toString());
        sender.value = null;
        return false;
    }
    //else return true;
}//end function
</script>