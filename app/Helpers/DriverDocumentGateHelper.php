<?php

namespace App\Helpers;

use App\Models\ProviderDocuments;
use App\Models\RequiredDocuments;
use Illuminate\Http\JsonResponse;

class DriverDocumentGateHelper
{
    /**
     * Block going online when manual doc approval is enabled and required docs are missing or not approved.
     */
    public static function onlineBlockResponse(int $userId): ?JsonResponse
    {
        $general = request()->get('general_settings');
        if ($general === null || (int) ($general->auto_approve ?? 0) === 1) {
            return null;
        }

        $requiredIds = RequiredDocuments::query()->where('status', 1)->pluck('id');
        if ($requiredIds->isEmpty()) {
            return null;
        }

        $documents = ProviderDocuments::query()
            ->join('required_documents', 'required_documents.id', '=', 'provider_documents.req_document_id')
            ->where('required_documents.status', 1)
            ->where('provider_documents.user_id', $userId)
            ->get(['provider_documents.status', 'provider_documents.req_document_id']);

        if ($documents->count() < $requiredIds->count()) {
            return response()->json([
                'status' => 0,
                'message' => __('driver_messages.370'),
                'message_code' => 370,
                'is_document_pending' => 1,
            ]);
        }

        if ($documents->contains(fn ($doc) => (int) $doc->status === 0)) {
            return response()->json([
                'status' => 0,
                'message' => __('driver_messages.370'),
                'message_code' => 370,
                'is_document_pending' => 1,
            ]);
        }

        if ($documents->contains(fn ($doc) => (int) $doc->status === 2)) {
            return response()->json([
                'status' => 0,
                'message' => __('driver_messages.368'),
                'message_code' => 370,
                'is_document_pending' => 1,
            ]);
        }

        if ($documents->contains(fn ($doc) => (int) $doc->status === 3)) {
            return response()->json([
                'status' => 0,
                'message' => __('driver_messages.342'),
                'message_code' => 342,
                'is_document_expired' => 1,
            ]);
        }

        return null;
    }
}
