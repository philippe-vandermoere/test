.PHONY: k8s_deploy_dev

k8s_deploy_dev:
	docker build . -f docker/php/Dockerfile --target prod -t local/test:latest
	cd kubernetes/terraform/dev && terraform init && terraform apply
