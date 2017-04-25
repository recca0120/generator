@component('admin::layouts.master', ['title' => 'Foo Bars'])

    @component('admin::components.panel', ['title' => 'Foo Bars'])

        <a href="{{ route('admin.foo-bars.create', request()->query()) }}" class="btn btn-success">
            <i class="fa fa-plus"></i>
            Add
        </a>

        <table class="table table-striped jambo_table bulk_action">
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
                            <a href="{{ route('admin.foo-bars.destroy', array_merge([$fooBar->id], request()->query())) }}" class="btn btn-danger btn-xs" data-method="DELETE">
                                <i class="fa fa-trash-o"></i>
                                Delete
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if ($fooBars->hasPages() === true)
            {{ $fooBars->render() }}
        @endif

    @endcomponent

@endcomponent
