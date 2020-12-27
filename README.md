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

## Running the program through Docker
1) Copy the CSV file to the root directory (The file will then be copied to the Docker container)
2) `docker-compose run garmin ./bin/console garmin:workout <file.csv>`

## Usage

Specify a CSV file to create and delete workouts in Garmin connect and schedule them on the Garmin calendar.

## Examples

```shell

# Basic example of importing workouts into Garmin
bin/console garmin:workout <file.csv> -m <garmin_email> -p <garmin_password>

# Import AND Schedule workouts into Garmin on January 1, 2020. End date is assumed based upon plan length.
# End date can be specified with the -d flag. The same can be assumed with the start date.
bin/console garmin:workout <file.csv> schedule -m <garmin_email> -p <garmin_password> -s '2020-01-01'

# Same as above but delete all previous items first, import items and schedule them, and prefix with HANSON: before all workouts
bin/console garmin:workout <file.csv> schedule -m <garmin_email> -p <garmin_password> -s '2020-01-01' -x -r 'HANSON:'

# Only delete the previous workouts (notice capital "x")
bin/console garmin:workout <file.csv> schedule -m <garmin_email> -p <garmin_password> -s '2020-01-01' -X -r 'HANSON:'

# Do a mock run of importing workouts like above
bin/console garmin:workout <file.csv> schedule -m <garmin_email> -p <garmin_password> -s '2020-01-01' --dry-run
```

## Arguments

`<file.csv>` - The specified CSV file that you want to import into Garmin connect.

`import|schedule` - Specify import or schedule to either just import the workouts into Garmin connect 
or import **AND** schedule the workouts.

## Options

`--email`, `-m` - email that you use to login to Garmin

`--password`, `-p` - password that you use to login to Garmin

`--delete`, `-x` - delete previous workouts that are in the plan from Garmin

`--delete-only`, `-X` - ONLY delete previous workouts that are in the plan from Garmin

`--dry-run` - List out workouts found in the plan and display them

`--prefix`, `-r` - Prefix all workouts with text

`--start`, `-s` - Specify the start date of when you want scheduled workouts to be planned on your calendar in Garmin

`--end`, `-d` - Specify the end date of when you want scheduled workouts to be planned on your calendar in Garmin

## Developing and running the program through Docker
1) Install dependencies - `docker-compose run composer install`
   
    a) Adding dependencies or removing dependencies can be done through `docker-compose run composer require <package>`
    
    b) Updating dependencies csn be done through `docker-compose run composer update`

2)  Run the docker install by running `docker-compose up garmin-php` (This runs the docker container and keeps it up)
3) Execute a command by running `docker-compose exec garmin-php bin/console garmin:workout ...`

## Overriding Docker

Create a **new** file in the root called `docker-compose.override.yaml`.

```yaml
version: '3.3'

services:
  garmin-dev:
    environment:
      XDEBUG_CONFIG: "client_host=<local_IP>"
```
### Debugging the application through PhpStorm
1) If running linux, then you will need to modify the `docker-compose.yaml` file and add your IP in place of `host.docker.internal`
2) Go to PhpStorm -> Settings -> Languages & Frameworks -> PHP -> Servers
   
   a) Click "+"
   
   b) Name docker-cli (Same as serverName under PHP_IDE_CONFIG environment variable)
   
   c) Host _
   
   d) Default 80
   
   e) Debugger Xdebug
   
   f) Check the checkbox next to "Use path mappings"
   
   g) Modify the absolute path on the server to /var/www/html
