{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !$group->service_urgences_id}}
  <div class="small-warning">{{tr}}dPurgences-no-service_urgences_id{{/tr}}</div>
  {{mb_return}}
{{/if}}

{{mb_script module=urgences   script=main_courante}}
{{mb_script module=urgences   script=uhcd}}
{{mb_script module=admissions script=identito_vigilance}}
{{mb_script module=patients   script=pat_selector}}
{{mb_script module=hprim21    script=pat_hprim_selector}}
{{mb_script module=hprim21    script=sejour_hprim_selector}}

{{if $isImedsInstalled}}
  {{mb_script module="Imeds" script="Imeds_results_watcher"}}
{{/if}}

<script>
  Consultations = {
    updater: null,
    start: function(frequency) {
      var url = new Url("cabinet", "vw_journee");
      url.addParam("date", "{{$date}}");
      url.addParam("mode_urgence", true);
      Consultations.updater = url.periodicalUpdate('consultations', { frequency: frequency } );
    },

    stop: function() {
      Consultations.updater.stop();
    }
  }

  onMergeComplete = function() {
    IdentitoVigilance.start(0, 80);
    MainCourante.start(1, 60);
  }

  reloadSynthese = function() {
    $$(".modal")[0].down("button.change").click();
  }

  showSynthese = function(sejour_id) {
    var url = new Url("soins", "ajax_vw_suivi_clinique");
    url.addParam("sejour_id", sejour_id);
    url.requestModal(800);
  }

  var ExtRefManager = {
    sejour_id : null,
    patient_id: null,

    submitIPPForm: function(patient_id) {
      ExtRefManager.patient_id = patient_id;
      var oForm = document.forms["editIPP" + patient_id];
      return onSubmitFormAjax(oForm, {onComplete: ExtRefManager.reloadIPPForm});
    },

    reloadIPPForm: function() {
      reloadAdmission();
    },

    submitNumdosForm: function(sejour_id) {
      ExtRefManager.sejour_id = sejour_id;
      var oForm = document.forms["editNumdos" + this.sejour_id];
      return onSubmitFormAjax(oForm, {onComplete: ExtRefManager.reloadNumdosForm});
    },

    reloadNumdosForm: function() {
      reloadAdmission();
    }
  };

  setExternalIds = function(oForm) {
    SejourHprimSelector["init"+oForm.sejour_id.value]();
  }

  PatHprimSelector.doSet = function(){
    var oForm = document[PatHprimSelector.sForm];
    $V(oForm[PatHprimSelector.sId], PatHprimSelector.prepared.id);
    ExtRefManager.submitIPPForm(oForm.patient_id.value);
  }

  SejourHprimSelector.doSet = function(){
    var oFormSejour = document[SejourHprimSelector.sForm];
    $V(oFormSejour[SejourHprimSelector.sId]  , SejourHprimSelector.prepared.id);
    ExtRefManager.submitNumdosForm(oFormSejour.object_id.value);
    if(SejourHprimSelector.prepared.IPPid) {
      var oFormIPP = document[SejourHprimSelector.sIPPForm];
      $V(oFormIPP[SejourHprimSelector.sIPPId]  , SejourHprimSelector.prepared.IPPid);
      ExtRefManager.submitIPPForm(oFormIPP.object_id.value);
    }
  }

  Main.add(function () {
    // Delays prevent potential overload with periodical previous updates

    // Main courante
    MainCourante.start(0, 60);

    // UHCD
    UHCD.date = "{{$date}}";
    UHCD.start(1, 80);

    // Reconvocations
    {{if $conf.dPurgences.gerer_reconvoc == "1"}}
      Consultations.start.delay(2, 100);
    {{/if}}

    // Identito-vigilance
    IdentitoVigilance.date = "{{$date}}";
    IdentitoVigilance.start(3, 120);

    var tabs = Control.Tabs.create('tab_main_courante', false);
  });

</script>

<ul id="tab_main_courante" class="control_tabs">
  <li style="float: right">
    <form action="?" name="FindSejour" method="get">
      <label for="sip_barcode" title="Veuillez doucher le num�ro de dossier sur un document ou bien le saisir � la main">
        Num�ro dossier
      </label>
        
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <input type="text" size="5" name="sip_barcode" onchange="this.form.submit()" />
      
      <button type="submit" class="search notext">{{tr}}Search{{/tr}}</button>
    </form>
  </li>
  <li><a href="#holder_main_courante">Main courante <small>(&ndash;)</small></a></li>
  <li><a href="#holder_uhcd" class="empty">UHCD <small>(&ndash;)</small></a></li>
	{{if $conf.dPurgences.gerer_reconvoc == "1"}}
  <li><a href="#consultations" class="empty">Reconvocations <small>(&ndash; / &ndash;)</small></a></li>
	{{/if}}
  <li><a href="#identito_vigilance" class="empty">Identito-vigilance <small>(&ndash;)</small></a></li>
  <li style="width: 20em; text-align: center">
    <script type="text/javascript">
    Main.add(function() {
      Calendar.regField(getForm("changeDate").date, null, { noView: true } );
    } );
    </script>
    <strong><big>{{$date|date_format:$conf.longdate}}</big></strong>
    
    <form action="?" name="changeDate" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
    </form>
  </li>
</ul>

<div id="holder_main_courante">
	<table style="width: 100%;">
	  <tr>
	    <td style="white-space: nowrap;" class="narrow">
	      <a class="button new" href="?m=dPurgences&amp;tab=vw_aed_rpu&amp;rpu_id=0">
	        {{tr}}CRPU-title-create{{/tr}}
	      </a>
	    </td>
			
	    <td style="text-align: left; padding-left: 2em;">
        {{mb_include template=inc_hide_missing_rpus}}
        {{mb_include template=inc_hide_previous_rpus}}
			</td>

	    <td style="text-align: right">
	      <form name="selView" action="?m=urgences&amp;tab=vw_idx_rpu" method="post">
          {{tr}}CService{{/tr}}
          <select name="service_id" onchange="this.form.submit()">
            <option value="" {{if !$service_id}}selected{{/if}}>&mdash; Tous les services</option>
            {{foreach from=$services item=_service}}
              <option value="{{$_service->_id}}" {{if $_service->_id == $service_id}}selected{{/if}}>{{$_service}}</option>
            {{/foreach}}
          </select>
          Affichage
		      <select name="selAffichage" onchange="this.form.submit();">
		        <option value="tous"               {{if $selAffichage == "tous"              }}selected{{/if}}>Tous</option>
		        <option value="presents"           {{if $selAffichage == "presents"          }}selected{{/if}}>Pr�sents</option>
		        <option value="prendre_en_charge"  {{if $selAffichage == "prendre_en_charge" }}selected{{/if}}>A PeC</option>
		        <option value="annule_hospitalise" {{if $selAffichage == "annule_hospitalise"}}selected{{/if}}>Annul� et Hospit.</option>
		      </select>
		    </form>
	      <a href="#" onclick="MainCourante.print('{{$date}}')" class="button print">Main courante</a>
	      <a href="#" onclick="MainCourante.legend()" class="button search">L�gende</a>
	    </td>
	  </tr>
	</table>
  
	<div id="main_courante"></div>
</div>

<div id="holder_uhcd" style="display: none;">
  <table style="width: 100%;" style="display: none;">
    <tr>
      <td style="text-align: right">
       Affichage
       <form name="UHCD-view" action="" method="post">
          <select name="uhcd_affichage" onChange="UHCD.refreshUHCD()">
            <option value="tous"              {{if $uhcd_affichage == "tous"             }}selected{{/if}}>Tous</option>
            <option value="presents"          {{if $uhcd_affichage == "presents"         }}selected{{/if}}>Pr�sents</option>
            <option value="prendre_en_charge" {{if $uhcd_affichage == "prendre_en_charge"}}selected{{/if}}>A PeC</option>
            <option value="annule"            {{if $uhcd_affichage == "annule"           }}selected{{/if}}>Annul�</option>
          </select>
        </form>
        <a href="#" onclick="MainCourante.legend()" class="button search">L�gende</a>
      </td>
    </tr>
  </table>
  
  <div id="uhcd">
    <div class="small-info">{{tr}}msg-common-loading-soon{{/tr}}</div>
  </div>
</div>

{{if $conf.dPurgences.gerer_reconvoc == "1"}}
<div id="consultations" style="display: none;">
  <div class="small-info">{{tr}}msg-common-loading-soon{{/tr}}</div>
</div>
{{/if}}

<div id="identito_vigilance" style="display: none; margin: 0 5px;">
  <div class="small-info">{{tr}}msg-common-loading-soon{{/tr}}</div>
</div>
