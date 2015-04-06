Given 'a subversion repository' do

end

When 'a developer commits a change to the repository' do
  svn_repository '/svn/' do
    svn_commit_new_file 'README', 'Hello World'
  end
end

Then 'the change will be visible when browsing the repository' do
  begin
    http_request('/svn/README').must_include 'Hello World'
  ensure
    svn_repository '/svn/' do
      svn_remove_file 'README'
    end
  end
end
