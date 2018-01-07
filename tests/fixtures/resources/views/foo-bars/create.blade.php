@component('admin::components.layouts.master')

    @slot('contentHeader')
        @component('admin::components.content-header')
            @slot('title')
                Foo Bars
            @endslot
        @endcomponent
    @endslot

    <form method="post" action="{{ route('admin.foo-bars.store') }}" enctype="multipart/form-data" data-parsley-validate>
        {{ csrf_field() }}

        @component('admin::components.box')
            @slot('title')
                Foo Bars
            @endslot

            @include('admin::foo-bars._form', ['fooBar' => $fooBar])

            @slot('footer')
                <button type="submit" class="btn btn-success">Submit</button>
                <a href="{{ route('admin.foo-bars.index', request()->query()) }}" class="btn btn-default">Cancel</a>
            @endslot
        @endcomponent

    </form>

@endcomponent
