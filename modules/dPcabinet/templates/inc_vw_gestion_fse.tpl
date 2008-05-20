{{assign var=patient value=$consult->_ref_patient}}
{{assign var=praticien value=$consult->_ref_chir}}

<script type="text/javascript">
Object.extend(Intermax.ResultHandler, {
  "Lire Vitale": function() {
    var oVitale = Intermax.oContent.VITALE;
    
    var msg = {{$patient->_id_vitale|json}} ?
      "Vous �tes sur le point de mettre � jour le patient" :
      "Vous �tes sur le point d'associer le patient";
    msg += printf("\n\t%s %s (%s)",
      '{{$patient->nom|smarty:nodefaults|JSAttribute}}', 
      '{{$patient->prenom|smarty:nodefaults|JSAttribute}}', 
      '{{mb_value object=$patient field=naissance}}');
    msg += "\nAvec le b�n�ficiaire Vitale";
    msg += printf("\n\t%s %s (%s)", 
      oVitale.VIT_NOM, 
      oVitale.VIT_PRENOM, 
      oVitale.VIT_DATE_NAISSANCE);
    msg += "\n\nVoulez-vous continuer ?";
        
    if (confirm(msg)) {
      Reglement.submit(document.BindVitale);
    }
  },
  
  "Lire CPS": function() {
    var oCPS = Intermax.oContent.CPS;
    
    var msg = {{$praticien->_id_cps|json}} ?
      "Vous �tes sur le point de mettre � jour le praticien" :
      "Vous �tes sur le point d'associer le pratcien";
    msg += printf("\n\t%s %s (%s)", 
      '{{$praticien->_user_first_name|smarty:nodefaults|JSAttribute}}', 
      '{{$praticien->_user_last_name|smarty:nodefaults|JSAttribute}}', 
      '{{mb_value object=$praticien field=adeli}}');
    msg += "\nAvec la Carte Professionnelle de Sant� de";
    msg += printf("\n\t%s %s (%s)", 
      oCPS.CPS_PRENOM,
      oCPS.CPS_NOM,
      oCPS.CPS_ADELI_NUMERO_CPS);
    msg += "\n\nVoulez-vous continuer ?";

    if (confirm(msg)) {
      Reglement.submit(document.BindCPS);
    }
  },

  "Formater FSE": function() {
    Reglement.submit(document.BindFSE);
  },

  "Annuler FSE": function() {
    Reglement.reload();
  }  
} );

Intermax.ResultHandler["Consulter Vitale"] = Intermax.ResultHandler["Lire Vitale"];
Intermax.ResultHandler["Consulter FSE"] = Intermax.ResultHandler["Formater FSE"];

// Use single quotes or fails ?!!
Intermax.Triggers['Formater FSE'].aActes = {{$consult->_fse_intermax|@json}};
</script>

<table class="form">
  <tr>
    <th class="category" colspan="2">{{tr}}CLmFSE{{/tr}}</th>
  </tr>
  <tr>
    <td colspan="2">
    <!-- Feuille de soins -->
      <table class="form">
        <tr>
          <td class="text">
            {{if !$patient->_id_vitale || !$praticien->_id_cps}}
              <div class="warning">
                Professionnel de Sant� ou B�n�ficiaire Vitale non identifi�<br/>
                Merci d'associer la CPS et la carte Vitale pour permettre le formatage d'une FSE. 
              </div>
            {{else}}
              <form name="BindFSE" action="?m={{$m}}" method="post">
                <input type="hidden" name="m" value="dPcabinet" />
                <input type="hidden" name="dosql" value="do_consultation_aed" />
                <input type="hidden" name="_delete_actes" value="1" />
                <input type="hidden" name="_bind_fse" value="1" />
                {{mb_field object=$consult field="consultation_id" hidden="1"}}
              </form>
            {{/if}}
          </td>
        </tr>
        
        <!-- Les FSE d�j� associ�es -->
        {{foreach from=$consult->_ext_fses key=_id_fse item=_ext_fse}}
        <tr>
          <td>
            <span class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, { params: { object_class: 'CLmFSE', object_id: '{{$_id_fse}}' } })">
              {{$_ext_fse->_view}}
            </span>
          </td>
          {{if $_ext_fse->_annulee}}
          <td class="cancelled">
            {{mb_value object=$_ext_fse field=S_FSE_ETAT}}
          </td>
          {{/if}}
        </tr>
          {{if !$_ext_fse->_annulee}}
          <tr>
            <td class="button" colspan="2">
              <button class="search" type="button" onclick="Intermax.Triggers['Consulter FSE']('{{$_id_fse}}');">
                Consulter 
              </button>
              <button class="print" type="button" onclick="Intermax.Triggers['Editer FSE']('{{$_id_fse}}');">
                Imprimer
              </button>
              <button class="cancel" type="button" onclick="Intermax.Triggers['Annuler FSE']('{{$_id_fse}}');">
                Annuler
              </button>
            </td>
          </tr>
          {{/if}}
        {{foreachelse}}
        <tr>
          <td>
            <em>Aucune FSE associ�e</em>
          </td>
        </tr>
        {{/foreach}}

        {{if $patient->_id_vitale && $praticien->_id_cps}}
        <tr>
          <td class="button" colspan="2">
            {{if !$consult->_current_fse}}
            <button class="new" type="button" onclick="Intermax.Triggers['Formater FSE']('{{$praticien->_id_cps}}', '{{$patient->_id_vitale}}');">
              Formater FSE
            </button>
            {{/if}}
            <button class="change intermax-result" type="button" onclick="Intermax.result(['Formater FSE', 'Consulter FSE', 'Annuler FSE']);">
              Mettre � jour FSE
            </button>
          </td>
        </tr>
        {{/if}}
        
      </table>
    </td>
  </tr>

  <!-- Patient Vitale et Professionnel de Sant� -->
  <tr>
    <th class="category">Professionnel de sant�</th>
    <th class="category">Patient Vitale</th>
  </tr>
  
  <tr>
    <!-- Professionnel de sant� -->
    <td class="text">
      <form name="BindCPS" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="mediusers" />
        <input type="hidden" name="dosql" value="do_mediusers_aed" />
        <input type="hidden" name="_bind_cps" value="1" />
        {{mb_field object=$praticien field="user_id" hidden="1"}}
      </form>
    
      {{if !$praticien->_id_cps}}
        <div class="warning">
          Praticien non associ� � une CPS. <br/>
          Merci d'effectuer une lecture de la CPS pour permettre le formatage d'une FSE. 
        </div>
      {{else}}
        <div class="message">
          Praticien correctement associ� � une CPS. <br/>
          Formatage des FSE disponible pour ce praticien.
        </div>
      {{/if}}
    </td>

    <!-- Patient Vitale -->
    <td class="text">
      <form name="BindVitale" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="dosql" value="do_patients_aed" />
        <input type="hidden" name="_bind_vitale" value="1" />
        {{mb_field object=$patient field="patient_id" hidden="1"}}
      </form>
            
      {{if !$patient->_id_vitale}}
        <div class="warning">
          Patient non associ� � un b�n�ficiaire Vitale. <br/>
          Merci d'�ffectuer une lecture de la carte pour permettre le formatage d'une FSE. 
        </div>
      {{else}}
        <div class="message">
          Patient correctement associ� � un b�n�ficiaire Vitale. <br/>
          Formatage des FSE disponible pour ce patient.
        </div>
      {{/if}}
    </td>
  </tr>
  
  <tr>
    <!-- Professionnel de sant� -->
    <td class="button">
      {{if !$praticien->_id_cps}}
        <button class="search" type="button" onclick="Intermax.trigger('Lire CPS');">
          Lire CPS
        </button>
        <button class="change intermax-result" type="button" onclick="Intermax.result('Lire CPS');">
          Associer CPS
        </button>
      {{/if}}
    </td>

    <!-- Patient Vitale -->
    <td class="button">
      {{if $patient->_id_vitale}}
        <button class="search" type="button" onclick="Intermax.Triggers['Consulter Vitale']({{$patient->_id_vitale}});">
          Consulter Vitale
        </button>
      {{else}}
        <button class="search" type="button" onclick="Intermax.trigger('Lire Vitale');">
          Lire Vitale
        </button>
        <button class="change intermax-result" type="button" onclick="Intermax.result();">
          Associer Vitale
        </button>
      {{/if}}
    </td>
  </tr>
</table>