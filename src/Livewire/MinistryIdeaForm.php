<?php

namespace Bishopm\Methodist\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Bishopm\Methodist\Models\Idea;
use Bishopm\Methodist\Models\Tag;
use Bishopm\Methodist\Models\Circuit;
use Illuminate\Support\Str;

class MinistryIdeaForm extends Component
{
    use WithFileUploads;

    public $circuit_id;
    public $email;
    public $description;
    public $image;
    public $tags = [];
    public $tagInput;
    public $circuits;

    public function mount()
    {
        $this->circuits = Circuit::orderBy('circuit')->get();

        // Prefill circuit and email from cookies
        $this->circuit_id = request()->cookie('user_circuit');
        $this->email = request()->cookie('user_email');
    }

    protected $rules = [
        'circuit_id' => 'required|exists:circuits,id',
        'email' => 'required|email|max:199',
        'description' => 'required|string|min:10',
        'image' => 'nullable|image|max:2048',
        'tags' => 'required|array|min:1',
        'tags.*' => 'string',
    ];

    public function addTag()
    {
        $tag = trim($this->tagInput);
        if ($tag && !in_array($tag, $this->tags)) {
            $this->tags[] = $tag;
        }
        $this->tagInput = '';
    }

    public function removeTag($index)
    {
        unset($this->tags[$index]);
        $this->tags = array_values($this->tags);
    }

    public function submit()
    {
        $this->validate();

        // Save image
        $imagePath = $this->image ? $this->image->store('ministry-ideas', 'public') : null;

        // Create idea
        $idea = Idea::create([
            'circuit_id' => $this->circuit_id,
            'email' => $this->email,
            'description' => $this->description,
            'image' => $imagePath,
            'published' => false,
        ]);

        // Attach tags
        $tagIds = [];
        foreach ($this->tags as $tagInput) {
            if (is_numeric($tagInput)) {
                $tagIds[] = $tagInput;
            } else {
                $tag = Tag::firstOrCreate(
                    ['slug' => Str::slug($tagInput)],
                    ['name' => $tagInput]
                );
                $tagIds[] = $tag->id;
            }
        }

        $idea->tags()->sync($tagIds);

        // Set cookies for circuit and email
        cookie()->queue('user_circuit', $this->circuit_id, 525600); // 1 year
        cookie()->queue('user_email', $this->email, 525600);

        // Reset form
        $this->reset(['circuit_id', 'email', 'description', 'image', 'tags', 'tagInput']);

        session()->flash('success', 'Thank you! Your ministry idea has been submitted and will be reviewed before publication.');
    }

    public function render()
    {
        return view('methodist::livewire.ministry-idea-form');
    }
}
