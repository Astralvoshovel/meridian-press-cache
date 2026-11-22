(function () {
    'use strict';

    var admin = window.mpcAdmin || { ajax: '', nonce: '' };

    function post(action) {
        var body = new URLSearchParams();
        body.set('action', action);
        body.set('nonce', admin.nonce);

        return fetch(admin.ajax, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: body
        }).then(function (r) { return r.json(); });
    }

    var tabs = document.querySelectorAll('.meridian-press-cache-tabs .nav-tab');
    var panels = document.querySelectorAll('.meridian-press-cache-panel');

    function activate(hash) {
        var id = (hash || '#dashboard').replace('#', '');
        var found = false;

        panels.forEach(function (panel) {
            var on = panel.id === id;
            panel.classList.toggle('is-active', on);
            found = found || on;
        });

        tabs.forEach(function (tab) {
            tab.classList.toggle('nav-tab-active', tab.getAttribute('href') === '#' + id);
        });

        if (!found && panels.length) {
            panels[0].classList.add('is-active');
        }
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function (e) {
            e.preventDefault();
            var hash = tab.getAttribute('href');
            history.replaceState(null, '', hash);
            activate(hash);
        });
    });

    activate(window.location.hash);

    function bind(id, action, render) {
        var btn = document.getElementById(id);
        if (!btn) {
            return;
        }

        btn.addEventListener('click', function () {
            btn.disabled = true;
            post(action).then(function (res) {
                btn.disabled = false;
                render(res);
            }).catch(function () {
                btn.disabled = false;
            });
        });
    }

    bind('meridian-press-cache-clear', 'mpc_clear_cache', function (res) {
        var out = document.getElementById('meridian-press-cache-clear-result');
        if (out) {
            out.textContent = res && res.success ? 'Purged ' + (res.data.removed || 0) + ' file(s).' : 'Failed.';
        }
    });

    bind('meridian-press-cache-db', 'mpc_db_run', function (res) {
        var out = document.getElementById('meridian-press-cache-db-result');
        if (out) {
            out.textContent = res && res.success ? 'Cleaned and optimized.' : 'Failed.';
        }
    });

    bind('meridian-press-cache-webp', 'mpc_bulk_webp', function (res) {
        var out = document.getElementById('meridian-press-cache-webp-result');
        if (!out) {
            return;
        }

        if (!res || !res.success) {
            out.textContent = 'Server lacks GD WebP support.';
            return;
        }

        out.textContent = 'Processed ' + res.data.processed + ' image(s).';
    });

    bind('meridian-press-cache-refresh', 'mpc_refresh_env', function (res) {
        if (!res || !res.success) {
            return;
        }

        var map = {
            'meridian-press-cache-config': res.data.config_readable ? 'Available' : 'None',
            'meridian-press-cache-env': res.data.env_detected ? 'Available' : 'None',
            'meridian-press-cache-entries': String(res.data.entries)
        };

        Object.keys(map).forEach(function (id) {
            var el = document.getElementById(id);
            if (el) {
                el.textContent = map[id];
            }
        });
    });

    bind('meridian-press-cache-cloud', 'mpc_cloud_check', function (res) {
        var note = document.getElementById('meridian-press-cache-cloud-result');
        var score = document.getElementById('meridian-press-cache-score');
        var list = document.getElementById('meridian-press-cache-notes');

        if (!res || !res.success) {
            if (note) { note.textContent = 'Failed.'; }
            return;
        }

        if (score) {
            score.textContent = res.data.score === null ? 'No score' : res.data.score + '/100';
        }

        if (list) {
            list.innerHTML = '';
            (res.data.notes || []).forEach(function (text) {
                var li = document.createElement('li');
                li.textContent = text;
                list.appendChild(li);
            });
        }

        var consent = document.querySelector('input[name="mpc_consent"]');
        if (consent) {
            consent.checked = true;
        }

        if (note) {
            note.textContent = 'Sent. Consent saved.';
        }
    });
})();
