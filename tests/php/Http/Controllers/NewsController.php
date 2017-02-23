<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsRequest;
use Illuminate\Http\Request;
use App\Repositories\Contracts\NewsRepository;
use Recca0120\Repository\Criteria;

class NewsController extends Controller
{
    protected $newsCollection;

    public function __construct(NewsRepository $newsCollection)
    {
        $this->newsCollection = $newsCollection;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminte\Http\Request $request
     *
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

        $newsCollection = $this->newsCollection
            ->paginate($criteria)
            ->appends($request->all());

        return view('news.index', compact('newsCollection'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $news = $this->newsCollection->newInstance([]);

        return view('news.create', compact('news'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\NewsRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(NewsRequest $request)
    {
        $attributes = $request->all();
        $news = $this->newsCollection->create($attributes);

        return redirect(route('news.index', $request->query()))
            ->with('success', sprintf('已新增 %s', $news->name));
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     *
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
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $news = $this->newsCollection->find($id);

        if (is_null($news) === true) {
            return redirect()->back()
                ->with('error', '資料不存在');
        }

        return view('news.edit', compact('news'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\NewsRequest $request
     * @param string $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(NewsRequest $request, $id)
    {
        $attributes = $request->all();
        $news = $this->newsCollection->update($attributes, $id);

        return redirect(route('news.index', $request->query()))
            ->with('success', sprintf('已修改 %s', $news->name));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $news = $this->newsCollection->find($id);

        if (is_null($news) === true) {
            return redirect()->back()
                ->with('error', '資料不存在');
        }

        if ($this->newsCollection->delete($id) == false) {
            return redirect()->back()
                ->with('error', sprintf('無法刪除 %s', $news->name));
        }

        return redirect(route('news.index', $request->query()))
            ->with('success', sprintf('已刪除 %s', $news->name));
    }
}
