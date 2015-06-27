# -*- mode: ruby -*-
# vi: set ft=ruby :

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
Vagrant.configure(2) do |config|
  config.vm.box = "centos65"
  config.vm.network "private_network", ip: "192.168.33.33"
  config.vm.synced_folder ".", "/var/www/vagrant", :create => true, :owner => 'vagrant', :group => 'vagrant', :mount_options => ['dmode=777', 'fmode=666']

  config.vm.provision "chef_solo" do |chef|
    chef.cookbooks_path = "chef/site-cookbooks/"
    chef.run_list = %w[
      recipe[localedef]
      recipe[remi]
      recipe[apache]
      recipe[apache::vagrant]
      recipe[php]
      recipe[mysql]
      recipe[mysql::createdb]
    ]
  end
 
  config.omnibus.chef_version = :latest
end
