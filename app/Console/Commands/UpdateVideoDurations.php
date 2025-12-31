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
    protected $signature = 'videos:update-durations {--video_id= : Process specific video by ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update video durations for videos that don\'t have duration set';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $videoId = $this->option('video_id');

        if ($videoId) {
            $video = \App\Models\Video::find($videoId);
            if (!$video) {
                $this->error("Video with ID {$videoId} not found.");
                return 1;
            }

            $this->processVideo($video);
        } else {
            $videos = \App\Models\Video::where('duration', 0)->orWhereNull('duration')->get();

            if ($videos->isEmpty()) {
                $this->info('No videos found that need duration processing.');
                return 0;
            }

            $this->info("Found {$videos->count()} videos to process.");

            $bar = $this->output->createProgressBar($videos->count());
            $bar->start();

            foreach ($videos as $video) {
                $this->processVideo($video);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info('Duration processing completed.');
        }

        return 0;
    }

    private function processVideo($video)
    {
        try {
            $filePath = storage_path('app/public/' . $video->video_path);

            if (!file_exists($filePath)) {
                $this->error("Video file not found: {$filePath}");
                return;
            }

            $duration = $this->getVideoDuration($filePath);

            if ($duration > 0) {
                $video->update(['duration' => $duration]);
                $this->info("Updated {$video->title}: " . gmdate('i:s', $duration));
            } else {
                $this->warn("Could not determine duration for {$video->title}");
            }
        } catch (\Exception $e) {
            $this->error("Failed to process {$video->title}: {$e->getMessage()}");
        }
    }

    private function getVideoDuration($filePath)
    {
        try {
            // Try to use FFmpeg if available
            if (class_exists('FFMpeg\FFMpeg')) {
                $ffmpeg = FFMpeg::create();
                $video = $ffmpeg->open($filePath);
                $duration = $video->getFormat()->get('duration');
                return (int) $duration;
            }
        } catch (\Exception $e) {
            // FFmpeg not available or failed
        }

        // Fallback: Try to get duration using getID3 if available
        try {
            if (class_exists('getID3')) {
                $getID3 = new \getID3();
                $fileInfo = $getID3->analyze($filePath);
                if (isset($fileInfo['playtime_seconds'])) {
                    return (int) $fileInfo['playtime_seconds'];
                }
            }
        } catch (\Exception $e) {
            // getID3 not available
        }

        // Last resort: Return 0
        return 0;
    }
}
