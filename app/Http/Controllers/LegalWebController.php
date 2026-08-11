<?php

namespace App\Http\Controllers;

use App\Models\PageSettings;
use App\Support\LegalConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LegalWebController extends Controller
{
    private function resolveLang(Request $request): string
    {
        $lang = strtolower(substr((string) $request->query('lang', ''), 0, 2));
        if (in_array($lang, ['es', 'en', 'pt', 'fr', 'it'], true)) {
            return $lang;
        }
        $accept = strtolower(substr((string) $request->header('Accept-Language', 'es'), 0, 2));

        return in_array($accept, ['es', 'en', 'pt', 'fr', 'it'], true) ? $accept : 'es';
    }

    private function legalView(string $view, Request $request, array $extra = [])
    {
        $lang = $this->resolveLang($request);
        app()->setLocale($lang);

        return view($view, array_merge([
            'lang' => $lang,
            'centroLegalUrl' => LegalConfig::centroLegalUrl($lang),
            'legalEmails' => LegalConfig::emails(),
            'storeLinks' => [
                'android' => LegalConfig::storeLink('android'),
                'ios' => LegalConfig::storeLink('ios'),
            ],
        ], $extra));
    }

    public function index(Request $request)
    {
        return $this->legalView('legal.index', $request);
    }

    public function cookies(Request $request)
    {
        return $this->legalView('legal.cookies', $request);
    }

    public function deleteAccountInfo(Request $request)
    {
        return $this->legalView('legal.delete-account', $request, [
            'deletionFlowUrl' => url('/account-deletion/login'),
        ]);
    }

    public function postContact(Request $request)
    {
        $lang = $this->resolveLang($request);
        app()->setLocale($lang);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:191',
            'message' => 'required|string|max:5000',
            'accept_terms' => 'accepted',
            'accept_data_processing' => 'accepted',
        ], [
            'accept_terms.accepted' => __('legal.validation.accept_terms'),
            'accept_data_processing.accepted' => __('legal.validation.accept_data_processing'),
        ]);

        if ($validator->fails()) {
            return redirect()->to(url('/#contact') . '?lang=' . $lang)
                ->withErrors($validator)
                ->withInput();
        }

        $to = LegalConfig::email('support') ?: LegalConfig::email('hello');
        if ($to !== '') {
            try {
                Mail::raw(
                    "Contact form\n\nName: {$request->input('name')}\nEmail: {$request->input('email')}\n\n{$request->input('message')}",
                    function ($message) use ($to, $request) {
                        $message->to($to)
                            ->subject('XISTI contact: ' . $request->input('name'))
                            ->replyTo($request->input('email'), $request->input('name'));
                    }
                );
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()->to(url('/#contact') . '?lang=' . $lang)
            ->with('contact_success', __('legal.contact_form.success'));
    }

    public function localizedSupportPage(Request $request, string $slug)
    {
        $lang = $this->resolveLang($request);
        app()->setLocale($lang);
        $idMap = [
            'contacto' => 1,
            'faq' => 2,
            'aviso-legal' => 3,
            'privacidad' => 4,
            'terminos' => 5,
            'seguridad' => 6,
        ];
        $pageId = $idMap[$slug] ?? null;
        if ($pageId === null) {
            abort(404);
        }

        $page = PageSettings::query()->where('type', 1)->where('id', $pageId)->first();
        if ($page === null) {
            abort(404);
        }

        $localized = $page->localized($lang);

        return view('legal.page', [
            'lang' => $lang,
            'title' => $localized->name,
            'body' => $localized->description,
            'centroLegalUrl' => LegalConfig::centroLegalUrl($lang),
        ]);
    }
}
