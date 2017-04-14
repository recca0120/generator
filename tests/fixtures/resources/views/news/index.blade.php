@component('admin::layouts.master', ['title' => 'News'])
    @component('admin::components.panel', ['title' => 'News'])
        <a href="{{ route('admin.news.create') }}" class="btn btn-success">
            <i class="fa fa-plus"></i>
            Add
        </a>
        <table class="table table-striped jambo_table bulk_action">
            <thead>
                <tr>
                    <th>Id</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($newsCollection as $news)
                    <tr>
                        <td>{{ $news->id }}</td>
                        <td>
                            <a href="{{ route('admin.news.edit', [$news->id]) }}" class="btn btn-info btn-xs">
                                <i class="fa fa-pencil"></i>
                                Edit
                            </a>
                            <a href="{{ route('admin.news.destroy', [$news->id]) }}" class="btn btn-danger btn-xs" data-method="DELETE">
                                <i class="fa fa-trash-o"></i>
                                Delete
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($newsCollection->hasPages() === true)
            {{ $newsCollection->render() }}
        @endif
    @endcomponent
@endcomponent
