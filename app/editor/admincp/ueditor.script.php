<script type="text/javascript">
window.catchRemoteImageEnable = <?php echo iCMS::$config['catch_remote']=="1"?'true':'false';?>;
</script>
<script type="text/javascript" charset="utf-8" src="./app/admincp/ui/iCMS.ueditor.js"></script>
<script type="text/javascript" charset="utf-8" src="./app/admincp/ui/ueditor/ueditor.all.min.js"></script>
<script>
$(function(){
    iCMS.editor.create("<?php echo $id;?>");
})
</script>
<div class="input-prepend">
  <div class="btn-group">
    <button type="button" class="btn" onclick="javascript:iCMS.editor.insPageBreak();"><i class="fa fa-ellipsis-h"></i> 插入分页符</button>
    <button type="button" class="btn" onclick="javascript:iCMS.editor.delPageBreakflag();"><i class="fa fa-ban"></i> 删除分页符</button>
    <button type="button" class="btn" onclick="javascript:iCMS.editor.cleanup();"><i class="fa fa-magic"></i> 自动排版</button>
  </div>
</div>
