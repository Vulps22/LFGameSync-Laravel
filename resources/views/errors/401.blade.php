@extends('layouts.app')

@section('content')

<div>
  <livewire:error-code code="401" message="Unauthorized" details="If you used /link to get here from discord your token may have expired. re-run the command and try again." />
</div>
@endsection