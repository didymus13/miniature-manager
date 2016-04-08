@extends('layouts.app')

<?php $token = csrf_token() ?>

@section('head')
    <title>{{ $collection->label }}</title>
    <meta name="blurb" content="{{ $collection->description }}"/>
    <meta name="keywords" content="{{ implode(',', $collection->tagNames()) }}">

    <link rel="canonical" href="{{ route('collections.show', $collection->slug, true) }}"/>
    @can('edit', $collection)
    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css"
          rel="stylesheet"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/jquery.slick/1.5.9/slick.css"/>
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/jquery.slick/1.5.9/slick-theme.css"/>
    @endcan
@endsection

@section('content')
    @can('delete', $collection)
    {!! Form::open(['route' => ['collections.destroy', $collection->slug], 'method' => 'DELETE']) !!}
    <div class="pull-right">
        {!! Form::submit('Delete Collection', ['class' => 'btn btn-danger confirm']) !!}
    </div>
    {!! Form::close() !!}
    @endcan
    <article>
    <div class="page-heading">
        <h1 data-type="text" data-pk="{{ $collection->slug }}" data-name="label"
            data-url="{{ route('collections.update', $collection->slug) }}" data-title="Name" class="editable">
            {{ $collection->label }}
        </h1>
    </div>

    <ul id="photo-gallery">
        @foreach($collection->miniatures as $miniature)
            @foreach($miniature->photos as $photo)
                <li><img data-lazy="{{ $photo->url }}" alt="{{ $photo->caption }}" /></li>
            @endforeach
        @endforeach
    </ul>

    <ul class="list-inline">
        <li><span class="fa fa-user"></span></li>
        <li>{{ $collection->user->name }}</li>
        <li><span class="fa fa-calendar"></span> {{ $collection->updated_at->diffForHumans() }}</li>

        @cannot('edit', $collection)
        @if($collection->tags)
            <li><span class="fa fa-tags"></span></li>
            @foreach($collection->tags as $tag)
                <li><a href="{{ route('collections.index', ['tags' => $tag->slug]) }}">{{ $tag->name }}</a></li>
            @endforeach
        @endif
        @endcannot
    </ul>

        @can('edit', $collection)
        {!! Form::label('tags', 'Tags') !!}
        <select multiple="multiple" class="form-control select" name="tags"
                data-url="{{ route('collections.update', $collection->slug) }}">
            @foreach(\App\Collection::existingTags() as $tag)
                <option value="{{ $tag->name }}"
                        @if(in_array($tag->name, $collection->tagNames())) selected="selected" @endif
                >
                    {{ $tag->name }}
                </option>
            @endforeach
        </select>
        @endcan

        <p data-type="textarea" data-pk="{{ $collection->slug }}" data-name="description"
           data-url="{{ route('collections.update', $collection->slug) }}" data-title="Name" class="editable">
            {{ $collection->description }}
        </p>

    <table class="table">
        <caption>Miniatures</caption>
        <thead>
        <tr>
            <th>Name</th>
            <th>Progress</th>
            <th>Last Updated</th>
        </tr>
        </thead>

        <tbody>
        @foreach($collection->miniatures as $mini)
            <tr>
                <td>
                    <span data-type="text" data-pk="{{ $mini->slug }}" data-name="label"
                          data-url="{{ route('miniatures.update', $mini->slug) }}" data-title="Name" class="editable">
                        {{ $mini->label }}
                    </span>
                </td>
                <td>
                    <div class="progress" data-value="{{ $mini->progress }}">
                        <div class="progress-bar" style="width: {{$mini->progress}}%">
                            <span class="editable" data-type="number" data-pk="{{ $mini->slug }}" data-name="progress"
                                  data-url="{{ route('miniatures.update', $mini->slug) }}" data-title="Progress">
                                {{ $mini->progress }}
                            </span> %
                        </div>
                    </div>
                </td>
                <td>{{ $mini->updated_at->diffForHumans() }}</td>
                @can('delete', $mini)
                <td>
                    <span data-url="{{ route('miniatures.destroy', $mini->slug) }}"
                          class="btn btn-danger confirm destroy">
                        Delete
                    </span>
                </td>
                @endcan
            </tr>
        @endforeach
        </tbody>
    </table>
    </article>

    @can('edit', $collection)
    {!! Form::open(['route' => ['miniatures.store'], 'method' => 'POST', 'class' => 'form-inline']) !!}
    {!! Form::hidden('collection', $collection->slug) !!}
    <div class="form-group">
        {!!  Form::label('label', 'Name:') !!}
        {!! Form::text('label', null, ['class' => 'form-control', 'placeholder' => 'New Miniature']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('progress', 'Progress:') !!}
        {!! Form::number('progress', null, ['class' => 'form-control', 'placeholder' => '0', 'min' => 0, 'max' => 100]) !!}
    </div>
    {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
    {!! Form::close() !!}
    @endcan
@endsection

@section('endBody')
    @can('edit', $collection)
    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/i18n/en.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/jquery.slick/1.5.9/slick.min.js"></script>
    <script>
        $(function () {
            $('.select').select2({
                tags: true
            });
            $('#photo-gallery').slick({
                lazyLoad: 'ondemand',
                lazyLoadBuffer: 0,
                slidesToShow: 3,
                slidesToScroll: 1,
                centerMode: true,
                variableWidth: true,
                dots: true
            });
            $.fn.editable.defaults.ajaxOptions = {method: 'PATCH'};
            $(".editable").editable({
                params: function (params) {
                    var data = {};
                    data['id'] = params.pk;
                    data[params.name] = params.value;
                    data['_token'] = '{{ $token }}';
                    return data;
                },
                success: function (response, newValue) {
                    var parent = $(this).parent();
                    if (parent.hasClass('progress-bar')) {
                        parent.width(newValue + '%');
                        }
                    }
            });

            $('tbody').delegate('.confirm.destroy', 'click', function (event) {
                if (!confirm('Are you sure?')) {
                    return;
                }
                var deleteThis = $(this).closest('tr');
                $.ajax({
                    url: $(this).data('url'),
                    type: 'DELETE',
                    data: {_token: '{{ $token }}'},
                    statusCode: {
                        204: function (response) {
                            console.log(row);
                            deleteThis.fadeOut(750, function () {
                                $(this).remove()
                            });
                        }
                    }
                })
            });
            $('.select').on('change', function (event) {
                console.log($(this).val());
                $.ajax({
                    url: $(this).data('url'),
                    type: 'PUT',
                    data: {_token: '{{ $token }}', tags: $(this).val()}
                });
            });
        });
    </script>
    @endcan
@endsection