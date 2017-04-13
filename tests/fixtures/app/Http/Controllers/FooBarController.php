<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Recca0120\Repository\Criteria;
use App\Http\Requests\FooBarRequest;
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

        return view('foo-bars.index', compact('fooBars'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $fooBar = $this->fooBars->newInstance([]);

        return view('foo-bars.create', compact('fooBar'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\FooBarRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(FooBarRequest $request)
    {
        $attributes = $request->all();
        $fooBar = $this->fooBars->create($attributes);

        return redirect(route('foo-bars.index', $request->query()))
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
            return redirect()->back()
                ->with('error', 'not found');
        }

        return view('foo-bars.edit', compact('fooBar'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\FooBarRequest $request
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function update(FooBarRequest $request, $id)
    {
        $attributes = $request->all();
        $fooBar = $this->fooBars->update($attributes, $id);

        return redirect(route('foo-bars.index', $request->query()))
            ->with('success', $fooBar->name.' updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $fooBar = $this->fooBars->find($id);

        if (is_null($fooBar) === true) {
            return redirect()->back()
                ->with('error', 'not found');
        }

        $this->fooBars->delete($id);

        return redirect(route('foo-bars.index', $request->query()))
            ->with('success', $fooBar->name.' deleted successfully.');
    }
}
