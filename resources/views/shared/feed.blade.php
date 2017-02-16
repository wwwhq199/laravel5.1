@if (count($feed_items))
    <ol class="statuses">
        @foreach ($feed_items as $status)
{{--            @include('layouts._status')--}}
            @include('layouts._status', ['user' => $status->user])
        @endforeach
        {!! $feed_items->render() !!}
    </ol>
@endif