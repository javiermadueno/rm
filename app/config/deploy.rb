set :application, "RM"

set :deploy_to,   "/var/www/html"
set :app_path,    "app"
set :web_path,    "web"
set :use_sudo,    false

set :stages,        %w(prod dev)
set :default_stage, "dev"
set :stage_dir,     "app/config"

require 'capistrano/ext/multistage'

default_run_options[:pty] = true

set :repository,  "https://iccacdb.dynalias.com:8443/svn/RLT_MESSAGES/Symfony/"
set :scm,         :subversion
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `subversion`, `mercurial`, `perforce`, or `none`

set :model_manager, "doctrine"
# Or: `propel`

#role :web,        domain                         # Your HTTP server, Apache/etc
#role :app,        domain, :primary => true       # This may be the same as your `Web` server

set :shared_files,        ["app/config/parameters.yml"]
set :shared_children,     [app_path + "/logs", app_path + "/cache", web_path + "/clientes"]
set :writable_dirs, [app_path + "/logs", app_path + "/cache"]
set :webserver_user,      "icca"
set :permission_method,   :acl
set :use_set_permissions, true
set :use_composer, true
set :update_vendors, false
set  :keep_releases,  4

before 'symfony:composer:update', 'symfony:copy_vendors'

namespace :symfony do
  desc "Copy vendors from previous release"
  task :copy_vendors, :except => { :no_release => true } do
    capifony_pretty_print "--> Copy vendor file from previous release"
    run "vendorDir=#{current_path}/vendor; if [ -d $vendorDir ] || [ -h $vendorDir ]; then cp -a $vendorDir #{latest_release}/vendor; fi;"
    capifony_puts_ok
  end
end

# Be more verbose by uncommenting the following line
# logger.level = Logger::MAX_LEVEL
