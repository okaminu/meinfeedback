node[:deploy].each do |application, deploy|
#  Chef::Log.debug("")

  file "#{release_path}/app/config/parameters.yml" do
    database = deploy[:database]
    content ERB.new(::File.read("#{release_path}/app/config/parameters.yml.#{node[:opsworks][:stack][:name]}")).result(binding)
    action :create
    mode "0644"
  end

end

directory "#{release_path}/app/cache" do
    mode 00777
    owner "www-data"
    group "www-data"
    action :create
    recursive true
end

directory "#{release_path}/app/logs" do
    mode 00777
    owner "www-data"
    group "www-data"
    action :create
    recursive true
end

execute "install composer.phar" do
    cwd release_path
    command "curl -s http://getcomposer.org/installer | php"
    action :run
end

execute "install composer.json (actually composer.lock)" do
    cwd release_path
    command "php composer.phar install --prefer-dist"
    action :run
end

execute "prepare symfony cache for production" do
    cwd release_path
    command "php app/console cache:clear --env=prod"
    action :run
end

execute "fix cache and logs owner" do
    cwd release_path
    command "chown -R www-data:www-data app/cache app/logs"
    action :run
end

execute "fix cache and logs permissions" do
    cwd release_path
    command "chmod -R 0777 app/cache app/logs"
    action :run
end

directory "/srv/media/meinfeedback_app/uploads" do
    mode 00777
    owner "www-data"
    group "www-data"
    action :create
    not_if do ::File.directory?('/srv/media/meinfeedback_app/uploads') end
    recursive true
end

execute "create Document bundle uploads symlink" do
    cwd release_path
    command "ln -s /srv/media/meinfeedback_app/uploads #{release_path}/src/MFB/DocumentBundle/Resources/public/uploads"
    action :run
end

execute "Bootstrap symlink 1 for Mopa" do
    cwd release_path
    command "php app/console mopa:bootstrap:symlink:less -m #{release_path}/vendor/twitter/bootstrap vendor/mopa/bootstrap-bundle/Mopa/Bundle/BootstrapBundle/Resources/public/bootstrap"
    action :run
end

execute "Bootstrap symlink 2 for Mopa" do
    cwd release_path
    command "php app/console mopa:bootstrap:symlink:less -m #{release_path}/vendor/twitter/bootstrap vendor/mopa/bootstrap-bundle/Mopa/Bundle/BootstrapBundle/Resources/bootstrap"
    action :run
end

execute "Dump Assets" do
    cwd release_path
    command "php app/console assetic:dump"
    action :run
end

execute "create bundle assets symlinks" do
    cwd release_path
    command "php app/console assets:install --symlink"
    action :run
end









