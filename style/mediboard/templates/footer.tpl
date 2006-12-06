      {{$errorMessage|smarty:nodefaults}}
    </td>
  </tr>
</table>

{{if $debugMode}}
<div style="margin: 10px; text-align: center;">
  Page g�n�r�e en {{$performance.genere}} secondes
  par PHP, utilisant {{$performance.memoire}} de m�moire 
  sur  {{$performance.objets}} objets m�tier + {{$performance.cache}} en cache
  {{foreach from=$dbChronos item=currdbChrono key=keydbConfigName}}
  <br />
  {{$currdbChrono->total|string_format:"%.3f"}} secondes prises
  par la base de donn�es <strong>{{$keydbConfigName}}</strong> en 
  {{$currdbChrono->nbSteps}} requ�tes SQL.
  {{/foreach}}
  <br />
  Poids de la page : {{$performance.size}}
  <br />
</div>
{{/if}}

{{if $demoVersion}}
<div style="margin: 10px; float:right">
  <a href="http://www.sourceforge.net/projects/mediboard/" title="Projet Mediboard sur Sourceforge">
    <img src="http://www.sourceforge.net/sflogo.php?group_id=112072&amp;type=2" alt="Sourceforge Project Logo" />
  </a>
</div>
{{/if}}

</body>
</html>