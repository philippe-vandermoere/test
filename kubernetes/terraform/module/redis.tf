locals {
  deployment_redis = {
    enabled    = var.deployment_redis.enabled
    name       = var.deployment_redis.name
    namespace  = (var.deployment_redis.namespace != "") ? var.deployment_redis.namespace : var.application.name
    repository = "https://charts.bitnami.com/bitnami"
    chart      = "redis"
    version    = var.deployment_redis.version
    timeout    = 60
  }

  redis = {
    host     = var.deployment_redis.enabled ? format("redis-master.%s.svc.cluster.local", local.deployment_redis.namespace): var.redis.host
    port     = var.redis.port
    password = (var.redis.password != "") ? var.redis.password : random_password.redis.result
  }
}

resource "random_password" "redis" {
  length  = 32
  special = false
}

resource "helm_release" "redis" {
  count = local.deployment_redis.enabled ? 1 : 0

  name       = local.deployment_redis.name
  namespace  = local.deployment_redis.namespace
  repository = local.deployment_redis.repository
  chart      = local.deployment_redis.chart
  version    = local.deployment_redis.version
  timeout    = local.deployment_redis.timeout

  dynamic "set" {
    for_each = {
      "cluster.enabled"            = false
      "master.service.port"        = var.redis.port
      "master.persistence.enabled" = false
      "slave.service.port"         = var.redis.port
      "slave.persistence.enabled"  = false
    }

    content {
      name  = set.key
      value = set.value
    }
  }

  dynamic "set_sensitive" {
    for_each = {
      "password" = local.redis.password
    }

    content {
      name  = set_sensitive.key
      value = set_sensitive.value
    }
  }

  depends_on = [kubernetes_namespace.this]
}
