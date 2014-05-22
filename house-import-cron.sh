#!/bin/bash

php=`which php`

$php artisan db:backup
$php artisan doc:download http://www.gpo.gov/fdsys/bulkdata/BILLS/113/2/hr/BILLS-113-2-hr.zip --bulk
$php artisan doc:import app/storage/docs/xml/ --all