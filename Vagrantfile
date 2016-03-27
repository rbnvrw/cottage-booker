Vagrant.configure("2") do |config|
  config.vm.box = "hashicorp/precise32"
  config.vm.provision :shell, path: "concrete563.sh"
  config.vm.network "private_network", ip: "192.168.10.11"
end
