<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class MigrateAuditLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:audit-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data from legacy audit_logs table to Spatie activity_log table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if legacy table exists
        if (!DB::getSchemaBuilder()->hasTable('audit_logs')) {
            $this->error('The legacy audit_logs table does not exist.');
            return;
        }

        $legacyLogs = DB::table('audit_logs')->get();

        if ($legacyLogs->isEmpty()) {
            $this->info('No legacy logs found to migrate.');
            return;
        }

        $this->info("Found {$legacyLogs->count()} legacy logs. Starting migration...");

        $bar = $this->output->createProgressBar(count($legacyLogs));
        $bar->start();

        foreach ($legacyLogs as $log) {
            $properties = [];
            
            if ($log->old_values) {
                $properties['old'] = json_decode($log->old_values, true) ?? [];
            }
            if ($log->new_values) {
                $properties['attributes'] = json_decode($log->new_values, true) ?? [];
            }

            // Fallback for action text
            $event = $log->action;
            $description = $log->action;

            Activity::create([
                'log_name' => 'default',
                'description' => $description,
                'event' => $event,
                'subject_type' => $log->model_type,
                'subject_id' => $log->model_id,
                'causer_type' => $log->user_id ? 'App\Models\User' : null,
                'causer_id' => $log->user_id,
                'properties' => $properties,
                'created_at' => $log->created_at,
                'updated_at' => $log->updated_at,
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Migration completed successfully!');
        $this->warn('Note: You can now safely drop the old audit_logs table if you no longer need it.');
    }
}
