Chrono = {
  total: 0,
  start: function() {
    this.reference = new Date();
  },
  stop: function() {
    return this.total += new Date().getTime() - this.reference.getTime();
  }
};

CodeSniffer = {

  getFile: function (element) {
    var header = element.up('.tree-header');
    var path = header.id.match(/mediboard:(.*)-header/)[1];
    return path.replace(/:/g, '/');
  },
  
  
  force: null,
  setForce: function(input) {
    this.force = input.checked;
  },

  auto: null,
  setAuto: function(input) {
    this.auto = input.checked;
  },
  
  index: 0,
  files: null,
  stats: null,
  run: function(button) {
    $('sniff-file').update();
    var run = $('sniff-run');
    run.down('button.change').enable();
    run.down('input.auto').checked  = this.auto  = true;
    run.down('input.force').checked = this.force = false;
    
    var tbody = run.down('table tbody.files');
    tbody.update();
    Modal.open(run);
    CodeSniffer.parse.bind(CodeSniffer).defer(button);
  },
  
  parse: function(button) {
    this.index = 0;
    var content = button.up('.tree-header').next();
    var sniffed = content.select('.sniffed');
    this.files = [];
    this.stats = {
      obsolete: 0,
      uptodate: 0
    };
    
    sniffed.each(function(div) {
      var tag = $w(div.className)[1];
      CodeSniffer.files.push({
        path: CodeSniffer.getFile(div),
        tag: tag,
        status: null
      });
      CodeSniffer.stats[tag] = CodeSniffer.stats[tag] ? CodeSniffer.stats[tag]+1 : 1;
    });
    
    var run = $('sniff-run');
    var count = run.down("small.count");
    $H(this.stats).each(function(pair) {
      $(pair.key).textContent = pair.value;
    });
    $('index').textContent = this.index;

    var tbody = run.down('table tbody.files');
    
    this.files.each(function(file) {
      tbody.insert(
        DOM.tr({ id: file.path.replace(/\//g, ':') }, 
          DOM.td({}, file.path.replace(/\//g, ' / ')),
          DOM.td({}, 
            DOM.div({ className: 'sniffed ' + file.tag }),
            DOM.div({ className: 'status' },
              DOM.div({ className: 'info' , style: 'visibility: hidden' }, 'todo')
            )
          )
        )
      );
    });
  },
  
  start: function() {
    if (this.index == this.files.length) {
      return;
    }

    Chrono.start();

    var file = this.files[this.index];
    
    var status = $(file.path.replace(/\//g, ':')).down('.status');
    status.update(DOM.div({ className: 'loading' }, 'Running'));

    if (file.tag == 'uptodate' && !this.force) {
      status.update(DOM.div({ className: 'info' }, 'Skipped'));
    }
    else {
      var options = {
        onComplete: function() {
          $('duration').textContent = printf('%.3f', Chrono.stop() / 1000);
          status.update(DOM.div({ className: 'info' }, 'Done'));
          if (CodeSniffer.auto) {
            CodeSniffer.start.bind(CodeSniffer).defer();
          }
        }
      };

      new Url('developpement', 'sniff_file') .
      addParam('file', file.path) .
      requestUpdate('sniff-file', options);
    }
    
    this.index++;
    $('index').textContent = this.index;

    if (this.index == this.files.length) {
      $('sniff-run').down('button.change').disable();
    }

    $('duration').textContent = printf('%.3f', Chrono.stop() / 1000);

    if (file.tag == 'uptodate' && this.auto && !this.force) {
      // Help the browser breath, once per hundred
      if (this.index % 100 == 0) {
        CodeSniffer.start.bind(CodeSniffer).defer();
        return;
      }

      // Otherwise, run as fast as you can
      CodeSniffer.start();
    }
  },
  
  close: function() {
    this.auto = false;
    Control.Modal.close();
    window.location.reload();
  },

  show: function(button) {
    new Url('developpement', 'sniff_file') .
      addParam('file', this.getFile(button)) .
      requestModal(800, 400);
  }
};
