#!/bin/bash

# Navigate to the Laravel project directory
cd /Users/daypoepoe/Herd/karen_culture_sales

# Start the queue worker with optimal settings
php artisan queue:work --queue=default --tries=5 --timeout=90 --backoff=10 