<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyTrainRequest;
use App\Http\Requests\StoreTrainRequest;
use App\Http\Requests\UpdateTrainRequest;
use App\Models\Train;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrainController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('train_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $trains = Train::with(['user'])->get();

        return view('admin.trains.index', compact('trains'));
    }

    public function create()
    {
        abort_if(Gate::denies('train_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.trains.create', compact('users'));
    }

    public function store(StoreTrainRequest $request)
    {
        $train = Train::create($request->all());

        return redirect()->route('admin.trains.index');
    }

    public function edit(Train $train)
    {
        abort_if(Gate::denies('train_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $train->load('user');

        return view('admin.trains.edit', compact('train', 'users'));
    }

    public function update(UpdateTrainRequest $request, Train $train)
    {
        $train->update($request->all());

        return redirect()->route('admin.trains.index');
    }

    public function show(Train $train)
    {
        abort_if(Gate::denies('train_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $train->load('user');

        return view('admin.trains.show', compact('train'));
    }

    public function destroy(Train $train)
    {
        abort_if(Gate::denies('train_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $train->delete();

        return back();
    }

    public function massDestroy(MassDestroyTrainRequest $request)
    {
        $trains = Train::find(request('ids'));

        foreach ($trains as $train) {
            $train->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
