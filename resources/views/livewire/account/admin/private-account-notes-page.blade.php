<div>
    {{-- Inner Banner --}}
    <section class="inner-banner">
        <div class="container">
            <div class="inner-banner-head">
                <h1><span class="normal-font">PRIVATE</span> <span class="bold-font">ACCOUNT NOTES</span></h1>
            </div>
        </div>
    </section>

    <style>
        .account-section { padding: 60px 0; background: #f9f9f9; }
        .account-sidebar { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); padding: 1.5rem; }
        .account-sidebar .nav-link { color: #555; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 500; transition: all 0.2s; }
        .account-sidebar .nav-link:hover, .account-sidebar .nav-link.active { background: rgba(214,137,16,0.1); color: #d68910; font-weight: 600; }
        .account-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); padding: 2rem; }
        .account-card .form-label { font-size: 0.85rem; font-weight: 600; color: #444; }
        .account-card .form-control { border: 2px solid #e8e8e8; border-radius: 10px; padding: 0.6rem 1rem; transition: border-color 0.3s, box-shadow 0.3s; }
        .account-card .form-control:focus { border-color: #d68910; box-shadow: 0 0 0 3px rgba(214,137,16,0.15); }
        .account-card textarea.form-control { resize: vertical; }
        .account-card textarea.form-control.locked-field { background-color: #d5d5d5 !important; color: #555; cursor: not-allowed; border-color: #ccc; }
        .btn-brand { background: linear-gradient(135deg, #d68910, #e8a020); border: none; border-radius: 10px; padding: 0.65rem 2rem; font-weight: 700; color: #fff; text-transform: uppercase; letter-spacing: 0.5px; transition: transform 0.2s, box-shadow 0.2s; }
        .btn-brand:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(214,137,16,0.35); color: #fff; }
        .error-text { color: #dc3545; font-size: 0.8rem; margin-top: 0.25rem; }
        .card-section-title { font-family: 'lemonMilk', serif; font-weight: 700; color: #333; font-size: 1.1rem; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 2px solid #f0f0f0; }
    </style>

    <section class="account-section">
        <div class="container">
            <div class="row">
                {{-- Sidebar --}}
                <div class="col-lg-3 mb-4">
                    @include('partials.account-sidebar')
                </div>

                {{-- Content --}}
                <div class="col-lg-9">
                    <div class="account-card">
                        <h5 class="card-section-title">Private Account Notes</h5>

                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form wire:submit.prevent="save">
                            <div class="mb-4">
                                <label for="admin_notes" class="form-label">Private Account Notes</label>
                                <textarea class="form-control {{ $admin_notes_locked ? 'locked-field' : '' }}" id="admin_notes" wire:model="admin_notes" rows="4" @if($admin_notes_locked) disabled readonly @endif></textarea>
                                @if ($admin_notes_locked)
                                    <small class="text-muted">These notes cannot be edited once saved. They will stay on the account permanently.</small>
                                @else
                                    <small class="text-muted">Notes added here cannot be edited once saved.</small>
                                @endif
                            </div>

                            <div class="mb-4">
                                <label for="notes" class="form-label">Internal Admin Notes</label>
                                <textarea class="form-control" id="notes" wire:model="notes" rows="3"></textarea>
                            </div>

                            <button type="submit" class="btn btn-brand mt-2" wire:loading.attr="disabled">
                                <span wire:loading wire:target="save">Saving...</span>
                                <span wire:loading.remove wire:target="save">Save Changes</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
