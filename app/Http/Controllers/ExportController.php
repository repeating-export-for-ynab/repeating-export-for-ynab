<?php

namespace App\Http\Controllers;

use App\Exports\RepeatingTransactionExport;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response|BinaryFileResponse
    {
        $accessToken = $request->cookie('ynab_access_token');

        if ($accessToken) {
            $accessToken = decrypt($accessToken);
        } else {
            return response('No access token', 404);
        }

        $fileExtension = $request->input('file_extension', 'csv');

        $response = Http::withToken($accessToken)->get("https://api.ynab.com/v1/budgets?include_accounts=true");

        $budgetId = data_get($response->json(), 'data.budgets.0.id', null);

        if (!$budgetId) {

            $error = data_get($response->json(), 'error', null);

            if ($error) {
                $errorId = data_get($error, 'id', null);
                $errorName = data_get($error, 'name', null);
                $errorDetail = data_get($error, 'detail', null);

                $errorString = "YNAB Error $errorId: $errorName - $errorDetail";

                return response($errorString, 404);
            }

            return response('Could not find budget', 404);
        }

        $response = Http::withToken($accessToken)->get("https://api.ynab.com/v1/budgets/$budgetId/scheduled_transactions");

        $scheduledTransactions = data_get($response->json(), 'data.scheduled_transactions', collect());

        if (!$scheduledTransactions instanceof Collection) {
            $scheduledTransactions = collect($scheduledTransactions);
        }

        $todaysDateFileFriendlyName = now()->format('Y-m-d');

        if ($fileExtension === 'csv') {
            $writerType = Excel::CSV;
        } else if ($fileExtension === 'excel') {
            $writerType = Excel::XLSX;
        } else {
            $writerType = Excel::CSV;
        }

        if ($fileExtension === 'csv') {
            $fileStringExtension = 'csv';
        } else if ($fileExtension === 'excel') {
            $fileStringExtension = 'xlsx';
        } else {
            $fileStringExtension = 'csv';
        }

        return (new RepeatingTransactionExport($scheduledTransactions))->download("$todaysDateFileFriendlyName-ynab-repeating-transactions.$fileStringExtension", $writerType);
    }
}
