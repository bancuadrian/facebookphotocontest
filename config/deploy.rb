# config valid only for Capistrano 3.1

set :application, 'Facebook_Photo_Contest'
set :repo_url, 'git@bitbucket.org:bancuadrian/fbphotocontest.git'

set :scm, :git

set :stages, ["production"]
set :default_stage, "production"
set :use_sudo, false
set :ssh_options, { :forward_agent => true, :keys => '~/.ssh/deployment_rsa'}

set :user, "admin"

namespace :deploy do
    desc "Build"
    after :updated, :build do
        on roles(:app) do
            within release_path  do
                execute :composer, "install" # install dependencies
                execute :chmod, "u+x artisan" # make artisan executable
                execute :php, "artisan cron:install" # make artisan executable
                execute :php, "artisan migrate" # make artisan executable
                execute :rm, "-rf storage/logs" # remove logs folder
                execute :ln, "-s #{shared_path}/logs storage/logs" # symlink uploads
                execute :ln, "-s #{shared_path}/backups backups" # symllink ips-files uploads
                execute :ln, "-s #{shared_path}/uploads public/uploads" # symllink ips-files uploads
                execute :ln, "-s #{shared_path}/sample_images sample_images" # symllink ips-files uploads
            end
        end
    end
end
