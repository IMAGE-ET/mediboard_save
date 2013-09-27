{{*
 * $Id$
 *  
 * @category Tasking
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<table class="main tbl">
  <tr>
    <td class="narrow">
      <img src="style/mediboard/images/icons/help.png" alt="{{tr}}Help{{/tr}}" onclick="Tasking.showHelp()"/>
    </td>
    <td>
      <form name="add-task" action="?" method="post" onsubmit="return Tasking.smartAddTaskingTicket(this);" >
        <input type="hidden" name="list_id" value="" />
        <input type="hidden" name="task_action" value="add" />
        <table class="form">
          <tr>
            <td>
              <button type="button" class="add notext" onclick="this.form.elements.task_multiple.up('tr').toggle()">{{tr}}Multiple{{/tr}}</button>
            </td>
            <td style="width: 100%;">
              <input type="text" id="task_smart" name="task_smart" style="width: 100%;" />
            </td>
            <td>
              <button type="submit" class="tick notext">{{tr}}Validate{{/tr}}</button>
            </td>
          </tr>
          <tr style="display: none;">
            <td colspan="3">
              <textarea name="task_multiple" rows="6" style="width: 100%" /></textarea>
              <div class="big-info">
                <strong>Nouveaux raccourcis pour les t�ches multiples</strong>
                <br />
                <ul>
                  <li>Parenth�ses ou crochets autoris�s : (raccourci) ou [raccourci]</li>
                  <li>Insensible � la casse : (P1) ou (p1)</li>
                  <li><code>OK</code> : T�che r�alis�e </li>
                  <li><code>nH</code> : T�che �valu�e � 'n' heures</li>
                  <li><code>Pn</code> : T�che de priorit� 'n'. Si 'n' > 3, pas de priorit�</li>
                  <li><code>--</code> : Sans OK, t�che abandonn�e : tag:cancelled, et r�alis�e; avec OK t�che � 0h</li>
                  <li><code>??</code> : T�che � d�finir : tag:definir, et priorit� !3</li>
                  <li><em>Pr�nom</em> : T�che assign�e au Mediuser correspondant</li>
                  <li><em>Autre cha�ne</em> : T�che avec une note 'Autre cha�ne'</li>
                  <li><em>Cha�ne vide</em> : Ne rien faire</li>
                </ul>
              </div>
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>
