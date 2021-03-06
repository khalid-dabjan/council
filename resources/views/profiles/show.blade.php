@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="page-header">
                    <avatar-form :user="{{ $profileUser }}"></avatar-form>
                </div>

                @forelse($activities as $date => $activity)
                    <h3 class="page_header">{{ $date }}</h3>
                    @foreach($activity as $record)
                        @if(view()->exists("profiles.activities.{$record->type}"))
                            @include("profiles.activities.{$record->type}",['activity' => $record])
                        @endif
                    @endforeach
                @empty
                    There is nothing to show.
                @endforelse
            </div>
        </div>

    </div>
@endsection