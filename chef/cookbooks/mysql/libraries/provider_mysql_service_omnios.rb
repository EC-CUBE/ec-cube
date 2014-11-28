require 'chef/provider/lwrp_base'
require 'shellwords'

include Opscode::Mysql::Helpers

class Chef
  class Provider
    class MysqlService
      class Omnios < Chef::Provider::MysqlService
        use_inline_resources if defined?(use_inline_resources)

        def whyrun_supported?
          true
        end

        action :create do
          converge_by 'omnios pattern' do
            ##########
            pkg_ver_string = new_resource.version.gsub('.', '')

            base_dir = "/opt/mysql#{pkg_ver_string}"
            prefix_dir = "/opt/mysql#{pkg_ver_string}"
            include_dir = "/opt/mysql#{pkg_ver_string}/etc/mysql/conf.d"
            run_dir = '/var/run/mysql'
            pid_file = '/var/run/mysql/mysql.pid'
            socket_file = '/tmp/mysql.sock'

            case new_resource.version
            when '5.5'
              my_cnf = "#{base_dir}/etc/my.cnf"
            when '5.6'
              my_cnf = "#{base_dir}/my.cnf"
            end
            ##########

            package new_resource.package_name do
              action :install
            end

            directory include_dir do
              owner 'mysql'
              group 'mysql'
              mode '0750'
              recursive true
              action :create
            end

            directory run_dir do
              owner 'mysql'
              group 'mysql'
              mode '0755'
              action :create
              recursive true
            end

            # data_dir
            directory new_resource.data_dir do
              owner 'mysql'
              group 'mysql'
              mode '0750'
              action :create
              recursive true
            end

            directory "#{new_resource.data_dir}/data" do
              owner 'mysql'
              group 'mysql'
              mode '0750'
              action :create
              recursive true
            end

            directory "#{new_resource.data_dir}/data/mysql" do
              owner 'mysql'
              group 'mysql'
              mode '0750'
              action :create
              recursive true
            end

            directory "#{new_resource.data_dir}/data/test" do
              owner 'mysql'
              group 'mysql'
              mode '0750'
              action :create
              recursive true
            end

            template my_cnf do
              if new_resource.template_source.nil?
                source "#{new_resource.version}/my.cnf.erb"
                cookbook 'mysql'
              else
                source new_resource.template_source
              end
              owner 'mysql'
              group 'mysql'
              mode '0600'
              variables(
                :base_dir => base_dir,
                :include_dir => include_dir,
                :data_dir => new_resource.data_dir,
                :pid_file => pid_file,
                :socket_file => socket_file,
                :port => new_resource.port,
                :lc_messages_dir => "#{base_dir}/share"
                )
              action :create
              notifies :run, 'bash[move mysql data to datadir]'
              notifies :restart, 'service[mysql]'
            end

            bash 'move mysql data to datadir' do
              user 'root'
              code <<-EOH
              /usr/sbin/svcadm disable mysql \
              && mv /var/mysql/* #{new_resource.data_dir}
              EOH
              action :nothing
              creates "#{new_resource.data_dir}/ibdata1"
              creates "#{new_resource.data_dir}/ib_logfile0"
              creates "#{new_resource.data_dir}/ib_logfile1"
            end

            execute 'initialize mysql database' do
              cwd new_resource.data_dir
              command "#{prefix_dir}/scripts/mysql_install_db --basedir=#{base_dir} --user=mysql"
              creates "#{new_resource.data_dir}/mysql/user.frm"
            end

            template '/lib/svc/method/mysqld' do
              cookbook 'mysql'
              source 'omnios/svc.method.mysqld.erb'
              cookbook 'mysql'
              owner 'root'
              group 'root'
              mode '0555'
              variables(
                :base_dir => base_dir,
                :data_dir => new_resource.data_dir,
                :pid_file => pid_file
                )
              action :create
            end

            template '/tmp/mysql.xml' do
              cookbook 'mysql'
              source 'omnios/mysql.xml.erb'
              owner 'root'
              mode '0644'
              variables(:version => new_resource.version)
              action :create
              notifies :run, 'execute[import mysql manifest]', :immediately
            end

            execute 'import mysql manifest' do
              command 'svccfg import /tmp/mysql.xml'
              action :nothing
            end

            service 'mysql' do
              supports :restart => true
              action [:start, :enable]
            end

            execute 'wait for mysql' do
              command "until [ -S #{socket_file} ] ; do sleep 1 ; done"
              timeout 10
              action :run
            end

            execute 'assign-root-password' do
              cmd = "#{prefix_dir}/bin/mysqladmin"
              cmd << ' -u root password '
              cmd << Shellwords.escape(new_resource.server_root_password)
              command cmd
              action :run
              only_if "#{prefix_dir}/bin/mysql -u root -e 'show databases;'"
            end

            template '/etc/mysql_grants.sql' do
              cookbook 'mysql'
              source 'grants/grants.sql.erb'
              owner 'root'
              group 'root'
              mode '0600'
              variables(:config => new_resource)
              action :create
              notifies :run, 'execute[install-grants]'
            end

            if new_resource.server_root_password.empty?
              pass_string = ''
            else
              pass_string = '-p' + Shellwords.escape(new_resource.server_root_password)
            end

            execute 'install-grants' do
              cmd = "#{prefix_dir}/bin/mysql"
              cmd << ' -u root '
              cmd << "#{pass_string} < /etc/mysql_grants.sql"
              command cmd
              retries 5
              retry_delay 2
              action :nothing
            end
          end
        end

        action :restart do
          converge_by 'omnios pattern' do
            service 'mysql' do
              supports :restart => true
              action :restart
            end
          end
        end

        action :reload do
          converge_by 'omnios pattern' do
            service 'mysql' do
              supports :reload => true
              action :reload
            end
          end
        end
      end
    end
  end
end

Chef::Platform.set :platform => :omnios, :resource => :mysql_service, :provider => Chef::Provider::MysqlService::Omnios
