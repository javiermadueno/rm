set :application, "RM"
set :domain,      "192.168.100.218"
set :user,        "javi"
set :password,    "icca"
set :deploy_to,   "/var/www/html"
set :app_path,    "app"
set :web_path,    "web"
set :use_sudo,    false

default_run_options[:pty] = true

set :repository,  "https://iccacdb.dynalias.com:8443/svn/RLT_MESSAGES/Symfony/"
set :scm,         :subversion
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `subversion`, `mercurial`, `perforce`, or `none`

set :model_manager, "doctrine"
# Or: `propel`

role :w¿eb,        domain                         # Your HTTP server, Apache/etc
role :app,        domain, :primary => true       # This may be the same as your `Web` server

set :shared_files,        ["app/config/parameters.yml"]
set :shared_children,     [app_path + "/logs", app_path + "/cache", "vendor", web_path + "/3"]
set :writable_dirs, [app_path + "/logs", app_path + "/cache"]
set :webserver_user,      "www-data"
set :permission_method,   :acl
set :use_set_permissions, true
set :use_composer, true
set :update_vendors, true

set  :keep_releases,  4

# Be more verbose by uncommenting the following line
# logger.level = Logger::MAX_LEVEL
