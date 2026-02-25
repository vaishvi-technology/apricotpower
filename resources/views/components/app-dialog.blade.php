{{--
|--------------------------------------------------------------------------
| AppDialog Component
|--------------------------------------------------------------------------
|
| Include this component in any layout to enable the AppDialog system.
| Usage: <x-app-dialog />
|
| Then use in your views:
|   AppDialog.confirm(...)  - Confirmation modal
|   AppDialog.notify(...)   - Toast notification
|
| See public/js/app-dialog.js for full API documentation.
|
--}}

{{-- Confirm Dialog --}}
<div id="app-dialog-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:99999; align-items:center; justify-content:center;" onclick="AppDialog.close()">
    <div id="app-dialog-box" onclick="event.stopPropagation()" style="background:#fff; border-radius:20px; box-shadow:0 20px 60px rgba(0,0,0,0.25); padding:40px 48px; width:480px; max-width:92vw; text-align:center; animation:appDialogFadeIn 0.3s ease;">
        <div id="app-dialog-icon" style="width:64px; height:64px; border-radius:50%; margin:0 auto 20px; display:flex; align-items:center; justify-content:center;"></div>
        <h4 id="app-dialog-title" style="margin:0 0 10px; font-size:1.35rem; font-weight:700; color:#222;"></h4>
        <p id="app-dialog-text" style="margin:0 0 32px; font-size:1rem; color:#666; line-height:1.6;"></p>
        <div style="display:flex; gap:14px; justify-content:center;">
            <button id="app-dialog-cancel" type="button" onclick="AppDialog.close()" style="padding:12px 32px; border-radius:10px; border:2px solid #ddd; background:#fff; color:#555; font-size:0.95rem; font-weight:600; cursor:pointer; transition:all 0.2s; min-width:120px;">Cancel</button>
            <button id="app-dialog-ok" type="button" style="padding:12px 32px; border-radius:10px; border:none; color:#fff; font-size:0.95rem; font-weight:600; cursor:pointer; transition:all 0.2s; min-width:120px;"></button>
        </div>
    </div>
</div>

{{-- Notification Toast Container --}}
<div id="app-notify-container" style="position:fixed; top:20px; right:20px; z-index:100000; display:flex; flex-direction:column; gap:10px; pointer-events:none;"></div>

{{-- Styles --}}
<style>
    @keyframes appDialogFadeIn { from { opacity:0; transform:scale(0.9); } to { opacity:1; transform:scale(1); } }
    @keyframes appNotifySlideIn { from { opacity:0; transform:translateX(100%); } to { opacity:1; transform:translateX(0); } }
    @keyframes appNotifySlideOut { from { opacity:1; transform:translateX(0); } to { opacity:0; transform:translateX(100%); } }
    #app-dialog-cancel:hover { background:#f5f5f5; border-color:#bbb; }
    #app-dialog-ok:hover { opacity:0.9; transform:translateY(-1px); box-shadow:0 6px 16px rgba(0,0,0,0.2); }
</style>

{{-- Script --}}
<script src="{{ asset('js/app-dialog.js') }}"></script>
