# Version Control

Version Control is an application that allows to manage the source files of a project in the development server / machine, keep the application updated and update the files in a remote location.

This application was built using [CakePHP](http://cakephp.org) 3.x.

The framework source code can be found here: [cakephp/cakephp](https://github.com/cakephp/cakephp).

## CONFIGURATION

## 1. Cake PHP Installation

1. Download [Composer](http://getcomposer.org/doc/00-intro.md) or update `composer self-update`.
2. Run `php composer.phar create-project --prefer-dist cakephp/app [app_name]`.

If Composer is installed globally, run
```bash
composer create-project --prefer-dist cakephp/app [app_name]
```

You should now be able to visit the path to where you installed the app and see the default home page.

## 2. Download application files into your server

## 3. Database Configuration

Read and edit `config/app.php` and setup the 'Datasources' and any other
configuration relevant for your application.

## 4. Build Database

After a successful app instalation and after seting up the database information in the app.php file, you need to build the application's database. You can do that by simply accessing the application Database index page and clicking the "CREATE DB" button. Link to the page is "yourserver/versioncontrolAppName/Database/index"

## 5. Run app at "yourserver/versioncontrolAppName/projects" !

## USAGE

## 1. Add new Project

Add a new project record with a meaningful name and the path to the source files. You can optionally set up the FTP info for a remote location to push the files. While creating project you can choose to send the files directly to the remote location or to download the files in a compressed generated file.

## 2. Keep track of changes

In the project's action view page you can check which files were changed, commit them to your local repository and push them to the remote server. 

## FEATURES TO COME

These features will be added in next versions by the following order of importance
1. User specific information (keeping track of who does what / permissions)
2. Versioning / Version files pull
3. Multi user support
4. File changes conflict manager
5. Branching
6. Local intermediate repository

## CONTRIBUTORS

@author     João Santos

@consultant Tiago Gomes

@consultant Valter Rodrigues
