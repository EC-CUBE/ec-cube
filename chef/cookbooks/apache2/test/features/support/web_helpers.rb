require 'httparty'
require 'nokogiri'

def test_host
  ENV['TEST_HOST'] || 'localhost'
end

def http_port
  ENV['TEST_HTTP_PORT'] || 80
end

def https_port
  ENV['TEST_HTTPS_PORT'] || 443
end

def cache_time_seconds(http_headers)
  expiry_time = Time.parse(http_headers['expires'])
  server_time = Time.parse(http_headers['date'])
  expiry_time - server_time
end

def compresses_response?(request_type)
  # httparty rewrites the response to hide compression from us
  encoding = %x{curl -s -i #{'--compressed ' if request_type == :client_supports} 'http://#{test_host}/' | grep 'Content-Encoding' | awk -F' ' '{print $2}'}.strip
  %w{deflate gzip}.include?(encoding)
end

def default_page_present?(body)
  ['This is the default web page for this server.',
   'Apache HTTP Server Test Page'].any? { |msg| body.include? msg }
end

# Filenames in a directory listing response
def dir_listing_entries
  Nokogiri::HTML(http_response.body).xpath('//td/a/text()').map { |a| a.to_s }
end

def environment_variables(response_body)
  Hash[response_body.split("\n").map { |v| v.split('=') }]
end

def http_request(path, options = {})
  if options.key?(:digest_auth)
    # HTTParty digest doesn't appear to work
    @response = http_request_digest_curl(path, options)
  else
    @response = HTTParty.get("http://#{test_host}:#{http_port}#{path}", options)
  end
  @response
end

def http_request_digest_curl(path, options)
  credentials = "#{options[:digest_auth][:username]}:#{options[:digest_auth][:password]}"
  curl_response = %x{curl -s -i --digest -u #{credentials} http://#{test_host}:#{http_port}#{path}}
  assert $CHILD_STATUS.success?
  @response = Class.new do
    def initialize(response)
      @curl_response = response
    end

    def code
      @curl_response.scan(/HTTP\/1.1 ([0-9]+)/).flatten.last.to_i
    end
  end.new(curl_response)
end

def https_request(path)
  @response = HTTParty.get("https://#{test_host}:#{https_port}#{path}")
end

def http_response
  @response
end

def http_response_version(user_agent, protocol_version)
  response_line = %x{curl -s #{'-0 ' if protocol_version == '1.0'} -i -A '#{user_agent}' 'http://#{test_host}/' | head -n1}
  assert $CHILD_STATUS.success?
  response_line.scan(/HTTP\/([0-9]+\.[0-9]+) [0-9]+.*/).flatten.first
end

def max_age_seconds(http_headers)
  http_headers['cache-control'].scan(/^max-age=([0-9]+)$/).flatten.first.to_i
end

def request_parameters(response_body)
  Hash[*Nokogiri::HTML(response_body).xpath('//td/text()').map { |h| h.to_s.strip.sub(/:$/, '') }]
end
