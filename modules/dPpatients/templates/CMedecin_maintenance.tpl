<script type="text/javascript">

var Process = {
  running: false,
  step: null,
  pass: {{$pass|json}},
  
  total: {
    medecins: 0,
    time: 0.0,
    updates: 0.0,
    errors: 0
  },
      
  doStep: function() {
    var form = getForm("importMedecinForm");
    
    this.step =  $V(form.step);
        
    var url = new Url("patients", "import_medecin");
    url.addElement(form.step);    
    url.addParam("mode", $V(form.mode));
    url.addParam("pass", this.pass);
    url.addParam("departement", $V(form.departement));
    url.addParam("mode_import", $V(form.mode_import));
    url.requestUpdate("process");
  },
  
  updateScrewed: function(medecins, time, updates, errors) {
    var tr = document.createElement("tr");
    var td;

    td = document.createElement("td");
    td.textContent = this.step;
    tr.appendChild(td);

    td = document.createElement("td");
    td.textContent = "XPAth Screwed, try again";
    tr.appendChild(td);

    $("results").appendChild(tr);
  },
  
  updateTotal: function(medecins, time, updates, errors) {
    this.total.medecins += medecins;
    this.total.time     += time;
    this.total.updates  += updates;
    this.total.errors   += errors;    

    var tr = document.createElement("tr");
    var td;
    td = document.createElement("td"); td.textContent = this.step; tr.appendChild(td);
    td = document.createElement("td"); td.textContent = medecins ; tr.appendChild(td);
    td = document.createElement("td"); td.textContent = time.toFixed(2)     ; tr.appendChild(td);
    td = document.createElement("td"); td.textContent = updates  ; tr.appendChild(td);
    td = document.createElement("td"); td.textContent = errors   ; tr.appendChild(td);
    
    var node = { tr: { td : [this.step, medecins, time, updates, errors] } };

    $("results").appendChild(tr);
    
    $("total-medecins").innerHTML = this.total.medecins;
    $("total-time"    ).innerHTML = this.total.time.toFixed(2);
    $("total-updates" ).innerHTML = this.total.updates;
    $("total-errors"  ).innerHTML = this.total.errors;    
  },
  
  endStep: function() {
    var form = getForm("import");
    var step = form.step;
    $V(step, parseInt($V(step))+1);
    if ($V(form.auto)) {
      Process.doStep.bind(Process).defer();
    }
  },
  
  nextDep: function() {
    var form = getForm("import");
    // Tester si on est sur le dernier d�partement
    if (form.departement.selectedIndex == form.departement.length) {
      return;
    }
    $V(form.step, 0);
    form.departement.selectedIndex++;
    this.endStep();
  }
};

function importSF(form) {
  var url = new Url("patients", "import_sages_femmes");
  url.addParam("pass", Process.pass);
  url.addElement(form.departement);
  url.requestUpdate("resultSF", {
    onComplete: function() {
      if (form.auto.checked) {
        var select = form.departement;
        if ((select.length -1) != select.selectedIndex) {
          select.selectedIndex += 1;
          importSF(form);
        }
      }
    },
    insertion: function(element, content){
      element.innerHTML += content;
    }
  });
}

function importKine(form) {
  var url = new Url("patients", "import_kines");
  url.addParam("pass", Process.pass);
  url.addElement(form.departement);
  url.requestUpdate("resultKine", {
    onComplete: function() {
      if (form.auto.checked) {
        var select = form.departement;
        if ((select.length -1) != select.selectedIndex) {
          select.selectedIndex += 1;
          importKine(form);
        }
      }
    },
    insertion: function(element, content){
      element.innerHTML += content;
    }
  });
}

function installMouvMedecinPatient() {
  var url = new Url('patients', 'install_mouv_medecin_patient');
  url.requestUpdate('installMouvMedecinPatient');
}

Main.add(function(){
  Control.Tabs.create("CMedecin-tab");
})
</script>

<ul class="control_tabs small" id="CMedecin-tab">
  <li><a href="#CMedecin-import">Importation de base</a></li>
  <li><a href="#CMedecin-install_trigger">Trigger medecin-patient</a></li>
  <li><a href="#sex_medecins">Table des sexes de Medecins</a></li>
  <li><a href="#CMedecin-tools">Outils</a></li>
</ul>

<div id="CMedecin-import" style="display: none;">
  <h2>Import de la base de donn�es de m�decins</h2>

  <table class="tbl">
    <tr>
      <th colspan="3" style="width: 50%">{{tr}}Action{{/tr}}</th>
      <th colspan="2" style="width: 50%">{{tr}}Status{{/tr}}</th>
    </tr>

    <tr>
      <td colspan="3">
        <form name="importMedecinForm" action="#" method="get" onsubmit="return false">
          <label>
            <input type="radio" name="mode" value="get" /> Import distant
          </label>
          <label>
            <input type="radio" name="mode" value="xml" /> Fichiers XML
          </label>
          <label>
            <input type="radio" name="mode" value="csv" checked="checked" /> Fichiers CSV
          </label>

          <input type="checkbox" name="auto" />
          <label for="auto">Automatique</label>
          &mdash;
          D�partement :
          <select name="departement">
            {{foreach from=$departements item=_departement}}
              <option value="{{$_departement}}">{{$_departement}}</option>
            {{/foreach}}
          </select>

          Mode d'import :
          <select name="mode_import">
            <option value="comp">Import complet</option>
            <option value="rpps">Mise � jour RPPS</option>
          </select>
          <br/>

          <label for="step">Etape</label>
          <input type="text" name="step" value="1" size="2" />

          <button class="tick" id="start-process" onclick="Process.doStep()">
            Traiter �tape
          </button>
        </form>
      </td>
      <td id="process" colspan="3"></td>
    </tr>

    <tbody id="results" style="text-align: right">
    <tr>
      <th>Etape #</th>
      <th>Nombre de m�decins</th>
      <th>Temps pris</th>
      <th>Mises � jour</th>
      <th>Erreurs</th>
    </tr>
    </tbody>

    <tr id="total" style="text-align: right">
      <th>Total</th>
      <td id="total-medecins"></td>
      <td id="total-time"></td>
      <td id="total-updates"></td>
      <td id="total-errors"></td>
    </tr>
  </table>

  <h2>
    Import de la base de donn�es des sages-femmes
  </h2>

  <table class="tbl">
    <tr>
      <th colspan="3" style="width: 50%">{{tr}}Action{{/tr}}</th>
      <th colspan="2" style="width: 50%">{{tr}}Status{{/tr}}</th>
    </tr>
    <tr>
      <td colspan="3" style="vertical-align: top">
        <form name="importSFForm" action="#" method="get" onsubmit="return false">
          <input type="checkbox" name="auto" />
          <label for="auto">Automatique</label>
          &mdash;
          D�partement :
          <select name="departement">
            {{foreach from=$departements item=_departement}}
              {{if is_numeric($_departement)}}
                <option value="{{$_departement}}">{{$_departement}}</option>
              {{/if}}
            {{/foreach}}
          </select>
          <button type="button" class="tick" onclick="importSF(this.form)">Traiter</button>
          <button type="button" class="cancel" onclick="$('resultSF').update()">Vider</button>
        </form>
      </td>
      <td id="resultSF"></td>
    </tr>
  </table>

  <h2>
    Import de la base de donn�es des kin�sith�rapeutes
  </h2>

  <table class="tbl">
    <tr>
      <th colspan="3" style="width: 50%">{{tr}}Action{{/tr}}</th>
      <th colspan="2" style="width: 50%">{{tr}}Status{{/tr}}</th>
    </tr>
    <tr>
      <td colspan="3" style="vertical-align: top">
        <form name="importKineForm" action="#" method="get" onsubmit="return false">
          <input type="checkbox" name="auto" />
          <label for="auto">Automatique</label>
          &mdash;
          D�partement :
          <select name="departement">
            {{foreach from=$departements item=_departement}}
              {{if is_numeric($_departement)}}
                <option value="{{$_departement}}">{{$_departement}}</option>
              {{/if}}
            {{/foreach}}
          </select>
          <button type="button" class="tick" onclick="importKine(this.form)">Traiter</button>
          <button type="button" class="cancel" onclick="$('resultKine').update()">Vider</button>
        </form>
      </td>
      <td id="resultKine"></td>
    </tr>
  </table>
</div>

<div id="CMedecin-install_trigger" style="display: none;">
  <h2>Mouvement (trigger) medecin-patient</h2>

  <table class="tbl">
    <tr>
      <th class="narrow">{{tr}}Action{{/tr}}</th>
      <th>{{tr}}Status{{/tr}}</th>
    </tr>
    <tr>
      <td>
        <button type="button" class="tick" onclick="installMouvMedecinPatient()">Installer le trigger</button>
      </td>
      <td class="text" id="installMouvMedecinPatient"></td>
    </tr>
  </table>
</div>

<div id="sex_medecins" style="display: none;">
  <div class="small-info">T�che de r�paration des sexes des correspondants M�decins</div>

  <form name="guess-medecin" method="post" onsubmit="return onSubmitFormAjax(this, {}, 'sex-medecin-log')">
    <input type="hidden" name="m" value="patients" />
    <input type="hidden" name="dosql" value="do_guess_massive_sex" />
    <input type="hidden" name="target_class" value="CMedecin"/>

    <table class="tbl">
      <tr>
        <th class="section">{{tr}}Action{{/tr}}</th>
        <th class="section">{{tr}}Status{{/tr}}</th>
      </tr>

      <tr>
        <td style="width: 20%">
          <table class="layout">
            <tr>
              <td><label> Automatique <input type="checkbox" name="callback" value="guess-medecin"/> </label></td>
            </tr>
            <tr>
              <td><label> Recommencer de z�ro <input type="checkbox" name="reset" value="1"/> </label></td>
            </tr>
            <tr>
              <td><button type="submit" class="tick">{{tr}}Go{{/tr}}</button></td>
            </tr>
          </table>
        </td>

        <td id="sex-medecin-log"></td>
      </tr>
    </table>
  </form>
</div>

<div id="CMedecin-tools" style="display: none;">
  <h2>Nettoyage des correspondants m�dicaux</h2>

  <div class="small-info">
    Cet outil permet d'effectuer une �puration des doublons de correspondants m�dicaux d�s � des importations en supprimant les doublons.
  </div>

  <form name="cleanup-correspondant" method="post" onsubmit="return onSubmitFormAjax(this, {}, 'cleanup-correspondant-log')">
    <input type="hidden" name="m" value="patients" />
    <input type="hidden" name="dosql" value="do_cleanup_correspondant" />

    <table class="tbl">
      <tr>
        <th class="section">{{tr}}Action{{/tr}}</th>
        <th class="section">{{tr}}Status{{/tr}}</th>
      </tr>

      <tr>
        <td style="width: 20%">
          <table class="layout">
            <tr>
              <td><label> Traiter les doublons qui sont plus de <input type="number" name="count_min" value="50" size="5" /> </label></td>
            </tr>
            <tr>
              <td><label> Dry run (n'effectue pas de suppression) <input type="checkbox" name="dry_run" value="1" checked /> </label></td>
            </tr>
            <tr>
              <td><button type="submit" class="tick">{{tr}}Clean up{{/tr}}</button></td>
            </tr>
          </table>
        </td>

        <td id="cleanup-correspondant-log"></td>
      </tr>
    </table>
  </form>
</div>
