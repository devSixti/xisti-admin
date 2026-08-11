@extends('legal.layout')

@section('title', __('legal.delete_account_page.title'))

@section('content')
<div class="container py-5">
    <h1>{{ __('legal.delete_account_page.title') }}</h1>
    <p class="mt-3">{{ __('legal.delete_account_page.how') }}</p>
    <h2 class="h5 mt-4">{{ __('legal.delete_account') }}</h2>
    <ul>
        <li>{{ __('legal.delete_account_page.data_removed') }}</li>
        <li>{{ __('legal.delete_account_page.data_retained') }}</li>
        <li>{{ __('legal.delete_account_page.timeline') }}</li>
    </ul>
    <p><strong>{{ __('legal.delete_account_page.email_label') }}:</strong>
        <a href="mailto:{{ $legalEmails['privacy'] ?? 'privacidad@zimo.com' }}">{{ $legalEmails['privacy'] ?? 'privacidad@zimo.com' }}</a>
    </p>
    <a class="btn btn-zimo mt-3" href="{{ $deletionFlowUrl ?? url('/account-deletion/login') }}">{{ __('legal.delete_account_page.start_flow') }}</a>
</div>
@endsection
