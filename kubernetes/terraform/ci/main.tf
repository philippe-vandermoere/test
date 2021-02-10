terraform {
  required_version = "0.14.3"

  required_providers {
    helm = {
      source = "hashicorp/helm"
      version = "2.0.1"
    }
    kubernetes = {
      source = "hashicorp/kubernetes"
      version = "1.13.3"
    }
    random = {
      source = "hashicorp/random"
      version = "3.0.0"
    }
  }
}

provider "helm" {
  kubernetes {
    config_path = "~/.kube/config"
  }
}

provider "kubernetes" {
  config_path = "~/.kube/config"
}

variable "name" {
  type = string
}

variable "docker_image" {
  type = string
}

variable "docker_image_tag" {
  type = string
}

module "this" {
  source = "../module"

  application = {
    name             = var.name
    version          = "ci"
    docker_image     = var.docker_image
    docker_image_tag = var.docker_image_tag
  }
}
