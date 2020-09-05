@php
$currentHost = request()->getHost();

$appUrl = config('app.name');
@endphp

<div class="links">

    @foreach ($routesWithNameAndLinks() as $eachRoute)
        <a target="_blank" href="{{Str::formatUrl($eachRoute['routename'])}}">{{$eachRoute['displayName']}}</a>
        @php
            unset($realUrl);
        @endphp
    @endforeach
</div>