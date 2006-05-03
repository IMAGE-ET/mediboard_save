<script language="JavaScript" type="text/javascript">
{literal}

function doAction(sAction) {
  var url = new Url();
  url.setModuleAction("dPinterop", sAction);
  url.addParam("u", "import/dermato");
  url.popup(400, 400, sAction);
}  

{/literal}
</script>

<h1>Proc�dure</h1>
<ol>
  <li>
    <a href="javascript:doAction('dermato_create_tables')">
      Cr�er les tables qui accueilleront les donn�es
    </a>
  </li>
  <li>Pour chaque import, placer le fichier TXT correspondant dans la racine du module dPinterop.
    <ul>
      <li><a href="javascript:doAction('dermato_patients')">Import des patients</a> : PATIENT.TXT, (le concat�ner avec PATIENT_RDV.TXT)</li>
      <li><a href="javascript:doAction('dermato_praticiens')">Import des praticiens</a> : PRATICIEN.TXT</li>
      <li><a href="javascript:doAction('dermato_medecins')">Import des m�decins traitants</a> : MED_TRAITANT.TXT</li>
      <li><a href="javascript:doAction('dermato_consult1')">Import des consultations (horaire)</a> : PLAGE CS.TXT</li>
      <li><a href="javascript:doAction('dermato_consult2')">Import des consultations (examen)</a> : CONSULTATIONS.TXT</li>
      <li><a href="javascript:doAction('dermato_rdv');">Import RDV � venir</a> : RDV.TXT</li>
    </ul>
  </li>
  <li>
    <a href="javascript:doAction('dermato_add_mb_id')">
      Ajouter un champ mb_id, indiquant la cl� de l'objet correspondant dans la base Mediboard
    </a>
  </li>
  <li>
    Voici les scripts � lancer :
    <ul>
      <li><a href="javascript:doAction('dermato_put_praticiens')">Envoie des praticiens vers Mediboard</a> (!! Attention, il faut le faire � la main !!)</li>
      <li><a href="javascript:doAction('dermato_put_medecins')">Envoie des medecins vers Mediboard</a></li>
      <li><a href="javascript:doAction('dermato_put_patients')">Envoie des patients vers Mediboard</a></li>
      <li><a href="javascript:doAction('dermato_put_consult')">Envoie des consultations vers Mediboard</a></li>
      <li><a href="javascript:doAction('dermato_put_rdv')">Envoie des rdv � venir vers Mediboard</a></li>
    </ul>
  </li>

  <li>
    Ajout des fichiers li�s :
    <ul>
      <li>Placer le dossier doc_recus et le fichier doc_recus.txt dans le module dPinterop</li>
      <li><a href="javascript:doAction('dermato_files')">Import des fichiers</a> : doc_recus.txt</li>
      <li><a href="javascript:doAction('dermato_put_files')">Envoie des fichiers vers Mediboard</a></li>
      <li>Les fichiers ainsi envoy�s iront se placer (le cas �ch�ant) dans la derni�re consultation du patient li�</li>
      <li>Placer le dossier courriers et le fichier chemin_courrier.txt dans le module dPinterop</li>
      <li><a href="javascript:doAction('dermato_courriers')">Import des courriers</a> : chemin_courrier.txt</li>
      <li><a href="javascript:doAction('dermato_put_courriers')">Envoie des courriers vers Mediboard</a></li>
    </ul>
  </li>
  
</ol>