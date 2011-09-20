{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>
    <th class="title" colspan="5">{{$table_description->description}}</th>
  </tr>
  <tr>
    <th style="width: 20px;">&nbsp;</th>
    <th class="category" style="width: 18%">{{mb_title object=$table_entry field=code_mb_from}}</th>
    <th class="category" style="width: 18%">{{mb_title object=$table_entry field=code_hl7}}</th>
    <th class="category" style="width: 18%">{{mb_title object=$table_entry field=code_mb_to}}</th>
    <th class="category">{{mb_title object=$table_entry field=description}}</th>
  </tr>
  {{foreach from=$table_entries item=_table_entry}}
    <tr>
      <td>
        {{if $_table_entry->user}}
          <form name="delTabEntry-{{$_table_entry->_id}}" action="?m=hl7" method="post" onsubmit="return onSubmitFormAjax(this, { 
            onComplete : loadEntries.curry('{{$_table_entry->number}}') });">
            <input type="hidden" name="m" value="hl7" />
            <input type="hidden" name="@class" value="{{$_table_entry->_class}}" />
            <input type="hidden" name="del" value="1" />
            {{mb_key object=$_table_entry}}
            <button type="submit" class="trash notext" style="margin: 0;">{{tr}}Delete{{/tr}}</button>
          </form>
        {{/if}}
      </td>
      <td class="disabled">
        <form name="editTabEntryMbCodeFrom-{{$_table_entry->_id}}" action="?m=hl7" method="post" onsubmit="return onSubmitFormAjax(this, { 
            onComplete : loadEntries.curry('{{$_table_entry->number}}') });">
          <input type="hidden" name="m" value="hl7" />
          <input type="hidden" name="@class" value="{{$_table_entry->_class}}" />
          {{mb_key object=$_table_entry}}
          {{mb_field object=$_table_entry field="code_mb_from" size="10"}}  <button type="submit" class="save notext" style="margin: 0;">{{tr}}Save{{/tr}}</button>
        </form>
      </td>
      <td class="disabled button">
        {{if !$_table_entry->user}}
          {{mb_value object=$_table_entry field="code_hl7"}}
        {{else}}
          <form name="editTabEntryHL7Code-{{$_table_entry->_id}}" action="?m=hl7" method="post" onsubmit="return onSubmitFormAjax(this, { 
            onComplete : loadEntries.curry('{{$_table_entry->number}}') });">
            <input type="hidden" name="m" value="hl7" />
            <input type="hidden" name="@class" value="{{$_table_entry->_class}}" />
            {{mb_key object=$_table_entry}}
            {{mb_field object=$_table_entry field="code_hl7" size="10"}}  <button type="submit" class="save notext" style="margin: 0;">{{tr}}Save{{/tr}}</button>
          </form>
        {{/if}}
      </td>
      <td class="disabled">
        <form name="editTabEntryMbCodeTo-{{$_table_entry->_id}}" action="?m=hl7" method="post" onsubmit="return onSubmitFormAjax(this, { 
            onComplete : loadEntries.curry('{{$_table_entry->number}}') });">
          <input type="hidden" name="m" value="hl7" />
          <input type="hidden" name="@class" value="{{$_table_entry->_class}}" />
          {{mb_key object=$_table_entry}}
          {{mb_field object=$_table_entry field="code_mb_to" size="10"}}  <button type="submit" class="save notext" style="margin: 0;">{{tr}}Save{{/tr}}</button>
        </form>
      </td>
      <td class="disabled text">
        {{if !$_table_entry->user}}
          {{mb_value object=$_table_entry field="description"}}
        {{else}}
          <form name="editTabEntryHL7Description-{{$_table_entry->_id}}" action="?m=hl7" method="post" onsubmit="return onSubmitFormAjax(this, { 
            onComplete : loadEntries.curry('{{$_table_entry->number}}') });">
            <input type="hidden" name="m" value="hl7" />
            <input type="hidden" name="@class" value="{{$_table_entry->_class}}" />
            {{mb_key object=$_table_entry}}
            {{mb_field object=$_table_entry field="description" size="40"}}  <button type="submit" class="save notext" style="margin: 0;">{{tr}}Save{{/tr}}</button>
          </form>
        {{/if}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="5" class="empty">{{tr}}CHL7v2TableEntry.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>

<form name="editTabEntryHL7Code" action="?m=hl7" method="post" onsubmit="return onSubmitFormAjax(this, { 
  onComplete : loadEntries.curry('{{$table_entry->number}}') });">
  <input type="hidden" name="m" value="hl7" />
  <input type="hidden" name="@class" value="{{$table_entry->_class}}" />
  {{mb_key object=$table_entry}}
  {{mb_field object=$table_entry field="number" hidden=true}}
  {{mb_field object=$table_entry field="user" hidden=true}}
  
  <table class="tbl">
    <tr>
      <td style="width: 20px;">
        <button type="submit" class="add notext" style="margin: 0;">{{tr}}Save{{/tr}}</button>
      </td>
      <td class="disabled" style="width: 18%">
        {{mb_field object=$table_entry field="code_mb_from" size="10"}} 
      </td> 
      <td class="disabled button" style="width: 18%">
        {{mb_field object=$table_entry field="code_hl7" size="10"}} 
      </td> 
      <td class="disabled" style="width: 18%">
        {{mb_field object=$table_entry field="code_mb_to" size="10"}} 
      </td> 
      <td class="disabled">
        {{mb_field object=$table_entry field="description" size="40"}} 
      </td>   
    </tr>
  </table>
</form>