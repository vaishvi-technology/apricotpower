/**
 * ==========================================================================
 * AppDialog - Generic Alert System (Confirm Modal + Notification Toast)
 * ==========================================================================
 *
 * CONFIRMATION DIALOG - Centered modal with icon, title, text, and actions.
 * Works with both Livewire methods and plain JS callbacks.
 *
 *   // Livewire usage (inside a Livewire component view):
 *   AppDialog.confirm(this, 'livewireMethod', [arg1, arg2], {
 *       title: 'Delete Item',
 *       text: 'This action cannot be undone.',
 *       type: 'danger',              // question | warning | danger | info | success
 *       confirmText: 'Yes, delete',  // default: 'Confirm'
 *       cancelText: 'No, keep it'    // default: 'Cancel'
 *   })
 *
 *   // Plain JS callback usage (works anywhere, no Livewire needed):
 *   AppDialog.confirm(null, function() { window.location = '/logout'; }, [], {
 *       title: 'Logout',
 *       text: 'Are you sure you want to logout?',
 *       type: 'question'
 *   })
 *
 * NOTIFICATION TOAST - Auto-dismiss notification in top-right corner.
 *
 *   AppDialog.notify('Item saved successfully!', 'success')   // auto-dismiss 3s
 *   AppDialog.notify('Something went wrong.', 'danger', 5000) // custom duration
 *   AppDialog.notify('Please check your input.', 'warning')
 *   AppDialog.notify('New update available.', 'info')
 *
 * ==========================================================================
 */

var AppDialog = (function () {
    var _icons = {
        question: {
            bg: 'rgba(214,137,16,0.1)', btn: '#d68910', border: '#d68910',
            svg: '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#d68910" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>'
        },
        warning: {
            bg: 'rgba(255,170,0,0.1)', btn: '#e8a020', border: '#e8a020',
            svg: '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#e8a020" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>'
        },
        danger: {
            bg: 'rgba(220,53,69,0.1)', btn: '#dc3545', border: '#dc3545',
            svg: '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>'
        },
        info: {
            bg: 'rgba(13,110,253,0.1)', btn: '#0d6efd', border: '#0d6efd',
            svg: '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0d6efd" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
        },
        success: {
            bg: 'rgba(25,135,84,0.1)', btn: '#198754', border: '#198754',
            svg: '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#198754" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>'
        }
    };

    return {
        /**
         * Show a confirmation dialog.
         * @param {HTMLElement|null} el       - The triggering element (needed for Livewire). Pass null for JS callbacks.
         * @param {string|Function}  action   - Livewire method name (string) or a JS callback function.
         * @param {Array}            args     - Arguments to pass to the Livewire method (ignored for callbacks).
         * @param {Object}           options  - { title, text, type, confirmText, cancelText }
         */
        confirm: function (el, action, args, options) {
            args = args || [];
            options = options || {};
            var type = options.type || 'question';
            var c = _icons[type] || _icons.question;

            document.getElementById('app-dialog-icon').style.background = c.bg;
            document.getElementById('app-dialog-icon').innerHTML = c.svg;
            document.getElementById('app-dialog-title').textContent = options.title || 'Are you sure?';
            document.getElementById('app-dialog-text').textContent = options.text || '';
            document.getElementById('app-dialog-ok').textContent = options.confirmText || 'Confirm';
            document.getElementById('app-dialog-ok').style.background = c.btn;
            document.getElementById('app-dialog-cancel').textContent = options.cancelText || 'Cancel';
            document.getElementById('app-dialog-overlay').style.display = 'flex';

            document.getElementById('app-dialog-ok').onclick = function () {
                if (typeof action === 'function') {
                    action();
                } else {
                    var component = Livewire.find(el.closest('[wire\\:id]').getAttribute('wire:id'));
                    component.call(action, ...args);
                }
                AppDialog.close();
            };
        },

        /** Close the confirmation dialog. */
        close: function () {
            document.getElementById('app-dialog-overlay').style.display = 'none';
        },

        /**
         * Show an auto-dismiss notification toast.
         * @param {string} message  - The notification message.
         * @param {string} type     - success | danger | warning | info (default: 'success')
         * @param {number} duration - Auto-dismiss time in ms (default: 3000)
         */
        notify: function (message, type, duration) {
            type = type || 'success';
            duration = duration || 3000;
            var c = _icons[type] || _icons.success;
            var container = document.getElementById('app-notify-container');

            var toast = document.createElement('div');
            toast.style.cssText = 'pointer-events:auto; display:flex; align-items:center; gap:12px; background:#fff; border-left:4px solid ' + c.border + '; border-radius:12px; box-shadow:0 8px 32px rgba(0,0,0,0.15); padding:16px 20px; min-width:300px; max-width:420px; animation:appNotifySlideIn 0.35s ease;';
            toast.innerHTML = '<div style="width:36px; height:36px; border-radius:50%; background:' + c.bg + '; display:flex; align-items:center; justify-content:center; flex-shrink:0;">' +
                c.svg.replace(/width="32" height="32"/g, 'width="20" height="20"') +
                '</div>' +
                '<p style="margin:0; font-size:0.92rem; font-weight:500; color:#333; flex:1;">' + message + '</p>' +
                '<button onclick="this.parentElement.style.animation=\'appNotifySlideOut 0.3s ease forwards\'; setTimeout(function(){ this.remove(); }.bind(this.parentElement), 300);" style="background:none; border:none; color:#999; cursor:pointer; font-size:18px; padding:0 0 0 8px; line-height:1;">&times;</button>';

            container.appendChild(toast);

            setTimeout(function () {
                toast.style.animation = 'appNotifySlideOut 0.3s ease forwards';
                setTimeout(function () { toast.remove(); }, 300);
            }, duration);
        }
    };
})();
