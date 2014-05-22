#!/bin/bash

PHP=`which php`
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

$PHP $DIR/artisan db:backup
$PHP $DIR/artisan doc:download http://www.gpo.gov/fdsys/bulkdata/BILLS/113/2/hr/BILLS-113-2-hr.zip --bulk
$PHP $DIR/artisan doc:import $DIR/app/storage/docs/xml/ --all