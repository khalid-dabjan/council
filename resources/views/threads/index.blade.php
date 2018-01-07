@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                @forelse($threads as $thread)
                    @include('threads._list')
                @empty
                    <p>There are no relevant results at this time</p>
                @endforelse
                {{$threads->render()}}
            </div>

            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">Search</div>
                    <div class="panel-body">
                        <form action="/threads/search">
                            <div class="form-group">
                                <input type="text" name="q" class="form-control" placeholder="Looking for something...">
                            </div>
                            <button type="submit" class="btn btn-default">Search</button>
                        </form>
                    </div>
                </div>
            </div>
            @if(count($trending))
                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">Trending Threads</div>
                        <div class="panel-body">
                            <ul class="list-group">
                                @foreach($trending as $thread)
                                    <li class="list-group-item">
                                        <a href="{{ $thread->path }}">{{ $thread->title }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection