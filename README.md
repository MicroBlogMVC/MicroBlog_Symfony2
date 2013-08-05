## Symfony 2.3.1 MicroBlog


### Installation

##### Clone this repo

    git clone https://github.com/steven-/SymfonyMvc MicroBlogSymfony2
    cd MicroBlogSymfony2

##### Install dependencies

    php composer.phar install

##### Create and fill the database (MySQL)

    php app/console doctrine:database:create
    php app/console doctrine:schema:create
    php app/console doctrine:fixture:load

##### Make some directories writable

    chmod -R o+w app/cache app/logs web/avatars



### Enjoy

At last you can browse to the /web directory to see the app.
All users have the same password : "pass"