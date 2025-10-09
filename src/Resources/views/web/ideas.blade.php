<x-methodist::layouts.web pageName="Ministry ideas">
<style>
    /* Choices.js Bootstrap 5.3 Integration Styles */

    /* Make Choices container match Bootstrap form-select */
    .choices {
        margin-bottom: 0;
    }

    .choices__inner {
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        font-size: 1rem;
        min-height: 38px;
        padding: 0.375rem 0.75rem;
    }

    .choices__inner:focus,
    .choices.is-focused .choices__inner {
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    /* Invalid state */
    .choices.is-invalid .choices__inner,
    .was-validated .choices:invalid .choices__inner {
        border-color: #dc3545;
    }

    .choices.is-invalid:focus .choices__inner,
    .choices.is-invalid.is-focused .choices__inner {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
    }

    /* Dropdown styling */
    .choices__list--dropdown {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        margin-top: 4px;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        z-index: 1060; /* Above Bootstrap modals */
    }

    .choices__item--selectable {
        padding: 0.5rem 1rem;
    }

    .choices__item--selectable.is-highlighted {
        background-color: #0d6efd;
        color: #fff;
    }

    /* Selected items (tags) */
    .choices__list--multiple .choices__item {
        background-color: #0d6efd;
        border: 1px solid #0d6efd;
        border-radius: 0.25rem;
        color: #fff;
        padding: 0.25rem 0.5rem;
        margin-right: 0.375rem;
        margin-bottom: 0.375rem;
    }

    .choices__button {
        border-left: 1px solid rgba(255, 255, 255, 0.3);
        opacity: 1;
        padding: 0 8px;
    }

    .choices__button:hover {
        opacity: 0.8;
    }

    /* Search input */
    .choices__input {
        background-color: transparent;
        margin-bottom: 0;
        padding: 0.25rem 0;
    }

    /* Empty state */
    .choices__item--disabled {
        padding: 0.5rem 1rem;
        color: #6c757d;
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        .choices__list--dropdown {
            max-height: 200px;
        }
    }
</style>
<div class="row justify-content-center">
    <div class="col-lg-10">
        <p class="mb-4">
            Our people are doing amazing things around the Connexion, but much of your creative work often stays a well-kept secret! Here we aim to collect and share ministry and mission ideas that may inspire you in your context. We'd love to hear from you - complete the form below to add your contribution.
        </p>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="card-title h4 mb-4">Share an Idea</h2>
                <form action="{{ route('ideas.store') }}" method="POST" enctype="multipart/form-data" id="ministryIdeaForm">
                    @csrf

                    <!-- Circuit Selection -->
                    <div class="mb-4">
                        <label for="circuit_id" class="form-label">
                            Circuit <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('circuit_id') is-invalid @enderror" 
                                id="circuit_id" 
                                name="circuit_id" 
                                required>
                            <option value="">Select a circuit...</option>
                            @foreach($circuits as $circuit)
                                <option value="{{ $circuit->id }}">{{ $circuit->circuit }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Select the circuit you belong to.</div>
                        @error('circuit_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tags -->
                    <div class="mb-4">
                        <label for="tags" class="form-label">
                            Subjects <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('tags') is-invalid @enderror" 
                                id="tags" 
                                name="tags[]" 
                                multiple 
                                required>
                            @foreach($tags as $tag)
                                <option value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', [])) ? 'selected' : '' }}>
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">
                            Select existing subjects or type to create new ones. You can select multiple subjects.
                        </div>
                        @error('tags')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="form-label">
                            Description <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="8" 
                                  required>{{ old('description') }}</textarea>
                        <div class="form-text">
                            Describe your ministry idea in detail. What makes it special? How has it impacted your community?
                        </div>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div class="mb-4">
                        <label for="email" class="form-label">
                            Email Address <span class="text-danger">*</span>
                        </label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               placeholder="your.email@example.com"
                               required>
                        <div class="form-text">
                            We'll use this to contact you if we need more information about your idea.
                        </div>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Image Upload -->
                    <div class="mb-4">
                        <label for="image" class="form-label">
                            Image (Optional)
                        </label>
                        <input type="file" 
                               class="form-control @error('image') is-invalid @enderror" 
                               id="image" 
                               name="image" 
                               accept="image/jpeg,image/png,image/jpg,image/gif">
                        <div class="form-text">
                            Upload an image that represents your ministry idea (JPG, PNG, GIF - max 2MB).
                        </div>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="imagePreview" class="mt-3" style="display: none;">
                            <img src="" alt="Preview" class="img-thumbnail" style="max-width: 300px; max-height: 300px;">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="bi bi-send me-2"></i>Submit Ministry Idea
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Choices.js with Bootstrap 5 compatible settings
            const tagsSelect = document.getElementById('tags');
            
            if (tagsSelect) {
                const choices = new Choices(tagsSelect, {
                    removeItemButton: true,
                    maxItemCount: -1,
                    searchEnabled: true,
                    searchChoices: true,
                    searchFloor: 1,
                    searchResultLimit: 10,
                    position: 'auto', // Changed from 'bottom' for better compatibility
                    
                    // Enable adding new tags
                    addItems: true,
                    addItemFilter: function(value) {
                        return value.trim().length > 0;
                    },
                    
                    placeholderValue: 'Select existing or type to create new tags...',
                    searchPlaceholderValue: 'Type to search or add new tag...',
                    
                    duplicateItemsAllowed: false,
                    delimiter: ',',
                    editItems: false,
                    
                    addItemText: (value) => {
                        return `Press Enter to add <b>"${value}"</b>`;
                    },
                    
                    // Bootstrap-friendly class names
                    classNames: {
                        containerOuter: 'choices form-select',
                        containerInner: 'choices__inner',
                        input: 'choices__input',
                        inputCloned: 'choices__input--cloned',
                        list: 'choices__list',
                        listItems: 'choices__list--multiple',
                        listSingle: 'choices__list--single',
                        listDropdown: 'choices__list--dropdown',
                        item: 'choices__item',
                        itemSelectable: 'choices__item--selectable',
                        itemDisabled: 'choices__item--disabled',
                        itemChoice: 'choices__item--choice',
                        placeholder: 'choices__placeholder',
                        group: 'choices__group',
                        groupHeading: 'choices__heading',
                        button: 'choices__button',
                        activeState: 'is-active',
                        focusState: 'is-focused',
                        openState: 'is-open',
                        disabledState: 'is-disabled',
                        highlightedState: 'is-highlighted',
                        selectedState: 'is-selected',
                        flippedState: 'is-flipped',
                        loadingState: 'is-loading',
                        noResults: 'has-no-results',
                        noChoices: 'has-no-choices'
                    }
                });

                // Handle Bootstrap validation styling
                const form = document.getElementById('ministryIdeaForm');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        const choicesContainer = tagsSelect.closest('.choices');
                        
                        if (!tagsSelect.value || tagsSelect.selectedOptions.length === 0) {
                            e.preventDefault();
                            e.stopPropagation();
                            
                            // Add Bootstrap invalid class
                            if (choicesContainer) {
                                choicesContainer.classList.add('is-invalid');
                            }
                            
                            // Show error message
                            const errorDiv = tagsSelect.parentElement.querySelector('.invalid-feedback');
                            if (errorDiv) {
                                errorDiv.style.display = 'block';
                            }
                        } else {
                            // Remove invalid class if valid
                            if (choicesContainer) {
                                choicesContainer.classList.remove('is-invalid');
                            }
                        }
                    });
                }

                // Optional: Handle when a new tag is added
                tagsSelect.addEventListener('addItem', function(event) {
                    console.log('Tag added:', event.detail.value, event.detail.label);
                    
                    // Remove invalid state when user adds an item
                    const choicesContainer = tagsSelect.closest('.choices');
                    if (choicesContainer) {
                        choicesContainer.classList.remove('is-invalid');
                    }
                });
            }

            // Rest of your existing code...
            function getCookie(name) {
                const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
                return match ? decodeURIComponent(match[2]) : null;
            }
            function setCookie(name, value, days = 365) {
                const expires = new Date(Date.now() + days*24*60*60*1000).toUTCString();
                document.cookie = name + "=" + encodeURIComponent(value) + "; expires=" + expires + "; path=/";
            }

            const circuit = getCookie('user_circuit');
            const email = getCookie('user_email');

            if(circuit) {
                const circuitSelect = document.getElementById('circuit_id');
                const optionExists = Array.from(circuitSelect.options).some(opt => opt.value === circuit);
                if(optionExists) circuitSelect.value = circuit;
            }

            if(email) {
                document.getElementById('email').value = email;
            }

            document.getElementById('ministryIdeaForm').addEventListener('submit', function() {
                const selectedCircuit = document.getElementById('circuit_id').value;
                const emailVal = document.getElementById('email').value;

                if(selectedCircuit) setCookie('user_circuit', selectedCircuit);
                if(emailVal) setCookie('user_email', emailVal);
            });

            // Image preview
            document.getElementById('image').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if(file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('imagePreview');
                        const img = preview.querySelector('img');
                        img.src = e.target.result;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    document.getElementById('imagePreview').style.display = 'none';
                }
            });
        });
    </script>
    @endpush
</x-methodist::layouts.web>
