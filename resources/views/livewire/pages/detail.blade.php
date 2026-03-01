@extends(config('inviteable.ui.layout', 'layouts.app'))

@section('content')
    <livewire:inviteable::invitation-detail :invite="$invite" />
@endsection
