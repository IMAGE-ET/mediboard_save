<script type="text/javascript">

function pageMain(){
  // Initialisation des onglets du menu
  new Control.Tabs('main_tab_group');
}
  
</script>

<table class="main">
  <tr>
    <th class="title" {{if $mbProduit->_ref_monographie->date_suppression}}style="background-color: red"{{/if}}>
    {{$mbProduit->libelle}}
    {{if $mbProduit->_ref_monographie->date_suppression}}
    <br />
    Produit supprim� depuis le {{$mbProduit->_ref_monographie->date_suppression|date_format:"%d %b %Y"}}
    {{/if}}
    </th>
    
  </tr>
  <!-- Menu de la monographie du medicament -->
  <tr>
    <td colspan="2">
      <ul id="main_tab_group" class="control_tabs">
        <li><a href="#one" style="font-size: 1em;">Composition et aspect</a></li>
        <li><a href="#two" style="font-size: 1em;">Donn�es cliniques</a></li>
        <li><a href="#three" style="font-size: 1em;">Propri�t�s pharmacologiques</a></li>
        <li><a href="#four" style="font-size: 1em;">Donn�es pharmaceutiques</a></li>
        <li><a href="#five" style="font-size: 1em;">Donn�es technico-r�glementaires</a></li>
      </ul>
      <hr class="control_tabs" />    
    </td>
    
  </tr>
  
  <!-- Commposition et aspect -->
  <tbody id="one">
    <tr>
      <td>
        <!-- Affichage de la composition du medicaments -->
        <table class="tbl">
          <tr>
            <th colspan="3" class="title">Principes actifs</th>
          </tr>
          <tr>
            <th>Libelle</th>
            <th>Commentaire</th>
            <th>Quantite</th>
          </tr>
          {{foreach from=$mbProduit->_ref_composition->principes_actifs item=PA}}
          <tr>
            <td>{{$PA->Libelle}}</td>
            <td>{{$PA->Commentaire}}</td>
            <td>{{$PA->Quantite}}{{$PA->Unite}}</td>
          </tr>
          {{/foreach}} 
        </table>
        <table class="tbl">
          <tr>
            <th class="title">Commentaires sur le composition</th>
          </tr>
          <td class="text">
           {{$mbProduit->_ref_composition->exprime_par|smarty:nodefaults}}
          </td>
        </table>
        <table class="tbl">
          <tr>
            <th colspan="2" class="title">Excipients</th>
          </tr>
          <tr>
            <th>Libelle</th>
            <th>Commentaire</th>
          </tr>
          {{foreach from=$mbProduit->_ref_composition->excipients item=excipient}}
          <tr>
            <td {{if $excipient->Notoire == -1}}style="color: red;"{{/if}}>
              {{if $excipient->Is_Info}}<strong>{{/if}}
              {{$excipient->Libelle}} 
              {{if $excipient->Is_Info}}</strong>{{/if}}
              {{if $excipient->Notoire == -1}}
                (Excipient � effet notoire)
              {{/if}}
            </td>
            <td>{{$excipient->Commentaire}}</td>
          </tr>
          {{/foreach}} 
        </table>
        <table class="tbl">
          <tr>
            <th class="title">Aspect</th>
          </tr>
          <tr>
            <td>
              {{$mbProduit->_ref_monographie->aspect_forme|smarty:nodefaults}}
           </td>
          </tr>
        </table>
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
        <div class="accordionMain" id="accordionPharmaco">
          <div id="Identite">
            <div id="IdentiteHeader" class="accordionTabTitleBar">
              Classification th�rapeutique  
            </div>
            <div id="IdentiteContent"  class="accordionTabContentBox">
              <table class="tbl">
                <tr>
                  <th>Classification ATC</th>
                </tr>
                <!-- Parcours des classes ATC du produit -->
                {{foreach from=$mbProduit->_ref_classes_ATC item=classeATC name=classesATC}}
                
                  <!-- Initialisation du compteur -->
                  {{counter start=0 skip=2 assign="compteur"}}
                  {{foreach from=$classeATC->classes item=classe}}
                    {{if $classe.libelle}}
                    <tr>
                      <td>
                        {{$tabEspace.$compteur|smarty:nodefaults}}
                        <img src="./images/icons/dotgrey.gif" alt="" title="" />
                        {{$classe.libelle}} ({{$classe.code}})
                      </td>
                    </tr>
                    {{/if}}
                    {{counter}}
                  {{/foreach}}
                  
									{{if !$smarty.foreach.classesATC.last}}
									<tr>
									<td>
									&nbsp;
									</td>
									</tr>
									{{/if}}
                {{/foreach}}
                
                <tr>
                  <th>Classification BCB</th>
                </tr>
                <!-- Parcours des classes ATC du produit -->
                {{foreach from=$mbProduit->_ref_classes_thera item=classeThera name=classesThera}}
                
                  <!-- Initialisation du compteur -->
                  {{counter start=0 skip=2 assign="compteur"}}
                  {{foreach from=$classeThera->classes item=classe}}
                    {{if $classe.libelle}}
                    <tr>
                      <td>
                        {{$tabEspace.$compteur|smarty:nodefaults}} 
                        <img src="./images/icons/dotgrey.gif" alt="" title="" />
                        {{$classe.libelle}} ({{$classe.code}})
                      </td>
                    </tr>
                    {{/if}}
                    {{counter}}
                  {{/foreach}}
                  
									{{if !$smarty.foreach.classesThera.last}}
									<tr>
									<td>
									&nbsp;
									</td>
									</tr>
									{{/if}}
                {{/foreach}}
                
              </table>
            </div>
          </div>
          <div id="Medical">
            <div id="MedicalHeader" class="accordionTabTitleBar">
              Propri�t�s pharmacodynamiques
            </div>
            <div id="MedicalContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->pharmacodynamie|smarty:nodefaults}}
            </div>
          </div>
          <div id="Medical">
            <div id="MedicalHeader" class="accordionTabTitleBar">
              Propri�t�s pharmacocin�tiques
            </div>
            <div id="MedicalContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->pharmacocinetique|smarty:nodefaults}}
            </div>
          </div>
          <div id="Medical">
            <div id="MedicalHeader" class="accordionTabTitleBar">
              Donn�es de s�curit� pr�cliniques
            </div>
            <div id="MedicalContent"  class="accordionTabContentBox">
              {{$mbProduit->_ref_monographie->securite_preclinique|smarty:nodefaults}}
            </div>
          </div>
        </div>
      </td>
    </tr>
  </tbody>

  <!-- Donn�es pharmaceutiques -->
  <tbody id="four">
    <tr>
      <td>
        <table class="tbl">
          <tr>
            <th>
              Incompatibilit�s
            </th>
          </tr>
          <tr>
            <td>
              {{$mbProduit->_ref_monographie->incompatibilite|smarty:nodefaults}}
            </td>
          </tr>
          <tr>
            <th>
              Dur�e et pr�cautions particuli�res de conservation
            </th>
          </tr>
          <tr>
            <td>
              {{$mbProduit->_ref_monographie->conservation|smarty:nodefaults}}
            </td>
          </tr>
          <tr>
            <th>Nature et contenu de l'emballage ext�rieur</th>
          </tr>
          <tr>
            <td>
              {{$mbProduit->_ref_monographie->emballage_ext|smarty:nodefaults}}
            </td>
          </tr>
          <tr>
            <th>Instructions pour l'utilisation, la manipulation et l'�limination</th>
          </tr>
          <tr>
            <td>
              {{$mbProduit->_ref_monographie->instruction_manipulation|smarty:nodefaults}}
            </td>
          </tr>
        </table>
       </td>
     </tr>
  </tbody>
  
  <tbody id="five">
    <tr>
      <td>
        <table class="tbl">
          <tr>
            <th colspan="2">Donn�es technico-r�glementaires</th>
          </tr>
          <tr>
            <td>Titulaire de l'AMM</th>
            <td>{{$mbProduit->_ref_economique->laboratoire}}</td>
          </tr>
          <tr>
            <td>Laboratoire exploitant</th>
            <td>{{$mbProduit->_ref_economique->labo_exploitant}}</td>
          </tr>
          <tr>
            <td>Prix de vente TTC</th>
            <td>
            {{if $mbProduit->_ref_economique->prix_vente != "0000000"}}
              {{$mbProduit->_ref_economique->prix_vente}} &euro;
            {{/if}}
            </td>
          </tr>
          <tr>
            <td>Taux de TVA</th>
            <td>{{$mbProduit->_ref_economique->taux_tva}} %</td>
          </tr>
          <tr>
            <td>Taux de remboursement SS</td>
            <td>{{$mbProduit->_ref_economique->taux_ss}} %</td>
          </tr>
          <tr>
            <td>Code AMM</th>
            <td>{{$mbProduit->numero_AMM}}</td>
          </tr>
          <tr>
            <td>Code UCD</th>
            <td>{{$mbProduit->_ref_economique->code_ucd}}</td>
          </tr>
          <tr>
            <th colspan="2">Statut</th>
          </tr>
          <tr>
            <td colspan="2">
              {{if $mbProduit->libelle_statut}}
                {{$mbProduit->libelle_statut}} du {{$mbProduit->date_AMM}}  
              {{/if}}
            </td>
          </tr>
          <tr>  
            <td colspan="2">
              <strong>Agr�ment collectivit�s : </strong>
              {{if $mbProduit->agrement}}
                Oui
              {{else}}
                Non
              {{/if}}        
            </td>
          </tr>
          <tr>
            <th colspan="2">Conditions de prescription et de d�livrance</th>
          </tr>
          <tr>
            <td colspan="2">{{$mbProduit->_ref_monographie->condition_delivrance|smarty:nodefaults}}</td>
          </tr>
        </table>
      </td>
    </tr>
  </tbody>
     
</table>

<script language="Javascript" type="text/javascript">

var oAccordClinique = new Rico.Accordion( $('accordionClinique'), { 
  panelHeight: 380,
  showDelay: 50, 
  showSteps: 3 
} );

var oAccordPharmaco = new Rico.Accordion( $('accordionPharmaco'), { 
  panelHeight: 380,
  showDelay: 50, 
  showSteps: 3 
} );


</script>

