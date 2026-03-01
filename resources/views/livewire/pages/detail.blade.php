@extends(config('inviteable.ui.layout', 'layouts.app'))

@section('content')
    <livewire:inviteable-detail :invite="$invite" />
@endsection
