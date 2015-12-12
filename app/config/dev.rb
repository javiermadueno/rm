server '192.168.100.83', :app, :web, :primary => true
set :symfony_env_prod, "dev"
set :clear_controllers, false
set :user,        "icca"
set :password,    "icca"