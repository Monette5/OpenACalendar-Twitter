# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|

	# It seems to need this here or "destroy" errors.
  config.vm.box = "boxcutter/debian8"

	config.vm.define "app" do |normal|

		config.vm.box = "boxcutter/debian8"

		config.vm.synced_folder ".", "/vagrant",  :owner=> 'vagrant', :group=>'users', :mount_options => ['dmode=777', 'fmode=777']

		config.vm.provider "virtualbox" do |vb|
			vb.gui = false

			vb.memory = "512"

			# https://github.com/boxcutter/ubuntu/issues/82#issuecomment-260902424
			vb.customize [
				"modifyvm", :id,
				"--cableconnected1", "on",
			]

		end

		config.vm.provision :shell, path: "vagrant/app/bootstrap.sh"

	end

end
