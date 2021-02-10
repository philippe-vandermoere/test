locals {
  deployment_rabbitmq = {
    enabled    = var.deployment_rabbitmq.enabled
    name       = var.deployment_rabbitmq.name
    namespace  = (var.deployment_rabbitmq.namespace != "") ? var.deployment_rabbitmq.namespace : var.application.name
    repository = "https://charts.bitnami.com/bitnami"
    chart      = "rabbitmq"
    version    = var.deployment_rabbitmq.version
    timeout    = 60
  }

  rabbitmq = {
    host     = var.deployment_rabbitmq.enabled ? format("rabbitmq.%s.svc.cluster.local", local.deployment_rabbitmq.namespace): var.rabbitmq.host
    port     = var.rabbitmq.port
    user     = (var.rabbitmq.user != "") ? var.rabbitmq.user : var.application.name
    password = (var.rabbitmq.password != "") ? var.rabbitmq.password : random_password.rabbitmq.result
    vhost = var.rabbitmq.vhost
  }
}

resource "random_password" "rabbitmq" {
  length  = 32
  special = false
}

resource "random_password" "rabbitmq_erlang_cookie" {
  length  = 32
  special = false
}

resource "helm_release" "rabbitmq" {
  count = local.deployment_rabbitmq.enabled ? 1 : 0

  name       = local.deployment_rabbitmq.name
  namespace  = local.deployment_rabbitmq.namespace
  repository = local.deployment_rabbitmq.repository
  chart      = local.deployment_rabbitmq.chart
  version    = local.deployment_rabbitmq.version
  timeout    = local.deployment_rabbitmq.timeout

  dynamic "set" {
    for_each = {
      "auth.username" = local.rabbitmq.user
      "service.port"  = local.rabbitmq.port
    }

    content {
      name  = set.key
      value = set.value
    }
  }

  dynamic "set_sensitive" {
    for_each = {
      "auth.password"     = local.rabbitmq.password
      "auth.erlangCookie" = random_password.rabbitmq_erlang_cookie.result
    }

    content {
      name  = set_sensitive.key
      value = set_sensitive.value
    }
  }

  depends_on = [kubernetes_namespace.this]
}
