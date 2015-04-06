case node['platform_family']
when 'debian'
  %w{libxml2 libxml2-dev libxslt1-dev}.each do |pkg|
    package pkg do
      action :install
    end
  end
when 'rhel'
  %w{gcc make ruby-devel libxml2 libxml2-devel libxslt libxslt-devel}.each do |pkg|
    package pkg do
      action :install
    end
  end
end

package 'curl' do
  action :install
end
