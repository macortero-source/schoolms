@props([
    'user',   // required
    'size' => 40,
    'class' => '',
])

@php
    $avatarUrl = $user->profile_photo
        ? asset('storage/'.$user->profile_photo)
        : 'https://ui-avatars.com/api/?name='.urlencode($user->name);

    $altText = $user->name;
@endphp

<img
    src="{{ $avatarUrl }}"
    alt="{{ $altText }}"
    class="{{ $class }}"
    style="width: {{ $size }}px; height: {{ $size }}px; object-fit: cover;">
