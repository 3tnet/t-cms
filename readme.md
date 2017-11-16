<p align="center">
  <img style="max-width:50%" src="https://avatars2.githubusercontent.com/u/15854856?v=3&s=150">
  <img style="max-width:50%" src="https://avatars1.githubusercontent.com/u/28562345?v=3&s=150">
  <br><strong>3t</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>E8net</strong>
</p>
<p align="center">
<a href="https://travis-ci.org/3tnet/t-cms"><img src="https://travis-ci.org/3tnet/t-cms.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/3tnet/t-cms"><img src="https://poser.pugx.org/3tnet/t-cms/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/3tnet/t-cms"><img src="https://poser.pugx.org/3tnet/t-cms/v/stable" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/3tnet/t-cms"><img src="https://poser.pugx.org/3tnet/t-cms/license.svg" alt="License"></a>
</p>

# 此项目已过时 新项目：https://github.com/3tnet/tiny

## About t-cms

t-cms is a simple cms based on laravel.

[完整中文文档](https://github.com/3tnet/t-cms/wiki)

## Install

### 1. Clone the source code or create new project.

```shell
git clone https://github.com/3tnet/t-cms.git
cd t-cms
composer install
```

OR

```shell
composer create-project 3tnet/t-cms
```

### 2. Run the cms install command, the command will run the `migrate` command and generate test data.
```shell
php artisan app:install
```

### 3. Installation frontend
```shell
npm install
// install theme
cd themes/default
npm install
cd ../../
```

### 4. Compile the frontend resources
```shell
npm run dev
// Compile theme resources
npm run dev --theme:default
```

## License

The T-cms is open-sourced software licensed under the [MIT license](https://mit-license.org/).
