@extends('layouts.app')

@section('content')
<div class="container">
    @if($posts->count())
    <div class="">
        <div>
            @isset($category)
            <h4>Category: {{ $category->name }}</h4>
            @endisset

            @isset($tag)
            <h4>Tag: {{ $tag->name }}</h4>
            @endisset

            @if(!isset($tag) && !isset($category))
            <h4>All Post</h4>
            @endif
            <hr>
        </div>
        {{-- <div>
            @if(Auth::check())
            <a href="{{ route('posts.create') }}" class="btn btn-primary">New Post</a>
            @else
            <a href="{{ route('login') }}" class="btn btn-primary">Login to create new post</a>
            @endif
        </div> --}}
    </div>
    <div class="row">
        <div class="col-md-7">
            @foreach($posts as $post)
            <div class="card mb-4">
                @if($post->thumbnail)
                <a href="{{ route('posts.show', $post->slug) }}">
                    <img style="height: 400px; object-fit: cover; object-position: center;" class="card-img-top" src="{{ $post->takeImage }}">
                </a>
                @endif
                <div class="card-body">
                    <div>
                        <a href="{{ route('categories.show', $post->category->slug) }}" class="text-secondary">
                            <small>
                                {{ $post->category->name }} -
                            </small>
                        </a>
                         
                        @foreach($post->tags as $tag)
                            <a href="{{ route('tags.show', $tag->slug) }}" class="text-secondary">
                                <small>
                                    {{ $tag->name }}
                                </small>
                            </a>
                        @endforeach
                    </div>
                    <h5>
                        <a class="text-dark" href="{{ route('posts.show', $post->slug) }}" class="card-title">
                            {{ $post->title }}
                        </a>
                    </h5>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div class="media align-items-center">
                            <img width="40" class="rounded-circle mr-3" src="{{ $post->author->gravatar() }}" alt="">
                            <div class="media-body">
                                <div>
                                    {{ $post->author->name }}
                                </div>
                            </div>
                        </div>
                        <div class="text-secondary my-3">
                            <small>
                                Published {{ $post->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>

                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{ $posts->links() }}
    
    @else
    <div class="alert alert-info">
        There are no posts.
    </div>
    @endif
</div>
@stop