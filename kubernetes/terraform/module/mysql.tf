locals {
  deployment_mysql = {
    enabled    = var.deployment_mysql.enabled
    name       = var.deployment_mysql.name
    namespace  = (var.deployment_mysql.namespace != "") ? var.deployment_mysql.namespace : var.application.name
    repository = "https://charts.bitnami.com/bitnami"
    chart      = "mysql"
    version    = var.deployment_mysql.version
    timeout    = 60
  }

  mysql = {
    host     = var.deployment_mysql.enabled ? format("mysql.%s.svc.cluster.local", local.deployment_mysql.namespace): var.mysql.host
    port     = var.mysql.port
    user     = (var.mysql.user != "") ? var.mysql.user : var.application.name
    password = (var.mysql.password != "") ? var.mysql.password : random_password.mysql.result
    database = (var.mysql.database != "") ? var.mysql.database : var.application.name
  }
}

resource "random_password" "mysql_root" {
  length  = 32
  special = false
}

resource "random_password" "mysql" {
  length  = 32
  special = false
}

resource "helm_release" "mysql" {
  count = local.deployment_mysql.enabled ? 1 : 0

  name       = local.deployment_mysql.name
  namespace  = local.deployment_mysql.namespace
  repository = local.deployment_mysql.repository
  chart      = local.deployment_mysql.chart
  version    = local.deployment_mysql.version
  timeout    = local.deployment_mysql.timeout

  dynamic "set" {
    for_each = {
      "auth.username"          = local.mysql.user
      "auth.database"          = local.mysql.database
      "primary.service.port"   = var.mysql.port
      "secondary.service.port" = var.mysql.port
    }

    content {
      name  = set.key
      value = set.value
    }
  }

  dynamic "set_sensitive" {
    for_each = {
      "auth.rootPassword" = random_password.mysql_root.result
      "auth.password"     = local.mysql.password
    }

    content {
      name  = set_sensitive.key
      value = set_sensitive.value
    }
  }

  depends_on = [kubernetes_namespace.this]
}
