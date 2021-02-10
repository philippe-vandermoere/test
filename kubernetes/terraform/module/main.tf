locals {
  deployment = {
    name       = var.deployment.name
    namespace  = (var.deployment.namespace != "") ? var.deployment.namespace : var.application.name
    repository = "https://philippe-vandermoere.github.io/helm-charts"
    chart      = "symfony"
    version    = var.deployment.version
    timeout    = 120

    config = {
      version                         = var.application.version
      "image.repository"              = var.application.docker_image
      "image.tag"                     = var.application.docker_image_tag
      "dependencies.services[0].host" = local.mysql.host
      "dependencies.services[0].port" = local.mysql.port
      "dependencies.services[1].host" = local.rabbitmq.host
      "dependencies.services[1].port" = local.rabbitmq.port
      "dependencies.services[2].host" = local.redis.host
      "dependencies.services[2].port" = local.redis.port
    }
  }

  environment_variables = {
    "env.DATABASE_URL" = format(
      "mysql://%s:%s@%s:%s/%s",
      local.mysql.user,
      local.mysql.password,
      local.mysql.host,
      local.mysql.port,
      local.mysql.database
    )

    "env.MESSENGER_TRANSPORT_DSN" = format(
      "amqp://%s:%s@%s:%s/%s/messages",
      local.rabbitmq.user,
      local.rabbitmq.password,
      local.rabbitmq.host,
      local.rabbitmq.port,
      urlencode(local.rabbitmq.vhost)
    )

    "env.REDIS_DSN" = format(
      "redis://%s@%s:%s",
      local.redis.password,
      local.redis.host,
      local.redis.port
    )
  }
}

resource "kubernetes_namespace" "this" {
  for_each = toset(compact([local.deployment.namespace, local.deployment_mysql.namespace]))

  metadata {
    name = each.value
  }
}

resource "helm_release" "symfony" {
  name       = local.deployment.name
  namespace  = local.deployment.namespace
  repository = local.deployment.repository
  chart      = local.deployment.chart
  version    = local.deployment.version
  timeout    = local.deployment.timeout
  values     = [file(format("%s/symfony.yaml", path.module))]

  dynamic "set" {
    for_each = var.deployment_config

    content {
      name  = set.key
      value = set.value
    }
  }

  dynamic "set" {
    for_each = local.deployment.config

    content {
      name  = set.key
      value = set.value
    }
  }

  dynamic "set_sensitive" {
    for_each = var.environment_variables

    content {
      name  = set_sensitive.key
      value = set_sensitive.value
    }
  }

  dynamic "set_sensitive" {
    for_each = local.environment_variables

    content {
      name  = set_sensitive.key
      value = set_sensitive.value
    }
  }

  depends_on = [
    kubernetes_namespace.this,
    helm_release.mysql,
    helm_release.rabbitmq,
    helm_release.redis,
  ]
}
