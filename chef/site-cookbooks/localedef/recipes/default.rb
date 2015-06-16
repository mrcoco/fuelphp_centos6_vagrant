#
# Cookbook Name:: localedef
# Recipe:: default
#
# Copyright 2015, YOUR_COMPANY_NAME
#
# All rights reserved - Do Not Redistribute
#
bash 'localedef' do
  code 'localedef -f UTF-8 -i ja_JP ja_JP.UTF-8'
end