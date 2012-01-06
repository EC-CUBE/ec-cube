#!/usr/bin/perl -w
use strict;

=head1 Simple SOAP TCP Server

    Simple SOAP TCP Server with test class (just to check things are working)

    Before you run this test server you will need some perl classes, namely:
        SOAP::Lite
        Hook::LexWrap (if you want to see some debugging)

        Flee off to the CPAN if you need them :)

    To run type 'perl <filename>' and if you dont get any errors, it's time
    to go write some code to connect.
=cut

use SOAP::Transport::TCP qw(trace);
use Data::Dumper;


#############
## if you want to see incoming/outgoing raw xml
## uncomment the following.

#use Hook::LexWrap;
#wrap *IO::SessionData::read, post => \&show_read;
#wrap *IO::SessionData::write, post => \&show_write;

##
#############


my $daemon = SOAP::Transport::TCP::Server->new(
    LocalAddr => '127.0.0.1',
    LocalPort => '82',
    Listen    => 5,
    Reuse     => 1
);

# dispatch
$daemon->dispatch_to('SOAP_Example_Server');
$daemon->handle;

#############
## callback functions for Hook::LexWrap;
##

# show incoming xml
sub show_read {
    print $/,'## read ##',$/;
    print Dumper($_[0]);
}

# show outgoing xml
sub show_write {
    print $/,'## write ##',$/;
    print Dumper($_[0]);
}
################################################################################



################################################################################
# SOAP_Example_Server
# Simple test class, method test returns double what you send to it, thats all!
################################################################################
package SOAP_Example_Server;

sub echoString {
    return $_[1] x 2;
}

1;
