<?php

namespace App\Livewire\Menus;

use Livewire\Component;

class Alerts extends Component
{
    public $success;
    public $error;
    public $type; // 'success', 'error', or null for both
    protected $listeners = [];

    public function mount($type = null)
    {
        $this->type = $type;
        if ($this->type === 'success') {
            $this->listeners = ['showSuccess'];
        } elseif ($this->type === 'error') {
            $this->listeners = ['showError'];
        } else {
            $this->listeners = ['showSuccess', 'showError'];
        }
    }
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
