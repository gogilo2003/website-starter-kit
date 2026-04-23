<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;

class MigrationController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Migration', [
            'output' => session('output'),
            'notification' => session('notification'),
        ]);
    }

    public function execute(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:migrate,rollback,seed',
            'step'   => 'nullable|integer|min:1',
            'fresh'  => 'nullable|boolean',
            'seed'   => 'nullable|boolean',
        ]);

        try {
            $params = ['--force' => true];

            if (!empty($validated['step'])) {
                $params['--step'] = $validated['step'];
            }

            if (!empty($validated['seed'])) {
                $params['--seed'] = true;
            }

            match ($validated['action']) {
                'migrate' => !empty($validated['fresh'])
                    ? Artisan::call('migrate:fresh', $params)
                    : Artisan::call('migrate', $params),

                'rollback' => Artisan::call('migrate:rollback', $params),

                'seed' => Artisan::call('db:seed', ['--force' => true]),
            };

            return back()->with([
                'notification' => [
                    'success' => 'Command executed successfully',
                ],
                'output' => Artisan::output(),
            ]);
        } catch (\Throwable $e) {
            return back()->with([
                'notification' => [
                    'error' => $e->getMessage(),
                ],
                'output' => '',
            ]);
        }
    }
}
