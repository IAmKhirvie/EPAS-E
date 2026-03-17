@extends('layouts.app')

@section('title', 'Trash - EPAS-E')

@section('content')
<div class="content-area">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Trash</li>
        </ol>
    </nav>

    <livewire:trash-table />
</div>
@endsection
