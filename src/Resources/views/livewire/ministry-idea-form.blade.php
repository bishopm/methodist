<div class="row justify-content-center">
    <div class="col-lg-10">

        @if(session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="card-title h4 mb-4">Share an Idea</h2>

                <form wire:submit.prevent="submit" enctype="multipart/form-data">
                    <!-- Circuit -->
                    <div class="mb-4">
                        <label class="form-label">Circuit <span class="text-danger">*</span></label>
                        <select wire:model="circuit_id" id="circuit_id_select" class="form-select @error('circuit_id') is-invalid @enderror">
                            <option value="">Select a circuit...</option>
                            @foreach($circuits as $c)
                                <option value="{{ $c->id }}">{{ $c->circuit }}</option>
                            @endforeach
                        </select>
                        @error('circuit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" wire:model="email" id="email_input" class="form-control @error('email') is-invalid @enderror">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea wire:model="description" rows="8" class="form-control @error('description') is-invalid @enderror"></textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Tags -->
                    <div class="mb-4">
                        <label class="form-label">Subjects <span class="text-danger">*</span></label>
                        <div class="mb-2">
                            @foreach($tags as $i => $tag)
                                <span class="badge bg-primary me-1 mb-1">
                                    {{ $tag }}
                                    <button type="button" wire:click="removeTag({{ $i }})" class="btn-close btn-close-white btn-sm" style="font-size: 0.7rem; vertical-align: middle;"></button>
                                </span>
                            @endforeach
                        </div>
                        <div style="position: relative;">
                            <input type="text" 
                                   wire:model.live.debounce.300ms="tagInput" 
                                   wire:keydown.enter.prevent="addTag" 
                                   placeholder="Type to search existing subjects or add new ones (press Enter)" 
                                   class="form-control"
                                   autocomplete="off">
                            
                            @if($showTagDropdown && !empty($filteredTags))
                                <div class="dropdown-menu show w-100" style="position: absolute; z-index: 1000; max-height: 200px; overflow-y: auto;">
                                    @foreach($filteredTags as $tag)
                                        <button type="button" 
                                                wire:click="selectTag('{{ $tag['name'] }}')" 
                                                class="dropdown-item">
                                            {{ $tag['name'] }}
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="form-text">Select from existing subjects or type a new one and press Enter.</div>
                        @error('tags') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <!-- Image -->
                    <div class="mb-4">
                        <label class="form-label">Image (Optional)</label>
                        <input type="file" wire:model="image" class="form-control @error('image') is-invalid @enderror">
                        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror

                        <div wire:loading wire:target="image" class="mt-2">
                            <small class="text-muted">Uploading...</small>
                        </div>

                        @if ($image)
                            <div class="mt-3">
                                <img src="{{ $image->temporaryUrl() }}" class="img-thumbnail" style="max-width:300px; max-height:300px;">
                            </div>
                        @endif
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <span wire:loading.remove wire:target="submit">
                                <i class="bi bi-send me-2"></i>Submit Ministry Idea
                            </span>
                            <span wire:loading wire:target="submit">
                                <span class="spinner-border spinner-border-sm me-2"></span>Submitting...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Cookie helper
function getCookie(name) {
    const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    return match ? decodeURIComponent(match[2]) : null;
}

// Prefill circuit and email from cookies
function prefillFromCookies() {
    console.log('Attempting to prefill from cookies...');
    console.log('All cookies:', document.cookie);
    
    const circuitSelect = document.getElementById('circuit_id_select');
    const emailInput = document.getElementById('email_input');
    
    const cookieCircuit = getCookie('user_circuit');
    const cookieEmail = getCookie('user_email');
    
    console.log('Circuit cookie:', cookieCircuit);
    console.log('Email cookie:', cookieEmail);
    console.log('Circuit select element:', circuitSelect);
    console.log('Email input element:', emailInput);

    if (cookieCircuit && circuitSelect) {
        console.log('Setting circuit to:', cookieCircuit);
        circuitSelect.value = cookieCircuit;
        // Use Livewire's @this to update the property directly
        @this.set('circuit_id', cookieCircuit);
    }

    if (cookieEmail && emailInput) {
        console.log('Setting email to:', cookieEmail);
        emailInput.value = cookieEmail;
        // Use Livewire's @this to update the property directly
        @this.set('email', cookieEmail);
    }
}

// Wait for Livewire to be ready
document.addEventListener('livewire:load', function () {
    console.log('Livewire loaded, prefilling...');
    setTimeout(prefillFromCookies, 500);
    
    // Close tag dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const tagInput = document.querySelector('input[wire\\:model\\.debounce\\.300ms="tagInput"]');
        const dropdown = document.querySelector('.dropdown-menu.show');
        
        if (dropdown && tagInput && !tagInput.contains(e.target) && !dropdown.contains(e.target)) {
            @this.set('showTagDropdown', false);
        }
    });
});

// Fallback for DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, prefilling...');
    setTimeout(prefillFromCookies, 500);
});
</script>
@endpush