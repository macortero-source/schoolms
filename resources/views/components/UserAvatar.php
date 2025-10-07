<?php

namespace App\View\Components;

use Illuminate\View\Component;

class UserAvatar extends Component
{
    public $user;
    public $size;
    public $class;

    public function __construct($user = null, $size = 150, $class = '')
    {
        $this->user = $user ?? auth()->user();
        $this->size = (int) $size;
        $this->class = $class;
    }

    public function render()
    {
        return view('components.user-avatar');
    }

    public function avatarUrl()
    {
        if ($this->user && $this->user->profile_photo) {
            // Optional cache-busting if users update photos often:
            return asset('storage/' . $this->user->profile_photo);
            // return asset('storage/' . $this->user->profile_photo) . '?v=' . time();
        }

        $name = $this->user ? urlencode($this->user->name) : 'User';
        return "https://ui-avatars.com/api/?name={$name}&size={$this->size}";
    }

    public function altText()
    {
        return $this->user ? ($this->user->name ?? 'User') : 'User';
    }
}
