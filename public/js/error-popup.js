(function() {
  // Error popup utility with console logging for debugging
  window.showErrorPopup = function(message, title, type) {
    type = type || 'error';
    var overlay = document.getElementById('errorPopupOverlay');
    var titleEl = document.getElementById('errorPopupTitle');
    var bodyEl = document.getElementById('errorPopupBody');
    var iconEl = document.getElementById('errorPopupIcon');

    // If popup elements don't exist, fall back to console only
    if (!overlay || !titleEl || !bodyEl || !iconEl) {
      console.error('[JOMS]', title || 'Error', '-', message);
      return;
    }

    var config = {
      error:   { title: title || 'Error',   icon: 'fas fa-exclamation-circle', iconClass: 'error' },
      warning: { title: title || 'Warning', icon: 'fas fa-exclamation-triangle', iconClass: 'warning' },
      info:    { title: title || 'Notice',  icon: 'fas fa-info-circle', iconClass: 'info' },
      success: { title: title || 'Success', icon: 'fas fa-check-circle', iconClass: 'info' }
    };

    var c = config[type] || config.error;
    titleEl.textContent = c.title;
    bodyEl.textContent = message;
    iconEl.className = 'error-popup-icon ' + c.iconClass;
    iconEl.innerHTML = '<i class="' + c.icon + '"></i>';
    overlay.classList.add('active');

    // Always log to console for developer debugging
    var logMethod = type === 'error' ? 'error' : (type === 'warning' ? 'warn' : 'info');
    console.groupCollapsed('%c[JOMS ' + (c.title) + ']%c ' + message,
      'color: ' + (type === 'error' ? '#dc3545' : type === 'warning' ? '#ffc107' : '#0d6efd') + '; font-weight: bold;',
      'color: inherit;'
    );
    console.log('Type:', type);
    console.log('Time:', new Date().toISOString());
    console.log('Page:', window.location.pathname);
    console.trace('Stack trace');
    console.groupEnd();
  };

  // Close popup handlers
  var closeBtn = document.getElementById('errorPopupCloseBtn');
  var overlayEl = document.getElementById('errorPopupOverlay');

  if (closeBtn) {
    closeBtn.addEventListener('click', function() {
      overlayEl.classList.remove('active');
    });
  }
  if (overlayEl) {
    overlayEl.addEventListener('click', function(e) {
      if (e.target === this) this.classList.remove('active');
    });
  }
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && overlayEl) overlayEl.classList.remove('active');
  });

  // ── Global uncaught JS error handler ──
  window.onerror = function(message, source, lineno, colno, error) {
    console.groupCollapsed('%c[JOMS JS Error]%c ' + message,
      'color: #dc3545; font-weight: bold;', 'color: inherit;'
    );
    console.error('Source:', source + ':' + lineno + ':' + colno);
    if (error && error.stack) console.error('Stack:', error.stack);
    console.groupEnd();
    // Don't show popup for JS errors — just log to console
    return false;
  };

  // ── Unhandled promise rejection handler ──
  window.addEventListener('unhandledrejection', function(event) {
    var msg = 'Unhandled promise rejection';
    if (event.reason) {
      msg = event.reason.message || event.reason.toString();
    }
    console.groupCollapsed('%c[JOMS Promise Error]%c ' + msg,
      'color: #dc3545; font-weight: bold;', 'color: inherit;'
    );
    console.error('Reason:', event.reason);
    console.log('Time:', new Date().toISOString());
    console.log('Page:', window.location.pathname);
    console.groupEnd();
    // Don't show popup — just log
  });

  // ── Intercept AJAX errors globally (jQuery) ──
  if (typeof $ !== 'undefined' && $.ajaxSetup) {
    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    $.ajaxSetup({
      headers: csrfMeta ? { 'X-CSRF-TOKEN': csrfMeta.content } : {},
      error: function(xhr) {
        var msg = 'An unexpected error occurred.';
        try {
          var json = JSON.parse(xhr.responseText);
          msg = json.message || json.error || msg;
        } catch(e) {}
        window.showErrorPopup(msg, 'Error ' + xhr.status, 'error');
      }
    });
  }

  // ── Intercept fetch errors globally ──
  var originalFetch = window.fetch;
  window.fetch = function() {
    return originalFetch.apply(this, arguments).then(function(response) {
      if (!response.ok && response.headers.get('content-type') && response.headers.get('content-type').includes('application/json')) {
        var cloned = response.clone();
        cloned.json().then(function(data) {
          if (data.error || data.message) {
            window.showErrorPopup(data.message || 'An error occurred.', 'Error ' + response.status, 'error');
          }
        }).catch(function() {});
      }
      return response;
    }).catch(function(error) {
      if (error.name !== 'AbortError') {
        window.showErrorPopup('Network error. Please check your connection and try again.', 'Connection Error', 'warning');
      }
      throw error;
    });
  };

  // ── Intercept Axios errors if available ──
  if (typeof axios !== 'undefined') {
    axios.interceptors.response.use(
      function(response) { return response; },
      function(error) {
        if (error.response) {
          var msg = 'An unexpected error occurred.';
          if (error.response.data && error.response.data.message) {
            msg = error.response.data.message;
          }
          window.showErrorPopup(msg, 'Error ' + error.response.status, 'error');
        } else if (error.request) {
          window.showErrorPopup('Network error. Please check your connection.', 'Connection Error', 'warning');
        }
        return Promise.reject(error);
      }
    );
  }
})();
