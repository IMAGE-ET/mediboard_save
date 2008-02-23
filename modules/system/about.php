<?php /* $Id:$ */

/**
 *	@package Mediboard
 *	@subpackage system
 *	@version $Revision: $
 *  @author Romain OLLIVIER
*/

?>

<script type="text/javascript">

function switchDiv(div1, div2) {
  divFade(div1);
  divAppear(div2);
}

function divAppear(div) {
  Effect.Appear(div, Effect.Appear, {speed:0.05, afterFinishInternal:function(){}});
  return false;
}

function divFade(div) {
  Effect.Fade(div, Effect.Fade, {speed:0.05, afterFinishInternal:function(){}});
  return false;
}

function pageMain() {
}

</script>

<div class="about" id="div1">
  <div class="text">
    <h2>Pr�sentation</h2>
    <a href="http://www.mediboard.org/" title="Site du projet Mediboard" target="_blank">Mediboard</a>
    est un <strong>syst�me web open source de gestion d'�tablissement de sant�</strong>.
    Il se d�finit plus pr�cis�ment comme un <strong>SIH</strong> (Syst�me d'Information Hospitalier)
    c'est-�-dire un PGI (Progiciel de Gestion Integr�) adapt� aux <strong>�tablissements de sant� de toute taille</strong>,
    du simple cabinet de praticien au centre m�dical multi-sites.
  </div>
  <div class="nav">
    -
    <a href="#" onclick="switchDiv('div1', 'div2')">L'equipe &gt;&gt;</a>
  </div>
</div>

<div class="about" id="div2" style="display: none;">
  <div class="text">
    <h2>L'equipe</h2>
    <ul>
      <li>Romain Ollivier</li>
      <li>Thomas Despoix</li>
      <li>Sebastien Fillonneau</li>
      <li>Alexis Granger</li>
    </ul>
    <h2>Soci�t�s actives</h2>
    <ul>
      <li>openXtrem</li>
      <li>Kheops Technologies</li>
    </ul>
  </div>
  <div class="nav">
    <a href="#" onclick="switchDiv('div2', 'div1')">&lt;&lt; Pr�sentation</a>
    -
    <a href="#" onclick="switchDiv('div2', 'div3')">Remerciements &gt;&gt;</a>
  </div>
</div>



<div class="about" id="div3" style="display: none;">
  <div class="text">
    <h2>Remerciements</h2>
    Merci � tous les praticiens qui ont pris du temps sur leurs emplois du temps d�j� charg�
    pour nous aider � ammener ce projet � maturation.
  </div>
  <div class="nav">
    <a href="#" onclick="switchDiv('div3', 'div2')">&lt;&lt; L'�quipe</a>
    -
  </div>
</div>
