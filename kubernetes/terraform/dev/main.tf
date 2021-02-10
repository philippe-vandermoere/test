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

module "this" {
  source = "../module"

  application = {
    name             = "test"
    version          = "dev"
    docker_image     = "local/test"
    docker_image_tag = "latest"
  }
}
