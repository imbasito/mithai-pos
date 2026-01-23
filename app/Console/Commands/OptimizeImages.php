<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class OptimizeImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'image:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize all existing product images by converting to WebP with 80% quality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $products = Product::whereNotNull('image')->where('image', '!=', '')->get();
        $this->info("Found " . $products->count() . " products to check.");

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        foreach ($products as $product) {
            $oldPath = $product->image;
            $absolutePath = storage_path('app/public/' . $oldPath);

            if (!file_exists($absolutePath) || is_dir($absolutePath)) {
                $bar->advance();
                continue;
            }

            $info = pathinfo($absolutePath);
            
            // Skip if already webp
            if (strtolower($info['extension'] ?? '') === 'webp') {
                $bar->advance();
                continue;
            }

            try {
                $newFileName = $info['filename'] . '.webp';
                $newRelativePath = dirname($oldPath) . '/' . $newFileName;
                $newAbsolutePath = $info['dirname'] . '/' . $newFileName;

                // Create WebP version
                Image::make($absolutePath)
                    ->encode('webp', 80)
                    ->save($newAbsolutePath);

                // Update Database
                $product->image = $newRelativePath;
                $product->save();

                // Delete old file if names are different
                if ($absolutePath !== $newAbsolutePath) {
                    unlink($absolutePath);
                }

            } catch (\Exception $e) {
                $this->error("\nError processing {$oldPath}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->info("\nOptimization complete!");
    }
}
