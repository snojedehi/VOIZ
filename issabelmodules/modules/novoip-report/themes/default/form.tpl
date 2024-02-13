<div class="container" >
// {foreach from=$trunks item=trunk}
//   <div>{$trunk}a</div>
// {/foreach}
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
  تماس گیر جدید
</button>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div style="max-width:90%">
        <form method="post" >
          <div class="form-group">
            <label for="call-ext">رقم اضافه شونده به شماره ها</label>
            <input type="text" name="call-ext" class="form-control" id="call-ext" aria-describedby="call-ext-help">
            <small id="call-ext-help" class="form-text text-muted">
            چنانچه شما نیاز  داشتید به شماره های تماس وارد شده، رقم یا ارقامی اضافه گردد می توانید از این گزینه استفاده نمایید
            </small>
          </div>
          <div class="form-group">
            <label for="count-ext">تعداد تکرار فایل صوتی</label>
            <input type="text" class="form-control" id="count-ext" aria-describedby="count-ext-help">
            <small id="count-ext-help" class="form-text text-muted">
            فایل صوتی آپلود شده در گروه تماسگیر چند مرتبه برای مخاطب پخش گردد    
            </small>
          </div>
          <div class="form-group" >
            <label for="trunk">ترانک خروجی</label>
            <select class="form-control" id="trunk">
              {foreach from=$trunks item=trunk} 
              <option>{$trunk}</option>
              {/foreach}
              
            </select>
          </div>
          <div class="form-group form-check ">
            <input type="checkbox" class="form-check-input" name="status" id="status">
            <label class="form-check-label" for="exampleCheck1">وضعیت</label>
          </div>
          
        </form>   
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">خروج</button>
        <button type="button" class="btn btn-primary">ذخیره</button>
      </div>
    </div>
  </div>
</div>
    {$novoip_data}
    
</div>