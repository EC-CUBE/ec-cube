require 'tmpdir'

def run(cmd)
  %x{#{cmd}}
  assert $CHILD_STATUS.success?
end

def svn_commit_new_file(filename, content)
  File.open(filename, 'w') { |f| f.write(content) }
  run "svn add #{filename} && svn commit -m 'Committed a change.'"
end

def svn_remove_file(filename)
  run "svn rm #{filename} && svn commit -m 'Revert previous commit.'"
end

def svn_repository(path)
  Dir.mktmpdir do |dir|
    Dir.chdir dir
    run "svn co http://#{test_host}#{path}"
    Dir.chdir File.join(dir, path)
    yield
  end
end
