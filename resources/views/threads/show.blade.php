@extends('layouts.app')
@section('header')
    <link rel="stylesheet" href="/css/vendor/jquery.atwho.css">
@endsection
@section('content')
    <thread-view inline-template :thread="{{ $thread }}">
        <div class="container">
            <div class="row">
                <div class="col-md-8" v-cloak>
                    @include('threads._question')
                    <replies
                            @remove="repliesCount--"
                            @added="repliesCount++">
                    </replies>


                </div>
                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <p>
                                This Thread was published {{ $thread->created_at->diffForHumans() }},
                                By <a href="#">{{ $thread->creator->name }}</a>, and currently
                                has <span
                                        v-text="repliesCount"></span> {{ str_plural('comment',$thread->replies_count) }}
                            </p>
                            <subscribe-button :active="{{json_encode($thread->isSubscribedTo)}}"
                                              v-if="signedIn"></subscribe-button>

                            <button class="btn btn-default"
                                    v-if="authorize('isAdmin')"
                                    @click="toggleLock"
                                    v-text="locked ? 'unlock' : 'lock'">
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </thread-view>
@endsection
