@extends('layouts.app')

@section('content')
<div>
<div class="mx-[45%] mt-52">
        <livewire:profile-card type="Discord" />
    </div>
    <div class="mx-[45%] mt-20">
        <livewire:profile-card type="Steam" useGameAccounts="true" />
    </div>
</div>
@endsection