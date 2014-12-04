/* $Id$ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

PlanningEvent = Class.create({
  initialize: function(event, planning) {
    Object.extend(this, event);
    this.planning = planning;
  }, 
  updateDimensions: function(){
    var container = $(this.internal_id);
    if (!container) return;

    var height = this.planning.getCellHeight() / 60;
   
    container.style.top    = (this.minutes * height)+"px";
    container.style.left   = (this.offset * 100)+"%";
    container.style.width  = (this.width * 100)+"%";
    container.style.height =  container.style.minHeight = ((this.length * height) || 1)+"px";
  },
  getElement: function(){
    return $(this.internal_id);
  },
  getTime: function(){
    
    var element = this.getElement();
    
    var divider = this.hour_divider || this.planning.hour_divider;
    
    var minutes = 60/divider;
    var cellHeight = this.planning.getCellHeight();
    var cellWidth = element.up().getWidth();
    
    var offset = {
      date: Math.round(element.offsetLeft / parseInt(cellWidth)),
      time: Math.round((element.offsetTop / cellHeight) * divider) / divider
    };
    
    if (this.planning.no_dates) {
      var date = element.up("td").className.match(/segment-([\d-]+)-(\d{2})/i);
      
      // Date fictive permettant de retrouver l'index de la colonne sur l'ann�e
      var annee = 2000+parseInt(date[1]);
      
      date = Date.fromDATETIME(annee+"-01-01 " + date[2]+":00:00");
      date.addYears(offset.date);
    }
    else {
      var date = element.up("td").className.match(/segment-([\d-]{10})-(\d{2})/i);
      date = Date.fromDATETIME(date[1] + " " + date[2] + ":00:00");
      date.addDays(offset.date);
    }
    
    date.addMinutes(offset.time * 60);
    
    var end = new Date(date);
    end.addMinutes((Math.round((element.getHeight() / cellHeight) * divider) / divider) * 60);
    
    return {
      start: date,
      end: end,
      length: (end - date) / (1000 * 60)
    };
  },
  getTimeString: function(){
    var time = this.getTime();
    return DateFormat.format(time.start, "HH:mm") + " - " + DateFormat.format(time.end, "HH:mm");
  },
  onChange: function(){
    this.planning.scrollTop = this.planning.container.down('.week-container').scrollTop;
    return this.planning.onEventChange(this);
  },
  setDraggable: function(resizable){
    (function(){
      var planning = this.planning;
      var element = this.getElement();
      var parent = element.up("td");
      var snap = [parent.getWidth(), planning.getCellHeight()/(this.hour_divider || planning.hour_divider)];
      
      // draggable
      new Draggable(element, {
        snap: snap, 
        handle: element.down(".handle"), 
        change: PlanningEvent.Drag.onDragPosition.bind(planning), 
        onEnd: PlanningEvent.Drag.onEndPosition.bind(planning)
      });

      // resizable
      if (resizable) {
        new Draggable(element.down(".footer"), {
          constraint: "vertical", 
          snap: snap, 
          change: PlanningEvent.Drag.onDragSize.bind(planning),
          onEnd: PlanningEvent.Drag.onEndSize.bind(planning)
        });
      }
    }.bind(this)).defer();
  }
});

Object.extend(PlanningEvent, {
  onMouseOver: Prototype.emptyFunction
});

PlanningEvent.Drag = {
  showTime: function(elt, event){
    elt.down(".time-preview").update(event.getTimeString()).show();
  },
  hideTime: function (elt){
    elt.down(".time-preview").hide();
  },
  onDragSize: function (d){
    var grip = d.element;
    var e = grip.up();
    e.style.height = (grip.offsetTop+grip.getHeight())+"px";
    var event = this.getEventById(e.id);
    PlanningEvent.Drag.showTime(e, event);
  },
  onDragPosition: function(d){
    var event = this.getEventById(d.element.id);
    PlanningEvent.Drag.showTime(d.element, event);
  },
  onEndPosition: function(d){
    var event = this.getEventById(d.element.id);
    PlanningEvent.Drag.hideTime(d.element);
    event.onChange();
  },
  onEndSize: function(d){
    var element = d.element.up();
    var event = this.getEventById(element.id);
    PlanningEvent.Drag.hideTime(element);
    event.onChange();
  }
};

PlanningRange = Class.create({
  initialize: function(event, planning) {
    Object.extend(this, event);
    this.planning = planning;
  }, 
  updateDimensions: function(){
    var container = $(this.internal_id);
    if (!container) return;

    var height = this.planning.getCellHeight() / 60;
   
    container.style.top    = (this.minutes * height)+"px";
    container.style.height = ((this.length * height) || 1)+"px";
  },
  getElement: function(){
    return $(this.internal_id);
  }
});

WeekPlanning = Class.create({
  scrollTop: null,
  load_data: [],
  maximum_load: null,
  dragndrop: false,
  resizable: false,
  no_dates: false,
  initialize: function(guid, hour_min, hour_max, events, ranges, hour_divider, scroll_top, adapt_range, selectable, dragndrop, resizable, no_dates) {
    var pref_dragndrop = Preferences.ssr_planning_dragndrop == 1;
    var _dragndrop = (pref_dragndrop || dragndrop == 1);
    var _resizable = (pref_dragndrop || resizable == 1);
    
    this.eventsById = {};
    for (var i = 0, l = events.length; i < l; i++) {
      events[i] = new PlanningEvent(events[i], this);
      var _event = this.eventsById[events[i].internal_id] = events[i];
      
      if (_dragndrop && _event.draggable) {
        _event.setDraggable(_resizable && _event.resizable);
      }
    }
    
    this.rangesById = {};
    for (var i = 0, l = ranges.length; i < l; i++) {
      ranges[i] = new PlanningRange(ranges[i], this);
      this.rangesById[ranges[i].internal_id] = ranges[i];
    }
    
    this.no_dates = no_dates;
    this.container = $(guid);
    this.hour_min = hour_min;
    this.hour_max = hour_max;
    this.events = events;
    this.ranges = ranges;
    this.hour_divider = hour_divider;
    this.adapt_range = adapt_range;
    this.selectable = selectable;
    this.dragndrop = dragndrop;
    
    // Event observation
    if (this.selectable) {
      this.observeEvent('click', function(event){
        event.toggleClassName('selected');
        this.updateNbSelectEvents();
      }.bind(this));
    }
    
    this.observeEvent('mouseover', PlanningEvent.onMouseOver);
    this.observeEvent('dblclick', PlanningEvent.onDblClic);
  },
  scroll: function(scroll_top) {
    if (this.container.down(".hour-"+this.hour_min)) {
      var top = this.container.down(".hour-"+this.hour_min).offsetTop;
      this.container.down('.week-container').scrollTop = (scroll_top !== null && !Object.isUndefined(scroll_top) ? scroll_top : top);
    }
  },
  setPlanningHeight: function(height) {
    var top = this.container.down("table").getHeight();
    this.container.down('.week-container').style.height = height - parseInt(top, 10) + "px";

    if (this.adapt_range) {
      this.adaptRangeHeight(); 
    }
    
    this.updateEventsDimensions();
    this.updateRangesDimensions();
  },
  adaptRangeHeight: function(){
    var weekContainer = this.container.down('.week-container table');
    var viewportHeight = this.container.down('.week-container').getHeight();
    var delta = parseInt(this.hour_max, 10) - parseInt(this.hour_min, 10) + 1;
    var visibleLines = this.countVisibleLines();
    var pauses = this.countPauses();
    var pausesHeight = pauses * 3; // cf. CSS 3px border bottom
    this._tableHeight = null;
    weekContainer.style.height = ((viewportHeight - pauses * 3)/ delta) * visibleLines + "px";
  },
  updateEventsDimensions: function(){
    this.events.invoke("updateDimensions");
  },
  updateRangesDimensions: function(){
    this.ranges.invoke("updateDimensions");
  },
  selectAllEvents: function(){
    this.container.select('.event:not(.now)').invoke('toggleClassName','selected');
    this.updateNbSelectEvents();
  },
  selectDayEvents: function(day){
    this.container.select('.day-'+day+' .event:not(.now)').invoke('toggleClassName','selected');
    this.updateNbSelectEvents();
  },
  updateNbSelectEvents : function(){
    this.container.down('.nbSelectedEvents').update("("+this.container.select('.event.selected').length+")");
  },
  getEventById: function(id) {
    return this.eventsById[id];
  },
  getRangeById: function(id) {
    return this.rangesById[id];
  },
  countVisibleLines: function(){
    return this.container.select(".week-container table tbody tr.hour_line").filter(Element.visible).length;
  },
  countPauses: function() {
    return this.container.select(".week-container tr.pause").length;
  },
  getCellHeight: function(){
    var tableHeight = this._tableHeight || this.container.down(".week-container table").getHeight();
    this._tableHeight = tableHeight;
    return tableHeight / this.countVisibleLines();
  },
  onEventChange: function(e){
    
  },
  setLoadData: function(load_data, maximum_load){
    this.load_data = load_data;
    this.maximum_load = maximum_load;
    
    if (!this.load_data) return;
    
    var cellHeight = this.getCellHeight();
    var height = Math.ceil(cellHeight / this.hour_divider);
    
    // Day
    $H(this.load_data).each(function(day){
      if (day.value.length === 0 || typeof day.value == "function") return;
      
      // Hour
      $H(day.value).each(function(hour){
        
        // Minute
        $H(hour.value).each(function(load){
          var container = $(this.container.id+"-"+day.key+"-"+hour.key+"-"+load.key);
          var top = Math.ceil(cellHeight * (load.key / this.hour_divider) / 10);
          container.style.top    = top+"px";
          container.style.height = height+"px";
        }, this);
      }, this);
    }, this);
  },
  observeEvent: function(eventName, handler) {
    if (!handler) return;
    
    this.container.observe(eventName, function(event) {
      var element = event.element();
      if (element.tagName == "DIV") {
        var div = element.hasClassName("event") ? element : element.up("div.event");
        if (div) handler(div, event);
      }
    });
  },
  /*Cette fonction (vide) permet d'instancier dans le semainier les �v�nements des menus*/
  onMenuClick: function(event, data, elem){
  },
  showHalf: function() {
    var table = $("planningWeek");
    var top = parseInt(table.down("td").getStyle("height")) / 2;
    table.select(".show_half").each(function(elt) {
      elt.setStyle({top: top+'px'});
    });
  }
});