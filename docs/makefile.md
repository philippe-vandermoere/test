# Makefile

## Application

### Installation

| Command               | Description                                         |
|---                    |---                                                  |
| make install          | Install the application (Run vendor and migrate).   |
| make vendor           | Install PHP vendor.                                 |
| make migrate          | Run all migrate commands.                           |
| make migrate_mysql    | Run MySql migration.                                |
| make migrate_rabbitmq | Create and configure RabbitMq queues and exchanges. |

### Tests

| Command              | Description                            |
|---                   |---                                     |
| make tests           | Run all tests commands.                |
| make phpcs           | Run PHP Code Sniffer tests.            |
| make phpstan         | Run PHP Stan tests.                    |
| make phpunit         | Run all unit tests with code coverage. |
| make infection       | Run mutation testing on PHP unit test. |

## Docker

### Stack

| Command        | Description                                                                                                   |
|---             |---                                                                                                            |
| make configure | Configure the stack [(see configuration)](../README.md#Start).                                                |
| make start     | Configure the stack if necessary, build Docker images, start Docker stack and install application dependency. |
| make stop      | Stop Docker's stack containers.                                                                               |
| make restart   | Stop and start Docker's stack containers.                                                                     |
| make ps        | View the status of Docker's stack containers.                                                                 |
| make remove    | Stop Docker's stack containers and remove containers, volumes and networks.                                   |

### CLI

| Command        | Description                                        |
|---             |---                                                 |
| make shell     | Connect to the terminal in PHP docker's container. |
| make mysql_cli | Connect to the MySql cli.                          |
| make redis_cli | Connect to the Redis cli.                          |

### Logs

| Command            | Description                                                         |
|---                 |---                                                                  |
| make logs          | Follow the last 50 lines of containers logs.                        |
| make logs_nginx    | Follow the last 50 lines of Nginx containers logs.                  |
| make logs_php      | Follow the last 50 lines of PHP-FPM containers logs.                |
| make logs_consumer | Follow the last 50 lines of PHP Messenger consumer containers logs. |
| make logs_mysql    | Follow the last 50 lines of MySql container logs.                   |
| make logs_rabbitmq | Follow the last 50 lines of RabbitMq container logs.                |
| make logs_redis    | Follow the last 50 lines of Redis container logs.                   |

## Kubernetes


