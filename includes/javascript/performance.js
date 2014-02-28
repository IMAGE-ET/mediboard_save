/**
 *
 * @param {String} type
 * @param {Object} pageInfo
 * @param {Number} time
 * @param {Number} duration
 * @param {PerformanceResourceTiming,PerformanceTiming} perfTiming
 * @param {Object} serverTiming
 *
 * @constructor
 */
function MbPerformanceTimeEntry(type, pageInfo, time, duration, perfTiming, serverTiming){
  this.type = type;
  this.pageInfo = pageInfo;

  this.time = time;
  this.duration = duration;

  this.perfTiming = perfTiming;
  this.serverTiming = serverTiming;
}

MbPerformance = {
  version: "0.2",

  /** {MbPerformanceTimeEntry[]} timeline */
  timeline: [],
  timers: {},
  intervalTimer: null,
  profiling: false,
  pageDetail: null,
  timingSupport: window.performance && window.performance.timing,
  timeScale: 5,
  timeOffset: null,
  types: {
    page:   1,
    ajax:   2,
    mark:   3,
    chrono: 4
  },
  responsabilityColors: {
    network: "#0000FF",
    server:  "#00FF00",
    client:  "#FF0000",
    other:   "#999999"
  },
  markingTypes: {
    redirect: {
      color: "rgba(184,125,0,0.2)",
      resp:  "other",
      label: "Redirection",
      desc:  "Temps de la redirection",
      start: "redirectStart",
      end:   "redirectEnd"
    },

    fetch: {
      color: "rgba(255,41,41,0.2)",
      resp:  "client",
      label: "Cache",
      desc:  "Temps de recherche dans le cache",
      start: "fetchStart",
      end:   "domainLookupStart"
    },

    // network request
    networkRequest: {
      color: "rgba(41,144,255,0.2)",
      resp:  "network",
      label: "Requ�te",
      desc:  "Temps d'initalisation de la connexion en envoi de la requ�te",
      start: "domainLookupStart",
      end:   "requestStart"
    },
    domainLookup: {
      sub: true,
      color: "rgba(41,144,255,0.2)",
      resp:  "network",
      label: "DNS",
      desc:  "R�solution du nom de domaine (DNS)",
      start: "domainLookupStart",
      end:   "domainLookupEnd"
    },
    connect: {
      sub: true,
      color: "rgba(41,144,255,0.2)",
      resp:  "network",
      label: "Connexion",
      desc:  "Initalisation de la connexion",
      start: "connectStart",
      end:   "connectEnd"
    },
    request: {
      sub: true,
      color: "rgba(41,144,255,0.2)",
      resp:  "network",
      label: "Requ�te",
      desc:  "Envoi de la requ�te",
      start: "connectEnd",
      end:   "requestStart"
    },

    // Server
    /*server: {
      color: "rgba(14,168,0,0.2)",
      resp:  "server",
      label: "Serveur",
      desc:  "Temps pass� sur le serveur",
      start: "requestStart",
      end:   "responseStart"
    },*/
    handler: {
      color: "rgba(14,168,0,0.2)",
      resp:  "server",
      label: "Apache",
      desc:  "Temps de la requ�te Apache",
      getValue: function(serverTiming, perfTiming){
        if (!serverTiming.handlerEnd || !serverTiming.handlerStart) {
          return null;
        }

        return (serverTiming.handlerEnd - serverTiming.handlerStart);
      },
      getStart: function(serverTiming, perfTiming){
        if (!serverTiming.handlerStart) {
          return null;
        }

        return serverTiming.handlerStart;
      }
    },
    handlerInit: {
      sub: true,
      color: "rgba(14,168,0,0.2)",
      resp:  "server",
      label: "Apache init.",
      desc:  "Initalisation d'Apache",
      getValue: function(serverTiming, perfTiming){
        return serverTiming.start - serverTiming.handlerStart;
      },
      getStart: function(serverTiming, perfTiming){
        return serverTiming.handlerStart;
      }
    },
    frameworkInit: {
      sub: true,
      color: "rgba(14,168,0,0.2)",
      resp:  "server",
      label: "Fx init",
      desc:  "Initalisation du framework",
      getValue: function(serverTiming, perfTiming){
        return serverTiming.steps.find(function(step){ return step.label === "init"; }).dur;
      },
      getStart: function(serverTiming, perfTiming){
        return serverTiming.steps.find(function(step){ return step.label === "init"; }).time;
      },
      getMemory: function(serverTiming, perfTiming){
        return serverTiming.steps.find(function(step){ return step.label === "init"; }).mem;
      }
    },
    session: {
      sub: true,
      color: "rgba(14,168,0,0.2)",
      resp:  "server",
      label: "Session",
      desc:  "Ouverture de la session",
      getValue: function(serverTiming, perfTiming){
        return serverTiming.steps.find(function(step){ return step.label === "session"; }).dur;
      },
      getStart: function(serverTiming, perfTiming){
        return serverTiming.steps.find(function(step){ return step.label === "session"; }).time;
      },
      getMemory: function(serverTiming, perfTiming){
        return serverTiming.steps.find(function(step){ return step.label === "session"; }).mem;
      }
    },
    framework: {
      sub: true,
      color: "rgba(14,168,0,0.2)",
      resp:  "server",
      label: "Framework",
      desc:  "Suite du chargement du framework",
      getValue: function(serverTiming, perfTiming){
        var total = 0;
        serverTiming.steps.each(function(step){
          if (["init", "session", "app"].indexOf(step.label) == -1) {
            total += step.dur;
          }
        });
        return total;
      },
      getStart: function(serverTiming, perfTiming){
        var start = 0;
        serverTiming.steps.each(function(step){
          if (step.label == "session") {
            start = step.time + step.dur;
          }
        });
        return start;
      },
      getMemory: function(serverTiming, perfTiming){
        var mem = 0;
        serverTiming.steps.each(function(step){
          if (["init", "session", "app"].indexOf(step.label) == -1) {
            mem = Math.max(mem, step.mem);
          }
        });
        return mem;
      }
    },
    app: {
      sub: true,
      color: "rgba(14,168,0,0.2)",
      resp:  "server",
      label: "App.",
      desc:  "Code applicatif (d�pend de la page affich�e) et construction de la page",
      getValue: function(serverTiming, perfTiming){
        return serverTiming.steps.find(function(step){ return step.label === "app"; }).dur;
      },
      getStart: function(serverTiming, perfTiming){
        return serverTiming.steps.find(function(step){ return step.label === "app"; }).time;
      },
      getMemory: function(serverTiming, perfTiming){
        return serverTiming.steps.find(function(step){ return step.label === "app"; }).mem;
      }
    },
    output: {
      sub: true,
      color: "rgba(14,168,0,0.2)",
      resp:  "server",
      label: "Sortie",
      desc:  "Sortie texte (output buffer)",
      getValue: function(serverTiming, perfTiming){
        if (!serverTiming.handlerEnd || !serverTiming.handlerStart) {
          return null;
        }

        var apacheTime = serverTiming.handlerEnd - serverTiming.handlerStart;
        var total = 0;
        serverTiming.steps.each(function(step){
          total += step.dur;
        });

        return apacheTime - total - (serverTiming.start - serverTiming.handlerStart); // (- apache init)
      },
      getStart: function(serverTiming, perfTiming){
        var start = 0;
        serverTiming.steps.each(function(step){
          if (step.label == "app") {
            start = step.time + step.dur;
          }
        });
        return start;
      }
    },

    // response
    response: {
      color: "rgba(41,144,255,0.2)",
      resp:  "network",
      label: "R�ponse",
      desc:  "Temps de r�ponse",
      getValue: function(serverTiming, perfTiming){
        return perfTiming.responseEnd - serverTiming.handlerEnd;
      },
      getStart: function(serverTiming, perfTiming){
        return serverTiming.handlerEnd;
      }
    },
    otherInfra: {
      sub: true,
      color: "rgba(41,144,255,0.2)",
      resp:  "network",
      label: "Autre infra",
      desc:  "Autre temps, acheminement de la requ�te et de la page",
      getValue: function(serverTiming, perfTiming){
        return perfTiming.responseStart - serverTiming.handlerEnd;
      },
      getStart: function(serverTiming, perfTiming){
        return serverTiming.handlerEnd;
      }
    },
    download: {
      sub: true,
      color: "rgba(41,144,255,0.2)",
      resp:  "network",
      label: "T�l�chargement",
      desc:  "Temps de t�l�chargement de la r�ponse",
      start: "responseStart",
      end:   "responseEnd"
    },

    // client
    dom: {
      color: "rgba(255,41,41,0.2)",
      resp:  "client",
      label: "Page",
      desc:  "Temps de lecture de la page",
      start: "domLoading",
      end:   "domComplete"
    },
    domInit: {
      sub: true,
      color: "rgba(255,41,41,0.2)",
      resp:  "client",
      label: "Init. DOM",
      desc:  "Temps d'init arbre DOM",
      start: "responseEnd",
      end:   "domLoading"
    },
    domLoading: {
      sub: true,
      color: "rgba(255,41,41,0.2)",
      resp:  "client",
      label: "Constr. DOM",
      desc:  "Temps de construction de l'arbre DOM",
      start: "domLoading",
      end:   "domContentLoadedEventStart"
    },
    domContentLoadedEvent: {
      sub: true,
      color: "rgba(255,41,41,0.2)",
      resp:  "client",
      label: "Charg. DOM",
      desc:  "Temps de l'�v�nement d'ex�cution des scripts suivant le chargement de l'arbe DOM",
      start: "domContentLoadedEventStart",
      end:   "domComplete"
    },
    loadEvent: {
      sub: true,
      color: "rgba(255,41,41,0.2)",
      resp:  "client",
      label: "Charg. contenu",
      desc:  "Temps de t�l�chargement des contenus externes (images, etc)",
      start: "domComplete",
      end:   "loadEventEnd"
    }
  },

  addEvent: function(eventName, callback) {
    if (window.addEventListener) {
      window.addEventListener(eventName, callback, false);
    }
    else {
      window.attachEvent("on"+eventName, callback);
    }
  },

  parseServerTiming: function(str) {
    var timing = /D=(\d+) t=(\d+)/.exec(str);
    if (!timing) {
      return null;
    }

    return {
      duration: timing[1] / 1000,
      start:    timing[2] / 1000
    };
  },

  toggleProfiling: function(){
    var cookie =  new CookieJar();
    var profiling = cookie.get("profiling");

    this.profiling = false;

    if (!MbPerformance.timingSupport) {
      alert("Votre navigateur ne permet pas d'activer le profilage de performances.");
      return;
    }

    if (profiling == 1) {
      cookie.put("profiling", 0);
      clearTimeout(this.intervalTimer);
    }
    else {
      if (confirm("Vous allez activer le mode 'profilage de performances' de Mediboard, ce qui peut ralentir Mediboard, voulez-vous continuer ?")) {
        cookie.put("profiling", 1);
        this.profiling = true;
        MbPerformance.startPlotting();
      }
    }
  },

  /**
   * Don't use cookiejar as it may not be ready yet
   *
   * @returns {String,null}
   */
  readCookie: function(cookieName) {
    cookieName = cookieName || "mediboard-profiling";
    var value = new RegExp(cookieName+"=([^;]+)").exec(document.cookie);
    if (!value) {
      return null;
    }

    return decodeURI(value[1]);
  },

  init: function(){
    if (!MbPerformance.timingSupport) {
      return;
    }

    // defer, but not with defer() because prototype is not here yet !
    try {
      MbPerformance.profiling = MbPerformance.readCookie() == '"1"';
      MbPerformance.addEvent("load", function(){
        setTimeout(function(){
          MbPerformance.startPlotting();
        }, 1000);
      });
    }
    catch (e) {}
  },

  startPlotting: function(){
    if (MbPerformance.profiling) {
      MbPerformance.plot();

      MbPerformance.addEvent("unload", function(){
        var pages = store.get("profiling-pages") || [];

        pages.push(MbPerformance.getCurrentPageData());

        store.set("profiling-pages", pages);
      });
    }
  },

  append: function(entry){
    this.timeline.push(entry);
  },

  logScriptEvent: function(type, pageInfo, serverTiming, time, duration) {
    var perfTiming, offset = MbPerformance.timeOffset;

    switch (type) {
      case "page":
        perfTiming = {};

        $H(performance.timing).each(function(pair){
          var value = pair.value;
          if (value === 0 || Object.isString(value) || pair.key === "duration") {
            perfTiming[pair.key] = value;
          }
          else {
            perfTiming[pair.key] = value - offset;
          }
        });
        break;

      case "ajax":
        perfTiming = MbPerformance.searchEntry(pageInfo.id);

        if (perfTiming) {
          duration = perfTiming.duration;
        }
        break;
    }

    var timeEntry = new MbPerformanceTimeEntry(type, pageInfo, time, duration, perfTiming, serverTiming);

    MbPerformance.append(timeEntry);
  },

  searchEntry: function(id) {
    var entries = performance.getEntries();

    if (entries && entries.length) {
      for (var i = 0, l = entries.length; i < l; i++) {
        var entry = entries[i];
        if (entry.initiatorType === "xmlhttprequest" && entry.name.indexOf("__uniqueID=|"+id+"|") > -1) {
          return entry;
        }
      }
    }

    return null;
  },

  mark: function(label){
    //this.append(new MbPerformanceTimeEntry("mark", label, performance.now()));
  },

  /**
   *
   * @param {HTMLSelectElement} select
   * @param {String=}           way
   */
  zoom: function (select, way) {
    var index = select.selectedIndex;
    switch (way) {
      case "+":
        if (index == 0) {
          return;
        }

        select.selectedIndex--;
        break;

      case "-":
        if (index == select.options.length-1) {
          return;
        }

        select.selectedIndex++;
        break;
    }

    MbPerformance.timeScale = select.value;
    MbPerformance.buildTimingDetails();
  },

  buildTimingDetails: function(){
    var left, right, table;

    if (MbPerformance.timingDetailsTable) {
      table = MbPerformance.timingDetailsTable;
      left  = table.down(".left-col").update("");
      right = table.down(".right-col").update("");
    }
    else {
      var zoom = DOM.div({className: "zoom"},
        "<button class='zoom-out notext' onclick='MbPerformance.zoom(this.next(), \"-\")'></button>",
        "<select style='display: none;' onchange='MbPerformance.zoom(this)'>" +
          "<option>0.125</option>" +
          "<option>0.25</option>" +
          "<option>0.5</option>" +
          "<option>1</option>" +
          "<option>2</option>" +
          "<option selected>5</option>" +
          "<option>10</option>" +
          "<option>20</option>" +
          "<option>50</option>" +
          "<option>100</option>" +
          "<option>200</option>" +
          "<option>400</option>" +
        "</select>",
        "<button class='zoom-in notext' onclick='MbPerformance.zoom(this.previous(), \"+\")'></button>"
      );

      var legendTitles = {
        "bar-network": "R�seau",
        "bar-server": "Serveur",
        "bar-client": "Navigateur",
        "bar-type-session": "Session",
        "bar-mem": "M�moire serveur"
      };
      var legend = "";
      $H(legendTitles).each(function(pair){
        legend += "<div class='legend-item'><div class='bar "+pair.key+"'></div> "+pair.value+"</div> ";
      });

      table = DOM.table({className: "main layout timeline"},
        DOM.tr({},
          DOM.td({}),
          DOM.td({className: "legend"}, legend)
        ),
        DOM.tr({},
          left  = DOM.td({className: "left-col"}),
          DOM.td({className: "right-col-cell"},
            right = DOM.div({className: "right-col"})
          )
        ),
        DOM.tr({},
          DOM.td({}, zoom),
          DOM.td({})
        )
      );
    }

    var ruler = DOM.div({className: "ruler"});

    for (var i = 0; i < 100; i++) {
      var ms = i*1000;
      var tick = DOM.span({}, ms);
      tick.style.left = (ms / MbPerformance.timeScale)+"px";
      ruler.insert(tick);
    }

    left.insert(DOM.div({className: "ruler"}));
    right.insert(ruler);

    var navStart;

    // Draw each bar
    MbPerformance.timeline.each(function(d){
      var container = DOM.div({});
      var perfTiming = d.perfTiming;
      var perfOffset;
      var serverTiming = d.serverTiming;
      var serverOffset;

      var title = DOM.div({title: d.pageInfo.a},
          "<span style='float: right; color: #999; font-size: 0.9em;'>#{size} Kio, DB: #{db} ms</span><strong>#{m}</strong><br />#{a}".interpolate({
            size: (serverTiming.size / 1024).toFixed(2),
            db:   (serverTiming.db * 1000).toFixed(2),
            m:    $T("module-"+d.pageInfo.m+"-court"),
            a:    $T("mod-"+d.pageInfo.m+"-tab-"+d.pageInfo.a)
          })
      );

      if (d.type == "page") {
        perfOffset   = perfTiming.navigationStart;
        serverOffset = perfTiming.navigationStart;
        navStart     = perfTiming.navigationStart;
      }
      else {
        perfOffset = 0;
        serverOffset = navStart;
      }

      Object.keys(MbPerformance.markingTypes).each(function(type) {
        MbPerformance.drawBar(type, container, perfTiming, perfOffset, serverTiming, serverOffset);
      });

      left.insert(title);
      right.insert(container);
    });

    if (!MbPerformance.timingDetailsTable) {
      document.body.insert(table);
      MbPerformance.timingDetailsTable = table;
    }
  },

  drawBar: function(type, container, perfTiming, perfOffset, serverTiming, serverOffset){
    var t = MbPerformance.markingTypes[type];

    if (t.sub && perfTiming && (perfTiming[t.end] && perfTiming[t.start] || t.getValue && t.getStart)) {
      var start, length;

      if (t.getValue) {
        start  = t.getStart(serverTiming, perfTiming) - serverOffset;
        length = t.getValue(serverTiming, perfTiming);
      }
      else {
        start  = perfTiming[t.start] - perfOffset;
        length = perfTiming[t.end]   - perfTiming[t.start];
      }

      var title = t.label+"\n"+t.desc+"\nD�but: "+Math.round(start)+"ms\nDur�e: "+Math.round(length)+"ms";
      var bar = DOM.div({
        title: title,
        className: "bar bar-"+t.resp+" bar-type-"+type+(t.sub ? " sub" : "")
      });

      bar.setStyle({
        left:  (start  / MbPerformance.timeScale)+"px",
        width: (length / MbPerformance.timeScale)+"px"
      });

      container.insert(bar);

      if (t.getMemory) {
        var mem = t.getMemory(serverTiming, perfTiming);
        var memBar = DOM.div({
          title: t.label+"\n"+t.desc+"\n"+Number(mem/1024).toLocaleString()+" Kio",
          className: "bar bar-mem"
        });

        memBar.setStyle({
          left:  (start  / MbPerformance.timeScale)+"px",
          width: (length / MbPerformance.timeScale)+"px",
          height: mem/(1024*1024*4)+"px"
        });

        container.insert(memBar);
      }
    }
  },

  showTimingDetails: function() {
    MbPerformance.buildTimingDetails();

    Modal.open(MbPerformance.timingDetailsTable, {
      showClose: true,
      width: -10,
      height: -10
    });
  },

  plot: function(){
    var profilingToolbar = jQuery("#profiling-toolbar");

    if (!profilingToolbar.size()) {
      profilingToolbar = jQuery('<div id="profiling-toolbar"></div>').hide().appendTo("body");

      // Toggle plot
      jQuery('<button class="stats notext" title="Afficher le graphique"></button>').click(function(){
        MbPerformance.showTimingDetails();
      }).appendTo(profilingToolbar);

      // Download report
      jQuery('<button class="download notext" title="T�l�charger le rapport"></button>').click(function(){
        MbPerformance.download();
      }).appendTo(profilingToolbar);

      // Remove report
      jQuery('<button class="trash notext" title="Supprimer le rapport courant"></button>').click(function(){
        MbPerformance.removeProfiling();
      }).appendTo(profilingToolbar);

      // Show toolbar
      jQuery('<button id="profiler-toggle" class="gantt notext" title="Profilage de performances en cours"></button>').click(function(){
        profilingToolbar.toggle();
      }).appendTo("body");

      // Show toolbar
      jQuery('<div id="profiler-overview"><div class="profiler-buttons"></div><div id="profiler-timebar"></div></div>').appendTo("body");
    }

    var markings = [];
    var overview = jQuery("#profiler-overview");

    var timeBar = [];
    var timeBarTotal = 0;
    var timeBarContainer = jQuery("#profiler-timebar");

    $H(MbPerformance.markingTypes).each(function(pair){
      this.addMarking(pair.key, markings);
    }, this);

    // Don't redraw markings bar
    if (!MbPerformance.markingsDrawn) {
      markings.each(function(marking){
        var resp = MbPerformance.responsabilityColors[marking.resp];
        var line = jQuery('<div title="'+marking.desc+'" class="marking '+(marking.sub ? 'sub' : '')+'" style="background: '+marking.color+'; border-color: '+resp+';">'+(marking.sub ? '&nbsp;- ' : '')+marking.label+"<span style='float:right;'>"+Math.round(marking.value)+" ms</span></div>");
        overview.append(line);

        if (!marking.sub) {
          timeBarTotal += marking.value;
          timeBar.push({
            type: marking.resp,
            value: marking.value
          });
        }
      });

      timeBar.each(function(time){
        timeBarContainer.append("<div style='width: "+(100 * (time.value / timeBarTotal))+"%; background-color: "+MbPerformance.responsabilityColors[time.type]+";'></div>");
      });
    }

    MbPerformance.markingsDrawn = true;
  },

  addMarking: function(key, markings){
    var timing = MbPerformance.timeline[0].perfTiming;

    if (!timing) {
      return;
    }

    var ref = timing.navigationStart;
    var type = MbPerformance.markingTypes[key];

    if (timing[type.start] == 0 && !type.getValue) {
      return;
    }

    var marking = {
      label: type.label,
      desc:  type.desc,
      color: type.color,
      resp:  type.resp,
      sub:   type.sub,
      key:   key,
      lineWidth: 1
    };

    if (type.getValue) {
      marking.value = type.getValue(MbPerformance.pageDetail, timing);
      marking.xaxis = {
        from: 0,
        to:   0
      };
    }
    else {
      marking.xaxis = {
        from: timing[type.start] - ref,
        to:   timing[type.end]   - ref
      };
      marking.value = timing[type.end] - timing[type.start];
    }

    markings.push(marking);
  },

  timeStart: function(label) {
    if (!this.profiling) {
      return;
    }

    this.timers[label] = performance.now()/* - MbPerformance.timeOffset*/;
  },

  timeEnd: function(label, ajaxId) {
    if (!this.profiling) {
      return;
    }

    if (!this.timers[label]) {
      return;
    }

    var now = performance.now()/* - MbPerformance.timeOffset*/;
    var time = this.timers[label];
    var timeEntry;

    delete this.timers[label];

    (function(timeEntry, ajaxId){
      if (!ajaxId) {
        timeEntry = MbPerformance.timeline[0];
      }
      else {
        timeEntry = MbPerformance.timeline.find(function(t){
          return t.pageInfo.id == ajaxId;
        });
      }

      if (timeEntry) {
        timeEntry.perfTiming = timeEntry.perfTiming || {};

        if (label == "eval") {
          timeEntry.perfTiming.domLoading = time;
          timeEntry.perfTiming.domContentLoadedEventStart = time;
          timeEntry.perfTiming.domContentLoadedEventEnd = now;
          timeEntry.perfTiming.domComplete = now;
        }
      }
    }).delay(2, timeEntry, ajaxId);
  },

  dump: function(){
    var label = prompt("Libell� du profilage", "Profilage du "+(new Date()).toLocaleDateTime());

    if (label == null) {
      return;
    }

    var struct = {
      version: MbPerformance.version,
      date: (new Date()).toDATETIME(),
      label: label,
      userAgent: navigator.userAgent,
      platform: navigator.platform,
      screen: window.screen,
      plugins: [],
      pages: []
    };

    if (navigator.plugins) {
      $A(navigator.plugins).each(function(plugin){
        struct.plugins.push({
          name: plugin.name,
          filename: plugin.filename,
          description: plugin.description
        });
      });
    }

    struct.pages = store.get("profiling-pages") || [];
    struct.pages.push(MbPerformance.getCurrentPageData());

    return struct;
  },

  removeProfiling: function(){
    if (confirm("Supprimer le rapport de profilage courant ? Pensez � le t�l�charger auparavant si vous souhaitez le garder.")) {
      store.remove("profiling-pages");
    }
  },

  getCurrentPageData: function(){
    return {
      timeline: MbPerformance.timeline,
      url: document.location.href,
      time: Date.now(),
      view: {
        m: App.m,
        a: App.tab || App.a
      }
    };
  },

  download: function() {
    var data = MbPerformance.dump();

    if (data == null) {
      return;
    }

    var form = DOM.form({
      target: "_blank",
      action: "?m=system&a=download_data",
      method: "post"
    }, DOM.input({
      type: "hidden",
      name: "m",
      value: "system"
    }), DOM.input({
      type: "hidden",
      name: "a",
      value: "download_data"
    }), DOM.input({
      type: "hidden",
      name: "filename",
      value: data.label+".json"
    }), DOM.input({
      type: "hidden",
      name: "data",
      value: Object.toJSON(data)
    }));

    $$("body")[0].insert(form);
    form.submit();
  }
};

if (window.performance && performance.setResourceTimingBufferSize) {
  performance.onresourcetimingbufferfull = function(){
    console.error("Resource timing buffer full");
  }

  performance.setResourceTimingBufferSize(500);
  performance.clearResourceTimings();
}

MbPerformance.init();
