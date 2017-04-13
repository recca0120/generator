@component('admin::components.layout', ['title' => 'News'])
    @component('admin::components.panel', ['title' => 'News'])
        {{ Form::open([
            'method' => 'POST',
            'route' => ['admin.news.store'],
            'class' => 'form-horizontal form-label-left',
            'data-parsley-validate' => 'data-parsley-validate',
        ]) }}

            @include('admin::news._form', ['news' => $news])

            <div class="ln_solid"></div>
            <div class="form-group">
                {{ Form::submit('Submit', ['class' => 'btn btn-success']) }}
                <a href="{{ route('admin.news.index', request()) }}" class="btn btn-default">Cancel</a>
            </div>
        {{ Form::close() }}
    @endcomponent
@endcomponent
