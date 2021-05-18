## APP Setup

1. Setup PHP, Composer, MYSQL, PHP GD extension, Imagick php extension
    * MYSQL 8
    * PHP 7.4
    * Composer 2
1. Setup MYSQL database
1. Create `.env.local` on root folder check `.env.example` for reference
1. Setup DATABASE Configuration on `.env.local` located on root folder
1. Go to root directory and run the ff:
    * `composer install`
    * `php artisan migrate`
1. to run API - `php artisan serve`
1. Make sure to have the right permissions specially on the API when running it via `php artisan serve` or NGINX

I use MailHog for SMTP Server, to learn more about installation and running it visit https://github.com/mailhog/MailHog

If you have more questions kindly email me @ ziegfrid.gualberto@gmail.com
