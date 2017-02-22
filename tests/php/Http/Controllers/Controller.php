<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserProviderRequest;
use Illuminate\Http\Request;
use App\Repositories\Contracts\UserProviderRepository;
use Recca0120\Repository\Criteria;

class UserProviderController extends Controller
{
    protected $userProviders;

    public function __construct(UserProviderRepository $userProviders)
    {
        $this->userProviders = $userProviders;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminte\Http\Request
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

        $userProviders = $this->userProviders
            ->paginate($criteria)
            ->appends($request->all());

        return view('user_providers.index', compact('userProviders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userProvider = $this->userProviders->newInstance([]);

        return view('user_providers.create', compact('userProvider'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\UserProviderRequest  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(UserProviderRequest $request)
    {
        $attributes = $request->all();
        $userProvider = $this->userProviders->create($attributes);

        return redirect(route('user_providers.index', $request->query()))
            ->with('success', sprintf('已新增 %s', $userProvider->name));
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
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
     * @param  string $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $userProvider = $this->userProviders->find($id);

        if (is_null($userProvider) === true) {
            return redirect()->back()
                ->with('error', '資料不存在');
        }

        return view('user_providers.edit', compact('userProvider'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UserProviderRequest  $request
     * @param  string $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UserProviderRequest $request, $id)
    {
        $attributes = $request->all();
        $userProvider = $this->userProviders->update($attributes, $id);

        return redirect(route('user_providers.index', $request->query()))
            ->with('success', sprintf('已修改 %s', $userProvider->name));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $userProvider = $this->userProviders->find($id);

        if (is_null($userProvider) === true) {
            return redirect()->back()
                ->with('error', '資料不存在');
        }

        if ($this->userProviders->delete($id) == false) {
            return redirect()->back()
                ->with('error', sprintf('無法刪除 %s', $userProvider->name));
        }

        return redirect(route('user_providers.index', $request->query()))
            ->with('success', sprintf('已刪除 %s', $userProvider->name));
    }
}
