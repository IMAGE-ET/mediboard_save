<table class="main">
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th>
            Liste des fonctions disponibles
            <a href="{{$serviceURL}}" title="Acc�s direct">
            	(acc�der directement au serveur)
            </a>
          </th>
        </tr>
        {{foreach from=$functions item=_function}}
        <tr>
          <td>
            {{$_function}}
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>