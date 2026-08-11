@extends('legal.layout')

@section('title', $title ?? __('legal.centro_legal'))

@section('content')
<div class="container py-5">
    {!! $body ?? '' !!}
    @if(!empty($centroLegalUrl))
        <p class="mt-4"><a class="btn btn-xisti" href="{{ $centroLegalUrl }}">{{ __('legal.centro_legal') }}</a></p>
    @endif
</div>
@endsection
