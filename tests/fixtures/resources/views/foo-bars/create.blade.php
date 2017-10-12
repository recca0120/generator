@component('admin::components.layouts.master')

    @slot('contentHeader')
        @component('admin::components.content-header')
            @slot('title')
                Foo Bars
            @endslot
        @endcomponent
    @endslot

    {{ Form::model($fooBar, [
        'method' => 'POST',
        'route' => ['admin.foo-bars.store'],
        'data-parsley-validate' => 'data-parsley-validate',
        'files' => true,
    ]) }}

        @component('admin::components.box')
            @slot('title')
                Foo Bars
            @endslot

            @include('admin::foo-bars._form', ['fooBar' => $fooBar])

            @slot('footer')
                {{ Form::submit('Submit', ['class' => 'btn btn-success']) }}
                <a href="{{ route('admin.foo-bars.index', request()->query()) }}" class="btn btn-default">Cancel</a>
            @endslot
        @endcomponent

    {{ Form::close() }}

@endcomponent
