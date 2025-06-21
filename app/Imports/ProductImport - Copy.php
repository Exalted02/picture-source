<?php
namespace App\Imports;

use App\Models\Products;
use App\Models\Media;
use App\Services\ArtistService;
use App\Services\OrientationService;
use App\Services\CategoryService;

use Maatwebsite\Excel\Concerns\ToModel;
//use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

use Illuminate\Support\Facades\Log;

class ProductImport implements ToModel, WithChunkReading, WithBatchInserts, WithStartRow, WithCalculatedFormulas
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
		$orientation_service = resolve(OrientationService::class);
		$category_service = resolve(CategoryService::class);

		$category_list = [];
		for ($i = 6; $i <= 23; $i++) {
			if ($row[$i] != null) {
				$category_list[] = $category_service->categoryInsertIfNotExists($row[$i]);
			}
		}

		// Check if product exists by product_code
		$existingProduct = Products::where('product_code', $row[1])->first();

		$data = [
			'product_code'        => $row[1],
			'name'               => $row[2],
			'image'              => $row[0],
			'description'        => $row[3],
			'moulding_description'=> $row[3],
			'artist_id'          => $artist_service->artistInsertIfNotExists($row[4]),
			'orientation'        => $orientation_service->orientationInsertIfNotExists($row[5]),
			'category'           => implode(",", $category_list),
			'width'              => $row[24],
			'length'             => $row[25],
			'depth'              => $row[26],
			'price'              => $row[28],
			'wholesale_price'    => $row[27],
			'status'             => 1,
		];

		if ($existingProduct) {
			// Update existing product
			$existingProduct->update($data);
			$product = $existingProduct; // Return null to prevent insert
		} else {
			// Insert new product
			$product = Products::create($data);
		}
		
		$existingMedia = Media::where('media_source_id', $product->id)->first();

		if ($existingMedia) {
			$existingMedia->update([
				'image' => $row[0], // new image path from Excel
			]);
		} else {
			Media::create([
				'media_source_id' => $product->id,
				'image'  => $row[0], // image path
				'media_type'  => 3, // product
				'status'  => 1,
			]);
		}
		
		return null;
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
