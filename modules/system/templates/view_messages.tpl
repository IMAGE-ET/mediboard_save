<!-- $Id$ -->

<script type="text/javascript">

function pageMain() {
  regFieldCalendar("editFrm", "deb", true);
  regFieldCalendar("editFrm", "fin", true);
}

</script>

<table class="main">

<tr>
  <td class="greedyPane">

    <form name="filterFrm" action="?" method="get">
    
    <input type="hidden" name="m" value="{{$m}}" />
        
    <table class="form">
      <tr>
        <th class="category" colspan="10">Filtrer les messages</th>
      </tr>

      <tr>
        <th><label for="filter_status" title="Filtrer les messages">Filtrer</label></th>
        <td>
          <select name="filter_status" onchange="this.form.submit()">
           {{html_options options=$mp_status selected=$filter_status}}
          </select>
        </td>
      </tr>
    </table>

    </form>
    
    <table class="tbl">
    
    <tr>
      <th colspan="10"><strong>Liste des messages</strong></th>
    </tr>
    
    <tr>
      <th>Titre</th>
      <th>D�but de publication</th>
      <th>Fin de publication</th>
    </tr>

    {{foreach from=$messages item=curr_message}}
    <tbody class="hoverable">
    <tr>
      {{assign var="message_id" value=$curr_message->message_id}}
      {{assign var="href" value="?m=$m&tab=$tab&message_id=$message_id"}}
      <td {{if $curr_message->urgence == "urgent"}}class="highlight"{{/if}}>
        <strong><a href="{{$href}}">{{$curr_message->titre}}</a></strong>
      </td>
      <td><a href="{{$href}}">{{$curr_message->deb|date_format:"%d/%m/%Y %H:%M"}}</a></td>
      <td><a href="{{$href}}">{{$curr_message->fin|date_format:"%d/%m/%Y %H:%M"}}</a></td>
    </tr>
    <tr>
      <td class="text" colspan="10">{{$curr_message->corps|nl2br}}</td>
    </tr>
    </tbody>
    {{/foreach}}
      
    </table>

  </td>
  
  <td class="pane">

  <a class="buttonnew" href="?m={{$m}}&amp;tab={{$tab}}&amp;message_id=0"><strong>Cr�er un message</strong></a>

    <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

    <input type="hidden" name="dosql" value="do_message_aed" />
    <input type="hidden" name="message_id" value="{{$message->message_id}}" />
    <input type="hidden" name="del" value="0" />

    <table class="form">

    <tr>
      <th class="category" colspan="2">
      {{if $message->message_id}}
        Modification d'un message
      {{else}}
        Cr�ation d'un message
      {{/if}}
      </th>
    </tr>

    <tr>
      <th>{{mb_label object=$message field="deb"}}</th>
      <td class="date">{{mb_field object=$message field="deb" form="editFrm"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$message field="fin"}}</th>
      <td class="date">{{mb_field object=$message field="fin" form="editFrm"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$message field="urgence"}}</th>
      <td class="date">{{mb_field object=$message field="urgence"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$message field="titre"}}</th>
      <td>{{mb_field object=$message field="titre"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$message field="corps"}}</th>
      <td>{{mb_field object=$message field="corps"}}</td>
    </tr>

    <tr>
      <td class="button" colspan="2">
        {{if $message->message_id}}
        <button class="modify" type="submit">Valider</button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'message',objName:'{{$message->_view|smarty:nodefaults|JSAttribute}}'})">
          Supprimer
        </button>
        {{else}}
        <button class="submit" type="submit">Cr�er</button>
        {{/if}}
      </td>
    </tr>

    </table>
    
    </form>

  </td>
</tr>

</table>

