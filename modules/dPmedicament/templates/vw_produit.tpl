<script type="text/javascript">
Main.add(function () {
  // Initialisation des onglets du menu
  var tabsProduit = Control.Tabs.create('tab-produit', false);
  var tabsClinique = Control.Tabs.create('tab-clinique', false);
  var tabsPharmaco = Control.Tabs.create('tab-pharmaco', false);
});
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
      <ul id="tab-produit" class="control_tabs">
        <li><a href="#one">Composition et aspect</a></li>
        <li><a href="#two">Donn�es cliniques</a></li>
        <li><a href="#three">Propri�t�s pharmacologiques</a></li>
        <li><a href="#four">Donn�es pharmaceutiques</a></li>
        <li><a href="#five">Donn�es technico-r�glementaires</a></li>
      </ul>
      <hr class="control_tabs" />    
  
      <!-- Commposition et aspect -->
      <div id="one" style="display: none;">
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
          <tr>
            <th class="title" colspan="3">Commentaires sur la composition</th>
          </tr>
          <tr>
            <td class="text" colspan="3">
             {{$mbProduit->_ref_composition->exprime_par|smarty:nodefaults}}
            </td>
          </tr>
          <tr>
            <th colspan="3" class="title">Excipients</th>
          </tr>
          <tr>
            <th>Libelle</th>
            <th colspan="2">Commentaire</th>
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
            <td colspan="2">{{$excipient->Commentaire}}</td>
          </tr>
          {{/foreach}} 
          <tr>
            <th class="title" colspan="3">Aspect</th>
          </tr>
          <tr>
            <td colspan="3">
              {{$mbProduit->_ref_monographie->aspect_forme|smarty:nodefaults}}
           </td>
          </tr>
        </table>
      </div>

      <!-- Donn�es cliniques -->
      <div id="two" style="display: none;">
        <ul id="tab-clinique" class="control_tabs">
          <li><a href="#indications">Indications th�rapeutiques</a></li>
          <li><a href="#posologie">Posologie et mode d'administration</a></li>
          <li><a href="#contre_indications">Contre-indications</a></li>
          <li><a href="#precautions_emploi">Mises en garde et pr�cautions d'emploi</a></li>
          <li><a href="#interactions">Interactions avec d'autres m�dicaments et autres formes d'int�ractions</a></li>
          <li><a href="#grossesse_allaitement">Grossesse et allaitement</a></li>
          <li><a href="#effets_aptitude">Effet sur l'aptitude � conduire des v�hicules et � utiliser des machines</a></li>
          <li><a href="#effets_indesirables">Effets ind�sirables</a></li>
          <li><a href="#surdosage">Surdosage</a></li>
        </ul>
        <hr class="control_tabs" />  
      
        <div id="indications" style="display: none;">{{$mbProduit->_ref_monographie->indications|smarty:nodefaults}}</div>
        <div id="posologie" style="display: none;">{{$mbProduit->_ref_monographie->posologie|smarty:nodefaults}}</div>
        <div id="contre_indications" style="display: none;">{{$mbProduit->_ref_monographie->contre_indications|smarty:nodefaults}}</div>
        <div id="precautions_emploi" style="display: none;">{{$mbProduit->_ref_monographie->precautions_emploi|smarty:nodefaults}}</div>
        <div id="interactions" style="display: none;">{{$mbProduit->_ref_monographie->interactions|smarty:nodefaults}}</div>
        <div id="grossesse_allaitement" style="display: none;">{{$mbProduit->_ref_monographie->grossesse_allaitement|smarty:nodefaults}}</div>
        <div id="effets_aptitude" style="display: none;">{{$mbProduit->_ref_monographie->effets_aptitude|smarty:nodefaults}}</div>
        <div id="effets_indesirables" style="display: none;">{{$mbProduit->_ref_monographie->effets_indesirables|smarty:nodefaults}}</div>
        <div id="surdosage" style="display: none;">{{$mbProduit->_ref_monographie->surdosage|smarty:nodefaults}}</div>
      </div>

      <!-- Propri�t�s pharmacologiques -->
      <div id="three" style="display: none;">
        <ul id="tab-pharmaco" class="control_tabs">
          <li><a href="#classification">Classification th�rapeutique</a></li>
          <li><a href="#pharmacodynamie">Propri�t�s pharmacodynamiques</a></li>
          <li><a href="#pharmacocinetique">Propri�t�s pharmacocin�tiques</a></li>
          <li><a href="#securite_preclinique">Donn�es de s�curit� pr�cliniques</a></li>
        </ul>
        <hr class="control_tabs" />  

        <div id="classification" style="display: none;">
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
							  <td>&nbsp;</td>
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
							<td>&nbsp;</td>
							</tr>
							{{/if}}
            {{/foreach}}
            
          </table>
        </div>
          
        <div id="pharmacodynamie" style="display: none;">{{$mbProduit->_ref_monographie->pharmacodynamie|smarty:nodefaults}}</div>
        <div id="pharmacocinetique" style="display: none;">{{$mbProduit->_ref_monographie->pharmacocinetique|smarty:nodefaults}}</div>
        <div id="securite_preclinique" style="display: none;">{{$mbProduit->_ref_monographie->securite_preclinique|smarty:nodefaults}}</div>
      </div>

      <!-- Donn�es pharmaceutiques -->
      <div id="four" style="display: none;">
        <table class="tbl">
          <tr>
            <th>Incompatibilit�s</th>
          </tr>
          <tr>
            <td>{{$mbProduit->_ref_monographie->incompatibilite|smarty:nodefaults}}</td>
          </tr>
          <tr>
            <th>Dur�e et pr�cautions particuli�res de conservation</th>
          </tr>
          <tr>
            <td class="text">
              {{$mbProduit->_ref_monographie->conservation|smarty:nodefaults}}
            </td>
          </tr>
          <tr>
            <th>Nature et contenu de l'emballage ext�rieur</th>
          </tr>
          <tr>
            <td class="text">
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
      </div>
      
      <div id="five" style="display: none;">
        <table class="tbl">
          <tr>
            <th colspan="2">Donn�es technico-r�glementaires</th>
          </tr>
          <tr>
            <th style="width: 50px;">Titulaire de l'AMM</th>
            <td>{{$mbProduit->_ref_economique->laboratoire}}</td>
          </tr>
          <tr>
            <th>Laboratoire exploitant</th>
            <td>{{$mbProduit->_ref_economique->labo_exploitant}}</td>
          </tr>
          <tr>
            <th>Prix de vente TTC</th>
            <td>
            {{if $mbProduit->_ref_economique->prix_vente != "0000000"}}
              {{$mbProduit->_ref_economique->prix_vente}} &euro;
            {{/if}}
            </td>
          </tr>
          <tr>
            <th>Taux de TVA</th>
            <td>{{$mbProduit->_ref_economique->taux_tva}} %</td>
          </tr>
          <tr>
            <th>Taux de remboursement SS</th>
            <td>{{$mbProduit->_ref_economique->taux_ss}} %</td>
          </tr>
          <tr>
            <th>Code AMM</th>
            <td>{{$mbProduit->numero_AMM}}</td>
          </tr>
          <tr>
            <th>Code UCD</th>
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
            <td colspan="2" class="text">{{$mbProduit->_ref_monographie->condition_delivrance|smarty:nodefaults}}</td>
          </tr>
        </table>
      </div>
    </td>
  </tr>
</table>
