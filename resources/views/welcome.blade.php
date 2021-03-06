@extends('layouts.app')

@section('head')
    <title>Hobby Showcase</title>
    <link rel="canonical" href="{{ url('/', true) }}"/>

@section('content')
    {!! Breadcrumbs::render('home') !!}
    <div class="page-header">
        <h1>Hobby showcase</h1>
    </div>
    <div class="row">
        {{-- Photo Gallery --}}
        <div class="col-xs-12 col-sm-7 col-md-9">
            <div class="row">
                @foreach($homepage->newPhotos as $photo)
                    <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                        <a href="{{ route('photos.show', $photo->id) }}">
                            <img src="{{ $photo->full_thumb_url }}" alt="{{ $photo->caption }}"
                                 class="img-responsive"/>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Panels --}}
    <div class="row">
        {{-- most viewed --}}
        @if($homepage->mostViewedCollection)
        <div class="col-xs-12 col-sm-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Most Viewed Collection
                </div>
                <div class="panel-body">
                    <a href="{{ route('collections.show', $homepage->mostViewedCollection->slug) }}">
                        @if($homepage->mostViewedCollection->featuredImage)
                            <img src="{{ $homepage->mostViewedCollection->featuredImage->full_thumb_url }}"
                                 class="img-responsive" alt="{{ $homepage->mostViewedCollection->label }}">
                        @endif
                        {{ $homepage->mostViewedCollection->label }}
                    </a>
                </div>
            </div>
        </div>
        @endif

        @if($homepage->lastUpdatedCollection)
        {{-- Last Updated --}}
        <div class="col-xs-12 col-sm-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Last Updated Collection
                </div>
                <div class="panel-body">
                    <a href="{{ route('collections.show', $homepage->lastUpdatedCollection->slug) }}">
                        @if($homepage->lastUpdatedCollection->featuredImage)
                            <img src="{{ $homepage->lastUpdatedCollection->featuredImage->full_thumb_url }}"
                                 class="img-responsive" alt="{{ $homepage->lastUpdatedCollection->label }}">
                        @endif
                        {{ $homepage->lastUpdatedCollection->label }}
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection
