@extends('layouts.app')

@section('title', 'Trash - Hasa')

@section('content')
<div class="content-area">
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Trash'],
    ]" />

    <livewire:trash-table />
</div>
@endsection
