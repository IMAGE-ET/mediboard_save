<!-- $Id: vw_idx_planning.tpl 7567 2009-12-09 17:03:37Z MyttO $ -->

<script type="text/javascript">
  ColorSelector.init = function(index) {
    Form.onSubmitComplete.index = index;
    this.sForm  = 'Edit-Color-'+index;
    this.sColor = "color";
    this.sColorView = 'View-Color-'+index;
    this.pop();
  }

  ColorSelector.empty = function(index) {
    Form.onSubmitComplete.index = index;
    $('View-Color-'+index).setStyle({ background: '' });
    $V(getForm('Edit-Color-'+index).color, '');
  }

  Form.onSubmitComplete = function (guid, properties) {
    var id = guid.split('-')[1];
    var form = getForm('Edit-Color-'+Form.onSubmitComplete.index);
    $V(form.color_id, id);
  }
</script>

<table class="tbl">
  <tr>
    <th colspan="2">{{mb_title class=CColorLibelleSejour field=libelle}}</th>
    <th>{{mb_title class=CColorLibelleSejour field=color}}</th>
  </tr>

  {{foreach from=$libelle_counts key=libelle item=count name=color}}
    <tr>
      <td class="text">{{$libelle}}</td>
      <td>{{$count}}</td>
      <td>
        {{assign var=libelle value=$libelle|upper}}
        {{assign var=color value=$colors.$libelle}}
        {{assign var=index value=$smarty.foreach.color.index}}
        <form name="Edit-Color-{{$index}}" action="?" onsubmit="return onSubmitFormAjax(this);" method="post">
          <input type="hidden" name="m" value="dPplanningOp" />
          <input type="hidden" name="dosql" value="do_color_libelle_sejour_aed" />
          <input type="hidden" name="del" value="0" />
          {{mb_key object=$color}}

          {{mb_field object=$color field=libelle hidden=1}}
          {{mb_field object=$color field=color form=Edit-Color-$index onchange="this.form.onsubmit()"}}
        </form>

      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="3" class="empty">{{tr}}None{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>