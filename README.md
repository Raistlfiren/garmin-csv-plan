# Garmin Plan
This is a PHP implementation of the great program that mgifos created [here](https://github.com/mgifos/quick-plan). 
The intentions of the program is to parse a CSV file and create workouts in Garmin Connect.
It also has the ability to:
 - Schedule workouts on Garmin Connect calendar based upon a start or end date
 - Delete workouts that are made based upon the CSV file
 - Add notes to workout steps
 - Prefix workouts with some text ex.: Convert `14k, 4x 1.6k @TMP` TO `HANSON:14k, 4x 1.6k @TMP`

## Setting up the application to develop or run
1) Download and install [PHP](https://www.php.net/) and [composer](https://getcomposer.org/)
2) Run `composer install`
3) Put your username and password in `.env` file
4) Run the application by `./bin/console garmin:workout`