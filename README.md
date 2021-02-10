# Test

@todo

## Installation with Docker

### Requirements

- [Docker](https://docs.docker.com/install/#supported-platforms) >= 19.03.0
- [Docker compose](https://docs.docker.com/compose/install) >= 1.26.0
- [Traefik](https://doc.traefik.io/traefik/) >= v2.3.0

### Start

To Start the application in Docker's stack, execute:

```bash
make start
```

This command does:
- Build and run Docker's images.
- Install application requirements.
- Install or upgrade database.
- Configure RabbitMQ exchanges and queues.

Before the application start, the value of the following parameters will be asked if necessary:

| Name                         | Description                          | Default value       |
|---                           |---                                   |---                  |
| TIMEZONE                     | Define timezone (PHP + MySql).       | Europe/Paris        |
| MYSQL_ROOT_PASSWORD          | Define MySql root password.          | root                |
| MYSQL_DATABASE               | Define MySql project database name.  | test |
| MYSQL_USER                   | Define MySql project user name.      | test |
| MYSQL_PASSWORD               | Define MySql project user password.  | test |
| AMQP_USER                    | Define RabbitMQ user name.           | test |
| AMQP_PASSWORD                | Define RabbitMQ user password.       | test |
| AMQP_VHOST                   | Define RabbitMQ virtual host.        | /                   |
|                              |                                      |                     |
| TRAEFIK_NETWORK              | Define Traefik docker network.       | traefik             |

### URL

To use these URL, [Traefik](./docs/traefik.md) must be running:

- Your application is reachable at `https://test.philou.dev`
- Adminer is reachable at `https://test.philou.dev/adminer`
- RabbitMq admin management is reachable at `https://test.philou.dev/rabbitmq`
- MailHog is reachable at `https://test.philou.dev/mailhog`

## Learn More

- [Configure Traefik with Docker provider](./docs/traefik.md)
- [Coding Convention and best practice](./docs/convention.md)
- [Using Makefile](./docs/makefile.md)
- [Understand the Continuous Integration](./docs/ci.md)
- [Deploy your application in Kubernetes cluster](./docs/kubernetes.md)
