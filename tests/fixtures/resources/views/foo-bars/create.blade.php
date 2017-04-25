@component('admin::layouts.master', ['title' => 'Foo Bars'])

    {{ Form::model($fooBar, [
        'method' => 'POST',
        'route' => ['admin.foo-bars.store'],
        'class' => 'form-horizontal form-label-left',
        'data-parsley-validate' => 'data-parsley-validate',
        'files' => true,
    ]) }}

        @component('admin::components.panel', ['title' => 'Foo Bars'])

            @include('admin::foo-bars._form', ['fooBar' => $fooBar])

            <div class="ln_solid"></div>

            <div class="form-group">
                {{ Form::submit('Submit', ['class' => 'btn btn-success']) }}
                <a href="{{ route('admin.foo-bars.index', request()->query()) }}" class="btn btn-default">Cancel</a>
            </div>

        @endcomponent

    {{ Form::close() }}

@endcomponent
