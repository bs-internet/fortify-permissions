<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('app/settings/Setting');
    }
}
