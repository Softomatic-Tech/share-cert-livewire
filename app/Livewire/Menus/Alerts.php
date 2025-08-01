<?php

namespace App\Livewire\Menus;

use Livewire\Component;

class Alerts extends Component
{
    public $success;
    public $error;
    protected $listeners = ['showSuccess', 'showError'];
    public function render()
    {
        return view('livewire.menus.alerts');
    }

    public function showSuccess($message)
    {
        $this->success = $message;
    }

    public function showError($message)
    {
        $this->error = $message;
    }
}
