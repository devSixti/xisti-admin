<?php

namespace App\Http\Controllers;

use App\Support\LegalCentro\LegalContent;
use App\Support\LegalCentro\LegalHub;
use App\Support\LegalConfig;
use App\Support\PublicLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LegalWebController extends Controller
{
    private function resolveLang(Request $request): string
    {
        return PublicLocale::fromRequest($request);
    }

    /** @param array<string, mixed> $extra */
    private function legalView(string $view, Request $request, array $extra = [])
    {
        $lang = $this->resolveLang($request);
        app()->setLocale($lang);

        return view($view, array_merge($this->sharedContext($lang, $extra['activeSlug'] ?? null), $extra));
    }

    /** @return array<string, mixed> */
    private function sharedContext(string $lang, ?string $activeSlug = null): array
    {
        return [
            'lang' => $lang,
            'activeSlug' => $activeSlug,
            'centroLegalUrl' => LegalConfig::centroLegalUrl($lang),
            'legalEmails' => LegalConfig::emails(),
            'storeLinks' => [
                'android' => LegalConfig::storeLink('android'),
                'ios' => LegalConfig::storeLink('ios'),
            ],
            'brandName' => LegalHub::brandName(),
            'tagline' => LegalHub::tagline(),
            'consentVersion' => LegalHub::consentVersion(),
            'lastUpdated' => LegalHub::lastUpdated(),
            'entity' => LegalHub::entity(),
            'navSections' => LegalHub::sections(),
            'langs' => LegalHub::LANGS,
        ];
    }

    public function index(Request $request)
    {
        return $this->legalView('legal.index', $request, ['activeSlug' => 'hub']);
    }

    public function cookies(Request $request)
    {
        $doc = LegalContent::resolve('cookies', $this->resolveLang($request));

        return $this->legalView('legal.page', $request, [
            'activeSlug' => 'cookies',
            'title' => $doc['title'] ?? __('legal.cookies'),
            'summary' => $doc['summary'] ?? '',
            'body' => $doc['body'] ?? view('legal.partials.cookies-fallback')->render(),
        ]);
    }

    public function deleteAccountInfo(Request $request)
    {
        $doc = LegalContent::resolve('eliminar-cuenta', $this->resolveLang($request));

        return $this->legalView('legal.page', $request, [
            'activeSlug' => 'eliminar-cuenta',
            'title' => $doc['title'] ?? __('legal.delete_account_page.title'),
            'summary' => $doc['summary'] ?? '',
            'body' => $doc['body'] ?? '',
            'deletionFlowUrl' => url('/account-deletion/login'),
        ]);
    }

    public function document(Request $request, string $slug)
    {
        $lang = $this->resolveLang($request);
        app()->setLocale($lang);

        $doc = LegalContent::resolve($slug, $lang);
        if ($doc === null) {
            abort(404);
        }

        return view('legal.page', $this->sharedContext($lang, $slug) + [
            'title' => $doc['title'],
            'summary' => $doc['summary'],
            'body' => $doc['body'],
        ]);
    }

    public function localizedSupportPage(Request $request, string $slug)
    {
        return $this->document($request, $slug);
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
            return redirect()->to(url('/#contact').'?lang='.$lang)
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
                            ->subject(LegalHub::brandName().' contact: '.$request->input('name'))
                            ->replyTo($request->input('email'), $request->input('name'));
                    }
                );
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()->to(url('/#contact').'?lang='.$lang)
            ->with('contact_success', __('legal.contact_form.success'));
    }
}
