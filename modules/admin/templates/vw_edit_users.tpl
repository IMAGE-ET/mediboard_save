<!-- $Id$ -->

<table class="main">
  <tr>
    <td class="halfPane">
      <a class="buttonnew" href="?m={{$m}}&amp;user_id=0">
        Cr�er un nouvel utilisateur
      </a>
      {{include file="inc_list_users.tpl"}}
    </td>
    <td class="halfPane">
      {{if $can->edit}}
      {{include file="inc_edit_user.tpl"}}
      {{else}}
      {{include file="inc_vw_user.tpl"}}
      {{/if}}
    </td>
  </tr>
</table>