
<script type="text/javascript">
  Main.add(function() {
    Calendar.regField(getForm("changeDate").elements["date"], null, {noView: true});
  });
</script>
<table class="main">
  <tr>
    <td style="width: 100px">
      <table class="tbl" style="text-align: center;">
        <tr>
          <th class="title" colspan="4">
            <a style="display: inline;" href="?m=maternite&tab=vw_admissions&date={{$prev_month}}">&lt;&lt;&lt;</a>
            {{$date|date_format:"%b %Y"}}
            <a style="display: inline;" href="?m=maternite&tab=vw_admissions&date={{$next_month}}">&gt;&gt;&gt;</a>
          </th>
        </tr>
        <tr>
          <th rowspan="2">Date</th>
        </tr>
        <tr>
          <th class="text">
            <a class="{{if $view == "all"}}selected{{else}}selectable{{/if}}" title="Toutes les admissions"
              href="?m=maternite&&tab=vw_idx_admission&view=all">
              Adm.
            </a>
          </th>
          <th class="text">
            <a class="{{if $view == "non_prep"}}selected{{else}}selectable{{/if}}" title="Admissions non pr�par�es"
              href="?m=maternite&&tab=vw_idx_admission&view=non_prep">
              Non pr�p.
            </a>
          </th>
          <th class="text">
            <a class="{{if $view == "non_eff"}}selected{{else}}selectable{{/if}}" title="Admissions non effectu�es"
              href="?m=maternite&&tab=vw_idx_admission&view=non_eff">
              Non eff.
            </a>
          </th>
        </tr>
      
        {{foreach from=$days key=day item=counts}}
        <tr {{if $day == $date}}class="selected"{{/if}}>
          {{assign var=day_number value=$day|date_format:"%w"}}
          <td align="right"
            {{if in_array($day, $bank_holidays)}}
              style="background-color: #fc0"
            {{elseif $day_number == '0' || $day_number == '6'}}
              style="background-color: #ccc;"
            {{/if}}>
            <a href="?m={{$m}}&amp;tab=vw_idx_admission&amp;date={{$day|iso_date}}" title="{{$day|date_format:$conf.longdate}}">
              <strong>
                {{$day|date_format:"%a"|upper|substr:0:1}}
                {{$day|date_format:"%d"}}
              </strong>
            </a>
          </td>
          <td {{if $view == "all" && $day == $date}}style="font-weight: bold;"{{/if}}>
            {{if $counts.num1}}{{$counts.num1}}{{else}}-{{/if}}
          </td>
          <td {{if $view == "non_prep" && $day == $date}}style="font-weight: bold;"{{/if}}>
            {{if $counts.num3}}{{$counts.num3}}{{else}}-{{/if}}
          </td>
          <td {{if $view == "non_eff" && $day == $date}}style="font-weight: bold;"{{/if}}>
            {{if $counts.num2}}{{$counts.num2}}{{else}}-{{/if}}
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    <td>
      <table class="tbl">
        <tr>
          <th class="title" colspan="4">
            <strong>
              <a href="?m=maternite&tab=vw_admissions&date={{$date_before}}" style="display: inline;">&lt;&lt;&lt;</a>
              {{$date|date_format:$conf.longdate}}
              <form name="changeDate" method="get">
                <input type="hidden" name="m" value="maternite" />
                <input type="hidden" name="tab" value="vw_admissions" />
                <input type="hidden" name="date" value="{{$date}}" onchange="this.form.submit();"/>
                
              </form>
              <a href="?m=maternite&tab=vw_admissions&date={{$date_after}}" style="display: inline;">&gt;&gt;&gt;</a>
            </strong>
          </th>
        </tr>
        <tr>
          <th class="category narrow">Admettre</th>
          <th class="category narrow">{{tr}}CPatient{{/tr}}</th>
          <th class="category narrow">Terme</th>
          <th class="category">Praticiens</th>
        </tr>
        {{foreach from=$sejours item=_sejour}}
          {{assign var=grossesse value=$_sejour->_ref_grossesse}}
          {{assign var=patient value=$_sejour->_ref_patient}}
          <tr>
            <td>
              <form name="editSejour-{{$_sejour->_id}}" method="post">
                <input type="hidden" name="m" value="dPplanningOp" />
                <input type="hidden" name="dosql" value="do_sejour_aed" />
                {{mb_key object=$_sejour}}
                {{mb_field object=$_sejour field=entree_reelle hidden=true}}
                {{if $_sejour->entree_reelle}}
                  <button type="button" onclick="$V(this.form.entree_reelle, ''); this.form.submit();" class="cancel">{{tr}}Cancel{{/tr}}</button>
                {{else}}
                  <button type="button" onclick="$V(this.form.entree_reelle, 'now'); this.form.submit();" class="tick">{{tr}}CSejour-admit{{/tr}}</button>
                {{/if}}
              </form>
            </td>
            <td>
              <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">{{$_sejour->_ref_patient}}</span>
            </td>
            <td>
              {{$grossesse->terme_prevu|date_format:$conf.date}}
            </td>
            <td>
              {{foreach from=$grossesse->_praticiens item=_praticien}}
                {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_praticien}}
              {{/foreach}}
            </td>
          </tr>
        {{foreachelse}}
          <tr>
            <td colspan="4" class="empty">{{tr}}CSejour.none{{/tr}}</td>
          </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>