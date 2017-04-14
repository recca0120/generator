@component('admin::layouts.master', ['title' => 'Foo Bars'])
    @component('admin::components.panel', ['title' => 'Foo Bars'])
        {{ Form::open([
            'method' =>'PUT',
            'route' => ['admin.foo-bars.update', $fooBar->id],
            'class' => 'form-horizontal form-label-left',
            'data-parsley-validate' => 'data-parsley-validate',
            'files' => true,
        ]) }}

            @include('admin::foo-bars._form', ['fooBar' => $fooBar])

            <div class="ln_solid"></div>
            <div class="form-group">
                {{ Form::submit('Submit', ['class' => 'btn btn-success']) }}
                <a href="{{ route('admin.foo-bars.index', request()) }}" class="btn btn-default">Cancel</a>
            </div>
        {{ Form::close() }}
    @endcomponent
@endcomponent
