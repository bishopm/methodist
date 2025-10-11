<x-methodist::layouts.web pageName="Ministry ideas">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <p class="mb-4">
                Our people are doing amazing things around the Connexion, but often our most creative ideas don't get shared. We are collecting those ideas and would love to hear from you!
            </p>          
            <livewire:ministry-idea-form 
                :prefilledCircuit="$_COOKIE['user_circuit'] ?? null" 
                :prefilledEmail="$_COOKIE['user_email'] ?? null" 
            />
        </div>
    </div>
</x-methodist::layouts.web>