<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Controller;
use Recca0120\Repository\Criteria;
use App\Repositories\Contracts\FooBarRepository;

class FooBarController extends Controller
{
    /**
     * $fooBars.
     *
     * @param \App\Repositories\Contracts\FooBarRepository
     */
    protected $fooBars;

    /**
     * Create a new controller instance.
     *
     * @param \App\Repositories\Contracts\FooBarRepository $fooBars
     */
    public function __construct(FooBarRepository $fooBars)
    {
        $this->fooBars = $fooBars;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $fooBars = $this->fooBars
            ->paginate(tap(Criteria::create(), function($criteria) use ($request) {
                if ($request->filled('sort') === true) {
                    $criteria->orderBy($request->get('sort'), $request->get('dir'));
                } else {
                    $criteria->orderBy('id', 'asc');
                }

                if ($request->filled('id') === true) {
                    $criteria->where('id', 'like', '%'.$request->get('id').'%');
                }
            }))
            ->appends($request->all());

        return response()->view('fooBar.index', compact('fooBars'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $fooBar = $this->fooBars->newInstance([]);

        return response()->view('fooBar.create', compact('dummyModel'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $attributes = $request->all();
        $fooBar = $this->fooBars->create($attributes);

        return response()
            ->redirectToRoute('fooBar.index', $request->query())
            ->with('success', $fooBar->name.' saved successfully.');
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
        $fooBar = $this->fooBars->find($id);

        if (is_null($fooBar) === true) {
            return redirect()
                ->back()
                ->with('error', 'not found');
        }

        return response()->view('fooBar.edit', compact('fooBar'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \DummyFullRequestClass $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $attributes = $request->all();
        $fooBar = $this->fooBars->update($id, $attributes);

        return response()
            ->redirectToRoute('fooBar.index', $request->query())
            ->with('success', $fooBar->name.' updated successfully.');
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
        $fooBar = $this->fooBars->find($id);

        if (is_null($fooBar) === true) {
            return redirect()
                ->back()
                ->with('error', 'not found');
        }

        $this->fooBars->delete($id);

        return response()
            ->redirectToRoute('fooBar.index', $request->query())
            ->with('success', $fooBar->name.' deleted successfully.');
    }
}