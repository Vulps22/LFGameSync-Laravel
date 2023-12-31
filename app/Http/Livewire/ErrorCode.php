<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ErrorCode extends Component
{
    public $code="500";
    public $message="Unknown Server Error";
    public $details="An unexpected error has occured. We have been notified and are working in it 👍 If you require urgent help you can open a ticket on the support server";

    public function render()
    {
        return view('livewire.error-code');
    }
}
