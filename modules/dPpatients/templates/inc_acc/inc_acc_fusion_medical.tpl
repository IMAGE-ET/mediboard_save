{{assign var=i value=200}}
<table class="form">
  <tr>
    <th class="category">Champ</th>
    <th width="30%" class="category">1er patient</th>
    <th width="30%" class="category">2�me patient</th>
    <th width="30%" class="category">R�sultat</th>
  </tr>
  
  <tr>
    <th class="title" colspan="4">B�n�ficiaire de soins</th>
  </tr>
  {{include field="code_regime"      file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="caisse_gest"      file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="centre_gest"      file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="deb_amo"          file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="fin_amo"          file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="code_exo"         file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="code_sit"         file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="regime_am"        file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="ald"              file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="cmu"              file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="notes_amo"        file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="incapable_majeur" file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}
  {{include field="ATNC"             file="inc_acc/inc_fusion_field.tpl"}}{{assign var=i value=$i+1}}

  <tr>
    <th class="title" colspan="4">M�decins correspondants</th>
  </tr>
  {{include field="medecin_traitant" file="inc_acc/inc_fusion_field_ref.tpl"}}
  {{include field="medecin1"         file="inc_acc/inc_fusion_field_ref.tpl"}}
  {{include field="medecin2"         file="inc_acc/inc_fusion_field_ref.tpl"}}
  {{include field="medecin3"         file="inc_acc/inc_fusion_field_ref.tpl"}}
</table>