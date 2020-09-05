@extends('layouts.app')

@section('content')
    @can('first-gate')
    <h1>
        first-gate
    </h1>            
    @endcan

    @can('second-gate')
    <h1>
        second-gate
    </h1>            
    @endcan

    <ul>
        @foreach ($paginatedPosts as $item)
            <li>{{$item->name}}</li>
        @endforeach
    </ul>

    {{$paginatedPosts->links()}}

@endsection