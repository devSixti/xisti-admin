<?php

namespace App\Http\Controllers;

use App\Helpers\AdminUi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminLocaleController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $locale = AdminUi::normalizeLocale((string) $request->input('locale', AdminUi::DEFAULT_LOCALE));
        session()->put(AdminUi::SESSION_KEY, $locale);

        return redirect()->back();
    }
}
