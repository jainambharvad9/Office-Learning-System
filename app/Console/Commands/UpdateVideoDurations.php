<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateVideoDurations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videos:update-durations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update video durations for videos that have duration set to 0';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $videos = \App\Models\Video::where('duration', 0)->get();

        if ($videos->isEmpty()) {
            $this->info('All videos have valid durations!');
            return;
        }

        $this->info('Found ' . $videos->count() . ' videos with duration 0:');
        $this->newLine();

        foreach ($videos as $video) {
            $this->line('ID: ' . $video->id);
            $this->line('Title: ' . $video->title);
            $this->line('Path: ' . storage_path('app/public/' . $video->path));
            $this->line('To update manually, use:');
            $this->line('php artisan tinker');
            $this->line('$video = App\Models\Video::find(' . $video->id . ');');
            $this->line('$video->update([\'duration\' => YOUR_DURATION_IN_SECONDS]);');
            $this->newLine();
            $this->line('---');
        }

        $this->warn('Note: Since FFmpeg is not available, durations must be set manually.');
        $this->info('Future video uploads will automatically extract duration using JavaScript.');
    }
}
