<?php
namespace App\Http\Livewire;

use Livewire\Component;

class AdminCalendar extends Component
{
    public $events = [];

    protected $listeners = ['loadEvents'];

    public function mount()
    {
        // Optionnel : charger les events dès le début
        $this->loadEvents();
    }

    public function loadEvents()
    {
        $this->events = [
            ['id' => 1, 'title' => 'Conférence A', 'start' => '2025-07-25'],
            ['id' => 2, 'title' => 'Réunion B', 'start' => '2025-07-26T10:00:00'],
        ];

        // Émission vers JS
        $this->emit('refreshCalendar', $this->events);
    }

    public function render()
    {
        return view('livewire.admin-calendar');
    }
}
