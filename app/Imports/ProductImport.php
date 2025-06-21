<?php

namespace App\Imports;

use App\Models\Products;
use App\Models\Media;
use App\Services\ArtistService;
use App\Services\OrientationService;
use App\Services\CategoryService;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;

class ProductImport implements 
    ToModel, 
    WithChunkReading, 
    WithBatchInserts, 
    WithStartRow, 
    WithCalculatedFormulas, 
    WithValidation, 
    SkipsOnFailure
{
    use SkipsFailures;

    public function startRow(): int
    {
        return 2; // Skip header row
    }

    public function model(array $row)
    {
        try {
            $artist_service = resolve(ArtistService::class);
            $orientation_service = resolve(OrientationService::class);
            $category_service = resolve(CategoryService::class);

            $category_list = [];
            for ($i = 6; $i <= 23; $i++) {
                if (!empty($row[$i])) {
                    $category_list[] = $category_service->categoryInsertIfNotExists($row[$i]);
                }
            }

            $artistId = $artist_service->artistInsertIfNotExists($row[4]);
            $orientationId = $orientation_service->orientationInsertIfNotExists($row[5]);

            // Skip this row if invalid artist/orientation ID
            if (!is_numeric($artistId) || !is_numeric($orientationId)) {
                Log::warning('Skipping row due to invalid artist/orientation: ' . json_encode($row));
                return null;
            }

            $existingProduct = Products::where('product_code', $row[1])->first();

            $data = [
                'product_code'          => $row[1] ?? '',
                'name'                 => $row[2] ?? '',
                'image'                => $row[0] ?? '',
                'description'          => $row[3] ?? '',
                'moulding_description' => $row[3] ?? '',
                'artist_id'            => $artistId,
                'orientation'          => $orientationId,
                'category'             => implode(",", $category_list),
                'width'                => $row[24] ?? null,
                'length'               => $row[25] ?? null,
                'depth'                => $row[26] ?? null,
                'wholesale_price'      => $row[27] ?? 0,
                'price'                => $row[28] ?? 0,
                'status'               => 1,
            ];

            if ($existingProduct) {
                $existingProduct->update($data);
                $product = $existingProduct;
            } else {
                $product = Products::create($data);
            }

            // Handle Media insert or update
            $existingMedia = Media::where('media_source_id', $product->id)
                                  ->where('media_type', 3)
                                  ->first();

            if ($existingMedia) {
                $existingMedia->update([
                    'image' => $row[0] ?? '',
                ]);
            } else {
                Media::create([
                    'media_source_id' => $product->id,
                    'image'           => $row[0] ?? '',
                    'media_type'      => 3,
                    'status'          => 1,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Import failed at row: ' . json_encode($row) . ' | Error: ' . $e->getMessage());
            return null;
        }

        return null;
    }

    public function rules(): array
    {
        return [
            '0' => 'nullable|string',         // image path
            '1' => 'nullable|string',         // product_code
            '2' => 'nullable|string',         // name
            '3' => 'nullable|string',         // description
            '4' => 'nullable|string',         // artist
            '5' => 'nullable|string',         // orientation
            '24'=> 'nullable|integer',        // width
            '25'=> 'nullable|integer',        // length
            '26'=> 'nullable|integer',        // depth
            '27'=> 'nullable|numeric',        // wholesale_price
            '28'=> 'nullable|numeric',        // price
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        // Log each failure for debugging
        foreach ($failures as $failure) {
            Log::error('Validation Failure at Row: ' . $failure->row() . ' | Errors: ' . json_encode($failure->errors()));
        }
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
