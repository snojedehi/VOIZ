<div class="container">
  {* {foreach from=$trunks item=trunk}
   <div>{$trunk}a</div>
 {/foreach} *}
  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
    تماس گیر جدید
  </button>
  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="addCall" value="true" />
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">تماس خودکار جدید</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div style="max-width:90%">
              <div class="form-group">
                <label for="call-ext">نام</label>
                <input type="text" name="name" class="form-control" id="call-ext" aria-describedby="name-help">
                <small id="name-help" class="form-text text-muted">
                  چنانچه شما نیاز داشتید به شماره های تماس وارد شده، رقم یا ارقامی اضافه گردد می توانید از این گزینه
                  استفاده نمایید
                </small>
              </div>
              <div class="form-group">
                <label for="call-ext">رقم اضافه شونده به شماره ها</label>
                <input type="text" name="prefix" class="form-control" id="prefix" aria-describedby="prefix-help">
                <small id="prefix-help" class="form-text text-muted">
                  چنانچه شما نیاز داشتید به شماره های تماس وارد شده، رقم یا ارقامی اضافه گردد می توانید از این گزینه
                  استفاده نمایید
                </small>
              </div>
              <div class="form-group">
                <label for="count-ext">تعداد تکرار فایل صوتی</label>
                <input type="text" class="form-control" name="repeat" id="count-ext" aria-describedby="count-ext-help">
                <small id="count-ext-help" class="form-text text-muted">
                  فایل صوتی آپلود شده در گروه تماسگیر چند مرتبه برای مخاطب پخش گردد
                </small>
              </div>
              <div class="form-group">
                <label for="trunk">ترانک خروجی</label>
                <select class="form-control" id="trunk" name="trunk">
                  {foreach from=$trunks item=trunk}
                    <option value="{$trunk.id}">{$trunk.name}</option>
                  {/foreach}
                </select>
              </div>
              <div class="form-group">
                <label for="count-ext">صوت</label>
                <input type="file" class="form-control-file" name="sound" id="sound" aria-describedby="sound-ext-help">
                <small id="sound-ext-help" class="form-text text-muted">
                </small>
              </div>
              <div class="form-group">
                <label for="numbers">شماره ها</label>
                <textarea class="form-control" id="numbers" name="numbers"></textarea>
              </div>
              <div class="form-group">
                <label for="call-ext">آدرس هوک:</label>
                <input type="text" name="hook" class="form-control" id="hook" aria-describedby="name-help">
                <small id="name-help" class="form-text text-muted">
                  پیش از ارسال به مقصد بعدی به این آدرس ارسال شود
                </small>
              </div>
              <div class="form-group form-check ">
                <input type="checkbox" class="form-check-input" name="status" id="status" checked>
                <label class="form-check-label" for="exampleCheck1">وضعیت</label>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">خروج</button>
            <input type="submit" class="btn btn-primary" value="ذخیره" />
          </div>
        </div>
      </div>
    </form>
  </div>

  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="editReq" id="editReq" value="" />
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">تماس خودکار جدید</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div style="max-width:90%">
              <div class="form-group form-check ">
                <input type="checkbox" class="form-check-input" name="status" id="editStatus" checked>
                <label class="form-check-label" for="exampleCheck1">وضعیت</label>
              </div>
              <div class="form-group">
                <label for="call-ext">نام</label>
                <input type="text" name="name" class="form-control" id="editName" aria-describedby="call-ext-help">
                <small id="call-ext-help" class="form-text text-muted">
                  چنانچه شما نیاز داشتید به شماره های تماس وارد شده، رقم یا ارقامی اضافه گردد می توانید از این گزینه
                  استفاده نمایید
                </small>
              </div>
              <div class="form-group">
                <label for="call-ext">رقم اضافه شونده به شماره ها</label>
                <input type="text" name="prefix" class="form-control" id="editPrefix" aria-describedby="prefix-help">
                <small id="call-ext-help" class="form-text text-muted">
                  چنانچه شما نیاز داشتید به شماره های تماس وارد شده، رقم یا ارقامی اضافه گردد می توانید از این گزینه
                  استفاده نمایید
                </small>
              </div>
              <div class="form-group">
                <label for="count-ext">تعداد تکرار فایل صوتی</label>
                <input type="text" class="form-control" name="repeat" id="editRepeat" aria-describedby="count-ext-help">
                <small id="count-ext-help" class="form-text text-muted">
                  فایل صوتی آپلود شده در گروه تماسگیر چند مرتبه برای مخاطب پخش گردد
                </small>
              </div>
              <div class="form-group">
                <label for="trunk">ترانک خروجی</label>
                <select class="form-control" id="editTrunk" name="trunk">
                  {foreach from=$trunks item=trunk}
                    <option value="{$trunk.id}">{$trunk.name}</option>
                  {/foreach}
                </select>
              </div>
              <div class="form-group">
                <label for="count-ext">صوت</label>
                <input type="file" class="form-control-file" name="sound" id="editSound"
                  aria-describedby="sound-ext-help">
                <small id="sound-ext-help" class="form-text text-muted">
                </small>
              </div>
              <div class="form-group">
                <label for="numbers">شماره ها</label>
                <textarea class="form-control" id="editNumbers" name="numbers"></textarea>
              </div>
              <div class="form-group">
                <label for="editHook">آدرس هوک:</label>
                <input type="text" name="hook" class="form-control" id="editHook" aria-describedby="name-help" />
                <small id="name-help" class="form-text text-muted">
                  پیش از ارسال به مقصد بعدی به این آدرس ارسال شود
                </small>
              </div>
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="inputCity">ورودی</label>
                  <input type="text" class="form-control" name="inp[]">
                </div>
                <div class="form-group col-md-6">
                  <label for="inputState">مقصد</label>
                  <input type="text" class="form-control" name="des[]">
                </div>
              </div>

            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">خروج</button>
            <input type="submit" class="btn btn-primary" value="ذخیره" />
          </div>
        </div>
      </div>
    </form>
  </div>
  //{$novoip_data}//

</div>