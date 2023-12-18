@extends('layouts.app')

@section('content')
<div>
<div class="mx-[45%] mt-52">
        <livewire:profile-card type="Discord" useGameAccounts="true" />
    </div>
    <div class="mx-[45%] mt-52">
        <livewire:profile-card type="Steam" useGameAccounts="true" />
    </div>
</div>
@endsection