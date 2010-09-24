<applet id="uploader" name="yopletuploader" width="0" height="0"
        code="org.yoplet.Yoplet.class" archive="includes/applets/yoplet2.jar">
  <param name="debug" value="false">
  <param name="action" value="">
  <param name="url" value="{{$base_url}}/modules/dPfiles/ajax_yoplet_upload.php">
  <param name="content" value="a">
</applet>

<div id="modal-yoplet" style="display: none;">
  <form name="addFastFile" method="post" action="?"
    onsubmit="return onSubmitFormAjax(this);">
    <input type="hidden" name="m" value="dPfiles" />
    <input type="hidden" name="dosql" value="do_file_aed" />
    <input type="hidden" name="_from_yoplet" value="1" />
    <input type="hidden" name="object_class" value="" />
    <input type="hidden" name="private" value="0" />
    <input type="hidden" name="object_id" value="" />
    <input type="hidden" name="file_date" value="now" />
    <input type="hidden" name="callback" value="File.applet.addfile_callback" />
    <input type="hidden" name="_index" value="" />
    <input type="hidden" name="_file_path" value="" />
    <input type="hidden" name="_checksum" value="" />
    
    <table class="tbl">
      <tr>
        <th class="title" colspan="4">
        	Fichiers disponibles dans : 
					<br />
 					{{$app->user_prefs.directory_to_watch}}
				</th>
      </tr>
			<tr>
        <th>{{mb_title class=CFile field=file_name}}</th>
        <th style="width: 2em;">
				  <img src="style/mediboard/images/buttons/change.png" title="{{tr}}Send{{/tr}}"/>
				</th>
        <th style="width: 2em;">
          <img src="style/mediboard/images/buttons/merge.png" title="{{tr}}Link{{/tr}}"/>
				</th>
        <th style="width: 2em;">
          <img src="style/mediboard/images/buttons/trash.png" title="{{tr}}Delete{{/tr}}"/>
				</th>
			</tr>
      <tbody id="file-list">
      </tbody>
		</table>
		
		<hr />
		
		<table class="form">
      <tr>
        <th style="width: 1px;">
          {{mb_label class=CFile field=_rename}}
        </th>
				<td>
          <input type="text" name="file_rename" value="" />
        </td>
      </tr>
      <tr>
        <th>
          {{mb_label class=CFile field=file_category_id}}
        </th>
        <td>
          <select name="file_category_id">
            <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          </select>
        </td>
      </tr>
      <tr>
        <td colspan="2">
				  <input type="checkbox" name="delete_auto" checked="true" />
					<label for="delete_auto">{{tr}}Delete after send{{/tr}}</label>
				</td>
      </tr>
      <tr>
        <td class="button" colspan="2">
	        <button type="button" class="cancel" onclick="File.applet.cancelModal();">
	        	{{tr}}Cancel{{/tr}}
					</button>
	        <button type="button" class="change uploadinmodal" onclick="this.disabled = 'disabled'; File.applet.uploadFiles();">
	          {{tr}}Upload{{/tr}}
					</button>
	        <button type="button" class="tick" onclick="File.applet.closeModal();">
	          {{tr}}Close{{/tr}}
	        </button>
        </td>
      </tr>
    </table>
  </form>
</div>