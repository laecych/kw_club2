<div class="row">
  <div class="col-sm-12">
      <h2><{$smarty.const._MD_KWCLUB_CLASS_LIST}></h2>
      <{if $all_class_content}>
          <script type="text/javascript">
              $(document).ready(function(){
                  $("#kw_club_class_sort").sortable({ opacity: 0.6, cursor: "move", update: function() {
                      var order = $(this).sortable("serialize");
                      $.post("<{$xoops_url}>/modules/kw_club/save_sort.php", order + "&op=update_kw_club_class_sort", function(theResponse){
                      $("#kw_club_class_save_msg").html(theResponse);
                      });
                  }
                  });
              });
              function delClass_func(class_id){
                swal({
                    title: '確定要刪除此資料？',
                    text: '此課程的所有報名資料通通都將會被刪除無法復原！',
                    type: 'warning',
                    html: '',
                    showCancelButton: true,
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: '確定刪除！',
                    closeOnConfirm: false ,
                    allowOutsideClick: true
                },
                function(){
                    location.href='config.php?op=delClass&class_id=' + class_id;
                });
            }
          </script>
          <div id="kw_club_class_save_msg"></div>
          <ul class="list-group" id="kw_club_class_sort">
            <{if $smarty.session.isclubAdmin}>
              <{foreach from=$all_class_content item=data}>
                  <li id="classli_<{$data.class_id}>" class="list-group-item">
                    <{$data.class_title}>
                      
                    <img src="<{$xoops_url}>/modules/tadtools/treeTable/images/updown_s.png" style="cursor: s-resize;margin:0px 4px;" alt="<{$smarty.const._TAD_SORTABLE}>" title="<{$smarty.const._TAD_SORTABLE}>">
                    <a href="javascript:delete_class_func(<{$data.class_id}>);" class="btn btn-sm btn-danger"><{$smarty.const._TAD_DEL}></a>
                    <a href="<{$xoops_url}>/modules/kw_club/club.php?class_id=<{$data.class_id}>" class="btn btn-sm btn-warning"><{$smarty.const._TAD_EDIT}></a>
                    <button class="btn btn-sm btn-primary" onclick="delClass_func( <{$data.class_id}> )">強制刪除此課程和報名資料</button>
                    
                  </li>
              <{/foreach}>
              <{/if}>
          </ul>
      <{/if}>
  </div>
</div>
