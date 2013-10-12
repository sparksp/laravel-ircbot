
# set :stages,          %w(production staging develop)
# set :default_stage,   'develop'
# require "capistrano/ext/multistage"

server1 = 'rommie'


set :application,     'rommie'
set :scm,             :git
set :repository,      "git@github.com:sparksp/laravel-ircbot.git"
set :ssh_options,     { forward_agent: true }
set :scm_username,    'me@phills.me.uk'
set :deploy_to,       "/home/phills/rommie"
set :deploy_via,      :remote_cache
set :keep_releases,   3
set :use_sudo,        false
set :shared_children, %w( storage/cache storage/database storage/logs storage/sessions storage/views storage/work config/irc )

set :user,  'phills'
set :group, 'phills'

role :web, 'rommie'

default_run_options[:pty] = true 

task :uname do
  run 'uname -a'
end

# we will ask which branch to deploy; default = current
# http://nathanhoad.net/deploy-from-a-git-tag-with-capistrano
set :branch do
  default_tag = `git rev-parse --abbrev-ref HEAD`.strip

  tag = Capistrano::CLI.ui.ask "Branch to deploy (make sure to push first): [#{default_tag}] "
  tag = default_tag if tag.empty?
  tag
end unless exists?(:branch)

task :create_symlinks, :roles => :web do  
  run "rm -rf #{current_release}/storage && ln -s #{shared_path}/storage #{current_release}/storage"
  run "ln -s #{shared_path}/config/irc #{current_release}/bundles/irc/config/local"
  run "ln -s #{shared_path}/env.php #{current_release}/env.php"
end

after 'deploy:update', 'deploy:cleanup'
after 'deploy:finalize_update', :create_symlinks
