<?php

namespace App\Traits;

trait FlashMessageTrait
{
    public function successMessage($message)
    {
        session()->flash('t-success', $message);
    }

    public function errorMessage($message)
    {
        session()->flash('t-error', $message);
    }
}
