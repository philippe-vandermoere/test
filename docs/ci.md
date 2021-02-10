# Continuous Integration

## Workflow CI

### Trigger

- On evey commits on the main branch.
- On every pull requests.

### Jobs

- [phpcs](#PHP Code Sniffer)
- [phpstan](#PHP Static Analysis Tool)
- [phpunit](#PHP Unit Test)
- [infection](#PHP Mutation Testing)

#### PHP Code Sniffer

- Install vendor.
- Lint PHP files with [PHP Code Sniffer](https://github.com/squizlabs/PHP_CodeSniffer).
- Annotate Pull Request with lint error.

#### PHP Static Analysis Tool

- Install vendor.
- Analyse PHP files with [PHP Stan](https://github.com/phpstan/phpstan).
- Annotate Pull Request with analysis error.

#### PHP Unit Test

- Install vendor.
- Run the unit tests with [PHP Unit](https://github.com/sebastianbergmann/phpunit).
- Annotate Pull Request with failed test.
- Upload artifact junit report
- Upload artifact code coverage report

#### PHP Mutation Testing

- Install vendor.
- Run mutation testing with  [infection](https://github.com/infection/infection).
- Annotate Pull Request with failed test.
- Upload artifact infection report

## Workflow Build

### Trigger

- On evey commits on the main branch.
- On every tag.
- On every pull requests.

### Jobs

- [build_docker](#Build Docker)
- [test_deploy_kubernetes](#Test Deploy Kubernetes)

#### Build Docker

- Lint Dockerfile with [hadolint](https://github.com/hadolint/hadolint).
- Annotate Pull Request with lint error.
- Build Docker image.

#### Test Deploy Kubernetes

- Push Docker image to local registry.
- Start a Kubernetes with [Minikube](https://minikube.sigs.k8s.io/docs/).
- Deploy application on Kubernetes Minikube cluster with [Terraform](https://www.terraform.io/docs).

## Workflow Security

### Trigger

- On the main branch every day at 6 am.
- On every pull requests.

### Jobs

- [symfony_security_checker](#Symfony Security Checker)

#### Symfony Security Checker

- Run [Symfony security-checker](https://security.symfony.com/).
