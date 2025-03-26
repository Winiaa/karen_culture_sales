<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class SecurityAudit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:audit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a security audit on the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Karen Culture Sales Security Audit...');
        $this->newLine();
        
        $issues = 0;
        $warnings = 0;
        $passes = 0;
        
        // Check debug mode
        $this->info('Checking app debug mode...');
        if (config('app.debug') === true) {
            $this->error('✗ App debug mode is ON. This should be turned off in production.');
            $issues++;
        } else {
            $this->info('✓ App debug mode is OFF.');
            $passes++;
        }
        $this->newLine();
        
        // Check environment
        $this->info('Checking environment...');
        if (app()->environment('local', 'testing', 'staging')) {
            $this->warn('⚠ App is in ' . app()->environment() . ' environment. Production is recommended for live sites.');
            $warnings++;
        } else if (app()->environment('production')) {
            $this->info('✓ App is in production environment.');
            $passes++;
        } else {
            $this->warn('⚠ App is in unknown environment: ' . app()->environment());
            $warnings++;
        }
        $this->newLine();
        
        // Check session configuration
        $this->info('Checking session security...');
        if (config('session.secure') !== true) {
            $this->warn('⚠ Session cookies are not set to secure-only. Enable SESSION_SECURE_COOKIE=true for HTTPS sites.');
            $warnings++;
        } else {
            $this->info('✓ Session cookies are secure-only.');
            $passes++;
        }
        
        if (config('session.http_only') !== true) {
            $this->error('✗ Session cookies are not set to HTTP-only. This is a security risk.');
            $issues++;
        } else {
            $this->info('✓ Session cookies are HTTP-only.');
            $passes++;
        }
        
        if (config('session.same_site') === 'none') {
            $this->warn('⚠ Session same_site is set to "none". Consider using "lax" or "strict".');
            $warnings++;
        } else {
            $this->info('✓ Session same_site is set to "' . config('session.same_site') . '".');
            $passes++;
        }
        $this->newLine();
        
        // Check storage permissions
        $this->info('Checking storage permissions...');
        $storagePath = storage_path();
        if (is_writable($storagePath)) {
            $this->info('✓ Storage directory is writable.');
            $passes++;
        } else {
            $this->error('✗ Storage directory is not writable. Check permissions.');
            $issues++;
        }
        $this->newLine();
        
        // Check MySQL version if using MySQL
        if (DB::connection()->getDriverName() === 'mysql') {
            $this->info('Checking MySQL version...');
            try {
                $mysqlVersion = DB::select('SELECT VERSION() as version')[0]->version;
                $this->info('Current MySQL version: ' . $mysqlVersion);
                
                $versionParts = explode('.', $mysqlVersion);
                $majorVersion = (int) $versionParts[0];
                
                if ($majorVersion < 5 || ($majorVersion == 5 && (int) $versionParts[1] < 7)) {
                    $this->error('✗ MySQL version ' . $mysqlVersion . ' is outdated. Consider upgrading to at least 5.7.');
                    $issues++;
                } else {
                    $this->info('✓ MySQL version is acceptable.');
                    $passes++;
                }
            } catch (\Exception $e) {
                $this->error('✗ Could not check MySQL version: ' . $e->getMessage());
                $issues++;
            }
            $this->newLine();
        }
        
        // Final summary
        $this->newLine();
        $this->info('Security Audit Complete!');
        $this->info('Passes: ' . $passes);
        $this->warn('Warnings: ' . $warnings);
        $this->error('Issues: ' . $issues);
        
        return $issues > 0 ? Command::FAILURE : Command::SUCCESS;
    }
} 