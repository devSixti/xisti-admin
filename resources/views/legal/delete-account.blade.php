@extends('legal.layout')

@section('title', __('legal.delete_account_page.title'))

@section('content')
<div class="container py-5">
    <h1>{{ __('legal.delete_account_page.title') }}</h1>
    <p class="mt-3">{{ __('legal.delete_account_page.how') }}</p>
    <ul>
        <li>{{ __('legal.delete_account_page.data_removed') }}</li>
        <li>{{ __('legal.delete_account_page.data_retained') }}</li>
        <li>{{ __('legal.delete_account_page.timeline') }}</li>
    </ul>
    @if(!empty($legalEmails['privacy']))
        <p><strong>{{ __('legal.delete_account_page.email_label') }}:</strong> {{ $legalEmails['privacy'] }}</p>
    @endif
    @if(!empty($deletionFlowUrl))
        <a class="btn btn-xisti mt-3" href="{{ $deletionFlowUrl }}">{{ __('legal.delete_account_page.start_flow') }}</a>
    @endif
</div>
@endsection
