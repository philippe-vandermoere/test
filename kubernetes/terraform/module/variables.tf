variable "application" {
  description = "Define the name of the application."

  type = object({
    name             = string
    version          = string
    docker_image     = string
    docker_image_tag = string
  })
}

variable "environment_variables" {
  description = "."
//  sensitive   = true
  type        = map(string)
  default     = {}
}

variable "deployment_config" {
  description = "."
  type        = map(string)
  default     = {}
}

variable "deployment" {
  description = "."

  type = object({
    name      = string
    namespace = string
    version   = string
  })

  default = {
    name      = "symfony"
    namespace = ""
    version   = "1.3.0"
  }
}

#### mysql ####
variable "deployment_mysql" {
  description = "."

  type = object({
    enabled   = bool
    name      = string
    namespace = string
    version   = string
  })

  default = {
    enabled   = true
    name      = "mysql"
    namespace = ""
    version   = "8.2.4"
  }
}

variable "mysql" {
  description = "."
  sensitive   = true

  type = object({
    host     = string
    port     = number
    user     = string
    password = string
    database = string
  })

  default = {
    host     = ""
    port     = 3306
    user     = ""
    password = ""
    database = ""
  }
}

#### rabbitmq ####
variable "deployment_rabbitmq" {
  description = "."

  type = object({
    enabled   = bool
    name      = string
    namespace = string
    version   = string
  })

  default = {
    enabled   = true
    name      = "rabbitmq"
    namespace = ""
    version   = "8.6.2"
  }
}

variable "rabbitmq" {
  description = "."
  sensitive   = true

  type = object({
    host     = string
    port     = number
    user     = string
    password = string
    vhost    = string
  })

  default = {
    host     = ""
    port     = 5672
    user     = ""
    password = ""
    vhost    = "/"
  }
}

#### redis ####
variable "deployment_redis" {
  description = "."

  type = object({
    enabled   = bool
    name      = string
    namespace = string
    version   = string
  })

  default = {
    enabled   = true
    name      = "redis"
    namespace = ""
    version   = "12.3.3"
  }
}

variable "redis" {
  description = "."
  sensitive   = true

  type = object({
    host     = string
    port     = number
    password = string
  })

  default = {
    host     = ""
    port     = 6379
    password = ""
  }
}

