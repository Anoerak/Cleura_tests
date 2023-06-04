# Cleura Development Evaluation Test

![GitHub repo size](https://img.shields.io/github/repo-size/Anoerak/Cleura_tests?style=plastic)
![GitHub language count](https://img.shields.io/github/languages/count/Anoerak/Cleura_tests?style=plastic)
![GitHub top language](https://img.shields.io/github/languages/top/Anoerak/Cleura_tests?style=plastic)
![GitHub last commit](https://img.shields.io/github/last-commit/Anoerak/Cleura_tests?color=red&style=plastic)

<h1 align="center">Welcome to Cleura's Development Evaluation Test 👋</h1>
<p>
  <a href="#" target="_blank">
    <img alt="License: MIT" src="https://img.shields.io/badge/License-MIT-yellow.svg" />
  </a>
</p>

> A technical test for the CLEURA company..

![PHP](https://img.shields.io/badge/php-8.1-informational?style=flat-square&logo=appveyor)
![Symfony](https://img.shields.io/badge/symfony-6.3-blue?style=flat-square&logo=appveyor)
![React](https://img.shields.io/badge/reactjs-18.0-blueviolet?style=flat-square&logo=appveyor)
![Webpack-Encore](https://img.shields.io/badge/Webpack_Encore-4.0-brightgreen?style=flat-square&logo=appveyor)

## Install

```sh
Download the project
```

```sh
Update the .env file with your database credentials
```

```sh
Install dependencies with composer install then npm install
```

```sh
Run the command `symfony console doctrine:database:create` to create the database
```

```sh
Run the command `symfony console doctrine:schema:update --force` to create the tables
```

> (If you encounter any issues with the previous command, you can try to run the following command instead: `symfony console doctrine:migrations:migrate`)

```sh
Run the command `symfony console doctrine:fixtures:load` to load the fixtures
```

```sh
Run the command `npm run watch` to build the assets
```

## Usage

```sh
Run the command `symfony server:start` to launch the development server
```

```sh
Follow the link into the console to access the website
```

```sh
You can use the following credentials to log in as an administrator:
email: admin@admin.se
password: Cleur@2023
```

```sh
You can use the following credentials to log in as a user:
email: user@user.se
password: Cleur@2023
```

## Author

👤 **Anørak**

-   Github: [@Anørak](https://github.com/Anørak)

## 🤝 Contributing

Contributions, issues and feature requests are welcome!<br />Feel free to check [issues page](https://github.com/Anoerak/Cleura_tests/issues).

## Show your support

Give a ⭐️ if this project helped you!

---

_This README was generated with ❤️ by [readme-md-generator](https://github.com/kefranabg/readme-md-generator)_

Update Tasks -> New user (anonymous)
-> update table with Migration +
update with command line (both).
