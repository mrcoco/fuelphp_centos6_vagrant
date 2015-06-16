
execute "createdb" do
  command "/usr/bin/mysql -u root < #{Chef::Config[:file_cache_path]}/createdb.sql"
  action :nothing
end
 
template "#{Chef::Config[:file_cache_path]}/createdb.sql" do
  owner 'root'
  group 'root'
  mode 644
  source 'createdb.sql.erb'
  variables({
      :db_dev => node['mysql']['database']['dev'],
      :db_stg => node['mysql']['database']['stg'],
      :db_prod => node['mysql']['database']['prod'],
      :db_test => node['mysql']['database']['test'],
    })
  notifies :run, 'execute[createdb]', :immediately
end
 
# create database user
execute 'createuser' do
  command "/usr/bin/mysql -u root < #{Chef::Config[:file_cache_path]}/createuser.sql"
  action :nothing
end
 
template "#{Chef::Config[:file_cache_path]}/createuser.sql" do
  owner 'root'
  group 'root'
  mode 644
  source 'createuser.sql.erb'
  variables({
      :db_dev => node['mysql']['database']['dev'],
      :db_stg => node['mysql']['database']['stg'],
      :db_prod => node['mysql']['database']['prod'],
      :db_test => node['mysql']['database']['test'],
      :username => node['mysql']['username'],
      :password => node['mysql']['password'],
    })
  notifies :run, 'execute[createuser]', :immediately
end