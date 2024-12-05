<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;

class GoogleSheetsService
{
    private $service;

    public function __construct()
    {
        $client = new Client();
        $client->setApplicationName('Laravel Google Sheets Integration');
        $client->setScopes([Sheets::SPREADSHEETS]);
        $client->setAuthConfig(storage_path('app/google/credentials.json'));

        $this->service = new Sheets($client);
    }

    public function createSpreadsheet($title)
    {
        $spreadsheet = new \Google\Service\Sheets\Spreadsheet([
            'properties' => ['title' => $title],
        ]);

        $response = $this->service->spreadsheets->create($spreadsheet);

        return $response;
    }

    public function appendData($spreadsheetId, $range, $values)
    {
        $body = new \Google\Service\Sheets\ValueRange([
            'values' => $values,
        ]);

        $params = ['valueInputOption' => 'RAW'];
        $response = $this->service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);

        return $response;
    }
}
