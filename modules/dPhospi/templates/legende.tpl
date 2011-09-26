{{if $conf.dPhospi.CLit.alt_icons_sortants}}
  {{assign var=suffixe_icons value="2"}}
{{else}}
  {{assign var=suffixe_icons value=""}}
{{/if}}

<table class="tbl">
  <tr>
    <th class="title">Icone</th>
    <th class="title">Description</th>
  </tr>
  <tr>
    <th colspan="2">Etat des patients</th>
  </tr>
  <tr>
    <td style="text-align: right;"><img src="modules/dPhospi/images/X{{$suffixe_icons}}.png" name="sortant ce soir" /></td>
    <td class="text">Ambulatoire sortant ce soir</td>
  </tr>
  <tr>
    <td style="text-align: right;"><img src="modules/dPhospi/images/O{{$suffixe_icons}}.png" name="sortant demain" /></td>
    <td class="text">Hospitalisation sortant demain</td>
  </tr>
  <tr>
    <td style="text-align: right;"><img src="modules/dPhospi/images/Oo{{$suffixe_icons}}.png" name="sortant aujourd'hui" /></td>
    <td class="text">Hospitalisation sortant aujourd'hui</td>
  </tr>
  <tr>
    <td style="text-align: right;"><img src="modules/dPhospi/images/OC{{$suffixe_icons}}.png" name="d�plac� demain" /></td>
    <td class="text">Hospitalisation d�plac�e demain</td>
  </tr>
  <tr>
    <td style="text-align: right;"><img src="modules/dPhospi/images/OoC{{$suffixe_icons}}.png" name="d�plac� aujourd'hui" /></td>
    <td class="text">Hospitalisation d�plac�e aujourd'hui</td>
  </tr>
  <tr>
    <td style="text-align: right;"><i><strong>M. X y</strong></i></td>
    <td class="text">Patient en ambulatoire</td>
  </tr>
  <tr>
    <td style="text-align: right; font-weight: bold;" class="patient-not-arrived">M. X y 27/03 17h00</td>
    <td class="text">Patient qui doit arriver dans cette chambre � 17h00 le 27/03</td>
  </tr>
  <tr>
    <td style="text-align: right; font-weight: bold;" class="septique">M. X y</td>
    <td class="text">Patient septique</td>
  </tr>
  <tr>
    <td style="background-image:url(images/icons/ray.gif); background-repeat:repeat; text-align: right; font-weight: bold;">M. X y</td>
    <td class="text">Patient dont la sortie aujourd'hui est confirm�e</td>
  </tr>
  <tr>
    <th colspan="2">Alertes</th>
  </tr>
  <tr>
    <td style="text-align: right;"><img src="modules/dPhospi/images/double.png" name="chambre double possible" /></td>
    <td class="text">Chambre double possible</td>
  </tr>
  <tr>
    <td style="text-align: right;"><img src="modules/dPhospi/images/seul.png" name="chambre simple obligatoire" /></td>
    <td class="text">Chambre simple obligatoire</td>
  </tr>
  <tr>
    <td style="text-align: right;"><img src="modules/dPhospi/images/surb.png" name="colision" /></td>
    <td class="text">Colision : deux patients dans un m�me lit</td>
  </tr>
  <tr>
    <td style="text-align: right;"><img src="modules/dPhospi/images/sexe.png" name="conflit de sexe" /></td>
    <td class="text">Un homme et une femme dans la m�me chambre</td>
  </tr>
  <tr>
    <td style="text-align: right;"><img src="modules/dPhospi/images/age.png" name="ecart d'age important" /></td>
    <td class="text">Ecart d'age important : plus de 15 ans d'�cart</td>
  </tr>
  <tr>
    <td style="text-align: right;"><img src="modules/dPhospi/images/prat.png" name="conflit de praticiens" /></td>
    <td class="text">Conflit de praticiens : deux patients op�r�s par deux medecins de m�me sp�cialit� dans la m�me chambre</td>
  </tr>
  <tr>
    <td style="text-align: right;"><img src="modules/dPhospi/images/path.png" name="conflit de pathologie" /></td>
    <td class="text">Pathologies incompatibles dans la m�me chambre</td>
  </tr>
  <tr>
    <td style="text-align: right;"><img src="modules/dPhospi/images/annule.png" name="Chambre plus utilis�e" /></td>
    <td class="text">Chambre plus utilis�e</td>
  </tr>
</table>