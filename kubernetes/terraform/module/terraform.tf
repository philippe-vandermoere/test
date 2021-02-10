terraform {
  required_version = ">=0.14.0"

  required_providers {
    helm = {
      source = "hashicorp/helm"
      version = ">=2.0.0"
    }
    kubernetes = {
      source = "hashicorp/kubernetes"
      version = ">=1.13.0"
    }
    random = {
      source = "hashicorp/random"
      version = ">=3.0.0"
    }
  }
}
