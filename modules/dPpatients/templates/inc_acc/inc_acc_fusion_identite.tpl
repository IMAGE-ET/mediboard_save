{{assign var=i value=100}}
<table class="form">
  <tr>
    <th class="category">Champ</th>
    <th width="30%" class="category">1er patient</th>
    <th width="30%" class="category">2�me patient</th>
    <th width="30%" class="category">R�sultat</th>
  </tr>
  
  <tr>
    <th class="title" colspan="4">Identit�</th>
  </tr>
  {{include field="nom"               file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="prenom"            file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="nom_jeune_fille"   file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="sexe"              file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="naissance"         file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="rang_naissance"    file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="lieu_naissance"    file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="nationalite"       file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="profession"        file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="matricule"         file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="rang_beneficiaire" file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  
  <tr>
    <th class="title" colspan="4">Coordonn�es</th>
  </tr>
  {{include field="adresse"    file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="cp"         file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="ville"      file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="pays"       file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="tel"        file="inc_acc/inc_fusion_field_tel.tpl" field_name=""}}{{assign var=i value=$i+5}}
  {{include field="tel2"       file="inc_acc/inc_fusion_field_tel.tpl" field_name=""}}{{assign var=i value=$i+5}}
  {{include field="fin_validite_vitale" file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="rques"      file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
</table>