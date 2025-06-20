<?php
namespace App\Imports;

use App\Models\Products;
use App\Services\ArtistService;

use Maatwebsite\Excel\Concerns\ToModel;
//use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithStartRow;

use Illuminate\Support\Facades\Log;

class ProductImport implements ToModel, WithChunkReading, WithBatchInserts, WithStartRow
{
	public function startRow(): int
    {
        return 2; // Skip the first row
    }
    public function model(array $row)
    {
		// Log::info('Import Row:', $row);
		// Log::info('Import value:', $row[1]);
		
		$artist_service = resolve(ArtistService::class);
		
        return new Products([
            'product_code'        => $row[1],
            'name'         => $row[2],
            'image'       => $row[0],
            'description'       => $row[3],
            'moulding_description'       => $row[3],
            'artist_id'       => $artist_service->artistInsertIfNotExists($row[4]),
            'status'       => 1,
        ]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }
}
