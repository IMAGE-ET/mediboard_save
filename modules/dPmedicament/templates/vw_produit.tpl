<script type="text/javascript">


function pageMain(){
  // Initialisation des onglets du menu
  new Control.Tabs('main_tab_group');
}
  
</script>

<table class="main">

  <!-- Menu de la monographie du medicament -->
  <tr>
    <td colspan="2">
      <ul id="main_tab_group" class="control_tabs">
        <li><a href="#one" style="height: 35px;">Composition et aspect</a></li>
        <li><a href="#two" style="height: 35px;">Donn�es cliniques</a></li>
        <li><a href="#three" style="height: 35px; width:120px">Propri�t�s pharmacologiques</a></li>
        <li><a href="#four" style="height: 35px; width:120px">Donn�es pharmaceutiques</a></li>
        <li><a href="#five" style="height: 35px; width:150px">Donn�es technico-r�glementaires</a></li>
      </ul>
    </td>
  </tr>
  
  <!-- Commposition et aspect -->
  <tbody id="one">
    <tr>
      <td>
        <div class="accordionMain" id="accordionComposition">
          <div id="Identite">
            <div id="IdentiteHeader" class="accordionTabTitleBar">
              Composition
            </div>
            <div id="IdentiteContent"  class="accordionTabContentBox">
              <!-- Affichage de la composition du medicaments -->
            </div>
          </div>
          <div id="Medical">
            <div id="MedicalHeader" class="accordionTabTitleBar">
              Aspect
            </div>
            <div id="MedicalContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->aspect_forme|smarty:nodefaults}}
            </div>
          </div>
        </div>
      </td>
    </tr>
  </tbody>

  <!-- Donn�es cliniques -->
  <tbody id="two">  
    <tr>
      <td>
        <div class="accordionMain" id="accordionClinique">
          <div id="Identite">
            <div id="IdentiteHeader" class="accordionTabTitleBar">
              Indications th�rapeutiques
            </div>
            <div id="IdentiteContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->indications|smarty:nodefaults}}
            </div>
          </div>
          <div id="Medical">
            <div id="MedicalHeader" class="accordionTabTitleBar">
              Posologie et mode d'administration
            </div>
            <div id="MedicalContent"  class="accordionTabContentBox">
               {{$mbProduit->_ref_monographie->posologie|smarty:nodefaults}}
            </div>
            </div>
          <div id="Corresp">
            <div id="CorrespHeader" class="accordionTabTitleBar">
              Contre-indications
            </div>
            <div id="CorrespContent"  class="accordionTabContentBox">
             {{$mbProduit->_ref_monographie->contre_indications|smarty:nodefaults}}
            </div>
          </div>
          <div id="Assure">
            <div id="AssureHeader" class="accordionTabTitleBar">
              Mises en garde et pr�cautions d'emploi
            </div>
            <div id="AssureContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->precautions_emploi|smarty:nodefaults}}
            </div>
          </div>
          <div id="Corresp">
            <div id="CorrespHeader" class="accordionTabTitleBar">
              Interactions avec d'autres m�dicaments et autres formes d'int�ractions
            </div>
            <div id="CorrespContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->interactions|smarty:nodefaults}}
            </div>
          </div>
          <div id="Corresp">
            <div id="CorrespHeader" class="accordionTabTitleBar">
              Grossesse et allaitement
            </div>
            <div id="CorrespContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->grossesse_allaitement|smarty:nodefaults}}
            </div>
          </div>
          <div id="Corresp">
            <div id="CorrespHeader" class="accordionTabTitleBar">
              Effet sur l'aptitude � conduire des v�hicules et � utiliser des machines
            </div>
            <div id="CorrespContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->effets_aptitude|smarty:nodefaults}}
            </div>
          </div>
          <div id="Corresp">
            <div id="CorrespHeader" class="accordionTabTitleBar">
               Effets ind�sirables
            </div>
            <div id="CorrespContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->effets_indesirables|smarty:nodefaults}}
            </div>
          </div>
          <div id="Corresp">
            <div id="CorrespHeader" class="accordionTabTitleBar">
              Surdosage
            </div>
            <div id="CorrespContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->surdosage|smarty:nodefaults}}
            </div>
          </div>
        </div>
      </td>
    </tr>
  </tbody>

  <!-- Propri�t�s pharmacologiques -->
  <tbody id="three">
    <tr>
      <td>
        <div class="accordionMain" id="accordionPharma">
          <div id="Identite">
            <div id="IdentiteHeader" class="accordionTabTitleBar">
              
            </div>
            <div id="IdentiteContent"  class="accordionTabContentBox">
              <!-- Affichage de la composition du medicaments -->
            </div>
          </div>
          <div id="Medical">
            <div id="MedicalHeader" class="accordionTabTitleBar">
              Aspect
            </div>
            <div id="MedicalContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->aspect_forme|smarty:nodefaults}}
            </div>
          </div>
          <div id="Medical">
            <div id="MedicalHeader" class="accordionTabTitleBar">
              Aspect
            </div>
            <div id="MedicalContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->aspect_forme|smarty:nodefaults}}
            </div>
          </div>
          <div id="Medical">
            <div id="MedicalHeader" class="accordionTabTitleBar">
              Aspect
            </div>
            <div id="MedicalContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->aspect_forme|smarty:nodefaults}}
            </div>
          </div>
        </div>
      </td>
    </tr>
  </tbody>

  <tbody id="four">
    <tr>
      <td>
      Donn�es pharmaceutiques
      </td>
    </tr>
  </tbody>

  <tbody id="five">
    <tr>
      <td>
      Donn�es technico-r�glementaires
      </td>
    </tr>
  </tbody>
     
</table>

<script language="Javascript" type="text/javascript">
var oAccordComposition = new Rico.Accordion( $('accordionComposition'), { 
  panelHeight: 400,
  showDelay: 50, 
  showSteps: 3 
} );

var oAccordClinique = new Rico.Accordion( $('accordionClinique'), { 
  panelHeight: 400,
  showDelay: 50, 
  showSteps: 3 
} );

var oAccordPharma = new Rico.Accordion( $('accordionPharma'), { 
  panelHeight: 400,
  showDelay: 50, 
  showSteps: 3 
} );
</script>

