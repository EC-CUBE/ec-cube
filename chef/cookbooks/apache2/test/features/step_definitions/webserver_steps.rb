Given(/^a new webserver.*$/) do

end

Given(/^an alias defined|a path configured to allow directory listing.*$/) do
  # /icons/ is defined by default
end

When(/^a request is made to a (CGI|Java|Perl|Python|PHP) (?:script|application) that generates a list of (?:environment variables|request parameters)$/) do |script_type|
  http_request case script_type
               when 'CGI' then '/cgi-bin/env'
               when 'Python' then '/env/python.py'
               else "/env/#{script_type.downcase}"
  end
end

When 'I request a path which has a cache directive applied' do
  http_request '/cachetest/'
end

When 'I request a URL known not to exist' do
  http_request '/this-path-does-not-exist'
end

When 'I request as a known browser that only supports HTTP/1.0' do
  @response_version = http_response_version('JDK/1.0', '1.0')
end

When(/^I request the (?:alias|directory listing) path$/) do
  http_request '/icons/'
end

When 'I request the root path of the webapp' do
  http_request '/basic_web_app/'
end

When(/^I request the root url( over HTTPS)?$/) do |secure|
  if secure
    https_request '/'
  else
    http_request '/'
  end
end

When 'I request the status page from a remote host' do
  http_request '/server-status/'
end

When(/^the authenticated user is (not )?listed (?:in the directory )(?:in the file|as authorized)$/) do |not_listed|
  http_request '/secure/',
               :basic_auth => {
                 :username => not_listed ? 'meatballs' : 'bork',
                 :password => 'secret'
               }
end

When 'the browser requests a page specifying that it does not support compression' do
  @response_was_compressed = compresses_response?(:client_no_support)
end

When 'the browser requests a page specifying that it supports compression' do
  @response_was_compressed = compresses_response?(:client_supports)
end

When(/^the remote address is (not )?listed as authorized$/) do |not_listed|
  http_request '/secure/'
end

When(/^the user requests the secure page authenticating with (in)?valid credentials over (basic|digest) auth$/) do |invalid, auth_type|
  http_request '/secure/', "#{auth_type}_auth".to_sym => {
    :username => 'bork',
    :password => invalid ? 'squirrel' : 'secret'
  }
end

When 'the user requests the secure page with no credentials' do
  http_request '/secure/'
end

Then(/^access will be (denied|rejected requiring (?:OpenID )?authentication|granted)$/) do |access|
  http_response.code.must_equal({
    'denied' => 403,
    'rejected requiring authentication' => 401,
    'rejected requiring OpenID authentication' => 200,
    'granted' => 200
  }[access])
  if access == 'rejected requiring OpenID authentication'
    http_response.body.must_include 'This site is protected and requires that you identify yourself with an <a href="http://openid.net">OpenID</a> url.'
  end
end

Then 'I will be able to sort the files by size' do
  http_request '/icons/?C=S;O=A'
  # icons differ on different distros
  dir_listing_entries[1].must_equal 'small/'
end

Then 'page not found should be returned' do
  http_response.body.must_include 'Not Found'
  http_response.code.must_equal 404
end

Then 'simple statistics will be shown' do
  http_response.body.must_include 'Apache Status'
  ['Server uptime', 'requests currently being processed', 'idle workers'].each do |stat|
    http_response.body.must_include stat
  end
end

Then 'the aliased resource should be returned successfully' do
  http_response.body.must_include 'Index of /icons'
  http_response.code.must_equal 200
end

Then 'the default page should be returned' do
  assert default_page_present?(http_response.body)
end

Then 'the directory listing should be returned successfully' do
  http_response.body.must_include 'Index of /icons'
  http_response.body.must_include 'Parent Directory'
  dir_listing_entries.must_include 'README'
  dir_listing_entries.must_include 'a.png'
  http_response.code.must_equal 200
end

Then 'the expected environment variables will be present' do
  env = environment_variables(http_response.body)
  env['GATEWAY_INTERFACE'].must_include 'CGI/1.1'
  env['SERVER_SOFTWARE'].must_equal 'Apache'
end

Then 'the expected request parameters will be present' do
  params = request_parameters(http_response.body)
  params['Method'].must_equal 'GET'
  params['Protocol'].must_equal 'HTTP/1.1'
  params['Request URI'].must_equal '/examples/servlets/servlet/RequestInfoExample'
end

Then 'the expiry time returned will match that configured' do
  http_response.code.must_equal 200
  cache_time_seconds(http_response.headers).must_equal 60
  max_age_seconds(http_response.headers).must_equal 60
end

Then 'the response should be HTTP/1.0 also' do
  @response_version.must_equal '1.0'
end

Then(/^the response will be sent (un)?compressed$/) do |expect_uncompressed|
  if expect_uncompressed
    refute @response_was_compressed
  else
    assert @response_was_compressed
  end
end

Then 'the webapp default page will be returned' do
  http_response.body.must_include 'Hello World'
end
