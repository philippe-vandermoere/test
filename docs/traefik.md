# Traefik

![Traefik](https://doc.traefik.io/traefik/assets/img/traefik-architecture.png)

## Example OVH Let's encrypt DNS Challenge

[Official documentation](https://doc.traefik.io/traefik/user-guides/docker-compose/acme-dns/).

Create a new folder `traefik`

Create a file `docker-compose.yaml` in the folder `traefik`

```yaml
version: '3.8'
services:
    traefik:
        image: traefik:v2.3
        restart: always
        ports:
            - 80:80
            - 443:443
        labels:
            - "traefik.enable=true"
            - "traefik.http.routers.traefik.rule=Host(`traefik.${DOMAIN_NAME}`)"
            - "traefik.http.routers.traefik.service=api@internal"
        environment:
            TRAEFIK_LOG_LEVEL: ${LOG_LEVEL}
            TRAEFIK_API: 'true'
            TRAEFIK_API_DASHBOARD: 'true'
            TRAEFIK_ACCESSLOG: 'true'
            # entrypoint http
            TRAEFIK_ENTRYPOINTS_WEB_ADDRESS: :80
            TRAEFIK_ENTRYPOINTS_WEB_HTTP_REDIRECTIONS_ENTRYPOINT_TO: websecure
            TRAEFIK_ENTRYPOINTS_WEB_HTTP_REDIRECTIONS_ENTRYPOINT_SCHEME: https
            TRAEFIK_ENTRYPOINTS_WEB_HTTP_REDIRECTIONS_ENTRYPOINT_PERMANENT: 'true'
            # entrypoint https
            TRAEFIK_ENTRYPOINTS_WEBSECURE_ADDRESS: :443
            TRAEFIK_ENTRYPOINTS_WEBSECURE_HTTP_TLS_CERTRESOLVER: ovh
            TRAEFIK_ENTRYPOINTS_WEBSECURE_HTTP_TLS_DOMAINS[0]_MAIN: ${DOMAIN_NAME}
            TRAEFIK_ENTRYPOINTS_WEBSECURE_HTTP_TLS_DOMAINS[0]_SANS: "*.${DOMAIN_NAME}"
            # provider docker
            TRAEFIK_PROVIDERS_DOCKER: 'true'
            TRAEFIK_PROVIDERS_DOCKER_EXPOSEDBYDEFAULT: 'false'
            TRAEFIK_PROVIDERS_DOCKER_NETWORK: traefik
            # letsencrypt OVH DNS01
            TRAEFIK_CERTIFICATESRESOLVERS_OVH_ACME_EMAIL: ${ACME_EMAIL}
            TRAEFIK_CERTIFICATESRESOLVERS_OVH_ACME_DNSCHALLENGE: 'true'
            TRAEFIK_CERTIFICATESRESOLVERS_OVH_ACME_CASERVER: https://acme-v02.api.letsencrypt.org/directory
            TRAEFIK_CERTIFICATESRESOLVERS_OVH_ACME_STORAGE: /letsencrypt/acme.json
            TRAEFIK_CERTIFICATESRESOLVERS_OVH_ACME_DNSCHALLENGE_PROVIDER: ovh
            TRAEFIK_CERTIFICATESRESOLVERS_OVH_ACME_DNSCHALLENGE_RESOLVERS: ${OVH_DNS_SERVERS}
            # OVH api
            OVH_ENDPOINT: ${OVH_ENDPOINT}
            OVH_APPLICATION_KEY: ${OVH_APPLICATION_KEY}
            OVH_APPLICATION_SECRET: ${OVH_APPLICATION_SECRET}
            OVH_CONSUMER_KEY: ${OVH_CONSUMER_KEY}
        volumes:
            - /var/run/docker.sock:/var/run/docker.sock:ro
            - letsencrypt:/letsencrypt
        networks:
            - traefik

volumes:
    letsencrypt:

networks:
    traefik:
        name: traefik
```

Create a file `.env` in the folder `traefik`

```ini
LOG_LEVEL=[YOUR_OWN_VALUE]
DOMAIN_NAME=[YOUR_OWN_VALUE]
ACME_EMAIL=[YOUR_OWN_VALUE]
OVH_DNS_SERVERS=[ns1,ns2]
OVH_ENDPOINT=[YOUR_OWN_VALUE]
OVH_APPLICATION_KEY=[YOUR_OWN_VALUE]
OVH_APPLICATION_SECRET=[YOUR_OWN_VALUE]
OVH_CONSUMER_KEY=[YOUR_OWN_VALUE]
```

```bash
docker-compose up --detach
```
