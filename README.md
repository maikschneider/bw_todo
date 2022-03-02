# TYPO3 Todo Extension

## Install

1. Start project with [ddev](https://ddev.readthedocs.io/), dependencies and
   database become installed automatically:

   ```
   ddev start
   ```

2. Open project. Username: admin, password: changeme

    ```
    ddev launch /typo3
    ```

## Frontend

The frontend is build with [lit](https://lit.dev/). To compile the templates via webpack, install the dependencies:

```
npm install
```

Dev build with file watcher:

```
npm run start
```

Production build:

```
npm run build
```

## Testing

The are some functional tests and code quality testing tools configured.

```
ddev composer run lint
ddev composer run tests

npm run lint
```
