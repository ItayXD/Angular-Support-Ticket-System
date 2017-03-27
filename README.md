# Angular-Support-Ticket-System
##Security
1. Avoiding SQLi by using PDO with prepare function.
1. Angular always escape HTML, and when HTML isn't escaped ng-sanitize is used.
##Style
Bootstrap is used exclusively.
##Notes - Important
* Using MariaDB for SQL database.
* Database includes triggers and custom settings, so make sure to checkout the included `test.sql`.
* For links to work as intended you have to set redirects to your `index.html` page on the server-side as described [here](https://github.com/angular-ui/ui-router/wiki/Frequently-Asked-Questions#how-to-configure-your-server-to-work-with-html5mode) .
* Please note that this app is meant to run on `example.domain/angular/`
if you want to run it on the root of your website make sure to make the necessary adjustments.