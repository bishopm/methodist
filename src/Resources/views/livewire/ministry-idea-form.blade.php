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
                        <select wire:model="circuit_id" class="form-select @error('circuit_id') is-invalid @enderror">
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
                        <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror">
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
                                <span class="badge bg-primary me-1">
                                    {{ $tag }}
                                    <button type="button" wire:click="removeTag({{ $i }})" class="btn-close btn-close-white btn-sm"></button>
                                </span>
                            @endforeach
                        </div>
                        <input type="text" wire:model.defer="tagInput" wire:keydown.enter.prevent="addTag" placeholder="Type subject and press Enter" class="form-control">
                        @error('tags') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <!-- Image -->
                    <div class="mb-4">
                        <label class="form-label">Image (Optional)</label>
                        <input type="file" wire:model="image" class="form-control @error('image') is-invalid @enderror">
                        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror

                        @if ($image)
                            <div class="mt-3">
                                <img src="{{ $image->temporaryUrl() }}" class="img-thumbnail" style="max-width:300px; max-height:300px;">
                            </div>
                        @endif
                    </div>

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
