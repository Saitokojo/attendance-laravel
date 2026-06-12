<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOfficeRequest;
use App\Http\Requests\UpdateOfficeRequest;
use App\Models\Office;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OfficeController extends Controller
{
    public function index(): View
    {
        $offices = Office::orderBy('name')->get();

        return view('admin.offices.index', compact('offices'));
    }

    public function create(): View
    {
        return view('admin.offices.create', ['timezones' => $this->timezones()]);
    }

    public function store(StoreOfficeRequest $request): RedirectResponse
    {
        Office::create($request->validated());

        return redirect()->route('admin.offices.index')->with('success', 'Office created.');
    }

    public function edit(Office $office): View
    {
        return view('admin.offices.edit', [
            'office' => $office,
            'timezones' => $this->timezones(),
        ]);
    }

    public function update(UpdateOfficeRequest $request, Office $office): RedirectResponse
    {
        $office->update($request->validated());

        return redirect()->route('admin.offices.index')->with('success', 'Office updated.');
    }

    private function timezones(): array
    {
        return \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
    }
}
