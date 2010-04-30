{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7951 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

Main.add(function() {
  var planning = new WeekPlanning(
    '{{$planning->guid}}', 
    '{{$planning->hour_min}}', 
    '{{$planning->hour_max}}', 
    {{$planning->events|@json}}
  );
  Event.observe(window, "resize", planning.updateEventsDimensions.bind(planning));
});

</script>

<div class="planning">
  <table class="tbl" style="table-layout: fixed;">
    <col style="width: 3.0em;" />
    <col span="7" />
    <col style="width: 16px;" />
    
    <tr>
    	 <th class="title" colspan="9">{{$planning->title}}</th>
    </tr>
    <tr>
       <th></th>
    	 {{foreach from=$planning->days key=_day item=_events}}
         {{if $_day < $planning->date_min || $_day > $planning->date_max}}
           {{assign var=disabled value=true}}
         {{else}}
           {{assign var=disabled value=false}}
         {{/if}}
             
         <th class="day {{if $disabled}}disabled{{/if}}">
           {{$_day|date_format:"%a %d"|nl2br}}
           
           {{if array_key_exists($_day, $planning->day_labels)}}
             {{assign var=label value=$planning->day_labels.$_day}}
             <label style="background: {{$label.color}};" title="{{$label.detail}}">
               {{$label.text}}
             </label>
           {{/if}}
         </th>
    	 {{/foreach}}
       <th></th>
    </tr>
  </table>
  
  <div id="{{$planning->guid}}" style="overflow-y: scroll; overflow-x: hidden; height: 250px;">
    <table class="tbl hours" style="table-layout: fixed;">
      <col style="width: 3.0em;" />
      <col span="7" />
      
      {{assign var=prev_pause value=false}}
      
      {{foreach from=$planning->hours item=_hour}}
        <tr class="hour-{{$_hour}} {{if in_array($_hour, $planning->pauses)}}pause{{/if}} {{if $prev_pause}}pause-next{{/if}}">
          <th class="hour">{{$_hour}}h</th>
          
          {{foreach from=$planning->days key=_day item=_events}}
            {{if $_day < $planning->date_min || $_day > $planning->date_max}}
              {{assign var=disabled value=true}}
            {{else}}
              {{assign var=disabled value=false}}
            {{/if}}
            
            {{if array_key_exists($_day, $planning->unavailabilities)}}
              {{assign var=unavail value=true}}
            {{else}}
              {{assign var=unavail value=false}}
            {{/if}}
            
            <td class="segment-{{$_day}}-{{$_hour}} {{if $disabled}}disabled{{/if}} {{if $unavail}}unavailable{{/if}}">
              <div><!-- <<< This div is necessary (relative positionning) -->
              {{foreach from=$_events item=_event}}
                {{if $_event->hour == $_hour}}
                  <div id="{{$_event->internal_id}}" {{if $_event->guid}}onmouseover="ObjectTooltip.createEx(this, '{{$_event->guid}}');" {{if $planning->selectable}}onclick="this.toggleClassName('selected');"{{/if}}{{/if}} class="event {{$_event->css_class}} {{$_event->guid}}" style="background-color: {{$_event->color}}; {{if !$_event->important}}opacity: 0.6{{/if}}">
                    <!--
										<div class="time" title="{{$_event->start|date_format:"%H:%M"}}{{if $_event->length}} - {{$_event->end|date_format:"%H:%M"}}{{/if}}">
											{{$_event->start|date_format:"%H:%M"}}
                      {{if $_event->length}}
                       - {{$_event->end|date_format:"%H:%M"}}
                      {{/if}}
                    </div>
										-->
                    <div class="body">
                      {{$_event->title|smarty:nodefaults}}
                    </div>
                  </div>
                {{/if}}
              {{/foreach}}
              </div>
            </td>
            {{if in_array($_hour,$planning->pauses)}}
              {{assign var=prev_pause value=true}}
            {{else}}
              {{assign var=prev_pause value=false}}
            {{/if}}
          {{/foreach}}
          
        </tr>
      {{/foreach}}
    </table>
  </div>
</div>