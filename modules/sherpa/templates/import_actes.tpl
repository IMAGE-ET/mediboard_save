<div class="big-info">
  L'import d'actes Sherpa est pour le moment <strong>silencieux</strong>.
  <br/>
  On ne fait qu'analyser le contenu de la requ�te sans effectuer l'ajout proprement dit.
</div>

S�jour concern� : {{$sejour->_view}}
<ul>
	{{foreach from=$sejour->_ref_operations item=_operation}}
	<li>Intervention concern�e: {{$_operation->_view}}</li>
	{{foreachelse}}
	<li><em>Aucune intervention</em></li>
	{{/foreach}}
</ul>