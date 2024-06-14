<?php
define("TOKEN", "secret-token");
define("REMOTE_REPOSITORY", "git@github.com:codemonkey2704/EF-New.git");
define("DIR", "/var/www/http");
define("BRANCH", "refs/heads/master");
define("LOGFILE", "deploy.log");
define("GIT", "/usr/bin/git");
define("MAX_EXECUTION_TIME", 180);
define("BEFORE_PULL", "/usr/bin/git reset --hard @{u}");
define("AFTER_PULL", "/usr/bin/node ./node_modules/gulp/bin/gulp.js default");
require_once("deploy.php");