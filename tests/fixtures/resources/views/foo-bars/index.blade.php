@component('admin::components.layouts.master')

    @slot('contentHeader')
        @component('admin::components.content-header')
            @slot('title')
                Foo Bars
            @endslot
        @endcomponent
    @endslot

    @component('admin::components.box', ['tableResponsive' => true, 'noPadding' => true])
        @slot('title')
            Foo Bars
        @endslot

        <div class="btn-toolbar pad">
            <a href="{{ route('admin.foo-bars.create') }}" class="btn btn-success btn-sm" data-tooltip="Add">
                <i class="fa fa-plus"></i>
            </a>
        </div>

        <table class="table table-hover bulk_action">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($fooBars as $fooBar)
                    <tr>
                        <td>{{ $fooBar->id }}</td>
                        <td>
                            <a href="{{ route('admin.foo-bars.edit', array_merge([$fooBar->id], request()->query())) }}" class="btn btn-info btn-xs">
                                <i class="fa fa-pencil"></i>
                                Edit
                            </a>
                            <a href="{{ route('admin.foo-bars.destroy', array_merge([$fooBar->id], request()->query())) }}" class="btn btn-danger btn-xs" data-method="delete" data-confirm="delete ?">
                                <i class="fa fa-trash-o"></i>
                                Delete
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @slot('footer')
            {{ $fooBars->render('admin::vendor.pagination.admin-lte') }}
        @endslot

    @endcomponent

@endcomponent
