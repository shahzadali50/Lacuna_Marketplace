<?php

namespace App\Jobs;

use App\Models\Category;
use Illuminate\Bus\Queueable;
use App\Models\CategoryTranslation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Foundation\Bus\Dispatchable; // ✅ required for dispatch()

class TranslateCategory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels; // ✅ added Dispatchable

    public $category;

    /**
     * Create a new job instance.
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $tr = new GoogleTranslate(); // auto-detect source


        foreach (['en', 'pt', 'ja'] as $lang) {
            $tr->setTarget($lang);

            CategoryTranslation::updateOrCreate(
                ['lang' => $lang, 'category_id' => $this->category->id],
                [
                    'name' => $tr->translate($this->category->name),
                    'description' => $tr->translate($this->category->description),
                    'user_id' => $this->category->user_id, 
                ]

            );
        }
    }
}
