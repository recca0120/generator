<?php

namespace App\Http\Controllers;

use App\Http\Requests\FooBarRequest;
use Illuminate\Http\Request;
use App\Repositories\Contracts\FooBarRepository;
use Recca0120\Repository\Criteria;

class FooBarController extends Controller
{
    protected $fooBars;

    public function __construct(FooBarRepository $fooBars)
    {
        $this->fooBars = $fooBars;
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

        $fooBars = $this->fooBars
            ->paginate($criteria)
            ->appends($request->all());

        return view('foo_bars.index', compact('fooBars'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $fooBar = $this->fooBars->newInstance([]);

        return view('foo_bars.create', compact('fooBar'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\FooBarRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(FooBarRequest $request)
    {
        $attributes = $request->all();
        $fooBar = $this->fooBars->create($attributes);

        return redirect(route('foo_bars.index', $request->query()))
            ->with('success', sprintf('已新增 %s', $fooBar->name));
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
        $fooBar = $this->fooBars->find($id);

        if (is_null($fooBar) === true) {
            return redirect()->back()
                ->with('error', '資料不存在');
        }

        return view('foo_bars.edit', compact('fooBar'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\FooBarRequest $request
     * @param string $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(FooBarRequest $request, $id)
    {
        $attributes = $request->all();
        $fooBar = $this->fooBars->update($attributes, $id);

        return redirect(route('foo_bars.index', $request->query()))
            ->with('success', sprintf('已修改 %s', $fooBar->name));
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
        $fooBar = $this->fooBars->find($id);

        if (is_null($fooBar) === true) {
            return redirect()->back()
                ->with('error', '資料不存在');
        }

        if ($this->fooBars->delete($id) == false) {
            return redirect()->back()
                ->with('error', sprintf('無法刪除 %s', $fooBar->name));
        }

        return redirect(route('foo_bars.index', $request->query()))
            ->with('success', sprintf('已刪除 %s', $fooBar->name));
    }
}
