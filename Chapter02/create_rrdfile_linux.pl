#/usr/bin/perl
###
# RRD File Creation Scrip
# Book: "Cacti 1.x Designing NOC,Beginner's Guide"
# Chapter 2 - "Creating graphs with the rrdtool"
# Version 1.0
###

use strict;

# Constant declaration
use constant DATACOUNT => 200;          # number of updates that should occur
                                        # ( 5 minute updates. 1440 = 24h )
use constant TIME => 1488153600;        # start time of the 1st update
use constant RANDOMRANGE => 100;        # Value to add ranges from 0 to 100

my $filename = $ARGV[0];                # rrdfile to use
my $count = 0;                          # no updates have been done

# For the rrdfile we need to start 1 second earlier than the first update.
my $startTime = TIME - 1;

# create the rrdfile as defined in the preface
`rrdtool create $filename --start $startTime --step 300 DS:data:GAUGE:600:U:U RRA:AVERAGE:0.5:1:16 RRA:AVERAGE:0.5:4:16 RRA:AVERAGE:0.5:12:16 RRA:MAX:0.5:1:16 RRA:MAX:0.5:4:16 RRA:MAX:0.5:12:16 RRA:LAST:0.5:1:16 RRA:LAST:0.5:4:16 RRA:LAST:0.5:12:16`;

# Update the rrd file with data. Cycle through each update step and increase
# the time by 300 seconds ( default rrd step of 5 minutes )
for ( my $myTime = TIME; $count < DATACOUNT; $myTime = $myTime + 300 ) {
        # Create a random number to fill the rrdfile with
        my $random_number = int( rand( RANDOMRANGE ) );
        # call the rrdtool to put the data at the specific time
        # errors returned will be printed to the CLI
        print `rrdtool update $filename $myTime:$random_number`;

        # After the update we increase the counter by 1
        $count++;
}

# end
