Local scripts
=============

Robots
------

### Example crontab

    MAILTO=your@email
    ATK14_ENV=production

    # Regular robots are executed every 5 minutes
    */5 * * * * /path/to/application/local_scripts/robots_regular

    # Daily robots are executed every day at 1:11
    11 1 * * * /path/to/application/local_scripts/robots_daily

    # Weekly robots are executed every Sunday at 3:22
    22 3 * * 0 /path/to/application/local_scripts/robots_weekly
