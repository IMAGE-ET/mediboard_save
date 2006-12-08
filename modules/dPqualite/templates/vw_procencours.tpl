<script language="Javascript" type="text/javascript">

function popFile(objectClass, objectId, elementClass, elementId){
  var url = new Url;
  url.addParam("nonavig", 1);
  url.ViewFilePopup(objectClass, objectId, elementClass, elementId, 0);
}

</script>
<table class="main">
  <tr>
    <td class="halfPane">
      <a class="buttonnew" href="index.php?m=dPqualite&amp;tab=vw_procencours&amp;doc_ged_id=0">
        {{tr}}CDocGed.create{{/tr}}
      </a>
      
      {{if $procTermine|@count}}
      <table class="form">
        <tr>
          <th class="category" colspan="5">
            {{tr}}Informations{{/tr}}
          </th>
        </tr>
        <tr>
          <th class="category">{{tr}}Date{{/tr}}</th>
          <th class="category">{{tr}}_CDocGed_demande{{/tr}}</th>
          <th class="category">{{tr}}CDocGed-etat{{/tr}}</th>
          <th class="category">{{tr}}CDocGedSuivi-remarques{{/tr}}</th>
          <th class="category"></th>
        </tr>
        {{foreach from=$procTermine item=currProc}}
        <tr>
          <td class="text">
            {{$currProc->_lastentry->date|date_format:"%d %b %Y � %Hh%M"}}
          </td>
          <td class="text">
            {{if $currProc->_lastactif->doc_ged_suivi_id}}
            {{tr}}_CDocGed_revision{{/tr}} {{$currProc->_reference_doc}}
            {{else}}
            {{tr}}_CDocGed_new{{/tr}}
            {{/if}}
          </td>
          
          {{if $currProc->_lastactif->doc_ged_suivi_id && $currProc->_lastactif->doc_ged_suivi_id>$currProc->_firstentry->doc_ged_suivi_id}} 
          <td class="text">
            <strong>{{tr}}_CDocGed_accepte{{/tr}}</strong>
          {{else}}
          <td class="text" style="color: #f00;">
            <strong>{{tr}}_CDocGed_refuse{{/tr}}</strong>
          {{/if}}
          </td>
          <td class="text">
            {{$currProc->_lastentry->remarques|nl2br}}
          </td>
          <td class="text">
            <form name="ProcInfos{{$currProc->doc_ged_id}}Frm" action="?m={{$m}}" method="post">
            <input type="hidden" name="dosql" value="do_docged_aed" />
            <input type="hidden" name="m" value="{{$m}}" />
            <input type="hidden" name="ged[doc_ged_id]" value="{{$currProc->doc_ged_id}}" />  
            <input type="hidden" name="ged[user_id]" value="" />  
            <input type="hidden" name="_validation" value="1" />
            {{if $currProc->_lastactif->doc_ged_suivi_id && $currProc->_lastactif->doc_ged_suivi_id>$currProc->_firstentry->doc_ged_suivi_id}} 
            <input type="hidden" name="del" value="0" />  
            <button type="submit" class="tick">
              {{tr}}OK{{/tr}}
            </button>
            {{else}}
            <input type="hidden" name="del" value="1" />  
            <button type="submit" class="trash">
              {{tr}}OK{{/tr}}
            </button>
            {{/if}}            
            </form>
          </td>
        </tr>
        {{/foreach}}
      </table><br /><br />
      {{/if}}
      
      
      {{if $procDemande|@count}}
      <table class="form">
        <tr>
          <th class="category" colspan="4">
            {{tr}}_CDocGed_attente_demande{{/tr}}
          </th>
        </tr>
        <tr>
          <th class="category">{{tr}}_CDocGed_demande{{/tr}}</th>
          <th class="category">{{tr}}CDocGed-group_id{{/tr}}</th>
          <th class="category">{{tr}}Date{{/tr}}</th>
          <th class="category">{{tr}}CDocGedSuivi-remarques{{/tr}}</th>
        </tr>
        {{foreach from=$procDemande item=currProc}}
        <tr>
          <td class="text">
            <form name="ProcDem{{$currProc->doc_ged_id}}Frm" action="?m={{$m}}" method="post">
            <input type="hidden" name="dosql" value="do_docged_aed" />
            <input type="hidden" name="m" value="{{$m}}" />
            <input type="hidden" name="ged[doc_ged_id]" value="{{$currProc->doc_ged_id}}" />
            <input type="hidden" name="del" value="0" />
            {{assign var="date_proc" value=$currProc->_lastentry->date|date_format:"%d %b %Y � %Hh%M"}}
            <a class="buttonedit notext" href="index.php?m=dPqualite&amp;tab=vw_procencours&amp;doc_ged_id={{$currProc->doc_ged_id}}"></a>            {{if $currProc->_lastactif->doc_ged_suivi_id}}
              {{tr}}_CDocGed_revision{{/tr}} {{$currProc->_reference_doc}}
            {{else}}
              {{tr}}_CDocGed_new{{/tr}}
            {{/if}}
            </form>
          </td>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_procvalid&amp;doc_ged_id={{$currProc->doc_ged_id}}">
              {{$currProc->_ref_group->text}}
            </a>
          </td>
          <td class="text">
            {{$currProc->_lastentry->date|date_format:"%A %d %B %Y � %Hh%M"}}
          </td>
          <td class="text">
            {{$currProc->_lastentry->remarques|nl2br}}
          </td>
        </tr>
        {{/foreach}}
      </table><br /><br />      
      {{/if}}
      
      <table class="form">
        <tr>
          <th class="category" colspan="5">{{tr}}_CDocGed_attente_demande{{/tr}}</th>
        </tr>
        <tr>
          <th class="category">{{tr}}CDocGed-titre{{/tr}}</th>
          <th class="category">{{tr}}CDocGed-_reference_doc{{/tr}}</th>
          <th class="category">{{tr}}CDocGed-group_id{{/tr}}</th>
          <th class="category">{{tr}}CDocGed-doc_theme_id{{/tr}}</th>
          <th class="category">{{tr}}CDocGed-etat{{/tr}}</th>
        </tr>
        {{foreach from=$procEnCours item=currProc}}
        <tr>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_procencours&amp;doc_ged_id={{$currProc->doc_ged_id}}">
              {{$currProc->titre}}
            </a>
          </td>
          <td>
            <a href="index.php?m=dPqualite&amp;tab=vw_procencours&amp;doc_ged_id={{$currProc->doc_ged_id}}">
              {{$currProc->_reference_doc}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_procencours&amp;doc_ged_id={{$currProc->doc_ged_id}}">
              {{$currProc->_ref_group->text}}
            </a>
          </td>
          <td class="text">{{$currProc->_ref_theme->nom}}</td>
          <td>{{$currProc->_etat_actuel}}</td>
        </tr>
        {{foreachelse}}
        <tr>
          <td colspan="5">
            {{tr}}CDocGed.none{{/tr}}
          </td>
        </tr>
        {{/foreach}}
      </table>

    </td>
    <td class="halfPane">      
      <form name="ProcEditFrm" action="?m={{$m}}" method="post" enctype="multipart/form-data" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_docged_aed" />
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />      
      
      <input type="hidden" name="ged[doc_ged_id]" value="{{$docGed->doc_ged_id}}" />  
      <input type="hidden" name="ged[user_id]" value="{{$user_id}}" />
      <input type="hidden" name="ged[annule]" value="{{$docGed->annule}}" />
            
      <input type="hidden" name="suivi[user_id]" value="{{$user_id}}" />  
      <input type="hidden" name="suivi[actif]" value="0" /> 
      <input type="hidden" name="suivi[file_id]" value="" />    
      
      <table class="form">
        <tr>          
          {{if $docGed->doc_ged_id && ($docGed->etat==CDOC_DEMANDE || $docGed->etat==CDOC_TERMINE)}}
            <th class="title modify" colspan="2">
              <input type="hidden" name="ged[etat]" value="{{$smarty.const.CDOC_DEMANDE}}" />
              <input type="hidden" name="suivi[etat]" value="{{$smarty.const.CDOC_DEMANDE}}" />
              {{if $docGed->etat==CDOC_TERMINE}}
              <input type="hidden" name="suivi[doc_ged_suivi_id]" value="" />
              {{tr}}msg-CDocGed-title-modify-demande{{/tr}}
              {{else}}
              <input type="hidden" name="suivi[doc_ged_suivi_id]" value="{{$docGed->_lastentry->doc_ged_suivi_id}}" />              
              {{tr}}msg-CDocGed-title-modify{{/tr}}
              {{/if}}
            </th>
          {{elseif $docGed->doc_ged_id && $docGed->etat==CDOC_REDAC}}
            <th class="title" colspan="2">
              <input type="hidden" name="ged[etat]" value="{{$smarty.const.CDOC_VALID}}" />
              <input type="hidden" name="suivi[etat]" value="{{$smarty.const.CDOC_REDAC}}" />
              <input type="hidden" name="suivi[doc_ged_suivi_id]" value="" />
              {{tr}}msg-CDocGed-etatredac_CDOC_REDAC{{/tr}}
            </th>
          {{elseif $docGed->doc_ged_id}}
            <th class="title modify" colspan="2">
              {{tr}}msg-CDocGed-title-valid{{/tr}}
            </th>
          {{else}}
            <th class="title" colspan="2">
              <input type="hidden" name="ged[etat]" value="{{$smarty.const.CDOC_DEMANDE}}" />
              <input type="hidden" name="suivi[etat]" value="{{$smarty.const.CDOC_DEMANDE}}" />
              <input type="hidden" name="suivi[doc_ged_suivi_id]" value="" />
              {{tr}}msg-CDocGed-title-create{{/tr}}
            </th>
          {{/if}}                    
        </tr>       
        {{if $docGed->etat==CDOC_TERMINE || $docGed->etat==CDOC_DEMANDE || !$docGed->doc_ged_id}}
          {{if $docGed->doc_ged_id && $docGed->etat!=CDOC_TERMINE}}
          <tr>
            <th>{{tr}}Date{{/tr}}</th>
            <td>{{$docGed->_lastentry->date|date_format:"%A %d %B %Y � %Hh%M"}}</td>
          </tr>
          {{/if}}
          <tr>
            <th>{{tr}}CDocGedSuivi-doc_ged_suivi_id-court{{/tr}}</th>
            <td>
              {{if $docGed->doc_ged_id && $docGed->_lastactif->doc_ged_suivi_id}}
                {{tr}}_CDocGed_revision{{/tr}} {{$docGed->_reference_doc}}<br />
                {{tr}}CDocGed-doc_theme_id{{/tr}} : {{$docGed->_ref_theme->nom}}
              {{else}}
                {{tr}}_CDocGed_new{{/tr}}
              {{/if}}
            </td>
          </tr>
          <tr>
            <th>
              <label for="ged[group_id]" title="{{tr}}CDocGed-group_id-desc{{/tr}}">{{tr}}CDocGed-group_id{{/tr}}</label>
            </th>
            <td colspan="2">
              <select title="{{$docGed->_props.group_id}}" name="ged[group_id]">
              {{foreach from=$etablissements item=curr_etab}}
                <option value="{{$curr_etab->group_id}}" {{if ($docGed->doc_ged_id && $docGed->group_id==$curr_etab->group_id) || (!$docGed->doc_ged_id && $g==$curr_etab->group_id)}} selected="selected"{{/if}}>{{$curr_etab->text}}</option>
              {{/foreach}}
              </select>
            </td>
          </tr>
          <tr>
            <th><label for="suivi[remarques]" title="{{tr}}CDocGedSuivi-remarques-desc{{/tr}}">{{tr}}CDocGedSuivi-remarques{{/tr}}</label></th>
            <td>
              <textarea name="suivi[remarques]" title="{{$docGed->_lastentry->_props.remarques}}|notNull">{{$docGed->_lastentry->remarques}}</textarea>
            </td>
          </tr>
          <tr>
            <td colspan="2" class="button">
              {{if $docGed->doc_ged_id && $docGed->etat!=CDOC_TERMINE}}
              <button class="modify" type="submit">
                {{tr}}Modify{{/tr}}
              </button>
              {{assign var="date_proc" value=$docGed->_lastentry->date|date_format:"%d %b %Y � %Hh%M"}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form, {typeName:'{{tr escape="javascript"}}CDocGed.one{{/tr}}',objName:'{{$date_proc|smarty:nodefaults|JSAttribute}}'})" title="{{tr}}Delete{{/tr}}">
                {{tr}}Delete{{/tr}}
              </button>              
              {{else}}
              <button class="modify" type="submit">
                {{tr}}Create{{/tr}}
              </button>              
              {{/if}}
            </td>
          </tr>
        {{elseif $docGed->etat==CDOC_REDAC}}
          <tr>
            <th>{{tr}}CDocGed.one{{/tr}}</th>
            <td>
              {{$docGed->_reference_doc}}
              <input type="hidden" name="file_class" value="CDocGed" />
              <input type="hidden" name="file_object_id" value="{{$docGed->doc_ged_id}}" />
              <input type="hidden" name="file_category_id" value="" />
            </td>
          </tr>
          <tr>
            <th>{{tr}}CDocGed-titre{{/tr}}</th>
            <td>{{$docGed->titre}}</td>
          </tr>
          <tr>
            <th>{{tr}}_CDocGed_validBy{{/tr}}</th>
            <td class="text">{{$docGed->_lastentry->_ref_user->_view}} ({{$docGed->_lastentry->date|date_format:"%d %B %Y - %Hh%M"}})</td>
          </tr>
          {{if $docGed->_lastentry->file_id}}
          <tr>
            <th>{{tr}}_CDocGed_lastfile{{/tr}}</th>
            <td>
              <a href="#" onclick="popFile('{{$docGed->_class_name}}','{{$docGed->_id}}','CFile','{{$docGed->_lastentry->file_id}}')" title="{{tr}}msg-CFile-viewfile{{/tr}}">
                <img src="index.php?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$docGed->_lastentry->file_id}}&amp;phpThumb=1&amp;wl=64&amp;hp=64" alt="-" />
              </a>
            </td>
          </tr>
          {{/if}}
          <tr>
            <th>{{tr}}_CDocGed_lastcomm{{/tr}}</th>
            <td class="text">
              {{$docGed->_lastentry->remarques|nl2br}}
            </td>
          </tr>          
          
          <tr>
            <th><label for="formfile">{{tr}}File{{/tr}}</label></th>
            <td>
              <input type="file" name="formfile" size="0" title="str|notNull" />
            </td>
          </tr>
          <tr>
            <th><label for="suivi[remarques]" title="{{tr}}CDocGedSuivi-remarques-desc{{/tr}}">{{tr}}CDocGedSuivi-remarques{{/tr}}</label></th>
            <td>
              <textarea name="suivi[remarques]" title="{{$docGed->_lastentry->_props.remarques}}|notNull"></textarea>
            </td>
          </tr>
          <tr>
            <td colspan="2" class="button">
              <button class="modify" type="submit">
                {{tr}}Add{{/tr}}
              </button>
            </td>
          </tr>
        {{else}}
          <tr>
            <td class="button text" colspan="2">
              <br />{{tr}}_CDocGed_valid{{/tr}}
              <br />
              <a href="#" onclick="popFile('{{$docGed->_class_name}}','{{$docGed->_id}}','CFile','{{$docGed->_lastentry->file_id}}')" title="{{tr}}msg-CFile-viewfile{{/tr}}">
                <img src="index.php?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$docGed->_lastentry->file_id}}&amp;phpThumb=1&amp;wl=64&amp;hp=64" alt="-" />
              </a>
              <br />{{$docGed->_reference_doc}}
              <br />{{$docGed->_lastentry->date|date_format:"%d %B %Y - %Hh%M"}}
            </td>
          </tr>
        {{/if}}
      </table>
      </form>
    </td>
  </tr>
</table>