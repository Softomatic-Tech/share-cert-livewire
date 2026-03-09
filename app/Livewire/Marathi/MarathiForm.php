<?php

namespace App\Livewire\Marathi;

use Livewire\Component;

class MarathiForm extends Component
{
    public $title;
    public $description;

    protected $rules = [
        'title' => 'required|min:3',
        'description' => 'required|min:10',
    ];

    public function save()
    {
        $this->validate();

        \DB::table('marathi_data')->insert([
            'title' => $this->title,
            'description' => $this->description,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->reset(['title', 'description']);

        \Flux::toast('Marathi data saved successfully!');
    }

    public function render()
    {
        return view('livewire.marathi.marathi-form');
    }
}
