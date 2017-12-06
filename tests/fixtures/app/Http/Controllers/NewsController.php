<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\NewsRequest;
use Recca0120\Repository\Criteria;
use App\Repositories\Contracts\NewsRepository;

class NewsController extends Controller
{
    /**
     * $news.
     *
     * @param \App\Repositories\Contracts\NewsRepository
     */
    protected $news;

    /**
     * Create a new controller instance.
     *
     * @param \App\Repositories\Contracts\NewsRepository $news
     */
    public function __construct(NewsRepository $news)
    {
        $this->news = $news;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $criteria = Criteria::create();

        if ($request->has('sort') === true) {
            $criteria->orderBy($request->get('sort'), $request->get('dir'));
        } else {
            $criteria->orderBy('id', 'asc');
        }

        $criteria->where(function ($criteria) use ($request) {
            if ($request->has('id') === true) {
                $criteria->where('id', 'like', '%'.$request->get('id').'%');
            }

            return $criteria;
        });

        $newsCollection = $this->news
            ->paginate($criteria)
            ->appends($request->all());

        return response()->view('news.index', compact('newsCollection'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $news = $this->news->newInstance([]);

        return response()->view('news.create', compact('news'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\NewsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(NewsRequest $request)
    {
        $attributes = $request->all();
        $news = $this->news->create($attributes);

        return response()
            ->redirectToRoute('news.index', $request->query())
            ->with('success', $news->name.' saved successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $news = $this->news->find($id);

        if (is_null($news) === true) {
            return redirect()
                ->back()
                ->with('error', 'not found');
        }

        return response()->view('news.edit', compact('news'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\NewsRequest $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(NewsRequest $request, $id)
    {
        $attributes = $request->all();
        $news = $this->news->update($id, $attributes);

        return response()
            ->redirectToRoute('news.index', $request->query())
            ->with('success', $news->name.' updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        $news = $this->news->find($id);

        if (is_null($news) === true) {
            return redirect()
                ->back()
                ->with('error', 'not found');
        }

        $this->news->delete($id);

        return response()
            ->redirectToRoute('news.index', $request->query())
            ->with('success', $news->name.' deleted successfully.');
    }
}
