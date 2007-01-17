      {{$errorMessage|nl2br|smarty:nodefaults}}
    </td>
  </tr>
</table>

<div id="console" style="display:none">
  <div id="console-title">Javascript console</div>
</div>

{{if $debugMode && !$offline}}
<div id="performance">
  PHP : {{$performance.genere}} secondes &ndash;
  Poids de la page : {{$performance.size}} &ndash;
  M�moire {{$performance.memoire}}
  <br />
  Erreurs : {{$performance.error}} &ndash;
  Alertes : {{$performance.warning}} &ndash;
  Notices : {{$performance.notice}}
  <br />
  Objets m�tier : {{$performance.objets}} &ndash;
  Objets en cache : {{$performance.cache}} &ndash;
  Classes auto-charg�es : {{$performance.autoload}}
  <br />
  Requ�tes SQL : 
  {{foreach from=$dbChronos item=currdbChrono key=keydbConfigName}}
  &ndash; {{$currdbChrono->nbSteps}} 
  sur '{{$keydbConfigName}}'
  en {{$currdbChrono->total|string_format:"%.3f"}} secondes
  {{/foreach}}
</div>
{{/if}}

{{if $demoVersion && !$offline}}
<div style="margin: 10px; float:right">
  <a href="http://www.sourceforge.net/projects/mediboard/" title="Projet Mediboard sur Sourceforge">
    <img src="http://www.sourceforge.net/sflogo.php?group_id=112072&amp;type=2" alt="Sourceforge Project Logo" />
  </a>
</div>
{{/if}}

</body>
</html>