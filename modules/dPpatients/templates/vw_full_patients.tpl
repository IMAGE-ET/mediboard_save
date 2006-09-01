<!-- $Id: $ -->

{{include file="../../dPfiles/templates/inc_files_functions.tpl"}}

<script type="text/javascript">

function view_history_patient(id){
  url = new Url();
  url.setModuleAction("dPpatients", "vw_history");
  url.addParam("patient_id", id);
  url.popup(600, 500, "history");
}

function editPatient() {
  var oForm = document.actionPat;
  var oTabField = oForm.tab;
  oTabField.value = "vw_edit_patients";
  oForm.submit();
}

function printPatient(id) {
  var url = new Url;
  url.setModuleAction("dPpatients", "print_patient");
  url.addParam("patient_id", id);
  url.popup(700, 550, "Patient");
}

function pageMain() {
  initAccord(true);
}

</script>

<table class="main">
  <tr>
    <td class="halfPane">

      <form name="FrmClass" action="?m={{$m}}" method="get">
      <input type="hidden" name="m"        value="{{$m}}" />
      <input type="hidden" name="selKey"   value="{{$selKey}}" />
      <input type="hidden" name="selClass" value="{{$selClass}}" />
      <input type="hidden" name="typeVue"  value="1" />
      <input type="hidden" name="cat_id"   value="{{$cat_id}}" />
      </form>
      
      {{assign var="href" value="index.php?m=dPpatients&tab=vw_full_patients"}}

      <table class="form">
        <tr>
          <th class="category" colspan="2">
            <a style="float:right;" href="javascript:view_history_patient({{$patient->patient_id}})">
              <img src="images/history.gif" alt="historique" />
            </a>
            Identit�
          </th>
          <th class="category" colspan="2">
            <a class="button" style="float:right;" href="{{$href}}&amp;patient_id={{$patient->patient_id}}">
              <img align="top" src="modules/{{$m}}/images/next.png" alt="Afficher les fichiers" />
            </a>
            Coordonn�es
          </th>
        </tr>

        <tr>
          <th>Nom</th>
          <td>{{$patient->nom}}</td>
          <th>Adresse</th>
          <td class="text">{{$patient->adresse|nl2br}}</td>
        </tr>
  
        <tr>
          <th>Pr�nom</th>
          <td>{{$patient->prenom}}</td>
          <th>Code Postal</th>
          <td>{{$patient->cp}}</td>
        </tr>
  
        <tr>
          <th>Nom de naissance</th>
          <td>{{$patient->nom_jeune_fille}}</td>
          <th>Ville</th>
          <td>{{$patient->ville}}</td>
        </tr>
  
        <tr>
          <th>Date de naissance</th>
          <td>{{$patient->_naissance}}</td>
          <th>T�l�phone</th>
          <td>{{$patient->_tel1}} {{$patient->_tel2}} {{$patient->_tel3}} {{$patient->_tel4}} {{$patient->_tel5}}</td>
        </tr>
  
        <tr>
          <th>Sexe</th>
          <td>
            {{if $patient->sexe == "m"}} masculin {{/if}}
            {{if $patient->sexe == "f"}} f�minin {{/if}}
            {{if $patient->sexe == "j"}} femme c�libataire {{/if}} 
          </td>
          <th>Portable</th>
          <td>{{$patient->_tel21}} {{$patient->_tel22}} {{$patient->_tel23}} {{$patient->_tel24}} {{$patient->_tel25}}</td>
        </tr>

        <tr>
          <th class="category" colspan="4">Remarques</th>
        </tr>

        <tr>
          <td colspan="4" class="text">{{$patient->rques|nl2br}}</td>
        </tr>
        <tr>
          <th colspan="2" class="category">Infos m�dicales</th>
          <th colspan="2" class="category">M�decins</th>
        </tr>
        <tr>
          <th>N� SS</th>
          <td>{{$patient->matricule}}</td>
          <th>Traitant</th>
          <td>Dr. {{$patient->_ref_medecin_traitant->_view}}</td>
        </tr>
        <tr>
          <th>R�gime de sant�</th>
          <td>{{$patient->regime_sante}}</td>
          <th rowspan="3">Correspondants</th>
          <td>Dr. {{$patient->_ref_medecin1->_view}}</td>
        </tr>
        <tr>
          <th>CMU</th>
          <td>
            {{if $patient->cmu}}
            jusqu'au {{$patient->cmu|date_format:"%d/%m/%Y"}}
            {{else}}
            -
            {{/if}}
          </td>
          <td>Dr. {{$patient->_ref_medecin2->_view}}</td>
        </tr>
        <tr>
          <th>ALD</th>
          <td>
            {{if $patient->ald}}
            {{$patient->ald|nl2br}}
            {{else}}
            -
            {{/if}}
          </td>
          <td>Dr. {{$patient->_ref_medecin3->_view}}</td>
        </tr>
        <tr>
          <td class="button" colspan="4">
            <form name="actionPat" action="./index.php" method="get">
            <input type="hidden" name="m" value="dPpatients" />
            <input type="hidden" name="tab" value="vw_idx_patients" />
            <input type="hidden" name="patient_id" value="{{$patient->patient_id}}" />
            <button type="button" class="print" onclick="printPatient({{$patient->patient_id}})">
              Imprimer
            </button>
            {{if $canEdit}}
            <button type="button" class="modify" onclick="editPatient()">
              Modifier
            </button>
            {{/if}}
            </form>
          </td>
        </tr>
      </table>
    </td>
    <td class="halfPane" id="listView">
      {{include file="../../dPfiles/templates/inc_list_view_colonne.tpl"}}
    </td>
  </tr>
</table>